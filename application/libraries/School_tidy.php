<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Simpler school defaults (migration 315 for existing schools, School_provisioner for new ones):
 *
 *   - the Add/Edit Staff form leaves out fields Nigerian schools do not use (father's and mother's
 *     name, marital status, EPF number, contract type, work shift, work location, social media);
 *     System Settings > System Fields brings any of them back
 *   - Visitor Book and Apply Leave are not in the student and parent portal
 *   - Behaviour > Report Behaviour (behaviour/log), first in the Behaviour menu
 *
 * Call inside without_tenant (or with the school as tenant). Running it again changes nothing.
 */
class School_tidy
{
    const STAFF_FIELDS_OFF = array('staff_father_name', 'staff_mother_name', 'staff_marital_status', 'staff_epf_no', 'staff_contract_type', 'staff_work_shift', 'staff_work_location', 'staff_social_media');
    const PORTAL_OFF = array('apply_leave', 'visitor_book');

    public function install($school_id)
    {
        $db = get_instance()->db;
        $school_id = (int) $school_id;
        $db->where('school_id', $school_id)->update('sch_settings', array_fill_keys(self::STAFF_FIELDS_OFF, 0));
        $db->where('school_id', $school_id)->where_in('short_code', self::PORTAL_OFF)->update('permission_student', array('student' => 0, 'parent' => 0));
        $this->behaviour_menu($db, $school_id);
    }

    private function behaviour_menu($db, $school_id)
    {
        $menu = $db->select('id, permission_group_id')->where(array('school_id' => $school_id, 'lang_key' => 'behaviour_records'))->get('sidebar_menus')->row();
        if (!$menu || $db->where(array('school_id' => $school_id, 'url' => 'behaviour/log'))->count_all_results('sidebar_sub_menus') > 0) {
            return;
        }
        $db->set('level', 'level + 1', false)->where(array('school_id' => $school_id, 'sidebar_menu_id' => (int) $menu->id))->update('sidebar_sub_menus');
        $db->insert('sidebar_sub_menus', array(
            'school_id' => $school_id,
            'sidebar_menu_id' => (int) $menu->id,
            'menu' => 'Report Behaviour',
            'key' => 'report_behaviour',
            'lang_key' => 'report_behaviour',
            'url' => 'behaviour/log',
            'level' => 1,
            'access_permissions' => "('behaviour_records_assign_incident', 'can_view')",
            'permission_group_id' => $menu->permission_group_id,
            'activate_controller' => 'log',
            'activate_methods' => 'index',
            'addon_permission' => null,
            'is_active' => 1,
        ));
    }
}
