
Your local Laragon site currently returns 503 because Apache doesn't pass the tenancy environment variables. I'm adding a small local Apache config so it does.

  Ran 7 shell commands

Still collecting the login accounts and front-site settings from your local edusys database:

  Ran 8 shell commands

Local testing needs one step from you: reload Apache in Laragon. Right now http://edusys_ng.test and http://xyz.edusys_ng.test both return 503, because Apache wasn't passing the tenancy settings to the app. I added them in a new local-only file, C:\laragon\etc\apache2\sites-enabled\edusys_ng-env.conf, outside the repo; Laragon doesn't overwrite it. Apache's config check passes, but I haven't loaded the pages since adding the file.

1. Start it

1. In Laragon, click Reload (or Menu → Apache → Reload).
2. Your local database edusys is already set up. It has two schools:

┌────────────────────────────────┬───────────────────────────┬───────────────────────────────────────┐
│             School             │          Address          │                 Notes                 │
├────────────────────────────────┼───────────────────────────┼───────────────────────────────────────┤
│ 1, platform (Edusys ERP ADMIN) │ http://edusys_ng.test     │ the central site                      │
├────────────────────────────────┼───────────────────────────┼───────────────────────────────────────┤
│ 2, XYZ University              │ http://xyz.edusys_ng.test │ the hosts-file entry is already there │
└────────────────────────────────┴───────────────────────────┴───────────────────────────────────────┘

The new file tells the app:
- schools are subdomains of edusys_ng.test;
- edusys_ng.test itself is the platform site;
- use the database edusys as root with no password;
- log unscoped SQL rather than refuse it, which is friendlier while you click around.

2. Portals and login URLs

Platform (central): http://edusys_ng.test

┌───────────────────────────────────┬────────────────┐
│              Portal               │      URL       │
├───────────────────────────────────┼────────────────┤
│ Platform super admin login        │ /site/login    │
├───────────────────────────────────┼────────────────┤
│ Schools registry, approve schools │ /admin/schools │
├───────────────────────────────────┼────────────────┤
│ Plans                             │ /admin/plan    │
├───────────────────────────────────┼────────────────┤
│ Plans page (public)               │ /plans         │
└───────────────────────────────────┴────────────────┘

School XYZ: http://xyz.edusys_ng.test (every school works the same way on its own subdomain)

┌──────────────────────────────────────────────────────────────────┬───────────────────────────────────────────────────────────┐
│                              Portal                              │                            URL                            │
├──────────────────────────────────────────────────────────────────┼───────────────────────────────────────────────────────────┤
│ Front website                                                    │ /                                                         │
├──────────────────────────────────────────────────────────────────┼───────────────────────────────────────────────────────────┤
│ Super Admin, Admin, Teacher, Accountant, Librarian, Receptionist │ /site/login (one page; the dashboard depends on the role) │
├──────────────────────────────────────────────────────────────────┼───────────────────────────────────────────────────────────┤
│ Student and Parent                                               │ /site/userlogin                                           │
├──────────────────────────────────────────────────────────────────┼───────────────────────────────────────────────────────────┤
│ Online admission (public)                                        │ /online_admission                                         │
├──────────────────────────────────────────────────────────────────┼───────────────────────────────────────────────────────────┤
│ Online courses: guest sign-up / guest login                      │ /online_course · /course/guestsignup · /course/guestlogin │
├──────────────────────────────────────────────────────────────────┼───────────────────────────────────────────────────────────┤
│ Staff forgot password / Student and parent forgot password       │ /site/forgotpassword · /site/ufpassword                   │
└──────────────────────────────────────────────────────────────────┴───────────────────────────────────────────────────────────┘

Sample accounts already in the local database (in both schools):

┌──────────────────┬────────────────────────────────────┐
│       Role       │              Account               │
├──────────────────┼────────────────────────────────────┤
│ Super Admin      │ superadmin@gmail.com               │
├──────────────────┼────────────────────────────────────┤
│ Admin            │ bright@example.com (school 1 only) │
├──────────────────┼────────────────────────────────────┤
│ Teacher          │ gita@gmai.com                      │
├──────────────────┼────────────────────────────────────┤
│ Accountant       │ james.deckar@gmail.com             │
├──────────────────┼────────────────────────────────────┤
│ Librarian        │ brandon@gmail.com                  │
├──────────────────┼────────────────────────────────────┤
│ Student / Parent │ std25 / parent25                   │
└──────────────────┴────────────────────────────────────┘

- Student and parent passwords are stored as plain text, so you can look them up:
  SELECT username, password FROM users WHERE school_id=2 AND username IN ('std25','parent25');
- Staff passwords are hashed and came from the old dump, so I don't know them. Forgot-password won't work locally because mail isn't set up. I can set a known local password, such as Local#123, on the sample staff accounts if you want. That changes your local database, so tell me first.
- Don't test students or parents with std1/parent1: std1 has no class enrolment, so its pages fail.

3. How each user gets an account (UI)

┌───────────────────────────────────┬────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                Who                │                                                                                How                                                                                 │
├───────────────────────────────────┼────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ A new school                      │ 1. On the central site the school applies at /plans → picks a plan (/subscription/add?plan_id=N) → fills the form → pays with Paystack.<br>2. Or the platform      │
│                                   │ admin adds it at /admin/schools/create.<br>3. Either way, the platform admin then sets its subdomain in /admin/schools and clicks Approve.                         │
├───────────────────────────────────┼────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ The school's Super Admin          │ Created when the school is approved. A set-password link is shown (and emailed when mail works): http://<sub>.edusys_ng.test/admin/resetpassword/<code>. The owner │
│                                   │  sets a password and logs in at /site/login.                                                                                                                       │
├───────────────────────────────────┼────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ Staff (Admin, Teacher,            │                                                                                                                                                                    │
│ Accountant, Librarian,            │ The school admin adds them in Human Resource → Staff Directory → Add Staff (/admin/staff/create) and picks the role. They log in at /site/login.                   │
│ Receptionist)                     │                                                                                                                                                                    │
├───────────────────────────────────┼────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ Students and parents              │ The school admin admits the student in Student Information → Student Admission (/student/create). The student and parent logins are created automatically. Or an   │
│                                   │ applicant applies at /online_admission and the admin approves them in Online Admission. They log in at /site/userlogin.                                            │
├───────────────────────────────────┼────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ Online-course guests              │ They sign up themselves at /course/guestsignup.                                                                                                                    │
└───────────────────────────────────┴────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘

4. Before the full registration flow works locally

- The plans page: the platform school's front website is off, so /plans redirects to the login page. Turn it on in the platform admin (Front CMS → Settings).
- Payment: add a Paystack test key as SetEnv EDUSYS_PAYSTACK_SECRET_KEY sk_test_... in edusys_ng-env.conf, then reload Apache. Without a key, payment refuses to start; you can still add the school from /admin/schools/create instead.
- Each new school's subdomain: the Windows hosts file can't hold a wildcard, so add a line like 127.0.0.1  greenfield.edusys_ng.test to C:\Windows\System32\drivers\etc\hosts as administrator. Laragon's Apache already accepts *.edusys_ng.test.

Once you've clicked Reload, I can load each portal and confirm they open.