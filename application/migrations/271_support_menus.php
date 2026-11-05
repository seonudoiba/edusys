<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Sidebar entries for the support pages:
 *   platform school   Support:          Support desk, Website enquiries
 *                     System Settings:  + Backups
 *   every other school Help & Support:  Support requests
 * Also lets platform roles be given "add" and "delete" on platform_settings (backups, enquiries).
 * New installations get the same rows from the default seed.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Support_menus extends CI_Migration
{
    const URLS = array('admin/support_desk', 'admin/enquiries', 'admin/backups', 'admin/support');

    public function up()
    {
        $this->db->without_tenant(function ($db) {
            foreach ($db->select('id, is_platform')->get('school')->result() as $school) {
                $sid = (int) $school->id;
                $settings = $db->where(array('school_id' => $sid, 'lang_key' => 'system_setting'))->get('sidebar_menus')->row();
                if (!$settings) {
                    continue; // not set up yet: the default seed brings everything
                }
                if ((int) $school->is_platform === 1) {
                    $this->for_platform($db, $sid, $settings);
                } else {
                    $menu = $this->ensure($db, 'sidebar_menus', array('school_id' => $sid, 'lang_key' => 'help_support'), array(
                        'permission_group_id' => $settings->permission_group_id, 'icon' => 'fa fa-life-ring ftlayer', 'menu' => 'Help & Support',
                        'activate_menu' => 'help_support', 'system_level' => 0, 'level' => 30, 'sidebar_display' => 1, 'is_active' => 1,
                        'access_permissions' => "('general_setting', 'can_view')",
                    ));
                    $this->sub($db, $sid, $menu, 'support_tickets', 'support_tickets', 'admin/support', 1, "('general_setting', 'can_view')", 'support', 'index,create,view', $settings->permission_group_id);
                }
            }
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            $db->where_in('url', self::URLS)->delete('sidebar_sub_menus');
            $db->where_in('lang_key', array('help_support', 'platform_support'))->delete('sidebar_menus');
        });
    }

    private function for_platform($db, $sid, $settings)
    {
        $group = $db->where(array('school_id' => $sid, 'short_code' => 'platform_admin'))->get('permission_group')->row();
        if (!$group) {
            return;
        }
        $db->where(array('school_id' => $sid, 'short_code' => 'platform_settings'))->update('permission_category', array('enable_add' => 1, 'enable_delete' => 1));
        $menu = $this->ensure($db, 'sidebar_menus', array('school_id' => $sid, 'lang_key' => 'platform_support'), array(
            'permission_group_id' => $group->id, 'icon' => 'fa fa-life-ring ftlayer', 'menu' => 'Support', 'activate_menu' => 'platform_support',
            'system_level' => 0, 'level' => 5, 'sidebar_display' => 1, 'is_active' => 1,
            'access_permissions' => "('platform_settings', 'can_view')",
        ));
        $this->sub($db, $sid, $menu, 'platform_support_desk', 'support_desk', 'admin/support_desk', 1, "('platform_settings', 'can_view')", 'support_desk', 'index,view', $group->id);
        $this->sub($db, $sid, $menu, 'platform_enquiries', 'website_enquiries', 'admin/enquiries', 2, "('platform_settings', 'can_view')", 'enquiries', 'index,view', $group->id);
        $this->sub($db, $sid, (int) $settings->id, 'platform_backups', 'platform_backups', 'admin/backups', 0, "('platform_settings', 'can_view')", 'backups', 'index', $group->id);
    }

    private function sub($db, $sid, $menu_id, $key, $lang_key, $url, $level, $access, $controller, $methods, $group_id)
    {
        $this->ensure($db, 'sidebar_sub_menus', array('school_id' => $sid, 'url' => $url, 'sidebar_menu_id' => $menu_id), array(
            'menu' => $key, 'key' => $key, 'lang_key' => $lang_key, 'level' => $level, 'access_permissions' => $access,
            'permission_group_id' => $group_id, 'activate_controller' => $controller, 'activate_methods' => $methods, 'addon_permission' => '', 'is_active' => 1,
        ));
    }

    /** Id of the row matching $where, inserted with $values when missing. */
    private function ensure($db, $table, array $where, array $values)
    {
        $row = $db->where($where)->get($table)->row();
        if ($row) {
            return (int) $row->id;
        }
        $db->insert($table, array_merge($where, $values));
        return (int) $db->insert_id();
    }
}
