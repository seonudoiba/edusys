<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The fees workspace, organised around what staff do:
 *
 *   admin/fees                 Overview: expected, collected, outstanding and overdue for the session,
 *                              one row per charge (a fee group in a class section)
 *   admin/fees/charge/F/C/S    one charge: every student's balance, with Collect, Fee bill, WhatsApp
 *   admin/fees/collections     Collections: find a student and collect, transfers to review, receipts
 *   admin/fees/takings         End-of-day takings: one day's payments by collector and payment mode (libraries/Fee_takings.php)
 *   admin/fees/create          Create fees: items, classes, amounts and due dates, preview, confirm
 *   admin/fees/repeat          Repeat a set of fees for the next term (opens Create fees filled in)
 *   admin/fees/edit|remove|remind/F/C/S   change a charge's amounts and due dates, take it off a class
 *                              section, or SMS every guardian who owes (libraries/Fee_sets.php, Fee_reminders.php)
 *   admin/fees/adjust          Discounts & balances: catch up late admissions, discounts, carry forward,
 *                              each previewed before it is applied (libraries/Fee_adjustments.php)
 *   admin/fees/followup        Reminders and reports
 *
 * The data is the same as the classic screens (Fees Type, Group, Master, Collect Fees), which stay.
 */
class Fees extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('Fees_overview', 'Fee_charges', 'Fee_adjustments', 'Fee_sets'));
        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    private function need($category, $action)
    {
        if (!$this->rbac->hasPrivilege($category, $action)) {
            access_denied();
        }
    }

    private function render($view, array $data, $tab)
    {
        $data['tab'] = $tab;
        $data['currency_symbol'] = $this->customlib->getSchoolCurrencyFormat();
        $data['session_name'] = $this->setting_model->getCurrentSessionName();
        $this->session->set_userdata('sub_menu', 'admin/fees' . ($tab === 'overview' ? '' : '/' . $tab));
        $this->load->view('layout/header', $data);
        $this->load->view('admin/fees/' . $view, $data);
        $this->load->view('layout/footer', $data);
    }

    /** Overview: the session's fees at a glance, then one row per charge. */
    public function index()
    {
        $this->need('collect_fees', 'can_view');
        $filters = array(
            'class_id' => (int) $this->input->get('class_id'),
            'fsg_id' => (int) $this->input->get('fsg_id'),
            'status' => in_array($this->input->get('status'), array('owing', 'overdue', 'paid'), true) ? $this->input->get('status') : '',
            'term' => in_array($this->input->get('term'), array('first', 'second', 'third', 'none'), true) ? $this->input->get('term') : '',
        );
        $items = $this->fees_overview->items();
        $this->render('overview', array(
            'title' => 'Fees overview',
            'totals' => $this->fees_overview->totals($items),
            'charges' => $this->fees_overview->charges($filters),
            'filters' => $filters,
            'group_list' => $this->fees_overview->groups(),
            'class_list' => $this->fees_overview->classes(),
            'term_list' => $this->fees_overview->terms(),
            'uncharged' => count($this->fees_overview->uncharged()),
            'pending_transfers' => $this->pending_transfers(),
        ), 'overview');
    }

    /** One charge in one class section: each student's balance. */
    public function charge($fsg_id = 0, $class_id = 0, $section_id = 0)
    {
        $this->need('collect_fees', 'can_view');
        $students = $this->fees_overview->students((int) $fsg_id, (int) $class_id, (int) $section_id);
        if (!$students) {
            show_404();
        }
        $first = $students[0]['items'][0];
        $all = array();
        foreach ($students as $s) {
            $all = array_merge($all, $s['items']);
        }
        $this->render('charge', array(
            'title' => $first['group'],
            'charge' => $first,
            'students' => $students,
            'totals' => $this->fees_overview->totals($all),
            'uncharged' => $first['is_system'] ? array() : $this->fees_overview->uncharged((int) $fsg_id, (int) $class_id, (int) $section_id),
            'last_reminder' => $this->last_reminder((int) $fsg_id, $students),
            'sch_setting' => $this->sch_setting_detail,
        ), 'overview');
    }

    /** Collections: find a student and collect; what needs attention today. */
    public function collections()
    {
        $this->need('collect_fees', 'can_view');
        $q = trim((string) $this->input->get('q'));
        $class_id = (int) $this->input->get('class_id');
        $section_id = (int) $this->input->get('section_id');
        $found = array();
        if (mb_strlen($q) >= 2 || $class_id) {
            $found = $this->find_students(mb_strlen($q) >= 2 ? $q : '', $class_id, $section_id);
        }
        $today = date('Y-m-d');
        $this->render('collections', array(
            'title' => 'Record payments',
            'q' => $q,
            'class_id' => $class_id,
            'section_id' => $section_id,
            'classes' => $this->class_sections(),
            'found' => $found,
            'pending_transfers' => $this->pending_transfers(),
            'collected_today' => $this->collected_since($today),
            'collected_week' => $this->collected_since(date('Y-m-d', strtotime('-6 days'))),
            'totals' => $this->fees_overview->totals($this->fees_overview->items()),
            'sch_setting' => $this->sch_setting_detail,
        ), 'collections');
    }

    /** End-of-day takings: one day's payments by collector and payment mode, printable for reconciling. */
    public function takings()
    {
        $this->need('collect_fees', 'can_view');
        $this->load->library('Fee_takings');
        $date = (string) $this->input->get('date');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !strtotime($date)) {
            $date = date('Y-m-d');
        }
        $by = (string) $this->input->get('by');
        if (!preg_match('/^(staff-\d+|online)$/', $by)) {
            $by = '';
        }
        $this->render('takings', array(
            'title' => 'Takings',
            'date' => $date,
            'by' => $by,
            'day' => $this->fee_takings->day($date, $by),
            'sch_setting' => $this->sch_setting_detail,
        ), 'collections');
    }

    /** Create fees: the form (GET), the review (POST step=review) and the creation (POST step=create). */
    public function create()
    {
        $this->need('fees_master', 'can_add');
        $data = $this->create_data();
        $step = $this->input->post('step');
        if ($step === 'edit') { // back from the review, with what was entered
            $plan = json_decode((string) $this->input->post('plan'), true);
            $data['plan'] = is_array($plan) && isset($plan['items'], $plan['classes'], $plan['amounts'], $plan['name']) ? $plan : null;
        } elseif ($step === 'review' || $step === 'create') {
            $plan = $step === 'create' ? json_decode((string) $this->input->post('plan'), true) : $this->plan_from_post();
            if (!is_array($plan) || !isset($plan['items'], $plan['classes'], $plan['amounts'], $plan['name'])) {
                $data['errors'] = array('Something went wrong with the form. Please start again.');
            } else {
                $data['plan'] = $plan;
                $data['errors'] = $this->fee_charges->check($plan);
                if (!$data['errors'] && $step === 'create') {
                    try {
                        $n = $this->fee_charges->create($plan);
                        $this->session->set_flashdata('fees_msg', '"' . $plan['name'] . '" created and charged to ' . $n . ' student' . ($n === 1 ? '' : 's') . '.');
                        redirect('admin/fees');
                    } catch (RuntimeException $e) {
                        $data['errors'] = array($e->getMessage());
                    }
                }
                if (!$data['errors']) {
                    $data['preview'] = $this->fee_charges->preview($plan);
                }
            }
        }
        $this->render($data['preview'] ? 'review' : 'create', $data, 'create');
    }

    /** Repeat fees: pick a set of fees from this or last session; it opens in Create fees for the next term. */
    public function repeat()
    {
        $this->need('fees_master', 'can_add');
        $sessions = array((int) $this->setting_model->getCurrentSession());
        $this->load->model('session_model');
        $previous = $this->session_model->getPreSession($sessions[0]);
        if ($previous) {
            $sessions[] = (int) $previous->id;
        }
        $sets = $this->fee_sets->sets($sessions);
        $key = (string) $this->input->post('set');
        if ($this->input->method() === 'post') {
            foreach ($sets as $set) {
                if ($set['key'] === $key) {
                    $data = $this->create_data();
                    $data['plan'] = $this->fee_sets->plan_from_set($set);
                    $data['copied_from'] = $set['name'] . ($set['session_id'] === $sessions[0] ? '' : ' (' . $set['session'] . ')');
                    $this->render('create', $data, 'create');
                    return;
                }
            }
        }
        $this->render('repeat', array('title' => 'Repeat fees', 'sets' => $sets, 'current_session_id' => $sessions[0]), 'create');
    }

    /** Change amounts and due dates of a charge (step=review shows the effect, step=apply saves). */
    public function edit($fsg_id = 0, $class_id = 0, $section_id = 0)
    {
        $this->need('fees_master', 'can_edit');
        list($charge) = $this->charge_or_404($fsg_id, $class_id, $section_id);
        $items = $this->fee_sets->items($fsg_id);
        if ($charge['is_system'] || !$items) {
            show_404();
        }
        $data = array('title' => 'Change fees', 'charge' => $charge, 'items' => $items, 'errors' => array(), 'preview' => null, 'changes' => array());
        $step = $this->input->post('step');
        if ($step === 'review' || $step === 'apply') {
            $changes = array();
            $amounts = (array) $this->input->post('amount');
            $dues = (array) $this->input->post('due');
            foreach ($items as $id => $it) {
                $a = str_replace(',', '', trim((string) (isset($amounts[$id]) ? $amounts[$id] : '')));
                if ($a === '' || !is_numeric($a) || $a < 0) {
                    $data['errors'][] = 'Enter an amount for ' . $it['item'] . ' (0 to stop charging it).';
                    continue;
                }
                $d = trim((string) (isset($dues[$id]) ? $dues[$id] : ''));
                $ymd = $d === '' ? null : (string) $this->customlib->dateFormatToYYYYMMDD($d);
                if ($ymd !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $ymd)) {
                    $data['errors'][] = 'The due date of ' . $it['item'] . ' is not a date.';
                    continue;
                }
                $changes[$id] = array('amount' => (float) $a, 'due_date' => $ymd);
            }
            $data['changes'] = $changes;
            if (!$data['errors']) {
                $data['preview'] = $this->fee_sets->edit_preview($fsg_id, $changes);
                if ($step === 'apply' && $data['preview']['changed']) {
                    try {
                        $n = $this->fee_sets->apply_edit($fsg_id, $changes);
                        $this->session->set_flashdata('fees_msg', $n . ' fee item' . ($n === 1 ? '' : 's') . ' changed for ' . $data['preview']['students'] . ' student' . ($data['preview']['students'] === 1 ? '' : 's') . '.');
                        redirect('admin/fees/charge/' . (int) $fsg_id . '/' . (int) $class_id . '/' . (int) $section_id);
                    } catch (RuntimeException $e) {
                        $data['errors'][] = $e->getMessage();
                    }
                }
            }
        }
        $this->render('edit', $data, 'overview');
    }

    /** Remove a charge from a class section's students (only when none of them has paid it). */
    public function remove($fsg_id = 0, $class_id = 0, $section_id = 0)
    {
        $this->need('fees_master', 'can_delete');
        list($charge) = $this->charge_or_404($fsg_id, $class_id, $section_id);
        $removal = $this->fee_sets->removal($fsg_id, $class_id, $section_id);
        if ($this->input->method() === 'post' && $this->input->post('confirm') === 'yes') {
            try {
                $n = $this->fee_sets->remove($fsg_id, $class_id, $section_id);
                $this->session->set_flashdata('fees_msg', '"' . $charge['group'] . '" removed from ' . $n . ' student' . ($n === 1 ? '' : 's') . ' of ' . $charge['class'] . ' ' . $charge['section'] . '.');
                redirect('admin/fees');
            } catch (RuntimeException $e) {
                $this->session->set_flashdata('fees_msg', $e->getMessage());
                redirect('admin/fees/remove/' . (int) $fsg_id . '/' . (int) $class_id . '/' . (int) $section_id);
            }
        }
        $this->render('remove', array('title' => 'Remove fees', 'charge' => $charge, 'removal' => $removal), 'overview');
    }

    /** Remind all who owe: an SMS to each guardian with their child's balance, previewed before sending. */
    public function remind($fsg_id = 0, $class_id = 0, $section_id = 0)
    {
        $this->need('collect_fees', 'can_add');
        $this->load->library('Fee_reminders');
        list($charge) = $this->charge_or_404($fsg_id, $class_id, $section_id);
        $title = $charge['is_system'] ? 'the balance brought forward' : $this->fee_sets->base_name($charge['group']);
        $rows = $this->fee_reminders->preview((int) $fsg_id, (int) $class_id, (int) $section_id, $title, $this->sch_setting_detail);
        if ($this->input->method() === 'post') {
            try {
                list($sent, $failed) = $this->fee_reminders->send((int) $fsg_id, $rows, (array) $this->input->post('pick'), $this->customlib->getStaffID());
                $this->session->set_flashdata('fees_msg', 'Reminder sent to ' . $sent . ' guardian' . ($sent === 1 ? '' : 's') . ($failed ? '; ' . $failed . ' could not be sent (see the SMS gateway settings)' : '') . '.');
                redirect('admin/fees/charge/' . (int) $fsg_id . '/' . (int) $class_id . '/' . (int) $section_id);
            } catch (RuntimeException $e) {
                $this->session->set_flashdata('fees_msg', $e->getMessage());
                redirect('admin/fees/remind/' . (int) $fsg_id . '/' . (int) $class_id . '/' . (int) $section_id);
            }
        }
        $this->render('remind', array('title' => 'Remind guardians', 'charge' => $charge, 'rows' => $rows, 'gateway' => $this->fee_reminders->gateway()), 'overview');
    }

    /** Reminders and reports: follow-up and summaries, apart from day-to-day collection. */
    public function followup()
    {
        $this->need('collect_fees', 'can_view');
        $this->render('followup', array('title' => 'Reminders and reports'), 'followup');
    }

    /** Sets the term of a charge (fee_session_groups.term), from its page. */
    public function term($fsg_id = 0)
    {
        $this->need('fees_master', 'can_edit');
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $term = (string) $this->input->post('term');
        $session = (int) $this->setting_model->getCurrentSession(); // before building the query: it runs one of its own
        $fsg = $this->db->select('id')->where(array('id' => (int) $fsg_id, 'session_id' => $session))->get('fee_session_groups')->row();
        if (!$fsg) {
            show_404();
        }
        $this->db->where('id', (int) $fsg->id)->update('fee_session_groups', array('term' => isset(Fees_overview::TERMS[$term]) ? $term : null));
        $this->session->set_flashdata('fees_msg', 'Term saved.');
        $back = (string) $this->input->post('back');
        redirect(preg_match('#^admin/fees/charge/\d+/\d+/\d+$#', $back) ? $back : 'admin/fees');
    }

    /** Discounts & balances: the three adjustments, with what is waiting in each. */
    public function adjust()
    {
        $this->need('fees_discount', 'can_view');
        $previous = $this->fee_adjustments->previous_session();
        $owing = 0;
        $carried = 0;
        if ($previous) {
            foreach ($this->fee_adjustments->carry_summary($previous->id) as $s) {
                $owing += $s['owing'];
                $carried += $s['carried'];
            }
        }
        $this->render('adjust', array(
            'title' => 'Discounts and balances',
            'uncharged' => count($this->fees_overview->uncharged()),
            'discounts' => $this->fee_adjustments->discounts(),
            'previous' => $previous,
            'carry_owing' => $owing,
            'carry_done' => $carried,
        ), 'adjust');
    }

    /** Students not charged their class's fees (admitted later): pick, see the total, charge. */
    public function catchup()
    {
        $this->need('fees_master', 'can_add');
        if ($this->input->method() === 'post') {
            try {
                list($students, $charges) = $this->fee_adjustments->catch_up((array) $this->input->post('pick'));
                $this->session->set_flashdata('fees_msg', $students ? 'Charged ' . $students . ' student' . ($students === 1 ? '' : 's') . ' (' . $charges . ' set' . ($charges === 1 ? '' : 's') . ' of fees).' : 'Nobody was charged: no student was ticked.');
            } catch (RuntimeException $e) {
                $this->session->set_flashdata('fees_msg', $e->getMessage());
            }
            redirect('admin/fees/catchup');
        }
        $this->render('catchup', array(
            'title' => 'Students not charged yet',
            'students' => $this->fees_overview->uncharged(),
            'sch_setting' => $this->sch_setting_detail,
        ), 'adjust');
    }

    /**
     * Discounts: choose a discount (or define one) and the classes (GET), then tick the students and
     * see what changes (step=students), then apply (step=apply).
     */
    public function discounts()
    {
        $this->need('fees_discount_assign', 'can_view');
        $discounts = $this->fee_adjustments->discounts();
        $data = array('title' => 'Discounts', 'discounts' => $discounts, 'classes' => $this->class_sections(), 'errors' => array(),
            'discount' => null, 'students' => null, 'sections' => array(), 'sch_setting' => $this->sch_setting_detail);
        $step = $this->input->post('step');
        if ($step === 'students' || $step === 'apply') {
            $sections = array_values(array_filter((array) $this->input->post('sections'), function ($v) { return preg_match('/^\d+-\d+$/', (string) $v); }));
            $discount = $this->discount_from_post($discounts, $data['errors']);
            if (!$sections) {
                $data['errors'][] = 'Choose at least one class.';
            }
            if (!$data['errors']) {
                $students = $this->fee_adjustments->discount_students($discount['id'], $sections);
                if ($step === 'apply') {
                    try {
                        list(, $added, $removed) = $this->fee_adjustments->apply_discount($discount, $students, (array) $this->input->post('add'), (array) $this->input->post('remove'));
                        $this->session->set_flashdata('fees_msg', '"' . $discount['name'] . '": given to ' . $added . ' student' . ($added === 1 ? '' : 's') . ($removed ? ', taken back from ' . $removed : '') . '. It comes off at their next payment.');
                        redirect('admin/fees/discounts');
                    } catch (RuntimeException $e) {
                        $data['errors'][] = $e->getMessage();
                    }
                }
                $data['discount'] = $discount;
                $data['students'] = $students;
                $data['sections'] = $sections;
            }
        }
        $this->render($data['students'] !== null ? 'discount_students' : 'discounts', $data, 'adjust');
    }

    /** Carry forward: choose classes and a due date, review every student's amount, bring them in. */
    public function carryforward()
    {
        $this->need('fees_carry_forward', 'can_view');
        $previous = $this->fee_adjustments->previous_session();
        $data = array('title' => 'Carry balances forward', 'previous' => $previous, 'errors' => array(), 'students' => null,
            'summary' => $previous ? $this->fee_adjustments->carry_summary($previous->id) : array(), 'sections' => array(), 'due' => '', 'sch_setting' => $this->sch_setting_detail);
        $step = $this->input->post('step');
        if ($previous && ($step === 'review' || $step === 'apply')) {
            $sections = array_values(array_filter((array) $this->input->post('sections'), 'is_string'));
            $due = trim((string) $this->input->post('due_date'));
            $due_ymd = $due === '' ? '' : (string) $this->customlib->dateFormatToYYYYMMDD($due);
            if (!$sections) {
                $data['errors'][] = 'Choose at least one class.';
            }
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $due_ymd)) {
                $data['errors'][] = 'Enter the date the brought-forward balances are due.';
            }
            if (!$data['errors']) {
                $students = $this->fee_adjustments->carry_students($previous->id, $sections);
                if ($step === 'apply') {
                    $amounts = array();
                    foreach ((array) $this->input->post('amount') as $ss => $v) {
                        $amounts[(int) $ss] = str_replace(',', '', (string) $v);
                    }
                    try {
                        $n = $this->fee_adjustments->carry($students, $amounts, $due_ymd);
                        $this->session->set_flashdata('fees_msg', $n ? 'Brought forward the balance of ' . $n . ' student' . ($n === 1 ? '' : 's') . '.' : 'Nothing to bring forward: no student owed anything.');
                        redirect('admin/fees/carryforward');
                    } catch (RuntimeException $e) {
                        $data['errors'][] = $e->getMessage();
                    }
                }
                $data['students'] = $students;
            }
            $data['sections'] = $sections;
            $data['due'] = $due;
        }
        $this->render($data['students'] !== null ? 'carry_review' : 'carryforward', $data, 'adjust');
    }

    // ------------------------------------------------------------------ helpers

    /** The first item row of a charge (its group, class, section, term...) and its students, or a 404. */
    private function charge_or_404($fsg_id, $class_id, $section_id)
    {
        $students = $this->fees_overview->students((int) $fsg_id, (int) $class_id, (int) $section_id);
        if (!$students) {
            show_404();
        }
        return array($students[0]['items'][0], $students);
    }

    private function last_reminder($fsg_id, array $students)
    {
        $this->load->library('Fee_reminders');
        return $this->fee_reminders->last($fsg_id, array_column($students, 'student_session_id'));
    }

    /** What the Create fees form needs. */
    private function create_data()
    {
        return array(
            'title' => 'Create fees',
            'feetypes' => $this->db->select('id, type, code')->where('is_system', 0)->order_by('type')->get('feetype')->result_array(),
            'classes' => $this->class_sections(),
            'errors' => array(),
            'plan' => null,
            'preview' => null,
            'copied_from' => null,
        );
    }

    /** The discount chosen on the form: an existing one, or a new one defined there (not saved until applied). */
    private function discount_from_post(array $discounts, array &$errors)
    {
        $id = (int) $this->input->post('discount_id');
        if ($id) {
            if (!isset($discounts[$id])) {
                $errors[] = 'Choose a discount from the list.';
                return null;
            }
            return $discounts[$id];
        }
        $name = trim(strip_tags((string) $this->input->post('new_name')));
        $type = $this->input->post('new_type') === 'percentage' ? 'percentage' : 'fix';
        $value = str_replace(',', '', trim((string) $this->input->post('new_value')));
        if ($name === '') {
            $errors[] = 'Choose a discount, or name a new one.';
        } elseif ($this->db->where('name', $name)->count_all_results('fees_discounts') > 0) {
            $errors[] = 'A discount called "' . $name . '" already exists: choose it from the list.';
        }
        if (!is_numeric($value) || $value <= 0 || ($type === 'percentage' && $value > 100)) {
            $errors[] = $type === 'percentage' ? 'Enter a percentage between 1 and 100.' : 'Enter the discount amount.';
        }
        return array('id' => null, 'name' => mb_substr($name, 0, 100), 'type' => $type,
            'amount' => $type === 'fix' ? (float) $value : 0, 'percentage' => $type === 'percentage' ? (float) $value : null);
    }

    /** The create form as a plan (see Fee_charges). */
    private function plan_from_post()
    {
        $items = array();
        $due = (array) $this->input->post('due');
        $date = function ($v) {
            $v = trim((string) $v);
            if ($v === '') {
                return null;
            }
            $ymd = $this->customlib->dateFormatToYYYYMMDD($v);
            return preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $ymd) ? $ymd : null;
        };
        $names = array();
        foreach ($this->db->select('id, type')->where('is_system', 0)->get('feetype')->result_array() as $f) {
            $names[(int) $f['id']] = $f['type'];
        }
        foreach ((array) $this->input->post('feetype_ids') as $id) {
            $id = (int) $id;
            if (isset($names[$id])) {
                $key = 'ft' . $id;
                $items[] = array('key' => $key, 'feetype_id' => $id, 'name' => $names[$id], 'due_date' => $date(isset($due[$key]) ? $due[$key] : ''));
            }
        }
        foreach ((array) $this->input->post('new_items') as $i => $name) {
            $name = trim(strip_tags((string) $name));
            if ($name !== '') {
                $key = 'new' . (int) $i;
                $items[] = array('key' => $key, 'feetype_id' => null, 'name' => mb_substr($name, 0, 100), 'due_date' => $date(isset($due[$key]) ? $due[$key] : ''));
            }
        }
        $classes = array();
        foreach ((array) $this->input->post('sections') as $pair) {
            if (preg_match('/^(\d+)-(\d+)$/', (string) $pair, $m)) {
                $classes[(int) $m[1]][] = (int) $m[2];
            }
        }
        $amounts = array();
        $posted = (array) $this->input->post('amount');
        foreach ($classes as $class_id => $sections) {
            foreach ($items as $it) {
                $v = isset($posted[$class_id][$it['key']]) ? str_replace(',', '', (string) $posted[$class_id][$it['key']]) : '';
                $amounts[$class_id][$it['key']] = $v === '' ? '' : (is_numeric($v) ? (float) $v : $v);
            }
        }
        $term = (string) $this->input->post('term');
        return array('name' => trim(strip_tags((string) $this->input->post('name'))), 'term' => isset(Fees_overview::TERMS[$term]) ? $term : '', 'items' => $items, 'classes' => $classes, 'amounts' => $amounts);
    }

    /** Classes with their sections: [class_id => ['name' => ..., 'sections' => [id => name]]] */
    private function class_sections()
    {
        $out = array();
        foreach ($this->db->select('c.id AS class_id, c.class, s.id AS section_id, s.section')->from('class_sections cs')
            ->join('classes c', 'c.id = cs.class_id')->join('sections s', 's.id = cs.section_id')
            ->order_by('c.id')->order_by('s.section')->get()->result_array() as $r) {
            $out[(int) $r['class_id']]['name'] = $r['class'];
            $out[(int) $r['class_id']]['sections'][(int) $r['section_id']] = $r['section'];
        }
        return $out;
    }

    /** Bank transfers parents uploaded that wait for a decision: [count, amount]. */
    private function pending_transfers()
    {
        if (!$this->db->table_exists('offline_fees_payments')) {
            return array(0, 0.0);
        }
        $row = $this->db->select('COUNT(*) AS n, COALESCE(SUM(amount), 0) AS total', false)->where('is_active', 0)->get('offline_fees_payments')->row();
        return array($row ? (int) $row->n : 0, $row ? (float) $row->total : 0.0);
    }

    /** Fees collected since a date (payments recorded on or after it), current session. */
    private function collected_since($date)
    {
        $total = 0.0;
        $count = 0;
        $session = (int) $this->setting_model->getCurrentSession();
        foreach ($this->db->select('d.amount_detail')->from('student_fees_deposite d')
            ->join('student_fees_master m', 'm.id = d.student_fees_master_id')
            ->join('student_session ss', 'ss.id = m.student_session_id')
            ->where('ss.session_id', $session)->get()->result_array() as $r) {
            foreach ((array) json_decode((string) $r['amount_detail']) as $x) {
                if (isset($x->date) && $x->date >= $date) {
                    $total += isset($x->amount) ? (float) $x->amount : 0;
                    $count++;
                }
            }
        }
        return array($count, $total);
    }

    /** Students of the current session by name or admission number, with their balance. */
    /** Students of this session by name / admission no ($q) and/or class and section, with balances. */
    private function find_students($q, $class_id = 0, $section_id = 0)
    {
        $session = (int) $this->setting_model->getCurrentSession();
        $this->db->select('ss.id AS student_session_id, st.firstname, st.middlename, st.lastname, st.admission_no, st.guardian_name, st.guardian_phone, c.class, s.section')
            ->from('student_session ss')->join('students st', 'st.id = ss.student_id')
            ->join('classes c', 'c.id = ss.class_id')->join('sections s', 's.id = ss.section_id')
            ->where('ss.session_id', $session)->where('st.is_active', 'yes');
        if ($q !== '') {
            $this->db->group_start()->like('st.firstname', $q)->or_like('st.lastname', $q)->or_like('st.admission_no', $q)
                ->or_like("CONCAT(st.firstname, ' ', st.lastname)", $q, 'both', false)->group_end();
        }
        if ($class_id) {
            $this->db->where('ss.class_id', (int) $class_id);
        }
        if ($section_id) {
            $this->db->where('ss.section_id', (int) $section_id);
        }
        $rows = $this->db->order_by('s.section')->order_by('st.firstname')->limit($class_id ? 500 : 25)->get()->result_array();
        if (!$rows) {
            return array();
        }
        $balances = array();
        foreach ($this->fees_overview->items() as $i) {
            $balances[$i['student_session_id']] = (isset($balances[$i['student_session_id']]) ? $balances[$i['student_session_id']] : 0) + $i['balance'];
        }
        foreach ($rows as &$r) {
            $r['balance'] = isset($balances[(int) $r['student_session_id']]) ? $balances[(int) $r['student_session_id']] : 0.0;
        }
        return $rows;
    }
}
