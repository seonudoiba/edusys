#!/usr/bin/env bash
# End-to-end checks for school-bound sessions (phase 4). Needs:
#   - a migrated + seeded TEST database (never a real one: this script changes passwords),
#   - the app served on 127.0.0.1:$PORT with EDUSYS_BASE_DOMAIN=edusys.test and
#     EDUSYS_CENTRAL_HOST=edusys.test, e.g.
#       php -S 127.0.0.1:8099 index.php
#   - MYSQL: a mysql client command line for that database.
#
# Usage: MYSQL="mysql -h127.0.0.1 -P3307 -uroot edusys_test" PORT=8099 bash tools/tenancy/tests/session_e2e.sh
set -u
PORT=${PORT:-8099}
MYSQL=${MYSQL:?set MYSQL to a mysql command for the TEST database}
PHP=${PHP:-php}
R="--resolve edusys.test:$PORT:127.0.0.1 --resolve xyz.edusys.test:$PORT:127.0.0.1"
S1=http://edusys.test:$PORT
S2=http://xyz.edusys.test:$PORT
TMP=$(mktemp -d)
FAIL=0
N=0
check() { N=$((N+1)); if [ "$2" = "$3" ]; then echo "ok   $1"; else FAIL=$((FAIL+1)); echo "FAIL $1: expected '$2', got '$3'"; fi; }
sid() { grep -P "\tci_session\t" "$1" | awk '{print $NF}' | tail -1; }
code() { curl -s -o /dev/null -w "%{http_code} %{redirect_url}" $R "$@"; }

# Same staff email and student username exist in both seeded schools; give each school its own password.
H1=$($PHP -r 'echo password_hash("School1#pw", PASSWORD_DEFAULT);')
H2=$($PHP -r 'echo password_hash("School2#pw", PASSWORD_DEFAULT);')
$MYSQL -e "update staff set password='$H1' where id=1 and school_id=1;
           update staff set password='$H2' where id=1000001 and school_id=2;
           update users set password='s1only' where id=1 and school_id=1;
           update users set password='s2only' where id=1000001 and school_id=2;
           update captcha set status=0 where name in ('login','userlogin');"

# Staff portal
curl -s -o /dev/null $R -c "$TMP/a" -b "$TMP/a" "$S1/site/login"; PRE=$(sid "$TMP/a")
check "staff login school 1" "303 $S1/admin/admin/dashboard" "$(code -c "$TMP/a" -b "$TMP/a" -d 'username=superadmin@gmail.com&password=School1%23pw' "$S1/site/login")"
POST=$(sid "$TMP/a")
check "new session id at staff login" "yes" "$([ "$PRE" != "$POST" ] && echo yes)"
check "dashboard school 1" "200 " "$(code -b "$TMP/a" "$S1/admin/admin/dashboard")"
check "school 1 cookie refused on school 2" "307 $S2/site/login" "$(code -b "ci_session=$POST" "$S2/admin/admin/dashboard")"
check "school 1 session survives the replay" "200 " "$(code -b "ci_session=$POST" "$S1/admin/admin/dashboard")"
check "school 1 password refused on school 2" "200 " "$(code -c "$TMP/b" -b "$TMP/b" -d 'username=superadmin@gmail.com&password=School1%23pw' "$S2/site/login")"
check "school 2 password refused on school 1" "200 " "$(code -c "$TMP/c" -b "$TMP/c" -d 'username=superadmin@gmail.com&password=School2%23pw' "$S1/site/login")"
check "staff login school 2" "303 $S2/admin/admin/dashboard" "$(code -c "$TMP/d" -b "$TMP/d" -d 'username=superadmin@gmail.com&password=School2%23pw' "$S2/site/login")"
check "cookie is host-only" "xyz.edusys.test" "$(grep -P "\tci_session\t" "$TMP/d" | awk '{print $1}' | sed 's/#HttpOnly_//')"

# Student portal
st() { curl -s $R -c "$1" -b "$1" -d "username=std1&password=$2" "$3/gauthenticate/verfiy_userlogin" | grep -o '"status":[0-9]'; }
check "school 1 student password refused on school 2" '"status":1' "$(st "$TMP/e" s1only "$S2")"
check "school 2 student password refused on school 1" '"status":1' "$(st "$TMP/f" s2only "$S1")"
curl -s -o /dev/null $R -c "$TMP/g" -b "$TMP/g" "$S2/site/userlogin"; PRE=$(sid "$TMP/g")
check "student login school 2" '"status":2' "$(st "$TMP/g" s2only "$S2")"
POST=$(sid "$TMP/g")
check "new session id at student login" "yes" "$([ -n "$PRE" ] && [ "$PRE" != "$POST" ] && echo yes)"
check "student panel school 2" "200 " "$(code -b "$TMP/g" "$S2/user/user/choose")"
check "school 2 student cookie refused on school 1" "307 $S1/site/userlogin" "$(code -b "ci_session=$POST" "$S1/user/user/dashboard")"

rm -rf "$TMP"
echo "$((N-FAIL)) of $N checks passed"
[ "$FAIL" -eq 0 ]
