#!/usr/bin/env bash
# Switches the site back to an earlier release (code only; the database stays as it is).
#
#   deploy/rollback.sh            # the release before the current one
#   deploy/rollback.sh <id>       # a given release (ls /var/www/edusys/releases)
#
# If the release being undone ran migrations, also restore its pre-deploy dump
# (EDUSYS_BACKUP_DIR/pre-deploy-<id>-v<version>.sql.gz) or step down with
# deploy/run.sh migrate version <version>, BEFORE switching back.
set -euo pipefail
APP_ROOT=${APP_ROOT:-/var/www/edusys}
releases=$APP_ROOT/releases
live=$(readlink -f "$APP_ROOT/current")
if [ $# -ge 1 ]; then
    target=$releases/$1
else
    # The newest release older than the live one (ids start with a UTC timestamp).
    target=$(ls -1d "$releases"/*/ | sed 's:/$::' | sort -r | awk -v live="$live" '$0 < live' | head -1)
fi
[ -n "$target" ] && [ -d "$target" ] || { echo "No release to roll back to."; exit 1; }
ln -sfn "$target" "$APP_ROOT/current.new"
mv -T "$APP_ROOT/current.new" "$APP_ROOT/current"
sudo -n /usr/bin/systemctl reload apache2
echo "current -> $(basename "$target") (was $(basename "$live"))"
