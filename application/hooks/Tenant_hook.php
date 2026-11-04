<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/Tenant.php';
require_once APPPATH . 'libraries/Tenant_resolver.php';

/**
 * pre_controller hook: decides which school this request belongs to, before
 * any controller constructor (and therefore any model or query) runs.
 *
 * Web:  Host header -> school (see config/tenancy.php). Unknown host -> 404,
 *       disabled school -> 403, missing configuration -> 503. Sets base_url
 *       to the request host.
 * CLI:  EDUSYS_SCHOOL_ID -> that school; unset -> no school (migrate, seed).
 */
class Tenant_hook
{
    /** Columns read from `school` for the tenant context. */
    const COLUMNS = 'id, school_name, subdomain, is_platform, status';

    /** `school.status` value of a school that may be served. */
    const STATUS_ACTIVE = 1;

    public function resolve()
    {
        $config =& load_class('Config', 'core');
        $config->load('tenancy');

        if (is_cli()) {
            $this->resolve_cli($config->item('tenancy_cli_school_id'));
            return;
        }

        $resolver = new Tenant_resolver(
            $config->item('tenancy_base_domain'),
            $config->item('tenancy_central_hosts'),
            $config->item('tenancy_reserved_subdomains')
        );
        if (!$resolver->is_configured()) {
            log_message('error', 'Tenancy is not configured: set EDUSYS_BASE_DOMAIN and EDUSYS_CENTRAL_HOST.');
            $this->fail(503, 'Service unavailable', 'This site is not configured yet.');
        }

        $match = $resolver->classify(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        if ($match['type'] === Tenant_resolver::REJECT) {
            show_404();
        }

        $db = $this->connect();
        $db->select(self::COLUMNS)->from('school');
        if ($match['type'] === Tenant_resolver::LOOKUP_PLATFORM) {
            $db->where('is_platform', 1);
        } else {
            $db->where('subdomain', $match['subdomain']);
        }
        $rows = $db->limit(2)->get()->result_array();
        $db->close();

        if (count($rows) !== 1) {
            if ($match['type'] === Tenant_resolver::LOOKUP_PLATFORM) {
                log_message('error', 'Tenancy: expected exactly one school with is_platform = 1, found ' . count($rows) . '.');
                $this->fail(503, 'Service unavailable', 'This site is not configured yet.');
            }
            show_404();
        }
        $school = $rows[0];
        if ((int) $school['status'] !== self::STATUS_ACTIVE) {
            $this->fail(403, 'School unavailable', 'This school is currently unavailable. Please contact the school or the platform administrator.', 'error_school_unavailable');
        }

        $https = is_https() || $config->item('tenancy_force_https');
        $config->set_item('base_url', Tenant_resolver::base_url($match, $https));
        Tenant::set($school, $match['host']);
    }

    private function resolve_cli($school_id)
    {
        if ($school_id === '' || $school_id === null) {
            Tenant::set_none();
            return;
        }
        if (!ctype_digit($school_id)) {
            $this->cli_fail("EDUSYS_SCHOOL_ID must be a school id, got '$school_id'.");
        }
        $db = $this->connect();
        $school = $db->select(self::COLUMNS)->where('id', (int) $school_id)->get('school')->row_array();
        $db->close();
        if (!$school) {
            $this->cli_fail("EDUSYS_SCHOOL_ID=$school_id: no such school.");
        }
        Tenant::set($school);
    }

    /**
     * Separate short-lived connection: the CodeIgniter instance (and $this->db) does not exist yet.
     * DB('group') would look up package paths through get_instance(), so the group's settings are
     * read here and passed as an array instead.
     */
    private function connect()
    {
        require_once BASEPATH . 'database/DB.php';
        $params = $this->connection_settings();
        $db =& DB($params);
        return $db;
    }

    /** Settings of the active group from config/database.php, read in an isolated scope. */
    private function connection_settings()
    {
        $file = APPPATH . 'config/' . ENVIRONMENT . '/database.php';
        if (!file_exists($file)) {
            $file = APPPATH . 'config/database.php';
        }
        $read = function ($file) {
            include $file;
            return isset($db, $active_group, $db[$active_group]) ? $db[$active_group] : null;
        };
        $settings = $read($file);
        if ($settings === null) {
            show_error('No active database connection group in config/database.php.');
        }
        return $settings;
    }

    private function fail($status, $heading, $message, $template = 'error_general')
    {
        $exceptions =& load_class('Exceptions', 'core');
        echo $exceptions->show_error($heading, $message, $template, $status);
        exit(1);
    }

    private function cli_fail($message)
    {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}
