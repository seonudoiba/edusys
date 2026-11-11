<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Defaults that suit Nigerian schools, in place of the Indian ones the original data shipped with.
 *
 *   apply($school_id, true)   a new school (School_provisioner): everything below.
 *   apply($school_id, false)  an existing school (migration 292): only what the school has not
 *                             changed itself, so its own choices and records are never overwritten.
 *
 * What it sets:
 *   - the session starts in September; the naira sign before the amount (₦1,000); transport fees
 *     per term (an existing school keeps monthly fees it already set up);
 *   - the student form without caste, RTE, IFSC, Samagra ID, height, weight and measurement date
 *     (an existing school keeps a field that any student already has a value in);
 *   - "State of origin" and "LGA" fields on the student form (custom fields);
 *   - Examinations grades on the WAEC scale (A1-F9) and Primary (A-F) in place of the sample scale;
 *     the sample marks divisions removed and their menu hidden;
 *   - role permissions: report cards for Admin and Teachers (marks, remarks, ratings, attendance,
 *     printing); Teachers can no longer delete students or edit disable reasons; behaviour settings
 *     and the Zoom/Google Meet settings for administrators only;
 *   - the sample website content of an Indian school removed (see Sample_content).
 */
class Nigeria_defaults
{
    /** sch_settings column => students column holding its value */
    const FIELDS_OFF = array(
        'cast' => 'cast',
        'rte' => 'rte',
        'ifsc_code' => 'ifsc_code',
        'local_identification_no' => 'samagra_id',
        'student_height' => 'height',
        'student_weight' => 'weight',
        'measurement_date' => 'measurement_date',
    );

    const CUSTOM_FIELDS = array('State of origin', 'LGA');

    /** exam_type => [name, from, upto] (Examinations module grades) */
    const GRADES = array(
        'school_grade_system' => array(
            array('A1', 75, 100), array('B2', 70, 74.99), array('B3', 65, 69.99), array('C4', 60, 64.99), array('C5', 55, 59.99),
            array('C6', 50, 54.99), array('D7', 45, 49.99), array('E8', 40, 44.99), array('F9', 0, 39.99),
        ),
        'basic_system' => array(
            array('A', 70, 100), array('B', 60, 69.99), array('C', 50, 59.99), array('D', 45, 49.99), array('E', 40, 44.99), array('F', 0, 39.99),
        ),
    );

    /** The sample grades of the default data, per exam type: [name, from, upto]. */
    const SAMPLE_GRADES = array(
        'school_grade_system' => array(array('B+', 0, 33), array('B', 33, 50), array('A+', 51, 75), array('A', 76, 100)),
        'basic_system' => array(array('B+', 0, 33), array('B', 33, 50), array('A', 51, 75), array('A+', 76, 100)),
    );

    const SAMPLE_DIVISIONS = array(array('First', 100, 60), array('Second', 345, 65), array('Third', 578, 89));

    /**
     * Role => category => [view, add, edit, delete]. 'from' is the default of the original data: an
     * existing school's row changes only while it still has exactly those values.
     */
    const PERMISSIONS = array(
        // Report cards: administrators manage everything; teachers enter marks, remarks, ratings and attendance.
        'Admin' => array(
            'cbse_exam' => array(1, 1, 1, 1), 'cbse_exam_schedule' => array(1, 0, 0, 0), 'cbse_exam_assign_view_student' => array(1, 0, 1, 0),
            'cbse_exam_subjects' => array(1, 0, 1, 0), 'cbse_exam_marks' => array(1, 0, 1, 0), 'cbse_exam_attendance' => array(1, 0, 1, 0),
            'cbse_exam_teacher_remark' => array(1, 0, 1, 0), 'cbse_exam_print_marksheet' => array(1, 0, 0, 0), 'cbse_exam_grade' => array(1, 1, 1, 1),
            'cbse_exam_assign_observation' => array(1, 1, 1, 1), 'cbse_exam_observation' => array(1, 1, 1, 1),
            'cbse_exam_observation_parameter' => array(1, 1, 1, 1), 'cbse_exam_assessment' => array(1, 1, 1, 1), 'cbse_exam_term' => array(1, 1, 1, 1),
            'cbse_exam_template' => array(1, 1, 1, 1), 'cbse_exam_link_exam' => array(1, 0, 0, 0), 'subject_marks_report' => array(1, 0, 0, 0),
            'template_marks_report' => array(1, 0, 0, 0), 'cbse_exam_setting' => array(1, 0, 0, 0), 'cbse_exam_generate_rank' => array(1, 0, 0, 0),
        ),
        'Teacher' => array(
            'cbse_exam' => array(1, 0, 0, 0), 'cbse_exam_schedule' => array(1, 0, 0, 0), 'cbse_exam_assign_view_student' => array(1, 0, 0, 0),
            'cbse_exam_subjects' => array(1, 0, 0, 0), 'cbse_exam_marks' => array(1, 0, 1, 0), 'cbse_exam_attendance' => array(1, 0, 1, 0),
            'cbse_exam_teacher_remark' => array(1, 0, 1, 0), 'cbse_exam_print_marksheet' => array(1, 0, 0, 0),
            'cbse_exam_assign_observation' => array(1, 1, 1, 0), 'subject_marks_report' => array(1, 0, 0, 0),
            'template_marks_report' => array(1, 0, 0, 0),
        ),
    );

    /** Narrowed defaults: role => category => [from, to] */
    const NARROWED = array(
        'Teacher' => array(
            'student' => array(array(1, 1, 1, 1), array(1, 1, 1, 0)),           // no deleting or bulk-deleting students
            'disable_reason' => array(array(1, 1, 1, 1), array(1, 0, 0, 0)),
            'behaviour_records_setting' => array(array(1, 0, 1, 0), array(0, 0, 0, 0)),
            'gmeet_setting' => array(array(1, 0, 1, 0), array(0, 0, 0, 0)),
            'setting' => array(array(1, 0, 1, 0), array(0, 0, 0, 0)),             // Zoom settings
        ),
        'Accountant' => array(
            'behaviour_records_setting' => array(array(1, 0, 1, 0), array(0, 0, 0, 0)),
            'gmeet_setting' => array(array(1, 0, 1, 0), array(0, 0, 0, 0)),
        ),
        'Librarian' => array(
            'behaviour_records_setting' => array(array(1, 0, 0, 0), array(0, 0, 0, 0)),
            'gmeet_setting' => array(array(1, 0, 1, 0), array(0, 0, 0, 0)),
            'setting' => array(array(1, 0, 0, 0), array(0, 0, 0, 0)),
        ),
        'Receptionist' => array(
            'gmeet_setting' => array(array(1, 0, 1, 0), array(0, 0, 0, 0)),
            'setting' => array(array(1, 0, 1, 0), array(0, 0, 0, 0)),
        ),
    );

    private $CI;
    private $db;
    private $sid;
    private $new;
    /** @var string[] what changed, for the migration's output */
    public $log = array();

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    /** Runs on the given school's rows only (call inside without_tenant). */
    public function apply($school_id, $new_school)
    {
        $this->db = $this->CI->db;
        $this->sid = (int) $school_id;
        $this->new = (bool) $new_school;
        $this->log = array();
        $this->settings();
        $this->custom_fields();
        $this->grades();
        $this->divisions();
        $this->permissions();
        $this->CI->load->library('Sample_content');
        $removed = $this->CI->sample_content->clean($this->sid, !$this->new);
        if ($removed) {
            $this->log[] = $removed;
        }
        return $this->log;
    }

    private function settings()
    {
        $row = $this->db->where('school_id', $this->sid)->order_by('id')->limit(1)->get('sch_settings')->row_array();
        if (!$row) {
            return;
        }
        $set = array();
        if ($this->new) {
            $set['start_month'] = '9';
        }
        // Transport fees per term, unless the school already has (monthly) transport fees set up.
        if (array_key_exists('transport_billing', $row) && $row['transport_billing'] !== 'term'
            && $this->db->where('school_id', $this->sid)->count_all_results('transport_feemaster') === 0) {
            $set['transport_billing'] = 'term';
        }
        $naira = $this->db->select('short_name')->where('id', (int) $row['currency'])->get('currencies')->row();
        if ($row['currency_place'] === 'after_number' && ($this->new || ($naira && $naira->short_name === 'NGN'))) {
            $set['currency_place'] = 'before_number';
        }
        foreach (self::FIELDS_OFF as $setting => $column) {
            if (!array_key_exists($setting, $row) || (int) $row[$setting] !== 1) {
                continue;
            }
            if (!$this->new && $this->db->field_exists($column, 'students')) {
                $used = $this->db->where('school_id', $this->sid)->where("`$column` IS NOT NULL AND `$column` NOT IN ('', '0', '0000-00-00')", null, false)->count_all_results('students');
                if ($used > 0) {
                    continue; // the school uses this field
                }
            }
            $set[$setting] = 0;
        }
        if ($set) {
            $this->db->where('id', (int) $row['id'])->update('sch_settings', $set);
            $this->log[] = 'settings: ' . implode(', ', array_keys($set));
        }
    }

    private function custom_fields()
    {
        $added = array();
        foreach (self::CUSTOM_FIELDS as $i => $name) {
            $exists = $this->db->where(array('school_id' => $this->sid, 'belong_to' => 'students'))->where('LOWER(name)', strtolower($name))->count_all_results('custom_fields');
            if ($exists) {
                continue;
            }
            $this->db->insert('custom_fields', array(
                'school_id' => $this->sid, 'name' => $name, 'belong_to' => 'students', 'type' => 'input', 'bs_column' => 6,
                'validation' => 0, 'field_values' => '', 'show_table' => null, 'visible_on_table' => 0, 'weight' => 10 + $i, 'is_active' => 1,
            ));
            $added[] = $name;
        }
        if ($added) {
            $this->log[] = 'student fields: ' . implode(', ', $added);
        }
    }

    private function grades()
    {
        foreach (self::GRADES as $type => $grades) {
            $rows = $this->db->select('name, mark_from, mark_upto')->where(array('school_id' => $this->sid, 'exam_type' => $type))->order_by('id')->get('grades')->result_array();
            if (!$this->new && !$this->same($rows, self::SAMPLE_GRADES[$type], array('name', 'mark_from', 'mark_upto'))) {
                continue; // the school set its own grades
            }
            $this->db->where(array('school_id' => $this->sid, 'exam_type' => $type))->delete('grades');
            foreach ($grades as $g) {
                $this->db->insert('grades', array('school_id' => $this->sid, 'exam_type' => $type, 'name' => $g[0], 'point' => 0,
                    'mark_from' => $g[1], 'mark_upto' => $g[2], 'description' => '', 'is_active' => 'no'));
            }
            $this->log[] = 'grades: ' . $type;
        }
    }

    private function divisions()
    {
        $rows = $this->db->select('name, percentage_from, percentage_to')->where('school_id', $this->sid)->order_by('id')->get('mark_divisions')->result_array();
        if (($rows && !$this->same($rows, self::SAMPLE_DIVISIONS, array('name', 'percentage_from', 'percentage_to'))) || (!$rows && !$this->new)) {
            return; // the school set its own divisions, or already has none
        }
        if ($rows) {
            $this->db->where('school_id', $this->sid)->delete('mark_divisions');
        }
        $this->db->where(array('school_id' => $this->sid, 'url' => 'admin/marksdivision'))->update('sidebar_sub_menus', array('is_active' => 0));
        $this->log[] = 'marks divisions: sample removed, menu hidden';
    }

    private function permissions()
    {
        $roles = array();
        foreach ($this->db->select('id, name')->where('school_id IS NULL', null, false)->or_where('school_id', $this->sid)->get('roles')->result_array() as $r) {
            $roles[$r['name']] = (int) $r['id'];
        }
        $cats = array();
        foreach ($this->db->select('id, short_code')->where('school_id', $this->sid)->get('permission_category')->result_array() as $c) {
            $cats[$c['short_code']] = (int) $c['id'];
        }
        $changed = 0;
        foreach (self::PERMISSIONS as $role => $grants) {
            if (!isset($roles[$role])) {
                continue;
            }
            // An existing school that already gave this role any report-card permission set it up itself.
            if (!$this->new) {
                $ids = array_values(array_intersect_key($cats, $grants));
                if ($ids && $this->db->where(array('school_id' => $this->sid, 'role_id' => $roles[$role]))->where_in('perm_cat_id', $ids)->count_all_results('roles_permissions') > 0) {
                    continue;
                }
            }
            foreach ($grants as $code => $flags) {
                if (isset($cats[$code])) {
                    $changed += $this->set_permission($roles[$role], $cats[$code], $flags, null);
                }
            }
        }
        foreach (self::NARROWED as $role => $list) {
            if (!isset($roles[$role])) {
                continue;
            }
            foreach ($list as $code => $from_to) {
                if (isset($cats[$code])) {
                    $changed += $this->set_permission($roles[$role], $cats[$code], $from_to[1], $from_to[0]);
                }
            }
        }
        if ($changed) {
            $this->log[] = 'role permissions: ' . $changed . ' changed';
        }
    }

    /** Sets one role's flags on a category; with $from, only while the row still has those values. */
    private function set_permission($role_id, $cat_id, array $flags, $from)
    {
        $values = array('can_view' => $flags[0], 'can_add' => $flags[1], 'can_edit' => $flags[2], 'can_delete' => $flags[3]);
        $row = $this->db->where(array('school_id' => $this->sid, 'role_id' => $role_id, 'perm_cat_id' => $cat_id))->get('roles_permissions')->row_array();
        if ($from !== null) {
            if (!$row || array((int) $row['can_view'], (int) $row['can_add'], (int) $row['can_edit'], (int) $row['can_delete']) !== $from) {
                return 0;
            }
        }
        if ($row) {
            if (array((int) $row['can_view'], (int) $row['can_add'], (int) $row['can_edit'], (int) $row['can_delete']) === $flags) {
                return 0;
            }
            if ($flags === array(0, 0, 0, 0)) {
                $this->db->where('id', (int) $row['id'])->delete('roles_permissions');
            } else {
                $this->db->where('id', (int) $row['id'])->update('roles_permissions', $values);
            }
            return 1;
        }
        if ($flags === array(0, 0, 0, 0)) {
            return 0;
        }
        $this->db->insert('roles_permissions', array('school_id' => $this->sid, 'role_id' => $role_id, 'perm_cat_id' => $cat_id) + $values);
        return 1;
    }

    /** Rows equal to [values...] in order, comparing numbers as numbers. */
    private function same(array $rows, array $expected, array $cols)
    {
        if (count($rows) !== count($expected)) {
            return false;
        }
        foreach ($rows as $i => $r) {
            foreach ($cols as $j => $c) {
                $a = $r[$c];
                $b = $expected[$i][$j];
                if (is_numeric($b) ? abs((float) $a - (float) $b) > 0.001 : (string) $a !== (string) $b) {
                    return false;
                }
            }
        }
        return true;
    }
}
