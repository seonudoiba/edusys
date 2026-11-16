<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Platform sidebar: Pricing Bands is removed from Subscription List; Plan > Plan (300) opens the same page.
 * Runs libraries/Platform_admin_setup.php again.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Platform_bands_menu_once extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $this->load->library('Platform_admin_setup');
            foreach ($db->select('id')->where('is_platform', 1)->get('school')->result_array() as $school) {
                $this->platform_admin_setup->install((int) $school['id']);
            }
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            foreach ($db->select('id')->where('is_platform', 1)->get('school')->result_array() as $school) {
                $sid = (int) $school['id'];
                $menu = $db->where(array('school_id' => $sid, 'lang_key' => 'subscription'))->get('sidebar_menus')->row_array();
                if ($menu && $db->where(array('school_id' => $sid, 'lang_key' => 'pricing_bands'))->count_all_results('sidebar_sub_menus') === 0) {
                    $db->insert('sidebar_sub_menus', array('school_id' => $sid, 'sidebar_menu_id' => $menu['id'], 'menu' => 'billing_bands',
                        'key' => 'billing_bands', 'lang_key' => 'pricing_bands', 'url' => 'admin/billing/bands', 'level' => 2,
                        'access_permissions' => "('pricing_bands', 'can_view')", 'permission_group_id' => $menu['permission_group_id'],
                        'activate_controller' => 'billing', 'activate_methods' => 'bands', 'addon_permission' => '', 'is_active' => 1));
                }
            }
        });
    }
}
