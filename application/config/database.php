<?php defined('BASEPATH') or exit('No direct script access allowed');

$query_builder = true;

// Connection settings may come from the environment (EDUSYS_DB_*); the literals are the legacy fallbacks.
$env = function ($name, $default) {
    $value = getenv($name);
    return $value === false ? $default : $value;
};

$db['default'] = array(
    'dsn' => '',
    'hostname' => $env('EDUSYS_DB_HOST', 'localhost'),
    'username' => $env('EDUSYS_DB_USER', 'root'),
    // EDUSYS_DB_PASSWORD_EMPTY=true means an empty password (Windows can't hold an empty
    // environment variable, e.g. for Laragon's passwordless root in child processes).
    // No password in code: set EDUSYS_DB_PASSWORD (unset = empty, e.g. Laragon's passwordless root).
    'password' => strtolower((string) getenv('EDUSYS_DB_PASSWORD_EMPTY')) === 'true' ? '' : $env('EDUSYS_DB_PASSWORD', ''),
    'database' => $env('EDUSYS_DB_NAME', 'smartschool'),
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => false,
    'db_debug' => (ENVIRONMENT !== 'development'),
    'cache_on' => false,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => false,
    'compress' => false,
    'stricton' => false,
    'failover' => array(),
    'save_queries' => true,
    'multi_branch' => false,
    'port' => (int) $env('EDUSYS_DB_PORT', 3306),
);

$active_group = 'default';
// One shared database for every school (single-database tenancy). The legacy multi-branch
// connection groups (built from the multi_branch table) are gone: core/MY_Loader.php only
// opens this default connection.
