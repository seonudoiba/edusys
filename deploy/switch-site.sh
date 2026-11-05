#!/usr/bin/env bash
# Makes Apache serve Edusys for <domain> (run as root; installed as /usr/local/sbin/edusys-switch-site).
#
#   edusys-switch-site edusys.ng                          # enable the Edusys virtual host
#   edusys-switch-site edusys.ng edusys.ng edusys.ng-le-ssl   # ...and disable these old sites
#   edusys-switch-site --undo edusys.ng edusys.ng edusys.ng-le-ssl   # back to the old sites
#
# Uses the HTTPS virtual host when /etc/letsencrypt/live/<domain>/ holds a certificate for <domain>
# (school subdomains also need *.<domain> in it), otherwise the HTTP one. Old sites stay on disk.
# Also sets EDUSYS_FORCE_HTTPS and EDUSYS_HEALTH_URL in /etc/edusys/edusys.env to match.
set -euo pipefail
undo=no
[ "${1:-}" = --undo ] && { undo=yes; shift; }
DOMAIN=${1:?usage: edusys-switch-site [--undo] <domain> [old site ...]}
shift
OLD_SITES=("$@")
ENV_FILE=/etc/edusys/edusys.env
[ "$(id -u)" = 0 ] || { echo "Run as root (sudo)."; exit 1; }

set_env() { # name value
    if grep -q "^$1=" "$ENV_FILE"; then sed -i "s|^$1=.*|$1=$2|" "$ENV_FILE"; else echo "$1=$2" >> "$ENV_FILE"; fi
}

if [ "$undo" = yes ]; then
    a2dissite -q edusys edusys-http >/dev/null 2>&1 || true
    for s in "${OLD_SITES[@]}"; do a2ensite -q "$s"; done
    apachectl configtest && systemctl reload apache2
    echo "Old sites enabled again: ${OLD_SITES[*]}"
    exit 0
fi

[ -d /var/www/edusys/current ] || { echo "No release yet (/var/www/edusys/current): deploy first."; exit 1; }

cert=/etc/letsencrypt/live/$DOMAIN/fullchain.pem
https=no
if [ -f "$cert" ] && openssl x509 -in "$cert" -noout -ext subjectAltName 2>/dev/null | grep -q "DNS:$DOMAIN\b"; then
    https=yes
    if ! openssl x509 -in "$cert" -noout -ext subjectAltName 2>/dev/null | grep -q "DNS:\*\.$DOMAIN"; then
        echo "Note: the certificate covers $DOMAIN but not *.$DOMAIN: the platform works over HTTPS; school"
        echo "      subdomains need the wildcard certificate later (deploy/cert-cloudflare.sh)."
    fi
fi

for s in "${OLD_SITES[@]}"; do a2dissite -q "$s" >/dev/null 2>&1 || true; done
if [ "$https" = yes ]; then
    a2dissite -q edusys-http >/dev/null 2>&1 || true
    a2ensite -q edusys
    set_env EDUSYS_FORCE_HTTPS true
    set_env EDUSYS_HEALTH_URL "https://$DOMAIN/site/login"
    mode=HTTPS
else
    echo "No certificate for $DOMAIN: serving plain HTTP (certbot, or deploy/cert-cloudflare.sh for the wildcard)."
    a2dissite -q edusys >/dev/null 2>&1 || true
    a2ensite -q edusys-http
    set_env EDUSYS_FORCE_HTTPS false
    set_env EDUSYS_HEALTH_URL "http://$DOMAIN/site/login"
    mode=HTTP
fi

if ! apachectl configtest; then
    echo "Apache configuration error: restoring the previous sites."
    a2dissite -q edusys edusys-http >/dev/null 2>&1 || true
    for s in "${OLD_SITES[@]}"; do a2ensite -q "$s" >/dev/null 2>&1 || true; done
    exit 1
fi
systemctl restart apache2   # restart, not reload: picks up the environment file changes

scheme=$( [ "$mode" = HTTPS ] && echo https || echo http )
code=$(curl -s -o /dev/null -w '%{http_code}' --max-time 20 --resolve "$DOMAIN:443:127.0.0.1" --resolve "$DOMAIN:80:127.0.0.1" "$scheme://$DOMAIN/site/login" || true)
echo "Edusys now serves $DOMAIN over $mode ($scheme://$DOMAIN/site/login answered $code)."
[ ${#OLD_SITES[@]} -gt 0 ] && echo "To go back: edusys-switch-site --undo $DOMAIN ${OLD_SITES[*]}"
exit 0
