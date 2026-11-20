<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Report Cards sidebar: Broadsheet (with transcripts and score export), Result Checker PINs and
 * Promotion, for every school that has the Report Cards menu. New schools get the same rows from
 * the default seed (097_sidebar_sub_menus).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Report_cards_menus extends CI_Migration
{
    /** url => [key, lang_key, access, controller, methods] */
    const MENUS = array(
        'cbseexam/broadsheet' => array('broadsheet', 'broadsheet', "('template_marks_report', 'can_view') || ('subject_marks_report', 'can_view')", 'broadsheet', 'index,transcript'),
        'cbseexam/pins' => array('result_checker_pins', 'result_checker_pins', "('cbse_exam_setting', 'can_view')", 'pins', 'index'),
        'cbseexam/promotion' => array('promotion_engine', 'promotion_engine', "('cbse_exam', 'can_view')", 'promotion', 'index'),
    );

    public function up()
    {
        $this->db->without_tenant(function ($db) {
            foreach ($db->select('school_id, sidebar_menu_id, permission_group_id')->where('url', 'cbseexam/exam')->get('sidebar_sub_menus')->result_array() as $anchor) {
                foreach (self::MENUS as $url => $m) {
                    $where = array('school_id' => (int) $anchor['school_id'], 'sidebar_menu_id' => (int) $anchor['sidebar_menu_id'], 'url' => $url);
                    if ($db->where($where)->count_all_results('sidebar_sub_menus') > 0) {
                        continue;
                    }
                    $db->insert('sidebar_sub_menus', $where + array('menu' => $m[0], 'key' => $m[0], 'lang_key' => $m[1], 'level' => 1, 'access_permissions' => $m[2],
                        'permission_group_id' => $anchor['permission_group_id'], 'activate_controller' => $m[3], 'activate_methods' => $m[4], 'addon_permission' => '', 'is_active' => 1));
                }
            }
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            $db->where_in('url', array_keys(self::MENUS))->delete('sidebar_sub_menus');
        });
    }
}
