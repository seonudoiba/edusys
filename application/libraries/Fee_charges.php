<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * "Create fees" (admin/fees/create): charge the students of chosen classes for chosen fee items, in
 * one step. Under the hood it writes what Fees Type, Fees Group, Fees Master and "assign" always
 * wrote, so collection, receipts, reminders and reports work unchanged:
 *
 *   fee items        feetype (existing ones, or new ones by name)
 *   one charge per class   fee_groups "<name> · <class>" + fee_session_groups (current session)
 *   amounts, due date      fee_groups_feetype (per item, per class)
 *   who is charged         student_fees_master for every active student of the chosen class sections
 *
 * $plan = [
 *   'name'     => 'First Term fees',
 *   'term'     => 'first' | 'second' | 'third' | '' (Fees_overview::TERMS; '' for the whole session),
 *   'items'    => [['key' => 'ft12' | 'new0', 'feetype_id' => 12 | null, 'name' => 'Tuition', 'due_date' => 'Y-m-d' | null], ...],
 *   'classes'  => [class_id => [section_id, ...], ...],
 *   'amounts'  => [class_id => [item key => amount, ...], ...],
 * ]
 */
class Fee_charges
{
    private $CI;
    private $session_id;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model(array('setting_model', 'feesessiongroup_model', 'studentfeemaster_model'));
        $this->session_id = (int) $this->CI->setting_model->getCurrentSession();
    }

    /** Problems with the plan, as a list of sentences (empty when it can be created). */
    public function check(array $plan)
    {
        $errors = array();
        $name = trim((string) $plan['name']);
        if ($name === '') {
            $errors[] = 'Give the fees a name, for example "First Term fees".';
        } elseif (mb_strlen($name) > 150) {
            $errors[] = 'The name is too long (150 characters at most).';
        }
        if (!empty($plan['term']) && !isset(Fees_overview::TERMS[$plan['term']])) {
            $errors[] = 'Choose a term from the list.';
        }
        if (!$plan['items']) {
            $errors[] = 'Choose at least one fee item.';
        }
        if (!$plan['classes']) {
            $errors[] = 'Choose at least one class.';
        }
        foreach ($plan['classes'] as $class_id => $sections) {
            if (!$sections) {
                $errors[] = 'Choose at least one section of ' . $this->class_name($class_id) . '.';
            }
            $sum = 0;
            foreach ($plan['items'] as $it) {
                $a = isset($plan['amounts'][$class_id][$it['key']]) ? $plan['amounts'][$class_id][$it['key']] : null;
                if ($a === null || $a === '' || !is_numeric($a) || $a < 0) {
                    $errors[] = 'Enter an amount for ' . $it['name'] . ' in ' . $this->class_name($class_id) . ' (0 if it is not charged).';
                } else {
                    $sum += (float) $a;
                }
            }
            if ($sum <= 0 && $plan['items']) {
                $errors[] = 'Every fee for ' . $this->class_name($class_id) . ' is 0: remove the class or enter amounts.';
            }
            $group = $this->group_name($name, $class_id);
            if ($this->CI->db->where('name', $group)->count_all_results('fee_groups') > 0) {
                $errors[] = '"' . $group . '" already exists. Choose another name.';
            }
        }
        foreach ($plan['items'] as $it) {
            if ($it['feetype_id'] === null && $this->CI->db->where('type', $it['name'])->count_all_results('feetype') > 0) {
                $errors[] = 'A fee item called "' . $it['name'] . '" already exists: tick it in the list instead.';
            }
        }
        return array_values(array_unique($errors));
    }

    /** Who would be charged and how much: per class section, and in total. */
    public function preview(array $plan)
    {
        $rows = array();
        $total = 0.0;
        $students = 0;
        foreach ($plan['classes'] as $class_id => $sections) {
            $per_student = 0.0;
            foreach ($plan['items'] as $it) {
                $per_student += (float) (isset($plan['amounts'][$class_id][$it['key']]) ? $plan['amounts'][$class_id][$it['key']] : 0);
            }
            foreach ($sections as $section_id) {
                $n = count($this->students($class_id, $section_id));
                $rows[] = array('class' => $this->class_name($class_id), 'section' => $this->section_name($section_id), 'students' => $n,
                    'per_student' => $per_student, 'total' => $n * $per_student, 'group' => $this->group_name($plan['name'], $class_id));
                $total += $n * $per_student;
                $students += $n;
            }
        }
        return array('rows' => $rows, 'total' => $total, 'students' => $students);
    }

    /** Creates everything in one transaction. Returns the number of students charged. */
    public function create(array $plan)
    {
        $db = $this->CI->db;
        $db->trans_start();
        $feetype_ids = array();
        foreach ($plan['items'] as $it) {
            if ($it['feetype_id'] !== null) {
                $feetype_ids[$it['key']] = (int) $it['feetype_id'];
                continue;
            }
            $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]+/', '', $it['name']), 0, 10)) ?: 'FEE';
            $db->insert('feetype', array('type' => $it['name'], 'code' => $code, 'is_system' => 0, 'feecategory_id' => null, 'description' => '', 'is_active' => 'no'));
            $feetype_ids[$it['key']] = (int) $db->insert_id();
        }
        $charged = 0;
        foreach ($plan['classes'] as $class_id => $sections) {
            $db->insert('fee_groups', array('name' => $this->group_name($plan['name'], $class_id), 'is_system' => 0,
                'description' => 'Created with Create fees on ' . date('Y-m-d'), 'is_active' => 'no'));
            $group_id = (int) $db->insert_id();
            foreach ($plan['items'] as $it) {
                $amount = (float) $plan['amounts'][$class_id][$it['key']];
                if ($amount <= 0) {
                    continue; // not charged in this class
                }
                $this->CI->feesessiongroup_model->add(array(
                    'fee_groups_id' => $group_id,
                    'feetype_id' => $feetype_ids[$it['key']],
                    'amount' => $amount,
                    'due_date' => $it['due_date'] ?: null,
                    'session_id' => $this->session_id,
                    'fine_type' => 'none',
                    'fine_percentage' => 0,
                    'fine_amount' => 0,
                ));
            }
            $fsg = $db->select('id')->where(array('fee_groups_id' => $group_id, 'session_id' => $this->session_id))->get('fee_session_groups')->row();
            if (!$fsg) {
                continue;
            }
            if (!empty($plan['term']) && isset(Fees_overview::TERMS[$plan['term']])) {
                $db->where('id', (int) $fsg->id)->update('fee_session_groups', array('term' => $plan['term']));
            }
            foreach ($sections as $section_id) {
                foreach ($this->students($class_id, $section_id) as $ss_id) {
                    $this->CI->studentfeemaster_model->add(array('student_session_id' => $ss_id, 'fee_session_group_id' => (int) $fsg->id, 'is_system' => 0));
                    $charged++;
                }
            }
        }
        $db->trans_complete();
        if ($db->trans_status() === false) {
            throw new RuntimeException('The fees could not be saved. Nothing was changed.');
        }
        return $charged;
    }

    public function group_name($name, $class_id)
    {
        return trim((string) $name) . ' · ' . $this->class_name($class_id);
    }

    /** Active students of a class section in the current session (student_session ids). */
    private function students($class_id, $section_id)
    {
        return array_map('intval', array_column($this->CI->db->select('ss.id')->from('student_session ss')
            ->join('students st', 'st.id = ss.student_id')
            ->where(array('ss.session_id' => $this->session_id, 'ss.class_id' => (int) $class_id, 'ss.section_id' => (int) $section_id, 'st.is_active' => 'yes'))
            ->get()->result_array(), 'id'));
    }

    private function class_name($class_id)
    {
        static $names = array();
        if (!isset($names[$class_id])) {
            $row = $this->CI->db->select('class')->where('id', (int) $class_id)->get('classes')->row();
            $names[$class_id] = $row ? $row->class : ('Class ' . (int) $class_id);
        }
        return $names[$class_id];
    }

    private function section_name($section_id)
    {
        static $names = array();
        if (!isset($names[$section_id])) {
            $row = $this->CI->db->select('section')->where('id', (int) $section_id)->get('sections')->row();
            $names[$section_id] = $row ? $row->section : '';
        }
        return $names[$section_id];
    }
}
