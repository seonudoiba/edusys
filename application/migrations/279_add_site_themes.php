<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * School website themes (config/site_themes.php): front_cms_settings.theme_options holds a school's
 * changes to its theme, and Front CMS gets a "Website design" page. The six original theme keys are
 * still themes, so every school keeps its choice and sees it redesigned.
 */
class Migration_Add_site_themes extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('theme_options', 'front_cms_settings')) {
            $this->db->query('ALTER TABLE `front_cms_settings` ADD COLUMN `theme_options` TEXT NULL AFTER `theme`'); // tenancy-lint: ok schema change
        }
        $this->db->without_tenant(function ($db) {
            foreach ($db->select('id')->get('school')->result() as $school) {
                $sid = (int) $school->id;
                $menu = $db->where(array('school_id' => $sid, 'lang_key' => 'front_cms'))->get('sidebar_menus')->row();
                if ($menu && $db->where(array('school_id' => $sid, 'url' => 'admin/front/design'))->count_all_results('sidebar_sub_menus') === 0) {
                    $db->insert('sidebar_sub_menus', array('school_id' => $sid, 'sidebar_menu_id' => $menu->id, 'menu' => 'website_design', 'key' => 'website_design',
                        'lang_key' => 'website_design', 'url' => 'admin/front/design', 'level' => 0, 'access_permissions' => "('front_cms_setting', 'can_view')",
                        'permission_group_id' => $menu->permission_group_id, 'activate_controller' => 'design', 'activate_methods' => 'index', 'addon_permission' => '', 'is_active' => 1));
                }
            }
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            $db->where('url', 'admin/front/design')->delete('sidebar_sub_menus');
        });
        if ($this->db->field_exists('theme_options', 'front_cms_settings')) {
            $this->db->query('ALTER TABLE `front_cms_settings` DROP COLUMN `theme_options`'); // tenancy-lint: ok schema change
        }
    }
}
