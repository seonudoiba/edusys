# Edusys deployment (single shared database, one subdomain per school)

This is the go-live checklist for the first production deployment. Background and design:
`SINGLE_DB_TENANCY_PLAN.md`. Example files: `deploy/` (never served by the web server).

Local C:\Windows\System32\drivers\etc\hosts 

## 1. What goes on the server

Deploy the code only. **Never upload these** (they're local material, and several hold real
personal data):

| Path | Why |
|---|---|
| `dbsample/` | Full SQL dumps of the old databases (students, staff, password hashes) |
| `school_instance_extracted/` | Old per-school copy. Its own `.htaccess` overrides the root rules, so it would run |
| `application/seeds/school_1/`, `application/seeds/school_2/` | Sample school data (personal data, plaintext student and parent passwords) |
| `adminer.php` | Database admin tool in the web root |
| `multitenancy.md`, `1`, `.env`, `.vscode/`, `.git/` | Local notes and tooling |
| `C:\laragon\edusys_legacy_archive\` | Old zips, SQL templates and dead code |

The root `.htaccess` also refuses hidden files, internal folders (`application`, `system`, `tools`,
`deploy`, `dbsample`, …), top-level `.sql`/`.md`/`.zip` files and `adminer.php` (tested on Apache).
Treat that as a second line of defence, not a reason to upload them.

Writable by the web server user: `uploads/`, `application/logs/`, `backend/captcha_images/`, `temp/`.
Each school's files are in `uploads/s<id>/` (created automatically); `uploads/_shared/` holds the default
images and comes with the code. Every `/uploads/` URL is served by `file.php`, so keep the root `.htaccess`.
Everything else is read-only.

## 2. Server

- PHP 8.1–8.3 with `mysqli`, `curl`, `mbstring`, `gd`, `zip`, `openssl`. MySQL 8 (or MariaDB 10.6+).
- Apache with `mod_rewrite`, `mod_ssl`, `mod_headers`, and `AllowOverride All` for the document root.
- `ENVIRONMENT` stays `production` (`index.php`): errors are hidden from visitors.

## 3. DNS and TLS

1. DNS: `A`/`AAAA` records for `edusys.ng` **and the wildcard `*.edusys.ng`**, pointing at the server.
2. One certificate for `edusys.ng` and `*.edusys.ng`. Wildcards require the DNS-01 challenge; use a
   certbot DNS plugin for your DNS provider so renewal is automatic (`deploy/apache-vhost.conf.example`).
3. School subdomains are single labels: `greenfield.edusys.ng` works, `a.b.edusys.ng` is refused.

## 4. Database

1. Create the database and the two accounts: `deploy/mysql-setup.sql.example` (application account
   limited to this database, separate backup account). Don't use `root`.
2. From the project directory, with the environment of step 5 loaded:
   ```
   php index.php migrate            # creates all tables (version 258)
   php index.php seed run platform  # plans, languages, system roles
   ```
   The registry rows in `seeds/platform/001_school.php` and `004_subscription.php` hold
   applicants' details, so they aren't in the repository. On a server without them,
   `seed run platform` loads only the plans, languages and roles. Load `school_1` /
   `school_2` only on a test server; they're sample data, also not in the repository.
3. Create the platform school (the owner of the central site, served on `edusys.ng`):
   ```
   INSERT INTO school (id, school_name, email_address, phone_number, school_address, owner_name,
                       is_platform, status, payment_status, cron_flag)
   VALUES (1, 'Edusys', 'admin@edusys.ng', '', 'Lagos', 'Platform Admin', 1, 1, 1, 0);
   ```
   ```
   php index.php school_setup run 1
   ```
   This loads the default data, including the platform-only School, Plan and Subscription
   menus, and prints a set-password link on the central host for the platform admin.
   (Verified on an empty database.) Then:
   - turn on its **front website** (Front CMS settings), otherwise `/plans` and registration
     redirect to the login page;
   - set its **cron key** and email settings.

## 5. Environment

Set the variables in the Apache virtual host (`SetEnv`, see `deploy/apache-vhost.conf.example`)
**and** in the cron environment (the CLI doesn't see Apache's values, see `deploy/crontab.example`):

| Variable | Value |
|---|---|
| `EDUSYS_BASE_DOMAIN` | `edusys.ng` |
| `EDUSYS_CENTRAL_HOST` | `edusys.ng,www.edusys.ng` |
| `EDUSYS_FORCE_HTTPS` | `true` when TLS ends at a proxy or load balancer (harmless otherwise) |
| `EDUSYS_DB_HOST` / `_PORT` / `_NAME` / `_USER` / `_PASSWORD` | the application account |
| `EDUSYS_TENANCY_RAW_SQL` | `throw` (refuse unscoped hand-written SQL). Use `log` only while investigating a new page |
| `EDUSYS_PAYSTACK_SECRET_KEY` | platform Paystack secret key (no key = online payment refuses to start) |
| `EDUSYS_SMTP_HOST` / `_PORT` / `_USER` / `_PASS` | outgoing mail. Host, port and user default to `smtp.mailgun.org` / `587` / `postmaster@edusys.ng`; the password has no default (empty = no SMTP login, e.g. a local mail catcher) |

Without `EDUSYS_BASE_DOMAIN`/`EDUSYS_CENTRAL_HOST`, every web request fails closed with 503.
No credentials are stored in the code. An unset `EDUSYS_DB_PASSWORD` means an empty password,
and an unset `EDUSYS_SMTP_PASS` means mail can't authenticate.

**Rotate the old credentials.** Before the code moved them to the environment, the old root
database password and two Mailgun SMTP keys sat in `config/database.php`, `config/config.php`
and `controllers/Plans.php`. Change the MySQL root password and revoke those Mailgun keys,
then issue new ones for `EDUSYS_SMTP_PASS`.

## 6. Mail

Owners receive their set-password link by email when a school is approved, and the reminders
and scheduled messages need mail too. Set the `EDUSYS_SMTP_*` variables (Apache and cron).
Until then the platform admin sees the link after approving and can pass it on.

## 7. Scheduled jobs and backups

Install `deploy/crontab.example` (edited): `cron_all run student_attendance` every 5 minutes,
`cron_all run` daily, the nightly `mysqldump` of the shared database, and a copy of `uploads/`
(every school's files). Backups are a platform
task: schools can no longer back up or restore from their admin panel. Test a restore on another
server before go-live.

## 8. Before opening to schools

- [ ] `php index.php migrate status` shows 258 of 258.
- [ ] `https://edusys.ng/` loads the platform; `https://unknown.edusys.ng/` gives 404.
- [ ] `https://edusys.ng/.env`, `/.git/HEAD`, `/application/logs/`, `/adminer.php` give 403 (or 404).
- [ ] Register a test school at `/plans`, pay with a Paystack **test** key, approve it in
      `admin/schools`. The owner gets the link, sets a password, and logs in at `<subdomain>.edusys.ng`.
- [ ] That school's admin gets 404 on `admin/schools`, and can't see the platform school's data.
- [ ] `php index.php cron_all schools` lists the schools; `cron_all run` reports 0 failed.
- [ ] Swap the Paystack key to the live key; delete the test school (it is only deletable while not set up, so disable it).
- [ ] `php tools/tenancy/lint_raw_sql.php` reports 0 problems (CI).

## 9. Rolling back

The release is code plus a database. Keep the previous release directory and the last nightly
dump. Rolling back means switching the document root back and restoring the dump. Schema
changes are migrations (`php index.php migrate version <n>` steps down), so test any `down()`
before relying on it.

## 10. Adding a school (day to day)

1. The school applies at `/plans` (and pays), or the platform admin adds it in `admin/schools`.
2. The platform admin sets its **subdomain** in the school form and clicks **Approve**.
3. Setup runs in about 2 seconds. The owner gets the set-password link (or the admin passes on the
   link shown). No folder, database or server change is needed.
4. Disable a school with its status toggle (its subdomain then shows "School unavailable").
