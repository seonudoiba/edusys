<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Tenancy (single shared database, one school per subdomain)
| -------------------------------------------------------------------------
| Every web request is mapped to exactly one school before any controller
| runs (hooks/Tenant_hook.php). Values come from the environment so each
| server sets its own; the defaults below are empty on purpose: when a
| value is missing, web requests fail closed with HTTP 503.
|
|   EDUSYS_BASE_DOMAIN   Parent domain of school subdomains.
|                        edusys.ng  ->  greenfield.edusys.ng = school with subdomain 'greenfield'
|   EDUSYS_CENTRAL_HOST  Comma-separated host names served by the platform school
|                        (school.is_platform = 1), e.g. "edusys.ng,www.edusys.ng".
|   EDUSYS_FORCE_HTTPS   "true" when TLS terminates in front of PHP, so generated
|                        URLs use https:// even though PHP sees plain HTTP.
|   EDUSYS_SCHOOL_ID     CLI only: school to run a command as (cron, scripts).
|                        Unset = no school (migrations, seeds).
*/

$tenancy_env = function ($name) {
    $value = getenv($name);
    return $value === false ? '' : trim($value);
};

$config['tenancy_base_domain'] = strtolower($tenancy_env('EDUSYS_BASE_DOMAIN'));
$config['tenancy_central_hosts'] = array_values(array_filter(array_map(
    function ($h) { return strtolower(trim($h)); },
    explode(',', $tenancy_env('EDUSYS_CENTRAL_HOST'))
)));
$config['tenancy_force_https'] = strtolower($tenancy_env('EDUSYS_FORCE_HTTPS')) === 'true';
$config['tenancy_cli_school_id'] = $tenancy_env('EDUSYS_SCHOOL_ID');

// Subdomain labels a school may never use (they are service or platform names).
$config['tenancy_reserved_subdomains'] = array(
    'www', 'admin', 'api', 'app', 'mail', 'smtp', 'imap', 'pop', 'ftp', 'cdn', 'static',
    'assets', 'files', 'uploads', 'support', 'help', 'docs', 'status', 'billing', 'pay',
    'platform', 'dashboard', 'login', 'register', 'signup', 'test', 'dev', 'staging',
);
