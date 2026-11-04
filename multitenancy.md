# Shared-codebase tenancy rollout

The application is being moved from per-school application folders to one
CodeIgniter codebase with a central school registry and one database per school.
The registry is authoritative: a tenant database is selected only when the
request's normalized `Host` exactly matches a school whose provisioning state
is `ready`. Forwarded-host headers, session database-group values, URL
parameters, and arbitrary database names are not used to select a tenant.

## Current implementation boundary

Migrations 127 through 130 add the hostname, database name, provisioning lifecycle,
per-tenant database credential fields, provisioning job queue, and school
application approval metadata. Migrations 131 through 133 add currency precision,
manual-payment review, and step-by-step provisioning progress.
Tenant database passwords are stored as authenticated Sodium ciphertext, using
the `EDUSYS_TENANT_CREDENTIAL_KEY` environment secret. The `Tenant_resolver`
establishes a central or tenant database context before application models load. Tenant
resolution is deliberately disabled by default so the current Mount Carmel
installation remains unchanged while the central/tenant data split is prepared.
The school registry form can reserve a hostname; a school is routed only after
its database is provisioned, its state is `ready`, and its status is enabled.
Disabling a school blocks tenant routing but preserves its registry and data.

The tenant setup workflow is approval-gated. A school application remains
pending until a Super Admin approves it, including applications whose online
payment has already been verified. A manual-transfer receipt must be confirmed
before the application can pass the payment gate. A hostname and any required
payment must be present before setup is queued.

The worker provisions a tenant database and user, applies the tenant schema,
seeds school settings and the initial school-admin account, and only then marks
the school `ready`. The school registry shows all nine lifecycle steps and
worker progress. The tenant resolver routes a request only for an approved,
enabled school in the `ready` state.

The legacy application schema has 237 tables, while the repository does not
contain a complete migration history for that schema. The tenant baseline and
bootstrap seed the currently defined safe subset; review and test application
features that depend on other legacy tables before production use.

The existing `smartschool` database has not been split, and no school records
have been moved by this change. Central migrations 127 through 129 were applied
locally for the tenant registry, encrypted credentials, and provisioning queue.
Migration 130 adds approval metadata. Migration 131 widens currency conversion
rate storage to preserve the precision used by the currency settings. Back up
and verify the database before any future data movement. Migration 132 adds
manual subscription payment settings and receipt-review fields; migration 133
adds provisioning progress to the queue.

## Apply central registry migrations once

From the project directory in PowerShell, run the migration controller with the
Laragon PHP executable:

```powershell
& 'C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe' index.php migrate
```

The migration endpoint is CLI-only. Migrations remain disabled for automatic
web requests. Migrations 129 through 133 create the provisioning queue,
approval fields, higher-precision currency rates, manual payment workflow, and
worker progress tracking. Confirm the CodeIgniter migration ledger reports
version 133 before using the school setup progress UI. Receipt files are stored
outside the web root in a sibling `edusys-private/manual_payment_receipts`
directory; ensure the PHP process can create and write that directory on the
deployment server.

The tenant-only schema baseline is a separate migration stream. Migration 2
widens currency conversion rate storage for existing tenant databases; new
tenant baselines use the wider column from the start. The baseline contains
only table and index DDL (no `INSERT` statements or school records) extracted
from the legacy schema. Run it against a registered tenant in `provisioning` or
`ready` state with:

```powershell
& 'C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe' index.php migrate_tenant index 123
```

Replace `123` with the central school registry ID. The tenant DB and its
least-privilege user must already exist and the encrypted database credentials
must already be stored in the central registry. The command creates/uses the
tenant's own `migrations` table; never run it against the central database.

## Enable tenant lookup for a development environment

Set `EDUSYS_TENANCY_ENABLED=true` and `EDUSYS_CENTRAL_HOST` in the Apache
environment, then restart Laragon. The central hostname uses the configured
default database. Other hosts must have an exact matching registry row with:

- a valid DNS hostname;
- a generated database name containing only letters, digits, or underscores;
- a tenant-specific MySQL username and an encrypted password;
- `provisioning_state` set to `ready`.

Generate the encryption key once and store it in the server environment (not in
the repository or database):

```powershell
& 'C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe' -r "echo base64_encode(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES)), PHP_EOL;"
```

## Apply for and approve a school

Visitors click **Register a School** in the public navigation, choose a plan,
and submit the application details. Configure `EDUSYS_PAYSTACK_SECRET_KEY` in
the web server's environment with the secret key from the Paystack dashboard;
never put it in source control. The application stores Paystack's generated
reference before redirecting the applicant and confirms payment only after
server-side verification confirms the same reference, successful
status, exact amount, and applicant email. A missing key or failed verification
does not confirm payment or create the school record. A successfully paid
application is created with approval status `pending`, just like a free-plan
application. Manual-transfer receipts appear in **Manage Plans > Plan Payments**;
confirming a receipt creates a pending school application but does not approve
it. No application path enables tenant routing or provisions the tenant database
before Super Admin approval.

A staff member with the existing role-7 Super Admin account can review pending
applications at **Admin Dashboard > Review Applications** or the Super Admin
sidebar **Applications to Review** link. Approve or reject there; rejection
requires a reason. Payment and application approval are separate decisions.
Approval does not activate a tenant or create its admin account. The separate
platform-admin identity is still a later rollout item.

After approval, confirm that any required payment is complete and assign and
save a unique tenant hostname in the school record. Once all three gates pass,
tenant setup is queued automatically. The registry shows nine steps: application,
approval, payment and hostname checks, queue, database and user creation, schema
migration, admin/settings bootstrap, ready state, and tenant access. The current
worker step is shown while setup runs. Use **Queue setup** to start a job that
has not yet been queued, **Resume setup** after a failure, or to finish an older
database/schema-only job. A no-payment-required plan still requires approval
and hostname assignment.

Super Admins can add or enable public plans under **Admin Dashboard >
Manage Plans** (`/admin/plan`). At least one enabled plan is required for a
visitor to proceed from `/plans` to the application and payment form.
When creating or editing a plan, select the currency used for entry. The form
previews the converted amount and the server recalculates it using the active
rates under **System Settings > Currency** before saving in the platform base
currency. Enter rates there as the amount of base currency for one unit of the
selected currency (for example, `1 USD = 1,500 NGN`). These rates are
administrator-maintained; they are not fetched from a live exchange-rate
service. Paystack is initialized and verified in that same base currency, so
configure the platform currency to one supported by the Paystack account.

The applicant-facing plan and payment screens are the public **Plans** page
(`/plans`) and its **Register School & Choose Plan** form
(`/subscription/add?plan_id=<plan id>`). The current application form collects
school name, owner, email, and phone. A Super Admin must complete the remaining
school profile and assign its tenant hostname in the registry before setup.
**Register School** in the Super Admin registry is for manually adding a school
record; it does not collect payment.

## Provision tenants from the school-list UI

After one-time setup below, use **Applications to Review** to approve each
application. Then confirm payment when required and assign a unique hostname
using **Edit** in the school registry. When all gates pass, setup is queued
automatically. The web request never connects with the privileged
database-provisioning account. The worker creates the tenant database and
restricted account, applies schema migrations, bootstraps the school settings
and admin account, and only then marks the school active and `ready`. The
registry progress tracker shows each phase. Keep the `tenant_worker work`
process running for queued jobs to be processed. Use **Queue setup** to start
or **Resume setup** to retry/finish a job.

The equivalent command-line operation, useful for diagnostics, is:

```powershell
& 'C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe' index.php provision_tenant index 123
```

The provisioner generates a unique database, username, and password, encrypts
the password into the central registry, grants privileges only on that
database, and verifies that the tenant account can connect. It is safe to rerun
for a school left in `provisioning` or `failed` state; it resumes using the
reserved credentials. Set `EDUSYS_TENANT_DB_USER_HOST` to the exact application
server host or IP when the default `localhost` is not correct. Wildcard MySQL
account hosts are rejected.

Database creation uses a separate privileged connection configured with
`EDUSYS_PROVISIONER_DB_HOST`, `EDUSYS_PROVISIONER_DB_USER`, and
`EDUSYS_PROVISIONER_DB_PASSWORD`. Provide these only to the worker environment,
not Apache. The privilege account should be dedicated to tenant provisioning;
the normal central application account must not have global database/user
creation privileges.

### Run the worker on Windows, Linux, or macOS

The worker supports a portable, long-running mode. Run this from the project
directory in a terminal:

```text
php index.php tenant_worker work
```

For Laragon, use its PHP executable:

```powershell
& 'C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe' index.php tenant_worker work
```

Keep that terminal open; the worker polls the database queue every five
seconds, processes jobs as they arrive, and can be stopped with Ctrl+C. Set
`EDUSYS_TENANT_WORKER_POLL_SECONDS` to an integer from 1 to 60 to change the
idle polling interval. The worker takes a database lock so two worker
processes cannot provision tenants at the same time. The existing
`php index.php tenant_worker index` command remains a one-job/one-check mode.

Before starting the worker, configure these variables in its process
environment:

- `EDUSYS_TENANT_CREDENTIAL_KEY`: the same secret used by the web app to
  decrypt tenant DB passwords.
- `EDUSYS_PROVISIONER_DB_HOST`, `EDUSYS_PROVISIONER_DB_USER`, and
  `EDUSYS_PROVISIONER_DB_PASSWORD`: a dedicated privileged MySQL account for
  creating tenant databases and accounts. Never expose these to Apache or use
  an empty password in production.
- `EDUSYS_TENANT_DB_USER_HOST`: the exact MySQL host/IP tenant DB accounts
  should use. Wildcard account hosts are rejected.

In local Laragon development only, where MySQL root intentionally has a blank
password, set `EDUSYS_PROVISIONER_DB_PASSWORD_EMPTY=true` for the worker and
leave `EDUSYS_PROVISIONER_DB_PASSWORD` unset. This must never be used in
production.

On any OS, an open terminal is sufficient for development and testing. For
production, configure the operating system's service manager (for example
systemd on Linux or a Windows service wrapper) to start this same long-running
command at boot and restart it if it exits. Use a dedicated OS account and
keep privileged provisioning credentials available to the worker only.

The worker applies the tenant schema and bootstrap automatically. This command remains
available for diagnostics or manual recovery:

```powershell
& 'C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe' index.php migrate_tenant index 123
```

The diagnostic schema migration does not seed a school or activate routing.
Only the complete provisioning worker flow creates the school-admin account
and marks the tenant `ready`.

## Remaining rollout gates

1. Establish and test explicitly approved default records; never seed
   school-specific records from `system/school.sql`.
2. Add a separate platform-admin identity and control-plane routes that do not
   load school models.
3. Provision and verify a Mount Carmel database from a backup before changing
   DNS or removing any school data from the central database.
4. Test unknown-host rejection, tenant separation, login/session boundaries,
   uploads, background jobs, backups, and rollback before production cutover.
