<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Who teaches each subject of a class and section (Academics > Assign Subject Teachers), without
 * having to build the class timetable first.
 *
 * An assignment is kept as a subject_timetable row with no day and no times: everything that works
 * out a teacher's classes, sections and subjects (Teacher Restricted Mode, the teacher's subjects in
 * attendance, marks, lesson plans, ...) already reads subject_timetable, so assigned teachers are
 * recognised everywhere; the timetable pages only show rows for a day, so these never show there.
 */
class Subject_teachers
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    const MENU_URL = 'admin/teacher/subject_teachers';

    /**
     * "Assign Subject Teachers" in the school's Academics menu, right after "Assign Class Teacher"
     * (migration 298 for existing schools, School_provisioner for new ones). Running it again changes nothing.
     * Call inside without_tenant (or with the school as tenant).
     */
    public function install_menu($school_id)
    {
        $db = $this->CI->db;
        $school_id = (int) $school_id;
        if ($db->where(array('school_id' => $school_id, 'url' => self::MENU_URL))->count_all_results('sidebar_sub_menus') > 0) {
            return 0;
        }
        $after = $db->where(array('school_id' => $school_id, 'url' => 'admin/teacher/assign_class_teacher'))->get('sidebar_sub_menus')->row();
        if (!$after) {
            return 0;
        }
        $db->set('level', 'level + 1', false)->where(array('school_id' => $school_id, 'sidebar_menu_id' => (int) $after->sidebar_menu_id))
            ->where('level >', (int) $after->level)->update('sidebar_sub_menus');
        $db->insert('sidebar_sub_menus', array(
            'school_id' => $school_id,
            'sidebar_menu_id' => (int) $after->sidebar_menu_id,
            'menu' => 'Assign Subject Teachers',
            'key' => 'assign_subject_teachers',
            'lang_key' => 'assign_subject_teachers',
            'url' => self::MENU_URL,
            'level' => (int) $after->level + 1,
            'access_permissions' => $after->access_permissions,
            'permission_group_id' => $after->permission_group_id,
            'activate_controller' => 'teacher',
            'activate_methods' => 'subject_teachers',
            'addon_permission' => null,
            'is_active' => 1,
        ));
        return 1;
    }

    private function session()
    {
        return (int) $this->CI->setting_model->getCurrentSession();
    }

    /** The class-section's subjects (from its subject groups) with the assigned teacher's staff id (0 = none). */
    public function subjects($class_id, $section_id)
    {
        $db = $this->CI->db;
        $session = $this->session();
        $rows = $db->select('sgs.id AS sgs_id, sg.id AS subject_group_id, sg.name AS group_name, s.id AS subject_id, s.name, s.code')
            ->from('class_sections cs')
            ->join('subject_group_class_sections sgcs', 'sgcs.class_section_id = cs.id')
            ->join('subject_groups sg', 'sg.id = sgcs.subject_group_id')
            ->join('subject_group_subjects sgs', 'sgs.subject_group_id = sg.id')
            ->join('subjects s', 's.id = sgs.subject_id')
            ->where('cs.class_id', (int) $class_id)->where('cs.section_id', (int) $section_id)
            ->where('sg.session_id', $session)
            ->order_by('sg.name')->order_by('s.name')
            ->get()->result_array();
        $assigned = array();
        foreach ($db->select('subject_group_subject_id, staff_id')->where(array('class_id' => (int) $class_id, 'section_id' => (int) $section_id, 'session_id' => $session))
            ->where("(day IS NULL OR day = '')", null, false)->get('subject_timetable')->result_array() as $r) {
            $assigned[(int) $r['subject_group_subject_id']] = (int) $r['staff_id'];
        }
        foreach ($rows as &$r) {
            $r['staff_id'] = isset($assigned[(int) $r['sgs_id']]) ? $assigned[(int) $r['sgs_id']] : 0;
        }
        return $rows;
    }

    /** Saves the teachers: $teachers = subject_group_subject_id => staff_id (0 or '' = no teacher). Returns how many changed. */
    public function save($class_id, $section_id, array $teachers)
    {
        $db = $this->CI->db;
        $session = $this->session();
        $valid = array();
        foreach ($this->subjects($class_id, $section_id) as $s) {
            $valid[(int) $s['sgs_id']] = $s;
        }
        $changed = 0;
        foreach ($teachers as $sgs_id => $staff_id) {
            $sgs_id = (int) $sgs_id;
            $staff_id = (int) $staff_id;
            if (!isset($valid[$sgs_id]) || $valid[$sgs_id]['staff_id'] === $staff_id) {
                continue;
            }
            $where = array('class_id' => (int) $class_id, 'section_id' => (int) $section_id, 'session_id' => $session, 'subject_group_subject_id' => $sgs_id);
            $db->where($where)->where("(day IS NULL OR day = '')", null, false)->delete('subject_timetable');
            if ($staff_id) {
                $db->insert('subject_timetable', $where + array('subject_group_id' => (int) $valid[$sgs_id]['subject_group_id'], 'staff_id' => $staff_id, 'day' => ''));
            }
            $changed++;
        }
        return $changed;
    }
}
