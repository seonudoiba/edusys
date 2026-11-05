<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Platform administration menus and permissions (platform schools only).
 *
 * - Permission group "Platform administration" (platform_admin) with the category
 *   platform_settings, and the categories the Plans and Subscriptions pages check (plan_list,
 *   subscription_list), so platform roles (Roles Permissions) can be given them.
 * - Sidebar:
 *     Platform Team:  Staff, Roles & permissions, Audit log, Login history
 *     Communication:  Email templates, Email log
 *     Schools:        + Waiting for approval
 *     System Setting: + Platform settings & health
 *     Front CMS:      + Events, Gallery, News (the platform website's own)
 * New installations get the same rows from the default seed (platform-only rows).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Platform_admin_menus extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            foreach ($db->select('id')->where('is_platform', 1)->get('school')->result() as $school) {
                $sid = (int) $school->id;
                if ($db->where('school_id', $sid)->count_all_results('sidebar_menus') === 0) {
                    continue; // not set up yet: the default seed brings everything
                }
                $this->for_platform_school($db, $sid);
            }
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            foreach ($db->select('id')->where('is_platform', 1)->get('school')->result() as $school) {
                $sid = (int) $school->id;
                $db->where('school_id', $sid)->where_in('url', array('admin/platform/settings', 'admin/platform/emails', 'admin/platform/email_log', 'admin/schools?tab=waiting_approval'))->delete('sidebar_sub_menus');
                $menus = array_column($db->select('id')->where('school_id', $sid)->where_in('lang_key', array('platform_team', 'platform_communication'))->get('sidebar_menus')->result_array(), 'id');
                if ($menus) {
                    $db->where_in('sidebar_menu_id', $menus)->delete('sidebar_sub_menus');
                    $db->where_in('id', $menus)->delete('sidebar_menus');
                }
                $group = $db->where(array('school_id' => $sid, 'short_code' => 'platform_admin'))->get('permission_group')->row();
                $db->where('school_id', $sid)->where_in('short_code', array('platform_settings', 'plan_list', 'subscription_list'))->delete('permission_category');
                if ($group) {
                    $db->where('id', $group->id)->delete('permission_group');
                }
            }
        });
    }

    private function for_platform_school($db, $sid)
    {
        // Permission group and categories
        $group_id = $this->ensure($db, 'permission_group', array('school_id' => $sid, 'short_code' => 'platform_admin'),
            array('name' => 'Platform administration', 'is_active' => 1, 'system' => 0));
        $plan_group = $db->where(array('school_id' => $sid, 'short_code' => 'plan_list'))->get('permission_group')->row();
        $sub_group = $db->where(array('school_id' => $sid, 'short_code' => 'subscription'))->get('permission_group')->row();
        $this->ensure($db, 'permission_category', array('school_id' => $sid, 'short_code' => 'platform_settings'),
            array('perm_group_id' => $group_id, 'name' => 'Platform settings and emails', 'enable_view' => 1, 'enable_add' => 0, 'enable_edit' => 1, 'enable_delete' => 0));
        if ($plan_group) {
            $this->ensure($db, 'permission_category', array('school_id' => $sid, 'short_code' => 'plan_list'),
                array('perm_group_id' => $plan_group->id, 'name' => 'Plans and features', 'enable_view' => 1, 'enable_add' => 1, 'enable_edit' => 1, 'enable_delete' => 1));
        }
        if ($sub_group) {
            $this->ensure($db, 'permission_category', array('school_id' => $sid, 'short_code' => 'subscription_list'),
                array('perm_group_id' => $sub_group->id, 'name' => 'Subscriptions and bank transfers', 'enable_view' => 1, 'enable_add' => 0, 'enable_edit' => 0, 'enable_delete' => 1));
        }

        // Menus
        $team = $this->ensure($db, 'sidebar_menus', array('school_id' => $sid, 'lang_key' => 'platform_team'), array(
            'permission_group_id' => $group_id, 'icon' => 'fa fa-users ftlayer', 'menu' => 'Platform Team', 'activate_menu' => 'platform_team',
            'system_level' => 0, 'level' => 3, 'sidebar_display' => 1, 'is_active' => 1,
            'access_permissions' => "('staff', 'can_view') || ('superadmin', 'can_view') || ('audit_trail_report', 'can_view') || ('user_log', 'can_view')",
        ));
        $comm = $this->ensure($db, 'sidebar_menus', array('school_id' => $sid, 'lang_key' => 'platform_communication'), array(
            'permission_group_id' => $group_id, 'icon' => 'fa fa-envelope-o ftlayer', 'menu' => 'Communication', 'activate_menu' => 'platform_communication',
            'system_level' => 0, 'level' => 4, 'sidebar_display' => 1, 'is_active' => 1,
            'access_permissions' => "('platform_settings', 'can_view')",
        ));
        $schools = $db->where(array('school_id' => $sid, 'lang_key' => 'school_information'))->get('sidebar_menus')->row();
        $settings = $db->where(array('school_id' => $sid, 'lang_key' => 'system_setting'))->get('sidebar_menus')->row();
        $schools_group = $db->where(array('school_id' => $sid, 'short_code' => 'school_information'))->get('permission_group')->row();

        $subs = array(
            array($team, 'platform_staff', 'platform_staff', 'admin/staff', 1, "('staff', 'can_view')", 'staff', 'index,edit,profile,create', $group_id),
            array($team, 'platform_roles', 'roles_permissions', 'admin/roles', 2, "('superadmin', 'can_view')", 'roles', 'index,permission', $group_id),
            array($team, 'platform_audit', 'audit_trail_report', 'admin/audit', 3, "('audit_trail_report', 'can_view')", 'audit', 'index', $group_id),
            array($team, 'platform_login_history', 'login_history', 'admin/userlog', 4, "('user_log', 'can_view')", 'userlog', 'index', $group_id),
            array($comm, 'platform_email_templates', 'email_templates', 'admin/platform/emails', 1, "('platform_settings', 'can_view')", 'platform', 'emails,email_edit', $group_id),
            array($comm, 'platform_email_log', 'email_log', 'admin/platform/email_log', 2, "('platform_settings', 'can_view')", 'platform', 'email_log', $group_id),
        );
        if ($schools && $schools_group) {
            $subs[] = array($schools->id, 'schools_waiting', 'waiting_for_approval', 'admin/schools?tab=waiting_approval', 3, "('schools', 'can_view')", 'schools', '', $schools_group->id);
        }
        if ($settings) {
            $subs[] = array($settings->id, 'platform_settings', 'platform_settings_health', 'admin/platform/settings', 0, "('platform_settings', 'can_view')", 'platform', 'settings', $group_id);
        }
        foreach ($subs as $sub) {
            list($menu_id, $menu, $lang_key, $url, $level, $access, $controller, $methods, $pgroup) = $sub;
            $this->ensure($db, 'sidebar_sub_menus', array('school_id' => $sid, 'url' => $url, 'sidebar_menu_id' => $menu_id), array(
                'menu' => $menu, 'key' => $menu, 'lang_key' => $lang_key, 'level' => $level, 'access_permissions' => $access,
                'permission_group_id' => $pgroup, 'activate_controller' => $controller, 'activate_methods' => $methods, 'addon_permission' => '', 'is_active' => 1,
            ));
        }
        // The platform website's events, gallery and news
        $db->where('school_id', $sid)->where_in('url', array('admin/front/events', 'admin/front/gallery', 'admin/front/notice'))
            ->update('sidebar_sub_menus', array('is_active' => 1));
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
