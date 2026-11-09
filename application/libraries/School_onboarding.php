<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Setting up a new school (admin/onboarding): a checklist of the first things a school does
 * (details, classes and arms, fees, how parents pay, online admission), and the Nigerian class
 * structure the classes step creates.
 *
 * Progress is kept in sch_settings.onboarding (JSON): {"done": {step: 1}, "skipped": {step: 1}, "hidden": 0}.
 * Payments and admission are also done when the school's settings show them set up.
 */
class School_onboarding
{
    const STEPS = array('details', 'classes', 'fees', 'payments', 'admission');

    /** Sections for senior secondary when the school splits SS classes by department. */
    const SS_DEPARTMENTS = array('Science', 'Art', 'Commercial');

    /** Sample class names from the default data: removed by the classes step when nothing uses them. */
    const SAMPLE_CLASSES = array('Class 1', 'Class 2', 'Class 3', 'Class 4');

    private $CI;
    private $row;
    /** @var int|null school the class methods work on; null: the current tenant */
    private $school_id;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** Works on this school (School_provisioner runs without a tenant). */
    public function for_school($school_id)
    {
        $this->school_id = (int) $school_id;
        return $this;
    }

    private function sid()
    {
        return $this->school_id ?: (int) Tenant::id();
    }

    /**
     * A new school's classes (School_provisioner): the sample classes from the default data
     * (with their sample timetable, subject links and questions) are replaced by the usual
     * Nigerian classes, arm A, SS by department. Sections nothing uses are removed too.
     * The classes step stays on the checklist so the school can check its arms.
     */
    public function provision_defaults($school_id)
    {
        $this->for_school($school_id);
        $classes = array();
        $ss = array();
        foreach (self::nigerian_classes() as $stage => $spec) {
            foreach ($spec[1] as $c) {
                if ($spec[2] === null || in_array($c[0], $spec[2], true)) {
                    $classes[] = $c[0];
                    if ($stage === 'ss') {
                        $ss[] = $c[0];
                    }
                }
            }
        }
        $this->create_classes($classes, array('A'), true, $ss, true, false);
        $db = $this->CI->db;
        $sid = $this->sid();
        $used = array_column($db->select('DISTINCT section_id', false)->where('school_id', $sid)->get('class_sections')->result_array(), 'section_id');
        $db->where('school_id', $sid);
        if ($used) {
            $db->where_not_in('id', array_map('intval', $used));
        }
        $db->delete('sections');
    }

    private function row()
    {
        if ($this->row === null) {
            $this->row = $this->CI->db->select('id, onboarding, online_admission, name, phone, address')
                ->order_by('id')->limit(1)->get('sch_settings')->row_array() ?: array();
        }
        return $this->row;
    }

    public function state()
    {
        $row = $this->row();
        $s = json_decode(isset($row['onboarding']) ? (string) $row['onboarding'] : '', true);
        $s = is_array($s) ? $s : array();
        return array(
            'done' => isset($s['done']) && is_array($s['done']) ? $s['done'] : array(),
            'skipped' => isset($s['skipped']) && is_array($s['skipped']) ? $s['skipped'] : array(),
            'hidden' => !empty($s['hidden']) ? 1 : 0,
        );
    }

    private function save_state(array $s)
    {
        $row = $this->row();
        if (empty($row['id'])) {
            return;
        }
        $this->CI->db->where('id', $row['id'])->update('sch_settings', array('onboarding' => json_encode($s)));
        $this->row = null;
    }

    /** $how: done, skipped or todo (undo). */
    public function mark($step, $how)
    {
        if (!in_array($step, self::STEPS, true)) {
            return;
        }
        $s = $this->state();
        unset($s['done'][$step], $s['skipped'][$step]);
        if ($how === 'done' || $how === 'skipped') {
            $s[$how][$step] = 1;
        }
        $this->save_state($s);
    }

    public function set_hidden($hidden)
    {
        $s = $this->state();
        $s['hidden'] = $hidden ? 1 : 0;
        $this->save_state($s);
    }

    /**
     * The checklist: each step with title, text, url, privilege and status (done, skipped, todo).
     * $auto says the school's settings show it set up (it can't be undone from the checklist).
     */
    public function steps()
    {
        $s = $this->state();
        $auto = array(
            'payments' => $this->payments_set_up(),
            'admission' => !empty($this->row()['online_admission']),
        );
        $steps = array(
            'details' => array(
                'title' => 'Check your school details',
                'text' => 'Name, address, phone, logo, the current session and the month your session starts. These appear on receipts, report cards and your website.',
                'url' => site_url('schsettings'),
                'button' => 'Open school settings',
                'privilege' => array('general_setting', 'can_view'),
            ),
            'classes' => array(
                'title' => 'Check your classes and arms',
                'text' => 'Your account starts with Nursery 1-2, Primary 1-6, JSS 1-3 (arm A) and SS 1-3 (Science, Art, Commercial). Add the arms and classes you run; delete any you don\'t under Academics > Class.',
                'url' => site_url('admin/onboarding/classes'),
                'button' => 'Set up classes',
                'privilege' => array('class', 'can_add'),
            ),
            'fees' => array(
                'title' => 'Set your school fees',
                'text' => 'Add your fee types (tuition, PTA levy, uniform ...), group them, then set the amount for each class in Fees Master.',
                'url' => site_url('admin/feemaster'),
                'button' => 'Open Fees Master',
                'privilege' => array('fees_master', 'can_view'),
            ),
            'payments' => array(
                'title' => 'Choose how parents pay',
                'text' => 'Connect an online gateway (Paystack, Flutterwave, Monnify, Remita ...) and/or give your bank details for transfers.',
                'url' => site_url('admin/paymentsettings'),
                'button' => 'Open payment methods',
                'privilege' => array('payment_methods', 'can_view'),
            ),
            'admission' => array(
                'title' => 'Open online admission',
                'text' => 'Set the dates, the classes taking applicants, the form fields and the form fee. Parents apply from your website.',
                'url' => site_url('admin/onlineadmission/admissionsetting'),
                'button' => 'Open admission settings',
                'privilege' => array('online_admission', 'can_view'),
            ),
        );
        foreach ($steps as $key => &$step) {
            $step['key'] = $key;
            $step['auto'] = !empty($auto[$key]);
            if ($step['auto'] || isset($s['done'][$key])) {
                $step['status'] = 'done';
            } elseif (isset($s['skipped'][$key])) {
                $step['status'] = 'skipped';
            } else {
                $step['status'] = 'todo';
            }
        }
        unset($step);
        return $steps;
    }

    /** array(finished, total): finished counts done and skipped steps. */
    public function progress(array $steps = null)
    {
        $steps = $steps === null ? $this->steps() : $steps;
        $finished = 0;
        foreach ($steps as $step) {
            $finished += $step['status'] !== 'todo' ? 1 : 0;
        }
        return array($finished, count($steps));
    }

    /** An online gateway is active, or bank transfer is on for school fees with full bank details. */
    private function payments_set_up()
    {
        $active = $this->CI->db->select('id')->where('is_active', 'yes')->limit(1)->get('payment_settings')->row();
        if ($active) {
            return true;
        }
        $this->CI->load->library('School_bank_transfer');
        $d = $this->CI->school_bank_transfer->details();
        return $d['school_fees'] && $this->CI->school_bank_transfer->complete();
    }

    // ---- classes

    /**
     * The usual Nigerian classes by stage: stage => array(label, classes, checked by default).
     * Each class is array(name, name under the UBE "Basic" naming).
     */
    public static function nigerian_classes()
    {
        $range = function ($prefix, $from, $to, $basic_from = null) {
            $out = array();
            for ($i = $from; $i <= $to; $i++) {
                $out[] = array($prefix . ' ' . $i, $basic_from === null ? $prefix . ' ' . $i : 'Basic ' . ($basic_from + $i - $from));
            }
            return $out;
        };
        return array(
            'early' => array('Early years', array(
                array('Creche', 'Creche'),
                array('Pre-Nursery', 'Pre-Nursery'),
                array('Nursery 1', 'Nursery 1'),
                array('Nursery 2', 'Nursery 2'),
                array('Nursery 3', 'Nursery 3'),
            ), array('Nursery 1', 'Nursery 2')),
            'primary' => array('Primary', $range('Primary', 1, 6, 1), null),
            'jss' => array('Junior secondary', $range('JSS', 1, 3, 7), null),
            'ss' => array('Senior secondary', $range('SS', 1, 3), null),
        );
    }

    /** Sample classes (Class 1-4 from the default data) that no student, application or record uses. */
    public function removable_samples()
    {
        $db = $this->CI->db;
        $sid = $this->sid();
        $rows = $db->select('id, class')->where('school_id', $sid)->where_in('class', self::SAMPLE_CLASSES)->get('classes')->result_array();
        $out = array();
        foreach ($rows as $r) {
            $id = (int) $r['id'];
            $used = $db->where(array('school_id' => $sid, 'class_id' => $id))->count_all_results('student_session')
                + $db->where(array('school_id' => $sid, 'class_id' => $id))->count_all_results('feemasters')
                + $db->select('online_admissions.id')->join('class_sections', 'class_sections.id = online_admissions.class_section_id')
                    ->where('online_admissions.school_id', $sid)->where('class_sections.class_id', $id)->count_all_results('online_admissions');
            if (!$used) {
                $out[$id] = $r['class'];
            }
        }
        return $out;
    }

    /**
     * Creates the classes, each with the arms (sections) given. A class that already exists only
     * gets the arms it is missing. SS classes get the departments instead when $ss_departments.
     *
     * @param string[] $classes class names
     * @param string[] $arms section names (empty: one section called General)
     * @param string[] $ss_classes the names among $classes that are senior secondary
     * @param bool $mark tick the classes step on the checklist
     * @return array('created' => n, 'updated' => n, 'removed' => n)
     */
    public function create_classes(array $classes, array $arms, $ss_departments, array $ss_classes, $remove_samples, $mark = true)
    {
        $db = $this->CI->db;
        $sid = $this->sid();
        $clean = function (array $names, $max) {
            $out = array();
            foreach ($names as $n) {
                $n = trim(preg_replace('/\s+/', ' ', strip_tags((string) $n)));
                if ($n !== '' && mb_strlen($n) <= $max && !in_array(mb_strtolower($n), array_map('mb_strtolower', $out), true)) {
                    $out[] = $n;
                }
            }
            return $out;
        };
        $classes = $clean($classes, 60);
        $arms = $clean($arms, 60);
        $ss_lower = array_map('mb_strtolower', $clean($ss_classes, 60));
        $result = array('created' => 0, 'updated' => 0, 'removed' => 0);

        $db->trans_start();
        if ($remove_samples) {
            $result['removed'] = $this->remove_samples();
        }
        $section_ids = array();
        $section_id = function ($name) use ($db, $sid, &$section_ids) {
            $key = mb_strtolower($name);
            if (!isset($section_ids[$key])) {
                $row = $db->select('id')->where(array('school_id' => $sid, 'section' => $name))->get('sections')->row();
                if (!$row) {
                    $db->insert('sections', array('school_id' => $sid, 'section' => $name, 'is_active' => 'no'));
                    $section_ids[$key] = (int) $db->insert_id();
                } else {
                    $section_ids[$key] = (int) $row->id;
                }
            }
            return $section_ids[$key];
        };
        foreach ($classes as $name) {
            $names = ($ss_departments && in_array(mb_strtolower($name), $ss_lower, true)) ? self::SS_DEPARTMENTS : $arms;
            $names = $names ? $names : array('General');
            $wanted = array_map($section_id, $names);
            $class = $db->select('id')->where(array('school_id' => $sid, 'class' => $name))->get('classes')->row();
            if ($class) {
                $class_id = (int) $class->id;
                $have = array_map('intval', array_column(
                    $db->select('section_id')->where(array('school_id' => $sid, 'class_id' => $class_id))->get('class_sections')->result_array(), 'section_id'));
                $missing = array_values(array_diff($wanted, $have));
                if (!$missing) {
                    continue;
                }
                $result['updated']++;
            } else {
                $db->insert('classes', array('school_id' => $sid, 'class' => $name, 'is_active' => 'no'));
                $class_id = (int) $db->insert_id();
                $missing = $wanted;
                $result['created']++;
            }
            $rows = array();
            foreach ($missing as $section) {
                $rows[] = array('school_id' => $sid, 'class_id' => $class_id, 'section_id' => $section, 'is_active' => 'no');
            }
            $db->insert_batch('class_sections', $rows);
        }
        $db->trans_complete();
        if (!$db->trans_status()) {
            throw new RuntimeException('The classes could not be saved. Nothing was changed.');
        }
        if ($mark) {
            $this->mark('classes', 'done');
        }
        return $result;
    }

    /** Deletes the unused sample classes (their sections, timetable, sample courses' links go too). */
    private function remove_samples()
    {
        $db = $this->CI->db;
        $sid = $this->sid();
        $ids = array_keys($this->removable_samples());
        if (!$ids) {
            return 0;
        }
        $cs = array_map('intval', array_column(
            $db->select('id')->where('school_id', $sid)->where_in('class_id', $ids)->get('class_sections')->result_array(), 'id'));
        if ($cs) {
            // These two don't cascade from class_sections.
            $db->where('school_id', $sid)->where_in('class_section_id', $cs)->delete('online_course_class_sections');
            $db->where('school_id', $sid)->where_in('class_section_id', $cs)->delete('share_content_for');
        }
        $db->where('school_id', $sid)->where_in('id', $ids)->delete('classes'); // class_sections and the rest cascade
        return count($ids);
    }
}
