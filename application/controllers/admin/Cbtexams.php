<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CBT Exams by class: class cards → the class's subjects → each subject's CBTs by term (and practice
 * CBTs not on a report card), each with its questions, window, who took it and its scores. Actions
 * (also used from Report Cards > Results > subject): make a CBT for a class, change its dates,
 * open / close it, draw questions from the question bank, pull its scores into the report card.
 * Library: Cbt_admin. The full list of CBTs stays at admin/onlineexam.
 */
class Cbtexams extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('cbt_admin', 'class_results'));
        $this->current_session = (int) $this->setting_model->getCurrentSession();
    }

    private function need($category, $action)
    {
        if (!$this->rbac->hasPrivilege($category, $action)) {
            access_denied();
        }
    }

    private function page($view, array $data)
    {
        $this->session->set_userdata('top_menu', 'Online_Examinations');
        $this->session->set_userdata('sub_menu', 'Online_Examinations/Onlineexam');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/cbtexams/' . $view, $data);
        $this->load->view('layout/footer', $data);
    }

    private function back($fallback)
    {
        $ret = (string) $this->input->post('return');
        if ($ret !== '' && preg_match('#^(cbseexam/results/|admin/cbtexams)#', $ret) && !preg_match('#[^A-Za-z0-9/_?=&%.-]#', $ret)) {
            redirect($ret);
        }
        redirect($fallback);
    }

    private function msg($type, $text)
    {
        $this->session->set_flashdata('cr_msg', array($type, $text));
    }

    // ------------------------------------------------------------------ pages

    public function index()
    {
        $this->need('online_examination', 'can_view');
        redirect('admin/onlineexam'); // the class cards are on top of the CBT list
        list($per, $none) = $this->cbt_admin->per_class($this->current_session);
        $this->page('index', array('title' => 'CBT Exams', 'classes' => $this->class_results->classes($this->current_session), 'per' => $per, 'none' => $none,
            'session_name' => $this->setting_model->getCurrentSessionName()));
    }

    public function klass($class_id = 0)
    {
        $this->need('online_examination', 'can_view');
        $class = $this->db->where('id', (int) $class_id)->get('classes')->row_array();
        if (!$class) {
            show_404();
        }
        $subjects = $this->class_results->class_subjects((int) $class_id, $this->current_session);
        $counts = array();
        foreach ($subjects as $sid => $s) {
            $rows = $this->cbt_admin->for_class_subject((int) $class_id, (int) $sid, $this->current_session);
            $counts[$sid] = array('total' => count($rows), 'open' => count(array_filter($rows, function ($r) { return $r['state'] === 'open'; })));
        }
        $this->page('klass', array('title' => 'CBT · ' . $class['class'], 'class' => $class, 'subjects' => $subjects, 'counts' => $counts));
    }

    public function subject($class_id = 0, $subject_id = 0)
    {
        $this->need('online_examination', 'can_view');
        $class = $this->db->where('id', (int) $class_id)->get('classes')->row_array();
        $subject = $this->db->where('id', (int) $subject_id)->get('subjects')->row_array();
        if (!$class || !$subject) {
            show_404();
        }
        $this->page('subject', array('title' => 'CBT · ' . $subject['name'] . ' · ' . $class['class'], 'class' => $class, 'subject' => $subject,
            'cbts' => $this->cbt_admin->for_class_subject((int) $class_id, (int) $subject_id, $this->current_session),
            'targets' => $this->targets((int) $class_id, (int) $subject_id), 'terms' => $this->class_results->terms()));
    }

    private function targets($class_id, $subject_id)
    {
        return $this->cbt_admin->targets((int) $class_id, (int) $subject_id, $this->current_session);
    }

    // ------------------------------------------------------------------ actions

    /** POST class_id, subject_id, title, target, from, to, minutes, attempts, pass, return. */
    public function create()
    {
        $this->need('online_examination', 'can_add');
        $class_id = (int) $this->input->post('class_id');
        $subject_id = (int) $this->input->post('subject_id');
        $students = array_map('intval', array_column($this->db->select('ss.id')->from('student_session ss')->join('students st', 'st.id = ss.student_id')
            ->where(array('ss.class_id' => $class_id, 'ss.session_id' => $this->current_session, 'st.is_active' => 'yes'))->get()->result_array(), 'id'));
        $target = (string) $this->input->post('target');
        $title = trim((string) $this->input->post('title'));
        if ($title === '') {
            $class = $this->db->select('class')->where('id', $class_id)->get('classes')->row('class');
            $subject = $this->db->select('name')->where('id', $subject_id)->get('subjects')->row('name');
            $label = isset($this->targets($class_id, $subject_id)[$target]) ? preg_replace('/ \(.*$/', '', $this->targets($class_id, $subject_id)[$target]['label']) : 'CBT';
            $title = $class . ' ' . $subject . ' - ' . str_replace(' — ', ' - ', $label);
        }
        try {
            $id = $this->cbt_admin->create(array('session_id' => $this->current_session, 'title' => $title, 'subject_id' => $subject_id, 'from' => $this->input->post('from'),
                'to' => $this->input->post('to'), 'minutes' => $this->input->post('minutes') ?: 40, 'attempts' => $this->input->post('attempts') ?: 1, 'pass' => $this->input->post('pass') ?: 40,
                'target' => $target), $students);
            $this->msg('success', '"' . $title . '" made for ' . count($students) . ' student(s). Now add its questions, then Open it.');
        } catch (RuntimeException $e) {
            $this->msg('danger', $e->getMessage());
        }
        $this->back('admin/cbtexams/subject/' . $class_id . '/' . $subject_id);
    }

    /** POST id, title, from, to, minutes, attempts, target. */
    public function update()
    {
        $this->need('online_examination', 'can_edit');
        $id = (int) $this->input->post('id');
        try {
            $this->cbt_admin->update($id, array('title' => $this->input->post('title'), 'from' => $this->input->post('from'), 'to' => $this->input->post('to'),
                'minutes' => $this->input->post('minutes'), 'attempts' => $this->input->post('attempts')));
            if ($this->input->post('target') !== null) {
                $t = (string) $this->input->post('target');
                if ($t === '') {
                    $this->db->where('id', $id)->update('onlineexam', array('marks_target' => null, 'marks_auto' => 0));
                } else {
                    $this->cbt_admin->link($id, $t);
                }
            }
            $this->msg('success', 'CBT saved.');
        } catch (RuntimeException $e) {
            $this->msg('danger', $e->getMessage());
        }
        $this->back('admin/cbtexams');
    }

    /** POST id, on (1/0). */
    public function toggle()
    {
        $this->need('online_examination', 'can_edit');
        try {
            $on = (int) $this->input->post('on') === 1;
            $this->cbt_admin->set_active((int) $this->input->post('id'), $on);
            $this->msg('success', $on ? 'CBT is open: students can take it between its opening and closing times.' : 'CBT closed.');
        } catch (RuntimeException $e) {
            $this->msg('danger', $e->getMessage());
        }
        $this->back('admin/cbtexams');
    }

    /** POST id, low, medium, high, marks. */
    public function draw()
    {
        $this->need('add_questions_in_exam', 'can_edit');
        try {
            list($added, $short) = $this->cbt_admin->draw((int) $this->input->post('id'), array('low' => $this->input->post('low'), 'medium' => $this->input->post('medium'), 'high' => $this->input->post('high')),
                (float) $this->input->post('marks'), (int) $this->input->post('class_id'));
            $this->msg($added ? 'success' : 'warning', $added . ' question(s) added from the question bank.' . ($short ? ' Not enough questions: ' . implode('; ', $short) . '.' : ''));
        } catch (RuntimeException $e) {
            $this->msg('danger', $e->getMessage());
        }
        $this->back('admin/cbtexams');
    }

    /** POST id: the finished attempts' scores into the report card now. */
    public function pull()
    {
        $this->need('online_examination', 'can_edit');
        try {
            $c = $this->cbt_admin->pull_scores((int) $this->input->post('id'));
            $this->msg('success', (int) $c['sent'] . ' score(s) sent to the report card.' . ($c['pending'] ? ' ' . (int) $c['pending'] . ' wait for written answers to be marked: use "Mark written answers" on the CBT card, then Pull scores again.' : '') . ($c['not_sat'] ? ' ' . (int) $c['not_sat'] . ' have not taken it.' : ''));
        } catch (RuntimeException $e) {
            $this->msg('danger', $e->getMessage());
        }
        $this->back('admin/cbtexams');
    }
}
