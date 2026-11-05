#!/usr/bin/env bash
# Wildcard certificate for <domain> and *.<domain> through Cloudflare DNS (run as root).
# Renews automatically (certbot's timer) and reloads Apache after each renewal.
#
#   bash deploy/cert-cloudflare.sh edusys.ng
#
# Asks for a Cloudflare API token with "Zone > DNS > Edit" on the zone (Cloudflare dashboard >
# My Profile > API Tokens > Create Token > "Edit zone DNS" template). It is kept in
# /etc/letsencrypt/cloudflare.ini (root only).
set -euo pipefail
DOMAIN=${1:?usage: cert-cloudflare.sh <domain>}
EMAIL=${EMAIL:-admin@$DOMAIN}
[ "$(id -u)" = 0 ] || { echo "Run as root (sudo)."; exit 1; }

apt-get install -yq python3-certbot-dns-cloudflare >/dev/null
ini=/etc/letsencrypt/cloudflare.ini
if [ ! -s "$ini" ]; then
    read -r -s -p "Cloudflare API token (DNS edit on $DOMAIN): " token < /dev/tty; echo
    install -d -m 700 /etc/letsencrypt
    umask 077; printf 'dns_cloudflare_api_token = %s\n' "$token" > "$ini"; chmod 600 "$ini"
fi

# Same certificate name as an existing "certbot --apache" one, so vhosts that use it keep working.
certbot certonly --non-interactive --agree-tos -m "$EMAIL" \
    --dns-cloudflare --dns-cloudflare-credentials "$ini" --dns-cloudflare-propagation-seconds 30 \
    --cert-name "$DOMAIN" --expand -d "$DOMAIN" -d "www.$DOMAIN" -d "*.$DOMAIN" \
    --deploy-hook "systemctl reload apache2"
openssl x509 -in "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" -noout -subject -ext subjectAltName -enddate
