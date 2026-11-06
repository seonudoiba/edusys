#!/usr/bin/env bash
# Runs an Edusys CLI command in the live release with the server environment loaded:
#   /var/www/edusys/current/deploy/run.sh migrate status
#   /var/www/edusys/current/deploy/run.sh cron_all run
# Used by the cron jobs (deploy/edusys.cron) and by hand. Run it as www-data or the deploy user.
set -euo pipefail
APP_ROOT=${APP_ROOT:-/var/www/edusys}
ENV_FILE=${ENV_FILE:-/etc/edusys/edusys.env}
set -a; . "$ENV_FILE"; set +a
[ -d "$APP_ROOT/current" ] || { echo "No Edusys release yet ($APP_ROOT/current)." >&2; exit 0; }
cd "$APP_ROOT/current"
umask 002 # folders and files stay writable by the web server (group www-data)
exec php index.php "$@"
