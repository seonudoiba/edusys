#!/usr/bin/env bash
# First-time setup on a server that already runs the OLD Edusys (run as root, from the uploaded
# deploy/ folder). Nothing that is live changes: the old site keeps serving until switch-site.
#
#   sudo DEPLOY_USER=edusys bash ~/edusys-deploy/bootstrap-existing-server.sh edusys.ng
#
#   1. backs up every MySQL database (the old site's included) to /var/backups/edusys/legacy/
#   2. runs server-setup.sh with ENABLE_SITE=no (packages, folders, env file, database, cron)
#   3. optionally issues the wildcard certificate through Cloudflare (cert-cloudflare.sh)
# Afterwards: deploy the first release (GitHub Actions), set up the platform school, then
#   sudo edusys-switch-site edusys.ng edusys.ng edusys.ng-le-ssl
set -euo pipefail
DOMAIN=${1:?usage: bootstrap-existing-server.sh <domain>}
HERE=$(cd "$(dirname "$0")" && pwd)
[ "$(id -u)" = 0 ] || { echo "Run as root (sudo)."; exit 1; }

echo "== MySQL root access"
if mysql -e 'SELECT 1' >/dev/null 2>&1; then
    mysql_root() { mysql "$@"; }; dump_root() { mysqldump "$@"; }
else
    read -r -s -p "   MySQL root password: " MYSQL_ROOT_PW < /dev/tty; echo
    export MYSQL_ROOT_PW
    mysql_root() { MYSQL_PWD=$MYSQL_ROOT_PW mysql -u root "$@"; }; dump_root() { MYSQL_PWD=$MYSQL_ROOT_PW mysqldump -u root "$@"; }
    mysql_root -e 'SELECT 1' >/dev/null
fi

echo "== Backing up the existing databases"
dest=/var/backups/edusys/legacy
install -d -m 700 "$dest"
stamp=$(date +%F-%H%M)
dbs=$(mysql_root -N -e "SELECT schema_name FROM information_schema.schemata WHERE schema_name NOT IN ('mysql','information_schema','performance_schema','sys')")
for db in $dbs; do
    dump_root --single-transaction --routines --events --no-tablespaces "$db" | gzip > "$dest/$db-$stamp.sql.gz"
    printf '   %-30s %s\n' "$db" "$(du -h "$dest/$db-$stamp.sql.gz" | cut -f1)"
done
ls -1 /etc/apache2/sites-enabled > "$dest/apache-sites-enabled-$stamp.txt"
echo "   saved in $dest (the old site's files stay in place, untouched)"

echo "== Server setup (the new site is NOT enabled yet)"
ENABLE_SITE=no bash "$HERE/server-setup.sh" "$DOMAIN"

echo
read -r -p "Issue the wildcard certificate for $DOMAIN and *.$DOMAIN through Cloudflare now? [y/N] " ans < /dev/tty
if [[ $ans =~ ^[Yy] ]]; then
    bash "$HERE/cert-cloudflare.sh" "$DOMAIN"
fi
echo
echo "Bootstrap done. The old site is still the one serving $DOMAIN."
