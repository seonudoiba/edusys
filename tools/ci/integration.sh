#!/usr/bin/env bash
# Fresh-install integration test (CI). Needs an EMPTY MySQL 8 database it may destroy.
#   1. migrate to the latest version, step down and up again (down() migrations work)
#   2. seed the platform, set up the platform school and a demo school (default data)
#   3. serve the app with PHP's built-in server and crawl both admin panels logged in,
#      with EDUSYS_TENANCY_RAW_SQL=throw (any unscoped SQL fails the page)
#
#   DB_HOST=127.0.0.1 DB_PORT=3306 DB_USER=root DB_PASS=root DB_NAME=edusys_ci bash tools/ci/integration.sh
set -euo pipefail
cd "$(dirname "$0")/../.."
PHP=${PHP:-php}
MYSQL=${MYSQL:-mysql}
PORT=${PORT:-8099}
OUT=${OUT:-$(mktemp -d)}
DOWN_TO=${DOWN_TO:-250}
mkdir -p "$OUT"

export EDUSYS_DB_HOST=${DB_HOST:-127.0.0.1} EDUSYS_DB_PORT=${DB_PORT:-3306}
export EDUSYS_DB_USER=${DB_USER:-root} EDUSYS_DB_NAME=${DB_NAME:-edusys_ci}
if [ -n "${DB_PASS:-}" ]; then export EDUSYS_DB_PASSWORD=$DB_PASS; else export EDUSYS_DB_PASSWORD_EMPTY=true; fi
export EDUSYS_BASE_DOMAIN=edusys.test EDUSYS_CENTRAL_HOST=edusys.test EDUSYS_TENANCY_RAW_SQL=throw
# Mail goes nowhere (no SMTP server): sends fail and are logged, which is fine here.
export EDUSYS_SMTP_HOST=127.0.0.1 EDUSYS_SMTP_PORT=2525

sql() { MYSQL_PWD=${DB_PASS:-} "$MYSQL" -h "$EDUSYS_DB_HOST" -P "$EDUSYS_DB_PORT" -u "$EDUSYS_DB_USER" -N "$@" | tr -d '\r'; }
step() { echo; echo "== $*"; }

step "Empty database $EDUSYS_DB_NAME"
sql -e "DROP DATABASE IF EXISTS \`$EDUSYS_DB_NAME\`; CREATE DATABASE \`$EDUSYS_DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

step "Migrate"
"$PHP" index.php migrate
"$PHP" index.php migrate status | tee "$OUT/migrate-status.txt"
cur=$(sed -n 's/^Current version: *//p' "$OUT/migrate-status.txt")
latest=$(sed -n 's/^Latest version: *\([0-9]*\).*/\1/p' "$OUT/migrate-status.txt")
[ "$cur" = "$latest" ] || { echo "Database at $cur, latest is $latest"; exit 1; }

step "Down to $DOWN_TO and up again"
"$PHP" index.php migrate version "$DOWN_TO"
"$PHP" index.php migrate

step "Platform seed, platform school and a demo school"
"$PHP" index.php seed run platform
# The registry seed (gitignored, absent in CI) may already hold the platform school: reuse it.
platform_id=$(sql "$EDUSYS_DB_NAME" -e "SELECT id FROM school WHERE is_platform = 1 ORDER BY id LIMIT 1")
if [ -z "$platform_id" ]; then
  platform_id=$(sql "$EDUSYS_DB_NAME" -e "
    INSERT INTO school (school_name, email_address, phone_number, school_address, owner_name, is_platform, status, payment_status, cron_flag)
    VALUES ('Edusys', 'platform.admin@edusys.test', '', 'Lagos', 'Platform Admin', 1, 1, 1, 0); SELECT LAST_INSERT_ID();")
fi
platform_email=$(sql "$EDUSYS_DB_NAME" -e "SELECT email_address FROM school WHERE id = $platform_id")
demo_id=$(sql "$EDUSYS_DB_NAME" -e "
  INSERT INTO school (school_name, email_address, phone_number, school_address, owner_name, subdomain, is_platform, status, payment_status, cron_flag)
  VALUES ('Demo Academy', 'owner@demo.test', '08000000000', 'Abuja', 'Demo Owner', 'ci-demo', 0, 1, 1, 0); SELECT LAST_INSERT_ID();")
"$PHP" index.php school_setup run "$platform_id"
"$PHP" index.php school_setup run "$demo_id"

# Known passwords for the two super admins (test database only).
hash=$("$PHP" -r 'echo password_hash("Ci#Pass123", PASSWORD_DEFAULT);')
sql "$EDUSYS_DB_NAME" -e "UPDATE staff SET password = '$hash', is_active = 1
  WHERE (school_id = $platform_id AND email = '$platform_email') OR (school_id = $demo_id AND email = 'owner@demo.test');"
[ "$(sql "$EDUSYS_DB_NAME" -e "SELECT COUNT(*) FROM staff WHERE password = '$hash'")" = 2 ] \
  || { echo "Super admins were not created"; exit 1; }

step "Serve and crawl"
"$PHP" -S "127.0.0.1:$PORT" tools/tenancy/tests/router.php > "$OUT/server.log" 2>&1 &
server=$!
trap 'kill $server 2>/dev/null || true' EXIT
for _ in $(seq 1 30); do curl -s -o /dev/null "http://127.0.0.1:$PORT/" && break; sleep 1; done

failed=0
crawl() { # label host user
  if "$PHP" tools/tenancy/tests/crawl.php --base="http://$2:$PORT" --resolve=127.0.0.1 --user="$3" --pass='Ci#Pass123' \
       --max="${MAX:-400}" --out="$OUT/$1.tsv" > "$OUT/$1.fail" 2> "$OUT/$1.sum"; then
    echo "$1: $(tail -1 "$OUT/$1.sum")"
  else
    echo "$1: FAILED $(tail -1 "$OUT/$1.sum")"; cat "$OUT/$1.fail"; failed=1
  fi
}
crawl platform edusys.test "$platform_email"
crawl school ci-demo.edusys.test owner@demo.test

# Unknown subdomains and platform pages on a school host must not resolve.
code=$(curl -s -o /dev/null -w '%{http_code}' --resolve "nope.edusys.test:$PORT:127.0.0.1" "http://nope.edusys.test:$PORT/")
[ "$code" = 404 ] || { echo "Unknown subdomain gave $code, expected 404"; failed=1; }
code=$(curl -s -o /dev/null -w '%{http_code}' --resolve "ci-demo.edusys.test:$PORT:127.0.0.1" "http://ci-demo.edusys.test:$PORT/admin/schools")
[ "$code" = 404 ] || { echo "admin/schools on a school host gave $code, expected 404"; failed=1; }

echo; echo "Reports in $OUT"
exit $failed
