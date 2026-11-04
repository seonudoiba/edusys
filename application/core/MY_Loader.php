<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Loads the tenant-aware database driver (libraries/tenancy/Tenant_DB_mysqli_driver.php)
 * for every $this->load->database() call, so all query-builder statements are confined
 * to the current school.
 *
 * Only the configured default connection may be loaded. A session, URL or caller can no
 * longer pick another database group (Db_manager used to take it from the session).
 */
class MY_Loader extends CI_Loader
{
    public function database($params = '', $return = false, $query_builder = null)
    {
        $CI =& get_instance();

        // Same short-circuit as CI_Loader: reuse an existing shared connection.
        if ($return === false && $query_builder === null && isset($CI->db) && is_object($CI->db) && !empty($CI->db->conn_id)) {
            return false;
        }

        $db = self::tenant_connection($params, $query_builder);
        if ($return === true) {
            return $db;
        }
        $CI->db = '';
        $CI->db = $db;
        return $this;
    }

    /**
     * Opens a connection with the tenant-aware driver.
     *
     * @param string $group '' or the active group name; anything else is refused
     * @param bool|null $query_builder
     * @return Tenant_DB_mysqli_driver
     */
    public static function tenant_connection($group = '', $query_builder = null)
    {
        list($active_group, $settings) = self::connection_settings();
        if (!is_string($group) || ($group !== '' && $group !== $active_group)) {
            $label = is_string($group) ? $group : gettype($group);
            throw new RuntimeException("Database connection '$label' is not allowed; only the configured '$active_group' connection is.");
        }
        if ($query_builder === false) {
            throw new RuntimeException('The tenant-aware driver requires the Query Builder.');
        }
        if (!isset($settings['dbdriver']) || $settings['dbdriver'] !== 'mysqli') {
            throw new RuntimeException('The tenant-aware driver supports dbdriver = mysqli only.');
        }

        // Mirrors system/database/DB.php for the mysqli + Query Builder case.
        require_once BASEPATH . 'database/DB_driver.php';
        require_once BASEPATH . 'database/DB_query_builder.php';
        if (!class_exists('CI_DB', false)) {
            eval('class CI_DB extends CI_DB_query_builder { }');
        }
        require_once BASEPATH . 'database/drivers/mysqli/mysqli_driver.php';
        require_once APPPATH . 'libraries/tenancy/Tenant_DB_mysqli_driver.php';

        $CI =& get_instance();
        $CI->config->load('tenancy_tables', true);
        $tables = $CI->config->item('tenancy_tables');

        $guard = new Tenant_raw_sql_guard(
            getenv('EDUSYS_TENANCY_RAW_SQL') ?: Tenant_raw_sql_guard::MODE_LOG,
            array_merge($tables['tenancy_tenant_tables'], $tables['tenancy_hybrid_tables']),
            APPPATH . 'logs'
        );

        $db = new Tenant_DB_mysqli_driver($settings);
        $db->configure_tenancy($tables['tenancy_tenant_tables'], $tables['tenancy_hybrid_tables'], $guard);
        $db->initialize();
        // The legacy queries rely on MySQL's pre-5.7 GROUP BY behaviour; MySQL 8 enables
        // ONLY_FULL_GROUP_BY by default and rejects them. Turn it off for this connection only.
        $db->simple_query("SET SESSION sql_mode = REPLACE(REPLACE(@@SESSION.sql_mode, 'ONLY_FULL_GROUP_BY,', ''), 'ONLY_FULL_GROUP_BY', '')");
        return $db;
    }

    /** @return array [active group name, settings] from config/database.php, read in an isolated scope */
    private static function connection_settings()
    {
        $file = APPPATH . 'config/' . ENVIRONMENT . '/database.php';
        if (!file_exists($file)) {
            $file = APPPATH . 'config/database.php';
        }
        $read = function ($file) {
            include $file;
            return isset($db, $active_group, $db[$active_group]) ? array($active_group, $db[$active_group]) : null;
        };
        $result = $read($file);
        if ($result === null) {
            show_error('No active database connection group in config/database.php.');
        }
        return $result;
    }
}
