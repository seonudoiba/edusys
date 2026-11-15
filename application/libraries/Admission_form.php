<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The school's online admission form: which built-in fields it shows, which are required, their
 * labels, help text and order, when admission is open and which classes take applications.
 *
 * Showing or hiding a field is still online_admission_fields.status (the switches on the settings
 * page). Everything else is JSON in sch_settings.online_admission_form:
 *   fields  => {key: {required: 0|1, label: '', help: ''}}
 *   order   => {section: [key, ...]}
 *   classes => {class_id: {open: 0|1, places: int|null}}  (a class not listed takes applications)
 *   counted_from => 'Y-m-d H:i:s'  (places count applications since then, or since the opening date)
 *   questions_title, closed_message
 * The opening and closing dates are their own columns (online_admission_opens/_closes).
 */
class Admission_form
{
    /** section => language key of its heading */
    const SECTIONS = array(
        'student'   => 'basic_details',
        'parents'   => 'parent_detail',
        'guardian'  => 'guardian_details',
        'address'   => 'student_address_details',
        'other'     => 'miscellaneous_details',
        'documents' => 'upload_documents',
    );

    private $CI;
    private $config;
    private $row;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model(array('onlinestudent_model', 'class_model'));
    }

    /**
     * Built-in fields, in their default order. key is the online_admission_fields name (and the
     * settings switch); post is the form field; lang is the default label; locked fields are always
     * on the form and always required.
     */
    public static function fields()
    {
        $f = function ($section, $post, $lang, $type = 'text', $extra = array()) {
            return array_merge(array('section' => $section, 'post' => $post, 'lang' => $lang, 'type' => $type, 'locked' => false, 'required' => false), $extra);
        };
        $locked = array('locked' => true, 'required' => true);
        return array(
            'class'                      => $f('student', 'class_id', 'class', 'class', $locked),
            'section'                    => $f('student', 'section_id', 'section', 'section', array('locked' => true)),
            'firstname'                  => $f('student', 'firstname', 'first_name', 'text', $locked),
            'middlename'                 => $f('student', 'middlename', 'middle_name'),
            'lastname'                   => $f('student', 'lastname', 'last_name'),
            'gender'                     => $f('student', 'gender', 'gender', 'gender', $locked),
            'dob'                        => $f('student', 'dob', 'date_of_birth', 'date', $locked),
            'mobile_no'                  => $f('student', 'mobileno', 'mobile_number'),
            'student_email'              => $f('student', 'email', 'email', 'email', array('required' => true)),
            'category'                   => $f('student', 'category_id', 'category', 'category'),
            'religion'                   => $f('student', 'religion', 'religion'),
            'is_student_house'           => $f('student', 'house', 'house', 'house'),
            'is_blood_group'             => $f('student', 'blood_group', 'blood_group', 'blood_group'),
            'student_height'             => $f('student', 'height', 'height'),
            'student_weight'             => $f('student', 'weight', 'weight'),
            'measurement_date'           => $f('student', 'measure_date', 'measurement_date', 'date'),
            'student_photo'              => $f('student', 'file', 'student_photo', 'file'),
            'father_name'                => $f('parents', 'father_name', 'father_name'),
            'father_phone'               => $f('parents', 'father_phone', 'father_phone'),
            'father_occupation'          => $f('parents', 'father_occupation', 'father_occupation'),
            'father_pic'                 => $f('parents', 'father_pic', 'father_photo', 'file'),
            'mother_name'                => $f('parents', 'mother_name', 'mother_name'),
            'mother_phone'               => $f('parents', 'mother_phone', 'mother_phone'),
            'mother_occupation'          => $f('parents', 'mother_occupation', 'mother_occupation'),
            'mother_pic'                 => $f('parents', 'mother_pic', 'mother_photo', 'file'),
            'if_guardian_is'             => $f('guardian', 'guardian_is', 'if_guardian_is', 'guardian_is', array('required' => true)),
            'guardian_name'              => $f('guardian', 'guardian_name', 'guardian_name', 'text', array('required' => true)),
            'guardian_relation'          => $f('guardian', 'guardian_relation', 'guardian_relation', 'text', array('required' => true)),
            'guardian_phone'             => $f('guardian', 'guardian_phone', 'guardian_phone'),
            'guardian_email'             => $f('guardian', 'guardian_email', 'guardian_email', 'email'),
            'guardian_occupation'        => $f('guardian', 'guardian_occupation', 'guardian_occupation'),
            'guardian_address'           => $f('guardian', 'guardian_address', 'guardian_address', 'textarea'),
            'guardian_photo'             => $f('guardian', 'guardian_pic', 'guardian_photo', 'file'),
            'current_address'            => $f('address', 'current_address', 'current_address', 'textarea'),
            'permanent_address'          => $f('address', 'permanent_address', 'permanent_address', 'textarea'),
            'bank_account_no'            => $f('other', 'bank_account_no', 'bank_account_number'),
            'bank_name'                  => $f('other', 'bank_name', 'bank_name'),
            'ifsc_code'                  => $f('other', 'ifsc_code', 'ifsc_code'),
            'national_identification_no' => $f('other', 'adhar_no', 'national_identification_number'),
            'local_identification_no'    => $f('other', 'samagra_id', 'local_identification_number'),
            'rte'                        => $f('other', 'rte', 'rte', 'rte'),
            'previous_school_details'    => $f('other', 'previous_school', 'previous_school_details', 'textarea'),
            'student_note'               => $f('other', 'note', 'note', 'textarea'),
            'upload_documents'           => $f('documents', 'document', 'documents', 'file'),
        );
    }

    /** sch_settings columns this needs (getSetting() doesn't select them). */
    private function row()
    {
        if ($this->row === null) {
            $this->row = $this->CI->db->select('id, online_admission, online_admission_form, online_admission_opens, online_admission_closes')
                ->order_by('id')->limit(1)->get('sch_settings')->row_array() ?: array();
        }
        return $this->row;
    }

    public function config()
    {
        if ($this->config === null) {
            $row = $this->row();
            $c = json_decode(isset($row['online_admission_form']) ? (string) $row['online_admission_form'] : '', true);
            $this->config = self::clean(is_array($c) ? $c : array());
        }
        return $this->config;
    }

    /** Keep only what the form understands; anything else is dropped. */
    public static function clean(array $c)
    {
        $fields = self::fields();
        $out = array('fields' => array(), 'order' => array(), 'classes' => array(), 'counted_from' => '', 'questions_title' => '', 'closed_message' => '');
        foreach ((array) (isset($c['fields']) ? $c['fields'] : array()) as $key => $v) {
            if (!isset($fields[$key]) || !is_array($v)) {
                continue;
            }
            $out['fields'][$key] = array(
                'required' => !empty($v['required']) ? 1 : 0,
                'label'    => self::text(isset($v['label']) ? $v['label'] : '', 80),
                'help'     => self::text(isset($v['help']) ? $v['help'] : '', 200),
            );
        }
        foreach (self::SECTIONS as $section => $lang) {
            $keys = isset($c['order'][$section]) && is_array($c['order'][$section]) ? $c['order'][$section] : array();
            $keys = array_values(array_unique(array_filter($keys, function ($k) use ($fields, $section) {
                return is_string($k) && isset($fields[$k]) && $fields[$k]['section'] === $section;
            })));
            if ($keys) {
                $out['order'][$section] = $keys;
            }
        }
        foreach ((array) (isset($c['classes']) ? $c['classes'] : array()) as $id => $v) {
            if ((int) $id > 0 && is_array($v)) {
                $places = isset($v['places']) && $v['places'] !== '' && $v['places'] !== null ? max(0, (int) $v['places']) : null;
                $out['classes'][(int) $id] = array('open' => !empty($v['open']) ? 1 : 0, 'places' => $places);
            }
        }
        if (!empty($c['counted_from']) && strtotime($c['counted_from'])) {
            $out['counted_from'] = date('Y-m-d H:i:s', strtotime($c['counted_from']));
        }
        $out['questions_title'] = self::text(isset($c['questions_title']) ? $c['questions_title'] : '', 80);
        $out['closed_message'] = self::text(isset($c['closed_message']) ? $c['closed_message'] : '', 1000);
        return $out;
    }

    private static function text($s, $max)
    {
        $s = trim(strip_tags((string) $s));
        return function_exists('mb_substr') ? mb_substr($s, 0, $max) : substr($s, 0, $max);
    }

    public function save(array $config)
    {
        $row = $this->row();
        $this->config = self::clean($config);
        $this->CI->db->where('id', $row['id'])->update('sch_settings', array('online_admission_form' => json_encode($this->config)));
    }

    // ---- fields

    /** Is the field on the form? Locked fields always are; the rest follow the settings switches. */
    public function shown($key)
    {
        $fields = self::fields();
        if (!isset($fields[$key])) {
            return false;
        }
        if ($fields[$key]['locked']) {
            return true;
        }
        // Guardian details only make sense with the "guardian is" question.
        if ($fields[$key]['section'] === 'guardian' && $key !== 'if_guardian_is' && !$this->CI->customlib->getfieldstatus('if_guardian_is')) {
            return false;
        }
        return (bool) $this->CI->customlib->getfieldstatus($key);
    }

    public function required($key)
    {
        $fields = self::fields();
        if (!isset($fields[$key])) {
            return false;
        }
        if ($fields[$key]['locked']) {
            return $fields[$key]['required'];
        }
        $c = $this->config();
        return isset($c['fields'][$key]) ? (bool) $c['fields'][$key]['required'] : $fields[$key]['required'];
    }

    public function default_label($key)
    {
        $fields = self::fields();
        return isset($fields[$key]) ? (string) $this->CI->lang->line($fields[$key]['lang']) : $key;
    }

    public function label($key)
    {
        $c = $this->config();
        return !empty($c['fields'][$key]['label']) ? $c['fields'][$key]['label'] : $this->default_label($key);
    }

    public function help($key)
    {
        $c = $this->config();
        return !empty($c['fields'][$key]['help']) ? $c['fields'][$key]['help'] : '';
    }

    /** Keys of a section in the school's order; fields new since the order was saved go at the end. */
    public function ordered($section)
    {
        $c = $this->config();
        $all = array();
        foreach (self::fields() as $key => $f) {
            if ($f['section'] === $section) {
                $all[] = $key;
            }
        }
        $saved = isset($c['order'][$section]) ? array_values(array_intersect($c['order'][$section], $all)) : array();
        return array_merge($saved, array_values(array_diff($all, $saved)));
    }

    /** The section's fields that are on the form, in order. */
    public function section_fields($section)
    {
        return array_values(array_filter($this->ordered($section), array($this, 'shown')));
    }

    /** Required rules for the fields the school made required (locked ones are set by the controller). */
    public function apply_rules($form_validation, $files = true)
    {
        foreach (self::fields() as $key => $f) {
            if ($f['locked'] || !$this->shown($key) || !$this->required($key)) {
                continue;
            }
            if ($f['type'] === 'file') {
                if (!$files) {
                    continue;
                }
                // Kept by the controller's upload callback when a file is sent.
                if (empty($_FILES[$f['post']]['name'])) {
                    $form_validation->set_rules($f['post'], $this->label($key), 'required');
                }
                continue;
            }
            $form_validation->set_rules($f['post'], $this->label($key), 'trim|required');
        }
    }

    public function questions_title()
    {
        $c = $this->config();
        return $c['questions_title'] !== '' ? $c['questions_title'] : 'Additional information';
    }

    // ---- when admission is open

    public function opens()
    {
        $r = $this->row();
        return !empty($r['online_admission_opens']) ? $r['online_admission_opens'] : '';
    }

    public function closes()
    {
        $r = $this->row();
        return !empty($r['online_admission_closes']) ? $r['online_admission_closes'] : '';
    }

    /**
     * array('open' => bool, 'reason' => off|not_yet|ended|full|'', 'message' => text for visitors)
     */
    public function state()
    {
        $r = $this->row();
        $today = date('Y-m-d');
        $c = $this->config();
        $custom = $c['closed_message'];
        $date = function ($d) {
            return date('j F Y', strtotime($d));
        };
        if (empty($r['online_admission'])) {
            return array('open' => false, 'reason' => 'off', 'message' => $custom !== '' ? $custom : 'Online admission is closed at the moment. Please check back later or contact the school.');
        }
        if ($this->opens() !== '' && $today < $this->opens()) {
            return array('open' => false, 'reason' => 'not_yet', 'message' => ($custom !== '' ? $custom . ' ' : '') . 'Applications open on ' . $date($this->opens()) . '.');
        }
        if ($this->closes() !== '' && $today > $this->closes()) {
            return array('open' => false, 'reason' => 'ended', 'message' => $custom !== '' ? $custom : 'Applications closed on ' . $date($this->closes()) . '.');
        }
        if (!$this->open_classes()) {
            return array('open' => false, 'reason' => 'full', 'message' => $custom !== '' ? $custom : 'All places for this admission have been filled.');
        }
        return array('open' => true, 'reason' => '', 'message' => '');
    }

    // ---- classes and places

    /** class_id => array(open, places), filled in for every class. */
    public function class_settings(array $classes)
    {
        $c = $this->config();
        $out = array();
        foreach ($classes as $class) {
            $id = (int) $class['id'];
            $out[$id] = isset($c['classes'][$id]) ? $c['classes'][$id] : array('open' => 1, 'places' => null);
        }
        return $out;
    }

    /** Applications counted against the places: since the opening date, or since places were set. */
    public function counted_since()
    {
        $c = $this->config();
        $from = $this->opens() !== '' ? $this->opens() . ' 00:00:00' : '';
        if ($c['counted_from'] !== '' && $c['counted_from'] > $from) {
            $from = $c['counted_from'];
        }
        return $from;
    }

    /** class_id => applications received in the counting window. */
    public function applications_by_class()
    {
        $db = $this->CI->db;
        $db->select('class_sections.class_id, COUNT(*) AS n', false)
            ->from('online_admissions')
            ->join('class_sections', 'class_sections.id = online_admissions.class_section_id');
        if ($this->counted_since() !== '') {
            $db->where('online_admissions.created_at >=', $this->counted_since());
        }
        $out = array();
        foreach ($db->group_by('class_sections.class_id')->get()->result_array() as $r) {
            $out[(int) $r['class_id']] = (int) $r['n'];
        }
        return $out;
    }

    /** Classes taking applications now, each with 'places_left' (null = no limit). */
    public function open_classes()
    {
        $classes = $this->CI->class_model->getAll();
        $settings = $this->class_settings($classes);
        $taken = $this->applications_by_class();
        $out = array();
        foreach ($classes as $class) {
            $s = $settings[(int) $class['id']];
            if (!$s['open']) {
                continue;
            }
            $left = $s['places'] === null ? null : max(0, $s['places'] - (isset($taken[(int) $class['id']]) ? $taken[(int) $class['id']] : 0));
            if ($left === 0) {
                continue;
            }
            $class['places_left'] = $left;
            $out[] = $class;
        }
        return $out;
    }

    public function class_is_open($class_id)
    {
        foreach ($this->open_classes() as $class) {
            if ((int) $class['id'] === (int) $class_id) {
                return true;
            }
        }
        return false;
    }
}
