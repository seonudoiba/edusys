<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Ensures the shared, tenant-aware connection is loaded as $this->db.
 *
 * This used to pick a database group from the logged-in user's session
 * (db_array.db_group) and reconnect to it. With one shared database, the
 * session no longer chooses a database: every request uses the configured
 * connection, scoped to the school resolved from the host (see
 * core/MY_Loader.php and libraries/tenancy/).
 */
class Db_manager
{
    public $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    /**
     * Kept for callers of the old API. Only the configured connection exists.
     *
     * @param string $db_name '' or the configured group name
     * @return CI_DB_query_builder the shared connection
     */
    public function get_connection($db_name = '')
    {
        if ($db_name !== '' && $db_name !== 'default') {
            throw new RuntimeException("Database connection '$db_name' is not available; all schools share one database.");
        }
        return $this->CI->db;
    }
}
