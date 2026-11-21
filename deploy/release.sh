#!/usr/bin/env bash
# Activates a new release on the server (run by the deploy workflow as the deploy user).
#
#   deploy/release.sh <release-id>
#
# The code must already be in $APP_ROOT/incoming (the workflow rsyncs it there). Steps:
#   1. copy incoming/ to releases/<id>/ (hard links: unchanged files take no space)
#   2. point the writable folders at shared/ (uploads, logs, caches) and refresh uploads/_shared
#   3. if migrations are pending: dump the database (EDUSYS_BACKUP_DIR), then migrate
#   4. switch "current" to the new release atomically and reload Apache
#   5. check EDUSYS_HEALTH_URL; on failure switch back to the previous release
#   6. keep the newest $KEEP releases
#
# Layout:  $APP_ROOT/current -> releases/<id>   (Apache DocumentRoot)
#          $APP_ROOT/releases/<id>/              code, read-only
#          $APP_ROOT/shared/                     uploads/, application/logs/, caches (kept across releases)
set -euo pipefail
APP_ROOT=${APP_ROOT:-/var/www/edusys}
ENV_FILE=${ENV_FILE:-/etc/edusys/edusys.env}
KEEP=${KEEP:-5}
id=${1:?usage: release.sh <release-id>}
[[ $id =~ ^[A-Za-z0-9._-]+$ ]] || { echo "Bad release id: $id"; exit 2; }

incoming=$APP_ROOT/incoming
releases=$APP_ROOT/releases
shared=$APP_ROOT/shared
rel=$releases/$id
log() { echo "[$(date '+%T')] $*"; }

exec 9> "$APP_ROOT/.deploy.lock"
flock -n 9 || { echo "Another deployment is running."; exit 1; }

set -a; . "$ENV_FILE"; set +a
[ -f "$incoming/index.php" ] || { echo "$incoming has no code: rsync it first."; exit 1; }
[ ! -e "$rel" ] || { echo "Release $id already exists."; exit 1; }
previous=""
[ -L "$APP_ROOT/current" ] && previous=$(readlink -f "$APP_ROOT/current")

log "Copying the code to releases/$id"
mkdir -p "$releases"
rsync -a --delete --link-dest="$incoming/" "$incoming/" "$rel/"

log "Linking the writable folders to shared/"
for d in uploads application/logs application/cache application/tmp application/cbt_snapshots backend/captcha_images temp; do
    if [ ! -d "$shared/$d" ]; then mkdir -p "$shared/$d"; chmod 2775 "$shared/$d"; fi  # group www-data (setgid shared/) can write
    if [ "$d" = uploads ] && [ -d "$rel/uploads/_shared" ]; then
        # Default images ship with the code; every school's own files live only in shared/.
        rsync -a "$rel/uploads/_shared/" "$shared/uploads/_shared/"
    fi
    [ -f "$rel/$d/index.html" ] && [ ! -f "$shared/$d/index.html" ] && cp "$rel/$d/index.html" "$shared/$d/"
    rm -rf "${rel:?}/$d"
    mkdir -p "$(dirname "$rel/$d")"
    ln -s "$shared/$d" "$rel/$d"
done
# Local material that must never be on the server, should it slip into the code.
rm -rf "$rel/dbsample" "$rel/school_instance_extracted" "$rel/adminer.php" "$rel/.env"

cd "$rel"
status=$(php index.php migrate status)
current_version=$(sed -n 's/^Current version: *//p' <<< "$status")
latest_version=$(sed -n 's/^Latest version: *\([0-9]*\).*/\1/p' <<< "$status")
if [ "$current_version" != "$latest_version" ]; then
    dump_dir=${EDUSYS_BACKUP_DIR:-/var/backups/edusys/app}
    mkdir -p "$dump_dir"
    dump="$dump_dir/pre-deploy-$id-v$current_version.sql.gz"
    log "Migrations pending ($current_version -> $latest_version): dumping the database to $dump"
    MYSQL_PWD=$EDUSYS_DB_PASSWORD mysqldump --single-transaction --no-tablespaces -h "$EDUSYS_DB_HOST" \
        -P "${EDUSYS_DB_PORT:-3306}" -u "$EDUSYS_DB_USER" "$EDUSYS_DB_NAME" | gzip > "$dump"
    log "Migrating"
    php index.php migrate
else
    log "Database is up to date (version $current_version)"
fi

switch_to() {
    ln -sfn "$1" "$APP_ROOT/current.new"
    mv -T "$APP_ROOT/current.new" "$APP_ROOT/current"
    sudo -n /usr/bin/systemctl reload apache2
}

log "Switching current -> releases/$id"
switch_to "$rel"

if [ -n "${EDUSYS_HEALTH_URL:-}" ]; then
    code=000
    for _ in 1 2 3 4 5; do
        code=$(curl -s -o /dev/null -w '%{http_code}' --max-time 20 "$EDUSYS_HEALTH_URL" || true)
        [ "$code" = 200 ] && break
        sleep 3
    done
    if [ "$code" != 200 ]; then
        log "Health check failed: $EDUSYS_HEALTH_URL answered $code"
        if [ -z "$previous" ]; then
            # First release: nothing to go back to. A fresh install answers 503 until the platform
            # school exists (DEPLOYMENT.md §0 step 8), so this is expected once.
            log "First release: keeping it. Set up the platform school, then check the site (503 = not set up yet)."
            exit 0
        fi
        if [ -d "$previous" ] && [ "$previous" != "$rel" ]; then
            log "Rolling back to $(basename "$previous") (the database is NOT rolled back; see the dump above)"
            switch_to "$previous"
        fi
        exit 1
    fi
    log "Health check OK ($EDUSYS_HEALTH_URL)"
fi

log "Keeping the newest $KEEP releases"
live=$(readlink -f "$APP_ROOT/current")
# Release ids start with a UTC timestamp, so name order is age order.
ls -1d "$releases"/*/ 2>/dev/null | sed 's:/$::' | sort -r | tail -n +"$((KEEP + 1))" | while read -r old; do
    [ "$(readlink -f "$old")" = "$live" ] || rm -rf "$old"
done
log "Release $id is live"
