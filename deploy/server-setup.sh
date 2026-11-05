#!/usr/bin/env bash
# One-time preparation of an Ubuntu 24.04 server for Edusys (run as root). Safe to re-run.
#
#   scp -r deploy root@SERVER:/root/edusys-deploy
#   ssh root@SERVER 'bash /root/edusys-deploy/server-setup.sh edusys.ng'
#
# Installs Apache + PHP 8.3 + MySQL 8, creates the deploy user, the release layout in
# /var/www/edusys, /etc/edusys/edusys.env (from edusys.env.example), the virtual host, the cron
# jobs and the log/backup folders. It does NOT create the database accounts, the TLS
# certificate or the platform school: DEPLOYMENT.md lists those steps.
set -euo pipefail
DOMAIN=${1:?usage: server-setup.sh <base domain, e.g. edusys.ng>}
APP_ROOT=/var/www/edusys
DEPLOY_USER=${DEPLOY_USER:-deploy}
HERE=$(cd "$(dirname "$0")" && pwd)
[ "$(id -u)" = 0 ] || { echo "Run as root."; exit 1; }

echo "== Packages"
export DEBIAN_FRONTEND=noninteractive
apt-get update -q
apt-get install -yq openssl apache2 libapache2-mod-php8.3 php8.3-cli php8.3-mysql php8.3-curl php8.3-mbstring \
    php8.3-gd php8.3-zip php8.3-xml php8.3-intl php8.3-bcmath rsync curl unzip \
    certbot python3-certbot-apache
command -v mysqld >/dev/null || apt-get install -yq mysql-server   # an existing MySQL is kept as it is
a2enmod -q rewrite ssl headers php8.3 >/dev/null
# The default site and any other sites are left alone (the server may host more than Edusys).

echo "== PHP settings"
for sapi in apache2 cli; do
    cat > /etc/php/8.3/$sapi/conf.d/99-edusys.ini <<'INI'
; Edusys
expose_php = Off
memory_limit = 512M
upload_max_filesize = 32M
post_max_size = 40M
max_execution_time = 300
date.timezone = Africa/Lagos
opcache.enable = 1
opcache.validate_timestamps = 1
opcache.revalidate_freq = 2
INI
done

echo "== Deploy user ($DEPLOY_USER)"
id "$DEPLOY_USER" >/dev/null 2>&1 || adduser --disabled-password --gecos "Edusys deployments" "$DEPLOY_USER"
usermod -aG www-data "$DEPLOY_USER"
install -d -m 700 -o "$DEPLOY_USER" -g "$DEPLOY_USER" "/home/$DEPLOY_USER/.ssh"
touch "/home/$DEPLOY_USER/.ssh/authorized_keys"
chown "$DEPLOY_USER:$DEPLOY_USER" "/home/$DEPLOY_USER/.ssh/authorized_keys"; chmod 600 "/home/$DEPLOY_USER/.ssh/authorized_keys"
# The only root command deployments need.
echo "$DEPLOY_USER ALL=(root) NOPASSWD: /usr/bin/systemctl reload apache2" > /etc/sudoers.d/edusys-deploy
chmod 440 /etc/sudoers.d/edusys-deploy
visudo -cf /etc/sudoers.d/edusys-deploy >/dev/null

echo "== Folders"
install -d -o "$DEPLOY_USER" -g www-data -m 2755 "$APP_ROOT" "$APP_ROOT/releases" "$APP_ROOT/incoming"
install -d -o www-data -g www-data -m 2775 "$APP_ROOT/shared"
for d in uploads application application/logs application/cache application/tmp backend backend/captcha_images temp; do
    install -d -o www-data -g www-data -m 2775 "$APP_ROOT/shared/$d"
done
install -d -o www-data -g www-data -m 2770 /var/backups/edusys /var/backups/edusys/app
install -d -o www-data -g www-data -m 2775 /var/log/edusys

echo "== Environment (/etc/edusys/edusys.env)"
install -d -m 750 -o root -g www-data /etc/edusys
# Passwords that pass MySQL's validate_password policy (upper, lower, digit, symbol) and are safe
# in the env file, in shell and in SQL quotes.
strong_pw() { printf '%s-Ed9_' "$(openssl rand -base64 48 | tr -dc 'A-Za-z0-9' | head -c 28)"; }
if [ -f /etc/edusys/edusys.env ] && grep -qE '^EDUSYS_DB_PASSWORD=[0-9a-f]{48}$' /etc/edusys/edusys.env; then
    # Written by an earlier version of this script (hex only, refused by validate_password).
    sed -i -e "s/^EDUSYS_DB_PASSWORD=.*/EDUSYS_DB_PASSWORD=$(strong_pw)/" \
           -e "s/^EDUSYS_BACKUP_DB_PASSWORD=.*/EDUSYS_BACKUP_DB_PASSWORD=$(strong_pw)/" /etc/edusys/edusys.env
    echo "   replaced the generated database passwords with stronger ones"
fi
if [ ! -f /etc/edusys/edusys.env ]; then
    app_pw=$(strong_pw); backup_pw=$(strong_pw)
    sed -e "s/edusys\.ng/$DOMAIN/g" \
        -e "s/^EDUSYS_DB_PASSWORD=.*/EDUSYS_DB_PASSWORD=$app_pw/" \
        -e "s/^EDUSYS_BACKUP_DB_PASSWORD=.*/EDUSYS_BACKUP_DB_PASSWORD=$backup_pw/" \
        -e "s/^EDUSYS_FORCE_HTTPS=.*/EDUSYS_FORCE_HTTPS=false/" \
        -e "s/^EDUSYS_HEALTH_URL=.*/EDUSYS_HEALTH_URL=/" \
        "$HERE/edusys.env.example" > /etc/edusys/edusys.env
    echo "   created with generated database passwords; still to fill in: EDUSYS_PAYSTACK_SECRET_KEY, EDUSYS_SMTP_*"
fi
chown root:www-data /etc/edusys/edusys.env; chmod 640 /etc/edusys/edusys.env

echo "== Database and accounts (passwords from the env file)"
set -a; . /etc/edusys/edusys.env; set +a
mysql_root() { mysql "$@"; }
if [ -n "${MYSQL_ROOT_PW:-}" ]; then
    mysql_root() { MYSQL_PWD=$MYSQL_ROOT_PW mysql -u root "$@"; }
elif ! mysql -e 'SELECT 1' >/dev/null 2>&1; then
    # MySQL root has a password (no socket login): ask once.
    read -r -s -p "   MySQL root password: " MYSQL_ROOT_PW < /dev/tty; echo
    mysql_root() { MYSQL_PWD=$MYSQL_ROOT_PW mysql -u root "$@"; }
fi
mysql_root <<SQL
CREATE DATABASE IF NOT EXISTS \`$EDUSYS_DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER IF NOT EXISTS '$EDUSYS_DB_USER'@'127.0.0.1' IDENTIFIED BY '$EDUSYS_DB_PASSWORD';
ALTER USER '$EDUSYS_DB_USER'@'127.0.0.1' IDENTIFIED BY '$EDUSYS_DB_PASSWORD';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX, REFERENCES, LOCK TABLES ON \`$EDUSYS_DB_NAME\`.* TO '$EDUSYS_DB_USER'@'127.0.0.1';
CREATE USER IF NOT EXISTS '$EDUSYS_BACKUP_DB_USER'@'127.0.0.1' IDENTIFIED BY '$EDUSYS_BACKUP_DB_PASSWORD';
ALTER USER '$EDUSYS_BACKUP_DB_USER'@'127.0.0.1' IDENTIFIED BY '$EDUSYS_BACKUP_DB_PASSWORD';
GRANT SELECT, LOCK TABLES, SHOW VIEW, TRIGGER, EVENT ON \`$EDUSYS_DB_NAME\`.* TO '$EDUSYS_BACKUP_DB_USER'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL
echo "   database $EDUSYS_DB_NAME, accounts $EDUSYS_DB_USER and $EDUSYS_BACKUP_DB_USER (127.0.0.1)"
# Apache gets the variables from systemd (the PHP code reads them with getenv()).
install -d /etc/systemd/system/apache2.service.d
cat > /etc/systemd/system/apache2.service.d/edusys.conf <<'UNIT'
[Service]
EnvironmentFile=/etc/edusys/edusys.env
UNIT
systemctl daemon-reload

echo "== Virtual hosts (written, enabled by switch-site.sh)"
sed "s/edusys\.ng/$DOMAIN/g" "$HERE/apache-vhost.conf.example" > /etc/apache2/sites-available/edusys.conf
sed "s/edusys\.ng/$DOMAIN/g" "$HERE/apache-vhost-http.conf.example" > /etc/apache2/sites-available/edusys-http.conf
install -m 755 "$HERE/switch-site.sh" /usr/local/sbin/edusys-switch-site
if [ "${ENABLE_SITE:-yes}" = yes ]; then
    bash "$HERE/switch-site.sh" "$DOMAIN"
else
    echo "   not enabled (ENABLE_SITE=no): run  sudo edusys-switch-site $DOMAIN [old sites to disable]  when ready"
    apachectl configtest
    systemctl restart apache2   # loads the environment file; no site changes
fi

echo "== Cron and log rotation"
install -m 644 "$HERE/edusys.cron" /etc/cron.d/edusys
cat > /etc/logrotate.d/edusys <<'ROT'
/var/log/edusys/*.log {
    weekly
    rotate 8
    compress
    missingok
    notifempty
    su www-data www-data
}
ROT

echo
echo "Done. Next (DEPLOYMENT.md):"
echo "  1. Fill in EDUSYS_PAYSTACK_SECRET_KEY and EDUSYS_SMTP_* in /etc/edusys/edusys.env (the database"
echo "     passwords are generated), then: systemctl restart apache2"
echo "  2. Add the GitHub Actions public key to /home/$DEPLOY_USER/.ssh/authorized_keys"
echo "  3. Run the Deploy workflow, then set up the platform school (DEPLOYMENT.md §0 step 8)"
echo "  4. Switch Apache to Edusys: sudo edusys-switch-site $DOMAIN [old sites to disable]"
echo "  5. Later, for school subdomains: wildcard DNS record and deploy/cert-cloudflare.sh $DOMAIN"
