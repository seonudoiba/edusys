# Single-database tenancy: architecture and implementation plan

Edusys becomes one CodeIgniter 3 codebase and **one MySQL database** shared by every
school. Each school is a tenant identified by `school.id`; every school-owned row
carries `school_id`. A request's school is chosen from its **subdomain**
(`greenfield.edusys.ng`). All existing portals stay as they are; only the way data
is separated changes:

| Portal | Entry point today | Who logs in |
|---|---|---|
| Front site | `Welcome` / front CMS (`Front_Controller`) | public |
| Admin, Super Admin, Teacher, Accountant, Receptionist, Librarian | `site/login` → `Admin_Controller` | `staff` + `staff_roles` (role ids 1, 7, 2, 3, 6, 4) |
| Student, Parent | `site/userlogin` → `Student_Controller` / `Parent_Controller` | `users` (+ `students`) |
| Platform (plans, registration, school registry) | central host, `admin/schools`, `admin/plan`, `/plans`, `/subscription` | role-7 staff of the platform school |

This plan supersedes `multitenancy.md` (database per school). Its per-school
databases, encrypted DB credentials and database-creating worker are no longer needed.

The software is not live yet. The two database dumps are reference material for the
schema and sample data. There is no production data to migrate and no cut-over
from running school instances.

## 0. No school instances

A new school is **rows in the shared database**, nothing else:

| Today (removed) | New |
|---|---|
| Unzip `system/school.zip` (164 MB, ~41,000 files) into `school_<id>/` | Nothing on disk. Every school runs the same codebase |
| `CREATE DATABASE school_<id>` + import `system/school.sql` | Insert a `school` row; the default seed adds reference rows with that `school_id` |
| Rewrite `{{DB_NAME}}`, `{{BASEURL}}`, `{{CUSTOM_UPLOAD_PATH}}` in copied config | `base_url` comes from the request host; DB config is the same for all |
| URL `edusys.ng/school_<id>/` | URL `<subdomain>.edusys.ng`, resolved by lookup in `school` |
| Disable = write a `disabled` file into the folder | Disable = `school.status`, checked by the resolver |
| A code fix has to be copied into every school folder | A code fix is deployed once |

The legacy instance-creation code was **removed on 2026-11-04**:

* `controllers/CronJob.php`: deleted (`make_school_folder`, `setCronJob`), with its route `runCronSchool`.
* `controllers/admin/Subscription_list.php`: removed `getSchoolRecord`, `UploadZip`, `generateUniqueId`.
* `controllers/admin/Schools.php`: removed `setCronJob`, its commented-out copy, `runCronJob`,
  `generateUniqueId`, `setPermissionsRecursive`, and the `disabled`-file writes in
  `delete()` / `status_update()` (status now lives only in `school.status`).
* `models/School_model.php`: removed `getSchoolsWithStatus()` (cron provisioning query).
* Views: the hidden "cron job" button and its JS (`school_list.php`), and the
  "create new DB" JS (`subscriptionlist.php`).
* `system/school*.zip` (12 archives) and `system/school*.sql` (1.9 GB) moved out of
  the project to `C:\laragon\edusys_legacy_archive\`. Delete that folder once you
  no longer need it.

Approval (`admin/schools/update_status`, `cron_flag = 2`) and enable/disable stay.
Until `School_provisioner` exists (phase 6), approving a school records the decision only.

None of the new files (migrations, seeds, `Seed`, `Migrate`, `Seeder`) create
folders, databases or config files.

---

## 1. Decisions

| Topic | Decision |
|---|---|
| Tenant key | `school_id` column on every school-owned table |
| Tenant resolution | Subdomain of `EDUSYS_BASE_DOMAIN` → `school.subdomain`; central host → the school with `is_platform = 1`; anything else → 404 |
| Existing schools | Start fresh, except: `smartschool` data → **school 1**, `school_182` data → **school 2** (separate seed groups) |
| IDs | Primary keys stay globally unique auto-increment `id` (application code addresses rows by `id` everywhere). School 2's seeded ids are offset by 1,000,000 |
| Shared tables | `school`, `plan`, `plan_list`, `subscription`, `settings`, `languages` |
| Hybrid table | `roles`: `school_id NULL` = system roles 1–7 shared by all schools (hard-coded in ~100 places); custom roles carry a `school_id` |
| Everything else | School-owned (234 tables), including currencies, permissions and sidebar menus, because these are per-school settings and differ between the two source schools |

---

## 2. Delivered in this step

### 2.1 Migrations: `application/migrations/` (241 files)

* One table per file, `NNN_create_<table>.php`, ordered so every foreign-key parent
  is created before its children (001 `school` … 007 `roles`, then the
  234 school-owned tables in dependency order).
* Schema = union of the central and school schemas (they are identical apart from
  the six platform tables and `multi_branch`).
* Every school-owned table gets:
  * `school_id int NOT NULL` (second column);
  * `KEY idx_<table>_school_id (school_id)`;
  * `CONSTRAINT fk_<table>_school FOREIGN KEY (school_id) REFERENCES school (id)`
    (no cascade: a school cannot be deleted while it still owns rows);
  * unique keys made per school: `staff.employee_id`, `leave_types.type`,
    `online_courses.title` → `(school_id, …)`.
* `school` gains `subdomain` (unique, ASCII DNS label) and `is_platform`.
* `qr_code_settings` (central) and `QR_code_settings` (schools) collapse into
  `QR_code_settings`, the name the code uses. They are one table on Windows anyway.
* All 312 original foreign keys are kept. With the 235 `school_id` keys that makes 547.

### 2.2 Seeds: `application/seeds/` (one table per file)

| Group | Files | Rows | Source |
|---|---|---|---|
| `platform/` | 7 | 100 | central `smartschool`: registry, plans, subscriptions, languages, system roles |
| `school_1/` | 155 | 4,671 | central `smartschool` school data |
| `school_2/` | 167 | 4,112 | `school_182` (XYZ University) |

* Seed file numbers equal the migration numbers, so parents load first.
* School 2: every row `id`, and every reference to a school-owned row, is shifted
  by `+1,000,000`. That covers declared FKs, undeclared references such as
  `users.user_id` and `students.parent_id`, the comma-separated `users.childs`,
  and ids inside JSON (`messages.user_list.user_id`, `student_fees_deposite.amount_detail.received_by`).
  References to shared tables (roles, languages) are not shifted.
* The registry is renumbered: legacy school 182 → id 2; `subscription.school_id` 182 → 2.
* `multi_branch` is migrated but **not seeded**. Its rows hold another database's credentials.
* **Orphans:** `school_182` was provisioned without 100 of the 312 foreign keys, so
  deletes never cascaded. The generator applies each FK's `ON DELETE` rule as MySQL
  would have. That resolved 313 rows in school 2 (school 1: none), listed in
  `application/seeds/ORPHANS.md`. The main cause is deleted classes 1–3: it drops
  36 enrolments and their marks/ranks, and **5 `student_fees_deposite` rows**
  (see §8, open decision 1).

### 2.3 Runners

```powershell
$php = 'C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe'
& $php index.php migrate status
& $php index.php migrate                 # to latest (241)
& $php index.php migrate version 0       # drop everything
& $php index.php seed run platform       # must run first
& $php index.php seed run school_1
& $php index.php seed run school_2
& $php index.php seed run all            # platform, then each school group
```

* Both are CLI-only (`show_404()` on the web). Migrations are enabled only for
  `is_cli()`, and `migration_auto_latest` is off.
* `migrate` refuses to run against a legacy database (application tables present,
  no `migrations` ledger), so it can't be pointed at the live `smartschool` by mistake.
* Seeds are repeatable. School groups delete only that school's rows (in reverse
  order), then insert. Platform seeds upsert and never delete.
* `config/database.php` now reads optional `EDUSYS_DB_HOST / _PORT / _USER / _PASSWORD / _NAME`;
  the old literals remain as fallbacks, so current behaviour is unchanged.

### 2.4 Regenerating

The migrations and seeds are generated from `dbsample/*.sql` by
`tools/tenancy/gen_migrations.php` and `tools/tenancy/gen_seeds.php`. The table
classification and reference map live in `tools/tenancy/config.php`.
Regeneration is deterministic. Regenerate instead of hand-editing generated files.

```powershell
& $php tools/tenancy/gen_migrations.php dbsample/smartschool.sql dbsample/school_182.sql application/migrations tools/tenancy/manifest.json
& $php tools/tenancy/gen_seeds.php      dbsample/smartschool.sql dbsample/school_182.sql tools/tenancy/manifest.json application/seeds
& $php tools/tenancy/gen_tenancy_tables.php tools/tenancy/manifest.json application/config/tenancy_tables.php
& $php tools/tenancy/gen_default_seed.php dbsample/smartschool.sql tools/tenancy/manifest.json application/seeds/default
```

### 2.5 Verified (throwaway MySQL 8.4 on port 3307)

* Migrate 0 → 241, down to 0, up to 241 again.
* All three seed groups load with foreign-key checks on; a second full run gives identical counts.
* All 308 FK paths between school-owned tables: zero rows whose parent belongs to another school.
* Undeclared references resolve inside the same school: student users → students,
  `students.parent_id` → parent users, `users.childs`, `sch_settings.session_id`, `sch_settings.currency`.
* Each school keeps its role-7 super admins (school 1: 3, school 2: 4).

---

## 3. Target architecture

```
 Browser ── greenfield.edusys.ng ──► Apache (*.edusys.ng vhost, wildcard TLS)
                                         │
                                         ▼
                                   index.php (one codebase)
                                         │
                 ┌───────────────────────┴────────────────────────┐
                 │ 1. Tenant resolver  (pre_controller hook)       │
                 │    Host → school (subdomain / is_platform)      │
                 │    unknown or disabled → 404 / suspended page   │
                 │    sets Tenant::id(), base_url                  │
                 └───────────────────────┬────────────────────────┘
                                         ▼
                 ┌────────────────────────────────────────────────┐
                 │ 2. Controllers (existing portals, unchanged     │
                 │    routes) – MY_Controller checks the session   │
                 │    belongs to Tenant::id()                      │
                 └───────────────────────┬────────────────────────┘
                                         ▼
                 ┌────────────────────────────────────────────────┐
                 │ 3. Models (184, mostly unchanged)               │
                 └───────────────────────┬────────────────────────┘
                                         ▼
                 ┌────────────────────────────────────────────────┐
                 │ 4. Tenant-aware DB driver (MY_Loader)           │
                 │    query builder: WHERE (…) AND t.school_id = ? │
                 │    inserts: school_id filled / verified         │
                 │    raw SQL: guard flags unscoped tenant tables  │
                 └───────────────────────┬────────────────────────┘
                                         ▼
                                One MySQL database
```

There's a single source of truth for the current school: a `Tenant` context object
(library) holding the resolved `school` row. Nothing else (session, URL, form fields)
may set or change it.

---

## 4. Components to build

### 4.1 Tenant context and resolver ✅ done (phase 2)

| File | Role |
|---|---|
| `application/config/tenancy.php` | Reads `EDUSYS_BASE_DOMAIN`, `EDUSYS_CENTRAL_HOST` (comma-separated), `EDUSYS_FORCE_HTTPS`, `EDUSYS_SCHOOL_ID` (CLI); reserved subdomain labels |
| `application/libraries/Tenant_resolver.php` | Pure host classification: central / subdomain / reject; base URL building |
| `application/libraries/Tenant.php` | Static per-request context: `Tenant::id()`, `school()`, `is_platform()`, `host()`. Set once, then locked; `id()` throws when no school is set |
| `application/hooks/Tenant_hook.php` | `pre_controller` hook: classify host → one indexed query on `school` → set `Tenant` and `base_url` |
| `application/views/errors/html/error_school_unavailable.php` | 403 page for a disabled school |
| `config/hooks.php`, `config/config.php` | Hook registered; `enable_hooks = TRUE` |
| `tools/tenancy/tests/test_tenant_resolver.php` | 46 unit checks |

Behaviour:

1. Only the `Host` header is used (lowercased, port and trailing dot stripped for
   matching). `X-Forwarded-Host`, session, URL and form input never select a school.
2. Host in `EDUSYS_CENTRAL_HOST` → the single `is_platform = 1` school (school 1).
   No such school, or more than one → 503.
3. `<label>.EDUSYS_BASE_DOMAIN` → `school WHERE subdomain = <label>`. Labels must be
   valid DNS labels and not reserved (`www`, `admin`, `api`, …). Nested labels
   (`a.b.edusys.ng`), IP addresses and other domains → 404.
4. Unknown subdomain → 404. `school.status != 1` → 403 "School unavailable".
5. `base_url` = `scheme://host[:port]/` of the request (default port omitted).
   This replaces `sch_settings.base_url` and `folder_path`. `EDUSYS_FORCE_HTTPS=true`
   when TLS ends at a proxy.
6. Missing `EDUSYS_BASE_DOMAIN` / `EDUSYS_CENTRAL_HOST` → every web request fails closed with 503.
7. CLI: `EDUSYS_SCHOOL_ID=<id>` runs a command as that school (unknown id → exit 1);
   unset → no school, as `migrate` and `seed` need.

The hook opens its own short-lived connection, because `$this->db` doesn't exist yet
before the controller. Phase 3's `MY_Loader` can reuse it.

Verified end-to-end (built-in PHP server, throwaway MySQL): central and `www.` → school 1;
`xyz.` (any case, with a port) → school 2; unknown, reserved, nested, foreign, IP → 404;
disabled school → 403 while school 1 stays up; spoofed `X-Forwarded-Host` ignored;
unconfigured → 503; missing platform school → 503.

**Until phase 3**, a resolved request still reads unscoped data: every school sees
school 1's settings and records. Phase 2 only decides *which* school a request is for.

**Local setup (Laragon).** Laragon's auto vhost for this folder is
`edusys_ng.test` with `ServerAlias *.edusys_ng.test`. Windows `hosts` has no wildcards,
so add one line per school subdomain, then set the environment for Apache
(e.g. `SetEnv` lines in the vhost, or system environment variables) and restart:

```
# C:\Windows\System32\drivers\etc\hosts
127.0.0.1  edusys_ng.test
127.0.0.1  xyz.edusys_ng.test

# Apache vhost (or system environment)
SetEnv EDUSYS_BASE_DOMAIN  edusys_ng.test
SetEnv EDUSYS_CENTRAL_HOST edusys_ng.test
SetEnv EDUSYS_DB_NAME      edusys
```

`getenv()` doesn't see `SetEnv` values under some PHP handlers. If the site shows
503 "not configured", use system environment variables instead.

### 4.2 Tenant-aware database layer (the core of isolation) ✅ done (phase 3)

| File | Role |
|---|---|
| `application/core/MY_Loader.php` | `$this->load->database()` (and `Db_manager`) now return the tenant-aware driver. Only the configured default connection may be opened; a session-chosen group is refused |
| `application/libraries/tenancy/Tenant_DB_mysqli_driver.php` | `extends CI_DB_mysqli_driver`; scopes the query builder at its compile points (`_compile_select`, `_compile_wh`, `_update`, `_update_batch`, `_delete`, `_insert`, `_insert_batch`, `_replace`) |
| `application/libraries/tenancy/Tenant_raw_sql_guard.php` | Reports raw `query()` SQL that touches school tables without `school_id` |
| `application/libraries/tenancy/Tenant_violation.php` | Exception for refused operations |
| `application/config/tenancy_tables.php` | 234 school-owned, 1 hybrid, 6 platform tables; generated by `tools/tenancy/gen_tenancy_tables.php` from the manifest |
| `application/controllers/Tenancy_test.php` | CLI test suite (71 checks per school + no-school mode) |

| Operation | Behaviour |
|---|---|
| `get`, `get_where`, `count_all_results`, `get_compiled_select`, subqueries | FROM tables that are school-owned (aliases and comma lists too): `WHERE (<original>) AND t.school_id = <id>`. **The wrapping is mandatory:** the student/parent login queries use `or_where` (`username = ? OR admission_no = ?`); a plain append would give `… OR (b AND school_id = ?)` and let a user log in to another school. |
| `join` of a school-owned table | `ON (<original>) AND t.school_id = <id>`. Inside ON, so LEFT JOINs keep their meaning. `JOIN … USING` on a school table is refused |
| `update`, `update_batch`, `delete`, `empty_table` | Same WHERE filter. Setting `school_id` to another school is refused |
| `insert`, `insert_batch`, `replace` | `school_id` set to the current school; another school's id is refused |
| `count_all` | Scoped count. `truncate` on school/hybrid tables and `start_cache` are refused |
| Hybrid `roles` | Reads `(school_id IS NULL OR school_id = <id>)`. Writes `school_id = <id>`, so system roles 1–7 are read-only for schools; new roles belong to the school |
| `query()` (raw SQL) | Not rewritten. The guard strips literals and comments, finds tables after FROM/JOIN/UPDATE/INTO, and reports a statement that names a school table but never `school_id`. `EDUSYS_TENANCY_RAW_SQL` = `log` (default; `application/logs/tenancy-raw-sql-YYYY-MM-DD.log`, one line per caller and statement with school, file:line and URI), `throw`, or `off`. Builder-generated SQL is never reported |
| Escape hatch | `$this->db->without_tenant(function ($db) { … })` for platform code (registry, cross-school jobs) |
| No school set | CLI without `EDUSYS_SCHOOL_ID` (migrate, seed): nothing filtered. Web request without a school: refused |

Verified against the seeded test database, as school 1 and as school 2:
- exact compiled SQL for every operation above;
- real reads return only the school's rows; the `std1` login lookup (the username exists in both schools) returns only the current school;
- another school's id returns nothing, and updating or deleting it affects 0 rows;
- writes land in the current school, and test writes are rolled back;
- raw-guard cases;
- migrate and seed still work.

Over HTTP, `edusys.test` and `xyz.edusys.test` now show their own school name and their own front-site setting.

There's a strict-mode safety net: `school_id` is `NOT NULL` with an FK to `school`,
so an unscoped insert fails loudly instead of creating an ownerless row. (CI runs with
`stricton = false`, which would otherwise insert `0`. The FK rejects that.)

### 4.3 Raw SQL conversion ✅ done (phase 5)

All **327** hand-written `->query()` calls (database and Datatables raw mode, in
models, controllers, helpers and libraries) are school-filtered or exempted with a
reason. 311 needed a filter. The rule:
- every school-owned table in a FROM clause gets
  `$this->db->school_filter('<alias>')` (`$CI->db->…` in helpers), at every level:
  subqueries, derived tables and each UNION branch;
- tables joined on an id reference are covered by their parent's filter;
- where a statement had a top-level OR, the original condition was parenthesised
  before the filter was added.

Driver helpers added for hand-written SQL: `$this->db->school_filter($alias, $hybrid = false)`
→ `` `alias`.`school_id` = <id> `` and `$this->db->school_id()`.

| Tool | Use |
|---|---|
| `tools/tenancy/lint_raw_sql.php` | Static check (the CI lint). Rebuilds each raw statement from its string literals and reports a school table without `school_id`. `--summary`, `--show`, `--all`. Exit 1 on problems. Exemptions: `// tenancy-lint: ok <reason>` or `/* tenancy-lint: ok <reason> */` on the call line |
| `application/controllers/Tenancy_sqlcheck.php` | CLI harness: calls every model method that has raw SQL as a given school, with real ids from that school's data, inside a rolled-back transaction, and reports SQL errors |
| `tools/tenancy/tests/crawl.php` | Logged-in GET crawl of the admin panel (skips links that change data); flags DB errors, 5xx and tenancy violations |

Verification:
- **SQL check:** 282 methods × 2 schools. 218 run cleanly; the other 64 behave exactly as
  in the pre-conversion baseline (legacy bugs or sample-argument errors). No regressions.
  One legacy query now works (a missing space before `WHERE`).
- **Crawl with `EDUSYS_TENANCY_RAW_SQL=throw`:** school 1, 223 pages, 0 failures;
  school 2, 288 pages, 0 failures. The pre-conversion baseline had 26 failures, all
  MySQL 8 `ONLY_FULL_GROUP_BY` rejections, now fixed by the sql_mode change below.
- The tenancy suites (71 checks per school, the no-school mode, 46 resolver checks) still pass.

Cross-school bugs fixed along the way (each worked on *every* school before):
- `Student_model::bulkdelete()` deleted orphaned parent accounts in all schools.
- `Onlinestudent_model::editguardianfield()` updated the online-admission field setting of all schools.
- `Session_model::getPreSession()` picked the "previous session" across schools.
- `Studentfeemaster_model::getBalanceMasterRecord()` found fee groups by name across schools.

Shared-database operations disabled for school admins:
- **Backup/restore** (`admin/admin/backup`, `downloadbackup`, `dropbackup`,
  `Cron::autobackup`). A dump contained every school; a restore ran an uploaded SQL
  file on a raw `mysqli` connection that bypassed the tenant layer. Backups are now a
  platform/server task.
- **Vendor updater:** `Auth::autoupdate`/`import_dump` (overwrote code and ran SQL) are
  disabled on the web. Updates are deployments plus migrations. Product registration
  and add-on activation (`app_update`, `addon_update`, `andapp_update`, `multiupdate`)
  write the shared `config/license.php`, so they run only on the platform school.
  `Admin_Controller::check_license()` (which rewrote `license.php` when the key didn't
  match `base_url()`) runs only on the platform school, so school subdomains can't wipe the key.

Other changes:
- `core/MY_Loader.php` turns off `ONLY_FULL_GROUP_BY` for the app's connection (the
  legacy GROUP BY queries need it; MySQL 8 enables it by default).
- Datatables raw mode adds its own `WHERE` only when `datatables->where()` is used.
  `Course_model::getcourselist()` moved its teacher filter (`teacher_id OR created_by`,
  unparenthesised before) into the query, and `Itemissue_model`/`Expensehead_model`
  gained a real WHERE (searching their lists produced invalid SQL before).

Left for later:
- `Subscription::data_set()` (platform registration) inserts a `guest` row with another
  school's id in `guest.school_id`, now the tenant key. The driver refuses it, so
  registration stops there. Phase 6 replaces this flow and needs a separate link column
  (e.g. `guest.registered_school_id`).
- When a raw query or new model is added, run the lint. After a full click-through
  of the remaining pages, switch production to `EDUSYS_TENANCY_RAW_SQL=throw`.

### 4.4 Settings ✅ done (phase 5)

No code change was needed. Settings are read through `Setting_model`'s query-builder
methods (scoped by the driver), and updates pass back that school's own row `id`
(also scoped). The `id = 1` lookups in `admin/Schools.php` and `CronJob.php` were
removed with the legacy provisioning code. `Cbseexam_result_model.php:337` still
reads `cbse_exams` id 1 (a legacy oddity): it now returns nothing for schools whose exam
isn't id 1, instead of reading another school's exam.

### 4.5 Authentication, sessions and portals ✅ done (phase 4)

| File | Change |
|---|---|
| `application/libraries/Session/MY_Session.php` (new) | Binds every session to one school (`__tenant_school_id`). A session from another school, or a logged-in session without a stamp, is replaced by a fresh empty session under a new id (logged out here). The other school's session is untouched. Rejections go to `application/logs/tenancy-security-YYYY-MM-DD.log`. Setting `admin` or `student` (every portal's login) issues a new session id, against session fixation |
| `application/libraries/Db_manager.php` | No longer reads `db_array.db_group` from the session or reconnects; it just ensures the shared tenant-aware connection |
| `application/config/database.php` | Removed the multi-branch block: it opened a raw MySQL connection on every load to build extra connection groups from `multi_branch` |
| `application/libraries/Customlib.php` | `getBaseUrl()` = request `base_url()`; `getFolderPath()` = codebase root. Both used to come from the session's legacy per-school `db_array` |
| `application/config/config.php` | `cookie_domain` stays empty (host-only, documented); `cookie_secure` on under HTTPS / `EDUSYS_FORCE_HTTPS` |

The binding sits in the session library itself, so it covers all nine portals without
editing each login. Staff portals (admin, super admin, teacher, accountant, receptionist,
librarian) store `admin`; student and parent store `student`. The login queries were
already scoped by the phase 3 driver.

The `db_array` entries still written by `Site.php`, `Course.php` and `Gauthenticate.php`
are now ignored. Remove them when those controllers are touched in phase 5.

Verified with `tools/tenancy/tests/session_e2e.sh` (15 checks, real HTTP, host-scoped
cookies). The same staff email and student username exist in both schools with different
passwords:
- each school's credentials are refused on the other school;
- the session id changes at staff and student login;
- a school 1 cookie replayed on school 2 (and the reverse) lands on the login page;
- the original school keeps its session;
- cookies are host-only.

The browse also produced the first raw-SQL guard report (`models/User_model.php:540`), which is phase 5 work.

Not changed: CSRF protection is off (`csrf_protection = FALSE`) as in the legacy app.
Turning it on means checking every AJAX form, so it's a separate task.

### 4.6 Platform (control plane) ✅ done (phase 6)

The platform runs on the central host, where the tenant is the `is_platform` school (school 1).
Its role-7 staff are the platform administrators. The existing screens are reused.

**Platform-only screens.** `Tenant::require_platform()` runs first in the constructors of
`admin/Schools`, `admin/Plan`, `admin/Plan_type`, `admin/Subscription`,
`admin/Subscription_list`, `Plans`, `Subscription` and `user/Plan`. On a school subdomain
they return 404 before any login or session work. The guest panel's "Plans" link only
shows on the central host. Verified: all platform URLs give 404 on `xyz.`, while that
school's own dashboard still works.

**Setup: `application/libraries/School_provisioner.php`**, the only provisioning path.
It's triggered by **Approve** in `admin/schools` (`update_status`), or from the CLI with
`php index.php school_setup <check|run> <id>`. One transaction:
1. Validate: not the platform school; subdomain valid, not reserved, not taken; email valid; not already set up.
2. Load the default data (§4.8) under new ids, remapping the references between those tables.
3. Fill the school's `sch_settings` row (name, email, phone, address, a new `cron_secret_key`).
4. Create the super admin (role 7, `employee_id` ADMIN, the owner's name and email) with an
   unusable random password and a one-time code.
5. Set `school.status = 1` and `cron_flag = 1`.

The owner gets an emailed link, `https://<subdomain>.<base>/site/admin_resetpassword/<code>`.
It reuses the existing reset screen, which scopes the code to that school and clears it
after use. If mail isn't configured, the platform admin sees the link instead.

Verified on the test database:
- schools 3 and 4 were set up via the CLI and via Approve, in about 2.5 s each;
- zero cross-school references; settings point at the school's own session and currency;
- the owner sets a password, logs in and gets a clean dashboard;
- the link doesn't work on another school and can't be reused;
- a second setup is refused, and a failed setup rolls back completely (it found the next bug).

**Registry changes:**
- The school form has a validated `subdomain` field.
- The list links to `<subdomain>.<base>` instead of `/school_<id>`.
- Delete refuses the platform school and any school that has been set up ("disable it
  instead"); only never-set-up applications are deleted. `Studentcourse_model::removebySchool()`,
  a `delete()` with no table that made every delete fail, is no longer called.

**Guest link (registration).** Migration 242 adds `guest.registered_school_id`. Registration
writes it, guest login reads it into the session (the `school_id` key that
`Subscription::edit()` and `user/Plan` use), and `Guest_model::removebySchool()` uses it.
`guest.school_id` stays the tenant key.

**Settings ids.** Migration 243 makes `sch_settings.id` and `QR_code_settings.id`
AUTO_INCREMENT (they were fixed single-row ids). Without it, every new school's row got id 0 and the second school collided.

**Payments (`controllers/Subscription.php`):**
- The secret key comes from the `EDUSYS_PAYSTACK_SECRET_KEY` environment variable. When it's
  missing, payment refuses to start (503); there's no key in the code any more.
- `initialize_payment()` stores Paystack's reference on the subscription.
- `verifyPayment()` only accepts that stored reference, refuses an already-paid
  subscription, and confirms with Paystack that the status is `success`, the reference
  matches, the amount equals `total × 100`, and the customer email matches.

  Before, any successful Paystack reference could mark any subscription paid, replayably.
  Paid renewals also created a new school, because the renewal flag wasn't passed on; it is now.
- The dead duplicates `controllers/Paystack.php` (it started with `print_r(); die();`) and
  `libraries/PaystackController.php` (autoloaded on every request, with the key) moved to
  `C:\laragon\edusys_legacy_archive\code\`.

Verified: wrong, missing or unknown references → failure page; no key → 503; already paid →
"already complete". The subscription stayed unpaid throughout. A real Paystack payment
needs a test key in the environment.

**Notes for running the platform:**
- `/plans` and registration extend `Front_Controller`, which redirects to login while the
  platform school's front website is off. School 1's seeded `front_cms_settings.is_active_front_cms`
  is 0. Turn it on in the platform's Front CMS settings.
- `admin/Subscription` was already broken: it loads a model `Subscription` and a view
  `admin/subscription/subscription_list`, and neither exists. `admin/subscription_list`
  is the working screen. Remove the dead one.
- `libraries/Paymongo.php`, a school-level payment gateway, has vendor test keys
  hard-coded. Move them to `payment_settings` when that gateway is used.

### 4.7 Uploads ✅ done (phase 9)

Every school has its own folder, **`uploads/s<school id>/`**. Code, database values and URLs keep
the logical path `uploads/<folder>/<file>`; only the place on disk changed.

**Writing and reading files** (`application/helpers/upload_helper.php`, autoloaded):
- `upload_root()` is `./uploads/s<id>/` for the current school (`./uploads/` with no school, e.g. a
  migration on the CLI). `upload_abs_root()` is the absolute form, `upload_fs_path($path)` maps a
  logical or legacy path (`uploads/x`, `./uploads/x`) to the school's file.
- The 239 `"./uploads/…"` literals were rewritten to `upload_root() . "…"` by
  `tools/tenancy/rewrite_upload_paths.php` (tokenizer based: PHP strings only, never HTML or
  JavaScript). The rest were fixed by hand: `Media_storage` (upload, download, delete),
  `ImageResize` (gallery, media, video thumbnails), barcode and QR code generation, course
  uploads, PDF backgrounds, online-admission photo copies, bank transfer receipts.
- `Media_storage::filedownload()`/`filedelete()` built `getFolderPath() . "uploads/…"` without a
  slash (`…/edusys_nguploads/…`), so many downloads and deletes didn't work; they go through
  `upload_fs_path()` now.
- A school's folder is created on first use (and by `School_provisioner`) with every upload
  subfolder (`config/uploads.php`) and the default images from **`uploads/_shared/`** (tracked in
  git: `no_image.png`, `default_male.jpg`, the default barcode and QR code …).

**Serving** (`file.php` → `application/controllers/Files.php`):
- The root `.htaccess` sends every `/uploads/…` request to `file.php`; `uploads/s<id>/` and
  `uploads/_shared/` are refused (403). The controller serves the file from the folder of the
  school of the host only, so another school's host gives 404.
- **Private folders** (`config/uploads.php`: staff and student documents, leave documents,
  timelines, offline payments, subscription receipts, income and expense documents, front office,
  email attachments, homework, admission forms, inventory attachments) need a logged-in staff
  member, student or parent of the same school; otherwise 404. Finer rules (a student sees only
  their own documents) are not implemented: any logged-in user of the school can open them.
- Images, PDF and audio/video are shown inline; any other type (HTML, SVG, office files) is sent
  as a download, with `nosniff` and a sandboxing CSP, so an uploaded page can't run scripts on the
  school's site. Public files are cacheable (ETag, 304); private ones `no-store`.

**Existing files**: `php index.php uploads_tool status | init <id|all> | copy_legacy <id|all> |
remove_legacy`. `copy_legacy` copies the old shared tree into school folders without overwriting;
files left in the old tree are no longer served.

Verified (test database, guard in `throw` mode): uploads and deletes land in the school's folder
(expense documents, gallery images with thumbnails, staff barcodes and QR codes); a file of one
school is 404 on the hosts of the others; private files need a login of that school (staff or
student), a login of another school doesn't count; `../`, encoded `..`, backslashes, the storage
folders and hidden files are refused; SVG/HTML uploads come back as downloads.
### 4.8 Default data for new schools ✅ done (phase 6, demo content added 2026-11-05)

`application/seeds/default/` holds 109 tables, one per file, generated by
`tools/tenancy/gen_default_seed.php` (regenerate; don't edit):

```
php tools/tenancy/gen_default_seed.php dbsample/smartschool.sql tools/tenancy/manifest.json application/seeds/default smartschool_test.json
php tools/tenancy/copy_demo_media.php     # the media the seed refers to -> uploads/_shared/
```

- **System tables** (permissions, menus, settings, templates for email/SMS, sessions, currencies) come
  from the central dump (school 1), school-specific values removed.
- **School content** comes from the demo school export `smartschool_test.json` (not in git): a new
  school starts with a working example of every module — classes, sections, subjects, subject
  groups, timetable, lesson plans and topics, exam groups, online exams and question bank, fee
  types/groups/amounts/discounts/reminders, transport routes/vehicles/pickup points, hostels and
  rooms, library books, inventory items and suppliers, every front website page and menu, media
  gallery, banners, news, events and notices, online courses (sections, lessons, quizzes), video
  tutorials, notices, download centre, custom fields, behaviour types, and richer starter lists.
- **Never**: people (students, parents, staff, logins) and their records (attendance, marks, fee
  payments, income/expenses, messages, logs, leave, front-office logs), credentials, platform
  tables, dated meetings (Google Meet/Zoom), a staff member's personal tasks, the CBSE exam
  instances (the CBSE lists stay), demo leftovers (two test classes, test pages), the third-party
  privacy policy page, and news about Indian holidays.
- **Safety check**: the generator fails on any `*_id` column that is neither remapped
  (`$references`, `$typed_references`), reset, set to the school's super admin
  (`$owner_columns`) nor listed as a plain value — so a new school's rows can never point at another
  school's rows. (It also caught school 1's SMS template registrations in `notification_setting`;
  they are blanked now.)
- **Sessions and dates**: demo content of the demo's current session is attached to the default
  data's current session; `School_provisioner` points that session at the academic year the school
  starts in (`start_month`), so a school set up in November 2026 starts in 2026-27, and moves the demo
  dates (events, notices, fee due dates) forward by the same number of years.
- **Order**: the provisioner creates the super admin before the default data, because some demo
  rows need a "created by" staff member.
- **Media**: the 95 files the seed refers to are in `uploads/_shared/` (copied into each new school's
  folder, §4.7).
- The platform school (the central website) gets none of the demo content: those rows are tagged `$school_only` and skipped for it, so it keeps the built-in pages only.
- The front website is switched on for new schools. Each school should replace the demo text of its
  Home, About Us and Contact pages and the demo contacts (address, phone, vehicles' drivers).

Verified on an empty database: platform school and a new school set up; the new school is in 2026-27
with 4 classes, 16 class sections, 18 class subjects, 16 timetable entries, fee setup, 16 news/events,
38 gallery images, 4 courses; no row points at another school's row; its public site and a logged-in
crawl of its admin area (241 pages, guard in `throw` mode) have 0 errors.
### 4.9 Cron and background work ✅ done (phase 7)

**One runner for all schools: `php index.php cron_all run [task]`**
(`application/controllers/Cron_all.php`, CLI only).
- Covers every enabled school that has been set up (`school.status = 1` with a settings row).
  `php index.php cron_all schools` lists them.
- Each school runs in its own PHP process (`php index.php cron <task> cli` with
  `EDUSYS_SCHOOL_ID`), because the tenant is fixed once per request. A failing school is
  reported, with its last output lines, and the run continues. The exit status is 1 if any failed.
- Tasks: `index` (the default: fee reminders, event reminders, scheduled messages),
  `student_attendance`, `feereminder`, `eventreminder`, `schedulesmsemails`. `autobackup`
  is not a school task any more (§4.3).
- A lock file per task (system temp dir, named per installation) stops overlapping runs.
- Crontab: `cron_all run student_attendance` every 5 minutes and `cron_all run` once a
  day, both from the project directory.

**Per-school URLs** (`/cron/<key>` on the school's own host) still work. `Cron::authorized()`
now requires a non-empty key equal to *that school's* `cron_secret_key` (constant-time
compare) on every task; the CLI is trusted. Before:
- the check `$key != "" && $this->cron_key != $key` let an empty key through;
- `schedulesmsemails` had no check, so `/cron/schedulesmsemails/<anything>` sent any school's
  due messages without a key.

Note that `routes.php` maps `cron/<one segment>` to `cron/index/<key>`, so a single-segment
call always runs the daily jobs; the runner uses three segments to reach a specific task.

Verified (test database, guard in `throw` mode):
- all three task groups run for the 4 schools with 0 failures;
- `student_attendance` wrote 3 absences, all school 2's own students, nothing in other
  schools; a second run added none;
- over HTTP on `xyz.`, the school's key works, while school 1's key, a wrong key or no key
  is refused, for every task.

Local note: Windows can't pass an empty environment variable to a child process.
`config/database.php` therefore also accepts `EDUSYS_DB_PASSWORD_EMPTY=true`, and the runner
sets it for its children when the password is empty (Laragon's passwordless root).

Seeded schools 1 and 2 have an empty `cron_secret_key`, so their cron URLs are refused until
a key is set (System Settings → General, or the cron key field). New schools get a random key at setup.

### 4.10 Legacy multi-branch module

`multi_branch`, `Multibranch_model` and `admin/multibranch/*` exist only in the
school copy (`school_instance_extracted`), not in this codebase. They switch between
**databases**, which no longer applies. Leave the table empty. If branches are
needed later, model them as schools linked by a `school.group_id`, with consolidated
reports through `without_tenant()`.

---

## 5. Implementation order

| Phase | Work | Done when |
|---|---|---|
| 0 | `git init`, create the new empty DB, set `EDUSYS_DB_*`, local wildcard host (`*.edusys.test`) | App boots against the new DB |
| 1 | Migrate + seed (delivered) | `migrate status` = 241; seeds load |
| 2 ✅ | `Tenant` + resolver hook (§4.1) | Unknown host → 404; central host → school 1; `xyz.` → school 2 |
| 3 ✅ | Tenant DB driver + config (§4.2) | Unit tests for where-wrapping, joins, inserts, hybrid roles |
| 4 ✅ | Auth/session binding, remove `Db_manager` DB switching (§4.5) | Cross-school login and session reuse rejected |
| 5 ✅ | Raw SQL conversion + lint (§4.3); settings `id = 1` removal (§4.4) | Lint clean; guard log empty during a full click-through |
| 6 ✅ | Platform guard, `School_provisioner`, default seed, payments (§4.6, §4.8) | New school self-serves from `/plans` to its login; no folder or database is created |
| 7 ✅ | Uploads phase A, cron CLI (§4.7, §4.9) | Images and cron jobs work for both schools |
| 8 ✅ | Test pass (§6), DNS/TLS, first deployment (§7) | Sign-off checklist |
| 9 ✅ | Uploads phase B, private file serving (§4.7) | Files isolated per school |

## 6. Testing ✅ test pass done (phase 8)

All suites run against a migrated and seeded **test** database, with the app on `127.0.0.1:8099`
and `EDUSYS_TENANCY_RAW_SQL=throw`:

| Suite | What it proves | Result |
|---|---|---|
| `php index.php tenancy_test` (per school) | Query-builder scoping, writes, the hybrid `roles` table, the raw-SQL guard | 71/71 for schools 1–4 (including two set up by `School_provisioner`); no-school mode 4/4 |
| `tools/tenancy/tests/test_tenant_resolver.php` | Host → school rules | 46/46 |
| `tools/tenancy/tests/session_e2e.sh` | Cross-school login refused, session ids rotate at login, replayed cookies refused, host-only cookies | 15/15 |
| `tools/tenancy/tests/portal_pass.sh` | Logged-in crawl of every portal | 0 failures, see below |
| `tools/tenancy/tests/idor.sh` | School 2 requesting school 1 record ids | 10/10 pages: school 1 sees its marked record, school 2 doesn't |
| `php tools/tenancy/lint_raw_sql.php` | No hand-written SQL without `school_id` | 327/327 |
| `Tenancy_sqlcheck` (per school) | Converted model SQL still runs | No change from the pre-conversion baseline |
| `cron_all` runs | Every school's jobs, writes stay in the school | 4 schools, 0 failed; attendance only in its school |

Portal crawl, pages loaded with no DB error, 5xx or tenancy violation:

| Portal (school 2 unless noted) | Pages |
|---|---|
| Super Admin | 250 (cap) |
| Admin | 250 (cap) |
| Teacher | 92 |
| Accountant | 85 |
| Librarian | 47 |
| Receptionist | 41 |
| Student | 22 |
| Parent | 22 |
| Front site (public) | 29 |
| Platform, central host (school 1 super admin) | 234 |
| New school (Greenfield, set up by the provisioner) | 200 |

The crawler only follows GET links and skips anything that looks like it changes data, so forms and
actions (save, delete, collect fee, …) were covered by the static lint and the SQL check, not by clicks.
A manual pass of the main workflows (admission, fee collection, attendance, exams, online
course purchase) on one school is still recommended before go-live.

Test-data notes: the seeded data has no Admin or Receptionist in school 2, so the test pass adds
`portal.admin@test` and `portal.reception@test`. School 2's `std1` has no enrolment (its class was
deleted, §2.2), so the student and parent crawls use `std25`/`parent25`.

Legacy pages that are broken for every school (missing view or model files, independent of tenancy):
`admin/income/view`, `admin/expense/view`, `feetype/edit`, `admin/subscription` (§4.6). Also
`student/view` for a student with no enrolment in the current session produces invalid SQL
(`student_session_id = ORDER BY`).

## 7. First deployment ✅ kit ready (phase 8)

`DEPLOYMENT.md` is the go-live checklist: what not to upload, server requirements,
DNS and wildcard TLS, the database and least-privilege accounts, environment variables,
mail, cron and backups, the pre-opening checks, rollback, and adding a school day to day.
The examples are in `deploy/`:
- `apache-vhost.conf.example`: one HTTPS vhost for `edusys.ng` and `*.edusys.ng`, with the environment set there;
- `crontab.example`: `cron_all` lines plus a nightly `mysqldump`;
- `mysql-setup.sql.example`: the database, an application account limited to it, and a backup account.

The root `.htaccess` now refuses, tested through Apache (403):
- hidden files and folders (`.env`, `.git`), apart from ACME `/.well-known/`;
- internal folders: `application`, `system`, `tools`, `deploy`, `dbsample`, `school_instance_extracted`, `backup`, `temp`, `cgi-bin`;
- top-level `.sql`/`.md`/`.zip`/`.sh`/`.log` files and `adminer.php`.

`backend/` and `uploads/` assets are still served. `school_instance_extracted/` has its own
`.htaccess`, which overrides these rules, so it must not be deployed at all (listed in `DEPLOYMENT.md`).

## 8. Decisions and risks

Settled:

* **School 2 orphans:** accepted. The 313 rows removed by FK rules are sample data
  (the software is not live).
* **School 2 subdomain:** `xyz` is fine for test data. School 1 has no subdomain
  and is served on the central host.
* **Platform administrators = school 1's role-7 staff.** This reuses the existing
  screens.

Risks:

1. **Sensitive data in seeds:** the seeds contain student/staff personal data,
   password hashes, **plaintext student/parent passwords** (`users.password`, as the
   legacy app stores them) and payment-gateway test keys. Keep them out of public
   repositories. Hashing user passwords is a recommended follow-up.
2. **`guest.school_id`** already existed with a different meaning (central row = 182).
   It's now the tenant key. Seeded guest rows belong to the school they came from.
3. **Charset:** most tables are `utf8mb3`, as in the source schema. Converting to
   `utf8mb4` is a separate migration.
4. **Hard-coded role ids** (1–7) stay valid because system roles are shared.
   Audit any other literal ids (e.g. `cbse_exams` id 1) while converting models.
5. **Security debts:** credentials now come from the environment. The old root database
   password and two Mailgun keys were removed from the code; rotate them (`DEPLOYMENT.md` §5).
   The root `.htaccess` refuses internal folders, hidden files and `adminer.php` (phase 8), but
   `adminer.php` should still be deleted from the server. Remaining low-risk keys in code:
   PayMongo test keys (`libraries/Paymongo.php`) and a Google Maps browser key
   (`views/admin/pickuppoint/index.php`).
