<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Who sees Billing in a school's admin panel (controllers/admin/Billing_access.php): the school's
 * own super admin, on a school address, not in a platform support session.
 */
class Billing_link
{
    const SUPER_ADMIN_ROLE = 7;

    public static function allowed()
    {
        $CI = &get_instance();
        if (!class_exists('Tenant', false) || !Tenant::has() || Tenant::is_platform() || $CI->session->userdata('impersonated_by')) {
            return false;
        }
        $user = $CI->customlib->getUserData();
        return isset($user['role_id']) && (int) $user['role_id'] === self::SUPER_ADMIN_ROLE;
    }

    public static function url()
    {
        return site_url('admin/billing_access/open');
    }
}
