<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Check whether the site is offline or not.
 *
 */
class Maintenance_hook
{
    public function __construct()
    {
        log_message('debug', 'Accessing maintenance hook!');
    }

    public function offline_check()
    {
        if (file_exists(APPPATH . 'config/maintenance_config.php')) {
            include APPPATH . 'config/maintenance_config.php';

            // Mode first: REMOTE_ADDR does not exist on the CLI.
            if (isset($config['maintenance_mode']) && $config['maintenance_mode'] === true
                && !in_array(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '', $config['maintenance_ips'])) {
                include APPPATH . 'views/maintenance.php';
                exit;
            }
        }
    }
}
