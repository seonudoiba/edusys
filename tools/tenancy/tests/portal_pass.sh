#!/usr/bin/env bash
# Logged-in crawl of every portal (TEST database only). Needs the app served on 127.0.0.1:$PORT
# with EDUSYS_BASE_DOMAIN=edusys.test, EDUSYS_CENTRAL_HOST=edusys.test and, for a strict pass,
# EDUSYS_TENANCY_RAW_SQL=throw. The accounts below must exist with these passwords in the test
# database (see SINGLE_DB_TENANCY_PLAN.md §6 for how the test pass prepares them).
#
# Usage: PHP=php PORT=8099 MAX=250 OUT=/tmp/portal bash tools/tenancy/tests/portal_pass.sh
set -u
PHP=${PHP:-php}
PORT=${PORT:-8099}
MAX=${MAX:-250}
OUT=${OUT:-$(mktemp -d)}
mkdir -p "$OUT"
DIR=$(cd "$(dirname "$0")" && pwd)

# label | host | login mode | username | password
ACCOUNTS="
xyz-super-admin|xyz.edusys.test|staff|superadmin@gmail.com|Portal#123
xyz-admin|xyz.edusys.test|staff|portal.admin@test|Portal#123
xyz-teacher|xyz.edusys.test|staff|gita@gmai.com|Portal#123
xyz-accountant|xyz.edusys.test|staff|james.deckar@gmail.com|Portal#123
xyz-librarian|xyz.edusys.test|staff|brandon@gmail.com|Portal#123
xyz-receptionist|xyz.edusys.test|staff|portal.reception@test|Portal#123
xyz-student|xyz.edusys.test|user|std25|Portal#123
xyz-parent|xyz.edusys.test|user|parent25|Portal#123
central-super-admin|edusys.test|staff|superadmin@gmail.com|School1#pw
greenfield-super-admin|greenfield.edusys.test|staff|owner@greenfield.test|Green#field1
"

FAILED=0
printf "%-26s %-8s %s\n" "portal" "result" "pages"
while IFS='|' read -r label host mode user pass; do
    [ -z "$label" ] && continue
    "$PHP" "$DIR/crawl.php" --base="http://$host:$PORT" --resolve=127.0.0.1 --login="$mode" \
        --user="$user" --pass="$pass" --max="$MAX" --out="$OUT/$label.tsv" > "$OUT/$label.fail" 2> "$OUT/$label.sum"
    rc=$?
    if [ $rc -eq 0 ]; then res=ok; else res=FAIL; FAILED=$((FAILED+1)); fi
    printf "%-26s %-8s %s\n" "$label" "$res" "$(tail -1 "$OUT/$label.sum")"
done <<< "$ACCOUNTS"
echo "reports in $OUT"
[ "$FAILED" -eq 0 ]
