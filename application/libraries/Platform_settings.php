<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The platform admin's settings (table platform_settings), with defaults.
 */
class Platform_settings
{
    /** name => default */
    const DEFAULTS = array(
        'platform_name' => 'Edusys',
        'support_email' => '',          // empty: the "from" address of outgoing mail
        'support_phone' => '',
        'notify_email' => '',           // where platform notifications go; empty: support email
        'reminders_enabled' => '1',
        'reminder_days' => '30,7,1',    // days before expiry
        'expired_notice' => '1',        // email the owner when a subscription has ended
        'extra_reserved_subdomains' => '',
    );

    /** @var CI_Controller */
    private $CI;
    private $cache = null;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    public function get($name)
    {
        if ($this->cache === null) {
            $this->cache = array();
            foreach ($this->CI->db->get('platform_settings')->result_array() as $row) {
                $this->cache[$row['name']] = $row['value'];
            }
        }
        if (isset($this->cache[$name]) && $this->cache[$name] !== '') {
            return $this->cache[$name];
        }
        $default = isset(self::DEFAULTS[$name]) ? self::DEFAULTS[$name] : '';
        if ($name === 'support_email' && $default === '') {
            return (string) $this->CI->config->item('from_email');
        }
        if ($name === 'notify_email' && $default === '') {
            return $this->get('support_email');
        }
        return $default;
    }

    public function set($name, $value)
    {
        $value = (string) $value;
        $exists = $this->CI->db->where('name', $name)->count_all_results('platform_settings') > 0;
        if ($exists) {
            $this->CI->db->where('name', $name)->update('platform_settings', array('value' => $value));
        } else {
            $this->CI->db->insert('platform_settings', array('name' => $name, 'value' => $value));
        }
        if ($this->cache !== null) {
            $this->cache[$name] = $value;
        }
    }

    /** Reminder days as sorted integers, e.g. [30, 7, 1]. */
    public function reminder_days()
    {
        $days = array();
        foreach (explode(',', $this->get('reminder_days')) as $d) {
            $d = (int) trim($d);
            if ($d > 0 && $d <= 365) {
                $days[$d] = $d;
            }
        }
        rsort($days);
        return array_values($days);
    }

    /** Subdomains reserved by the platform admin, on top of config/tenancy.php. */
    public function extra_reserved_subdomains()
    {
        $out = array();
        foreach (preg_split('/[\s,]+/', strtolower($this->get('extra_reserved_subdomains'))) as $s) {
            if ($s !== '') {
                $out[] = $s;
            }
        }
        return array_values(array_unique($out));
    }
}
