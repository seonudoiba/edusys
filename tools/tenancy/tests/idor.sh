#!/usr/bin/env bash
# Cross-school id tampering check (TEST database only: it renames a few school 1 records).
# For each detail page, a school 1 record gets a unique marker. Logged in as school 1 the marker
# must appear (the URL really shows that record); logged in as school 2, requesting the same
# school 1 id must not show it. (admin/income/view, admin/expense/view and feetype/edit are left
# out: their view files are missing in the legacy code, so they fail for every school.)
#
# Usage: MYSQL="mysql -h127.0.0.1 -P3307 -uroot edusys_test" PORT=8099 bash tools/tenancy/tests/idor.sh
# Needs the test logins of tools/tenancy/tests/portal_pass.sh (school 2) and school 1's
# superadmin@gmail.com / School1#pw.
set -u
PORT=${PORT:-8099}
MYSQL=${MYSQL:?set MYSQL to a mysql command for the TEST database}
R="--resolve edusys.test:$PORT:127.0.0.1 --resolve xyz.edusys.test:$PORT:127.0.0.1"
S1=http://edusys.test:$PORT
S2=http://xyz.edusys.test:$PORT
TMP=$(mktemp -d)
q() { $MYSQL -N -e "$1"; }

# Student pages work on the current session; point school 1 at the session its students are enrolled in.
q "update sch_settings set session_id=(select session_id from (select session_id, count(*) c from student_session where school_id=1 group by session_id order by c desc limit 1) t) where school_id=1"
STUDENT=$(q "select s.id from students s join student_session ss on ss.student_id=s.id where s.school_id=1 and s.is_active='yes' and ss.session_id=(select session_id from sch_settings where school_id=1) order by s.id limit 1")
SESSION=$(q "select id from student_session where school_id=1 and student_id=$STUDENT and session_id=(select session_id from sch_settings where school_id=1) limit 1")
STAFF=$(q "select s.id from staff s join staff_roles r on r.staff_id=s.id where s.school_id=1 and r.role_id=2 order by s.id limit 1")
CLASS=$(q "select id from classes where school_id=1 order by id limit 1")
SECTION=$(q "select id from sections where school_id=1 order by id limit 1")
INCOME=$(q "select id from income where school_id=1 order by id limit 1")
EXPENSE=$(q "select id from expenses where school_id=1 order by id limit 1")
FEETYPE=$(q "select id from feetype where school_id=1 order by id limit 1")
BOOK=$(q "select id from books where school_id=1 order by id limit 1")
q "update students set firstname='IDORSTUDENT' where id=$STUDENT;
   update staff set name='IDORSTAFF' where id=$STAFF;
   update classes set class='IDORCLASS' where id=$CLASS;
   update sections set section='IDORSECTION' where id=$SECTION;
   update income set name='IDORINCOME' where id=$INCOME;
   update expenses set name='IDOREXPENSE' where id=$EXPENSE;
   update feetype set type='IDORFEETYPE' where id=$FEETYPE;
   update books set book_title='IDORBOOK' where id=$BOOK;"

curl -s -o /dev/null $R -c "$TMP/s1" -b "$TMP/s1" -d 'username=superadmin@gmail.com&password=School1%23pw' "$S1/site/login"
curl -s -o /dev/null $R -c "$TMP/s2" -b "$TMP/s2" -d 'username=superadmin@gmail.com&password=Portal%23123' "$S2/site/login"

FAIL=0; N=0
while read -r path marker; do
    [ -z "$path" ] && continue
    N=$((N+1))
    own=$(curl -s $R -b "$TMP/s1" "$S1/$path" | grep -c "$marker")
    other=$(curl -s $R -b "$TMP/s2" "$S2/$path" | grep -c "$marker")
    if [ "$other" -ne 0 ]; then res="LEAK"; FAIL=$((FAIL+1)); elif [ "$own" -eq 0 ]; then res="no-control"; else res="ok"; fi
    printf "%-11s %-36s school1-sees:%s school2-sees:%s\n" "$res" "$path" "$own" "$other"
done <<EOF
student/view/$STUDENT IDORSTUDENT
student/edit/$STUDENT IDORSTUDENT
studentfee/addfee/$SESSION IDORSTUDENT
admin/staff/profile/$STAFF IDORSTAFF
admin/staff/edit/$STAFF IDORSTAFF
classes/edit/$CLASS IDORCLASS
sections/edit/$SECTION IDORSECTION
admin/income/edit/$INCOME IDORINCOME
admin/expense/edit/$EXPENSE IDOREXPENSE
admin/book/edit/$BOOK IDORBOOK
EOF
rm -rf "$TMP"
echo "$N pages: $FAIL leaks (ok = school 1 sees its record, school 2 does not; no-control = the page didn't show the record even to school 1)"
[ "$FAIL" -eq 0 ]
