#!/usr/bin/env bash
# Nightly backup (deploy/edusys.cron): one dump of the shared database and one archive of every
# school's files, kept $KEEP_DAYS days in /var/backups/edusys. Copy them off the server too.
set -euo pipefail
APP_ROOT=${APP_ROOT:-/var/www/edusys}
ENV_FILE=${ENV_FILE:-/etc/edusys/edusys.env}
DEST=${DEST:-/var/backups/edusys}
KEEP_DAYS=${KEEP_DAYS:-14}
set -a; . "$ENV_FILE"; set +a
mkdir -p "$DEST"
day=$(date +%F)

MYSQL_PWD=${EDUSYS_BACKUP_DB_PASSWORD:-$EDUSYS_DB_PASSWORD} mysqldump --single-transaction --no-tablespaces \
  -h "$EDUSYS_DB_HOST" -P "${EDUSYS_DB_PORT:-3306}" -u "${EDUSYS_BACKUP_DB_USER:-$EDUSYS_DB_USER}" "$EDUSYS_DB_NAME" \
  | gzip > "$DEST/edusys-$day.sql.gz.part"
mv "$DEST/edusys-$day.sql.gz.part" "$DEST/edusys-$day.sql.gz"

tar -czf "$DEST/uploads-$day.tar.gz.part" -C "$APP_ROOT/shared" uploads
mv "$DEST/uploads-$day.tar.gz.part" "$DEST/uploads-$day.tar.gz"

find "$DEST" -maxdepth 1 \( -name 'edusys-*.sql.gz' -o -name 'uploads-*.tar.gz' \) -mtime +"$KEEP_DAYS" -delete
echo "$(date '+%F %T') backup ok: $(du -h "$DEST/edusys-$day.sql.gz" | cut -f1) database, $(du -h "$DEST/uploads-$day.tar.gz" | cut -f1) files"
