<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Report Cards > Classes & Results: pick a class (and term) → its subjects → each subject's
 * assessments (e.g. First CA 10, Mid-term Test 20, Exam 70; typed in or sent by a CBT) → scores →
 * attendance and comments → results (totals, grades, positions) → report cards (print, PDF, email,
 * publish to the student and parent portal). Library: Class_results.
 */
class Results extends MY_Addon_CBSEController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('class_results', 'cbse_marks_guard'));
        $this->current_session = (int) $this->setting_model->getCurrentSession();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    private function need($category, $action = 'can_view')
    {
        if (!$this->rbac->hasPrivilege($category, $action)) {
            access_denied();
        }
    }

    private function can_view()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam', 'can_view') && !$this->rbac->hasPrivilege('cbse_exam_marks', 'can_view')) {
            access_denied();
        }
    }

    private function page($view, array $data)
    {
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbseexam/results');
        $data['manager'] = $this->rbac->hasPrivilege('cbse_exam', 'can_edit');
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/results/' . $view, $data);
        $this->load->view('layout/footer', $data);
    }

    private function term_id()
    {
        $terms = $this->class_results->terms();
        $t = (int) $this->input->get_post('term');
        if ($t && in_array($t, array_map('intval', array_column($terms, 'id')), true)) {
            $this->session->set_userdata('cr_term', $t);
            return $t;
        }
        $s = (int) $this->session->userdata('cr_term');
        if ($s && in_array($s, array_map('intval', array_column($terms, 'id')), true)) {
            return $s;
        }
        return $terms ? (int) $terms[0]['id'] : 0;
    }

    /** An exam of a class, checked: array(exam, class row). */
    private function load($exam_id, $class_id)
    {
        $exam = $this->db->where('id', (int) $exam_id)->get('cbse_exams')->row_array();
        $class = $this->db->where('id', (int) $class_id)->get('classes')->row_array();
        if (!$exam || !$class) {
            show_404();
        }
        $exam['shared'] = empty($exam['class_id']);
        return array($exam, $class);
    }

    private function back($exam, $class_id, $tab = '')
    {
        $ret = (string) $this->input->post('return');
        if ($ret !== '' && strpos($ret, 'cbseexam/results/') === 0 && !preg_match('#[^A-Za-z0-9/_?=&%.-]#', $ret)) {
            redirect($ret);
        }
        redirect('cbseexam/results/klass/' . (int) $class_id . '?tab=reportcards&term=' . (int) $exam['cbse_term_id'] . ($tab ? '&step=' . $tab : ''));
    }

    // ------------------------------------------------------------------ Results: classes → subjects → terms → exams → scores

    /** Every class as a card, with each term's state. */
    public function index()
    {
        $this->can_view();
        $terms = $this->class_results->terms();
        $classes = $this->class_results->classes($this->current_session);
        foreach ($classes as &$c) {
            $c['terms'] = array();
            foreach ($terms as $t) {
                $exam = $this->class_results->exam_for($c['id'], (int) $t['id'], $this->current_session);
                $c['terms'][(int) $t['id']] = $exam ? ((int) $exam['is_publish'] ? 'published' : 'started') : 'none';
            }
        }
        unset($c);
        $this->page('index', array('title' => 'Results', 'terms' => $terms, 'classes' => $classes, 'session_name' => $this->setting_model->getCurrentSessionName()));
    }

    /** A class: the subjects it offers (filter by section); tab reportcards: attendance, comments, results and publishing per term. */
    public function klass($class_id = 0)
    {
        $this->can_view();
        $class = $this->db->where('id', (int) $class_id)->get('classes')->row_array();
        if (!$class) {
            show_404();
        }
        $terms = $this->class_results->terms();
        $section = (int) $this->input->get('section');
        $tab = $this->input->get('tab') === 'reportcards' ? 'reportcards' : 'subjects';
        $data = array('title' => $class['class'], 'class' => $class, 'terms' => $terms, 'tab' => $tab, 'section' => $section,
            'sections' => $this->db->select('s.id, s.section')->from('class_sections cs')->join('sections s', 's.id = cs.section_id')->where('cs.class_id', (int) $class_id)->order_by('s.section')->get()->result_array());
        $exams = array();
        foreach ($terms as $t) {
            $exams[(int) $t['id']] = $this->class_results->exam_for((int) $class_id, (int) $t['id'], $this->current_session);
        }
        $data['exams'] = $exams;
        if ($tab === 'subjects') {
            $data['subjects'] = $this->class_results->class_subjects((int) $class_id, $this->current_session, $section);
            $data['progress'] = array();
            foreach ($exams as $tid => $exam) {
                if ($exam) {
                    foreach ($this->class_results->subjects($exam, (int) $class_id) as $tt => $s) {
                        $data['progress'][(int) $s['subject_id']][$tid] = $s;
                    }
                }
            }
        } else {
            $term = $this->term_id();
            $data['term'] = $term;
            $data['step'] = $this->input->get('step') === 'comments' ? 'comments' : 'results';
            $exam = isset($exams[$term]) ? $exams[$term] : null;
            $data['exam'] = $exam;
            if ($exam) {
                $this->class_results->sync($exam, (int) $class_id, $this->current_session);
                $data['subjects_t'] = $this->class_results->subjects($exam, (int) $class_id);
                $data['grade_scales'] = $this->db->select('id, name')->order_by('name')->get('cbse_exam_grades')->result_array();
                $data['results'] = $this->class_results->results($exam, (int) $class_id);
                if ($data['step'] === 'comments') {
                    $this->load->library('cbse_comments');
                    $data['bank'] = $this->cbse_comments->bank();
                }
            }
        }
        $this->page('klass', $data);
    }

    /**
     * A subject of a class: every term with its exams (CAs, tests, exam; written or CBT) and, for the
     * chosen term, every student's score in each exam (typed in here). ?term, ?section.
     */
    public function subject($class_id = 0, $subject_id = 0)
    {
        $this->can_view();
        $class = $this->db->where('id', (int) $class_id)->get('classes')->row_array();
        $subject = $this->db->where('id', (int) $subject_id)->get('subjects')->row_array();
        if (!$class || !$subject) {
            show_404();
        }
        $terms = $this->class_results->terms();
        $section = (int) $this->input->get('section');
        $per = array();
        foreach ($terms as $t) {
            $tid = (int) $t['id'];
            $exam = $this->class_results->exam_for((int) $class_id, $tid, $this->current_session);
            $row = array('term' => $t, 'exam' => $exam, 'tt' => null);
            if ($exam) {
                $this->class_results->sync($exam, (int) $class_id, $this->current_session);
                foreach ($this->class_results->subjects($exam, (int) $class_id) as $tt => $s) {
                    if ((int) $s['subject_id'] === (int) $subject_id) {
                        $row['tt'] = $s;
                    }
                }
            }
            $per[$tid] = $row;
        }
        $term = (int) $this->input->get('term');
        if (!isset($per[$term])) {
            // the latest term that has this subject, else the first term
            $term = 0;
            foreach ($per as $tid => $row) {
                if ($row['tt']) {
                    $term = $tid;
                }
            }
            $term = $term ?: ($terms ? (int) $terms[0]['id'] : 0);
        }
        $data = array('title' => $subject['name'] . ' · ' . $class['class'], 'class' => $class, 'subject' => $subject, 'terms' => $terms, 'per' => $per, 'term' => $term,
            'section' => $section, 'sections' => $this->db->select('s.id, s.section')->from('class_sections cs')->join('sections s', 's.id = cs.section_id')->where('cs.class_id', (int) $class_id)->order_by('s.section')->get()->result_array(),
            'cbts' => $this->db->select('id, exam, subject_id, marks_target')->where('session_id', $this->current_session)->order_by('id', 'desc')->get('onlineexam')->result_array(),
            'students' => array(), 'marks' => array(), 'lock_reason' => '', 'tt_row' => null, 'can_approve' => $this->cbse_marks_guard->can_approve());
        $cur = isset($per[$term]) ? $per[$term] : null;
        if ($cur && $cur['tt']) {
            $data['students'] = $this->class_results->exam_students($cur['exam'], (int) $class_id, $section);
            if ($data['students']) {
                foreach ($this->db->where('cbse_exam_timetable_id', (int) $cur['tt']['id'])->where_in('cbse_exam_student_id', array_keys($data['students']))->get('cbse_student_subject_marks')->result_array() as $m) {
                    $data['marks'][(int) $m['cbse_exam_student_id']][(int) $m['cbse_exam_assessment_type_id']] = (int) $m['is_absent'] ? 'ABS' : ($m['marks'] === null ? '' : rtrim(rtrim(number_format((float) $m['marks'], 2, '.', ''), '0'), '.'));
                }
            }
            $data['tt_row'] = $this->cbse_marks_guard->subject((int) $cur['tt']['id']);
            $data['lock_reason'] = $this->cbse_marks_guard->lock_reason($data['tt_row']);
        }
        $this->page('subject', $data);
    }

    /** POST class_id, term, return: sets up a class's term (all its subjects and students). */
    public function setup_term()
    {
        $this->need('cbse_exam', 'can_edit');
        try {
            $this->class_results->create((int) $this->input->post('class_id'), (int) $this->input->post('term'), $this->current_session);
            $this->session->set_flashdata('cr_msg', array('success', "Term set up: every subject of the class has its exams. Change any subject's exams on its page."));
        } catch (RuntimeException $e) {
            $this->session->set_flashdata('cr_msg', array('danger', $e->getMessage()));
        }
        $ret = (string) $this->input->post('return');
        redirect(strpos($ret, 'cbseexam/results/') === 0 ? $ret : 'cbseexam/results');
    }

    // ------------------------------------------------------------------ setting up

    /** POST rows (name[], max[]), tt_id (one subject) or none (the class default), reset (subject back to default). */
    public function breakdown($exam_id = 0, $class_id = 0)
    {
        $this->need('cbse_exam', 'can_edit');
        list($exam) = $this->load($exam_id, $class_id);
        $rows = array();
        foreach ((array) $this->input->post('name') as $i => $n) {
            $rows[] = array($n, isset($_POST['max'][$i]) ? $_POST['max'][$i] : 0);
        }
        try {
            $tt = (int) $this->input->post('tt_id');
            if ($tt && $this->input->post('all')) {
                // these exams for every subject of the class in this term
                $this->class_results->set_default($exam, $rows);
                foreach ($this->db->select('id')->where(array('cbse_exam_id' => (int) $exam['id'], 'custom_breakdown' => 1))->get('cbse_exam_timetable')->result_array() as $other) {
                    try {
                        $this->class_results->set_subject($exam, (int) $other['id'], null);
                    } catch (RuntimeException $e) {
                        // a subject with scores in an exam that goes keeps its own
                    }
                }
            } elseif ($tt) {
                $this->class_results->set_subject($exam, $tt, $this->input->post('reset') ? null : $rows);
            } else {
                $this->class_results->set_default($exam, $rows);
            }
            $total = array_sum(array_map(function ($r) { return (float) $r[1]; }, $rows));
            $this->session->set_flashdata('cr_msg', array('success', 'Score breakdown saved' . (!$this->input->post('reset') && abs($total - 100) > 0.01 ? ' (it adds up to ' . $total . ', not 100: percentages still work)' : '') . '.'));
        } catch (RuntimeException $e) {
            $this->session->set_flashdata('cr_msg', array('danger', $e->getMessage()));
        }
        $this->back($exam, $class_id);
    }

    public function add_subject($exam_id = 0, $class_id = 0)
    {
        $this->need('cbse_exam', 'can_edit');
        list($exam) = $this->load($exam_id, $class_id);
        foreach ((array) $this->input->post('subject_id') as $sid) {
            $this->class_results->add_subject($exam, (int) $sid);
        }
        $this->back($exam, $class_id);
    }

    public function remove_subject($exam_id = 0, $class_id = 0)
    {
        $this->need('cbse_exam', 'can_edit');
        list($exam) = $this->load($exam_id, $class_id);
        $tt = (int) $this->input->post('tt_id');
        if ($this->db->where('cbse_exam_timetable_id', $tt)->count_all_results('cbse_student_subject_marks')) {
            $this->session->set_flashdata('cr_msg', array('danger', 'That subject has scores, so it stays. Clear its scores first.'));
        } else {
            $this->db->where('cbse_exam_timetable_id', $tt)->delete('cbse_exam_timetable_assessment_types');
            $this->db->where(array('id' => $tt, 'cbse_exam_id' => (int) $exam['id']))->delete('cbse_exam_timetable');
            $this->session->set_flashdata('cr_msg', array('success', 'Subject removed from this term.'));
        }
        $this->back($exam, $class_id);
    }

    /** POST grade scale, school days, next term date. */
    public function settings($exam_id = 0, $class_id = 0)
    {
        $this->need('cbse_exam', 'can_edit');
        list($exam) = $this->load($exam_id, $class_id);
        $next = trim((string) $this->input->post('next_term_begins'));
        $this->db->where('id', (int) $exam['id'])->update('cbse_exams', array(
            'cbse_exam_grade_id' => (int) $this->input->post('grade_id') ?: null,
            'total_working_days' => max(0, (int) $this->input->post('total_working_days')),
            'next_term_begins' => $next !== '' && strtotime($next) ? date('Y-m-d', strtotime($next)) : null,
        ));
        $this->session->set_flashdata('cr_msg', array('success', 'Saved.'));
        $this->back($exam, $class_id, $this->input->post('tab'));
    }

    /** POST tt_id, type_id, onlineexam_id (0 = make a new CBT for it): its scores come from that CBT. */
    public function cbt($exam_id = 0, $class_id = 0)
    {
        $this->need('cbse_exam', 'can_edit');
        list($exam, $class) = $this->load($exam_id, $class_id);
        $tt = (int) $this->input->post('tt_id');
        $type = (int) $this->input->post('type_id');
        $key = 'cbse:' . $tt . ':' . $type;
        $link = $this->db->select('t.name, t.maximum_marks, s.id AS subject_id, s.name AS subject')->from('cbse_exam_timetable_assessment_types l')
            ->join('cbse_exam_assessment_types t', 't.id = l.cbse_exam_assessment_type_id')->join('cbse_exam_timetable tt', 'tt.id = l.cbse_exam_timetable_id')
            ->join('subjects s', 's.id = tt.subject_id')->where(array('l.cbse_exam_timetable_id' => $tt, 'l.cbse_exam_assessment_type_id' => $type))->get()->row_array();
        if (!$link) {
            show_404();
        }
        $oid = (int) $this->input->post('onlineexam_id');
        if ($this->input->post('unlink')) {
            $this->db->where('marks_target', $key)->update('onlineexam', array('marks_target' => null, 'marks_auto' => 0));
            $this->session->set_flashdata('cr_msg', array('success', $link['subject'] . ' ' . $link['name'] . ': scores are typed in again.'));
            $this->back($exam, $class_id);
        }
        if (!$oid) {
            $this->db->insert('onlineexam', array('session_id' => $this->current_session, 'exam' => $class['class'] . ' ' . $link['subject'] . ' — ' . $link['name'],
                'subject_id' => (int) $link['subject_id'], 'attempt' => 1, 'exam_from' => date('Y-m-d 08:00:00'), 'exam_to' => date('Y-m-d 17:00:00', strtotime('+7 days')),
                'duration' => '00:40:00', 'passing_percentage' => 40, 'description' => $link['name'] . ' of ' . $link['subject'] . ' (' . (float) $link['maximum_marks'] . ' marks) for ' . $class['class'] . '.',
                'publish_result' => 0, 'is_active' => 0, 'is_marks_display' => 1, 'is_neg_marking' => 0, 'is_random_question' => 1, 'is_random_option' => 1, 'is_quiz' => 0,
                'answer_word_count' => -1, 'max_tab_switches' => 3, 'block_copy' => 1));
            $oid = (int) $this->db->insert_id();
            foreach ($this->class_results->exam_students($exam, (int) $class_id) as $st) {
                $this->db->insert('onlineexam_students', array('onlineexam_id' => $oid, 'student_session_id' => (int) $st['student_session_id']));
            }
            $msg = 'CBT made for ' . $link['subject'] . ' ' . $link['name'] . ' with the class\'s students. Add its questions and dates in CBT Exams, then turn it on. Scores come here when students submit.';
        } else {
            $msg = $link['subject'] . ' ' . $link['name'] . ' now takes its scores from that CBT.';
        }
        $this->db->where('marks_target', $key)->where('id !=', $oid)->update('onlineexam', array('marks_target' => null, 'marks_auto' => 0));
        $this->db->where('id', $oid)->update('onlineexam', array('marks_target' => $key, 'marks_auto' => 1, 'marks_scale' => 1, 'marks_replace' => 1));
        $this->session->set_flashdata('cr_msg', array('success', $msg));
        $this->back($exam, $class_id);
    }

    // ------------------------------------------------------------------ scores

    /** Scores of one subject for the class: every assessment as a column. */
    public function scores($exam_id = 0, $class_id = 0, $tt_id = 0)
    {
        $this->can_view();
        list($exam, $class) = $this->load($exam_id, $class_id);
        $subjects = $this->class_results->subjects($exam, (int) $class_id);
        if (!isset($subjects[(int) $tt_id])) {
            show_404();
        }
        $students = $this->class_results->exam_students($exam, (int) $class_id);
        if ($this->input->method() !== 'post') {
            redirect('cbseexam/results/subject/' . (int) $class_id . '/' . (int) $subjects[(int) $tt_id]['subject_id'] . '?term=' . (int) $exam['cbse_term_id']);
        }
        if ($this->input->method() === 'post') {
            $this->need('cbse_exam_marks', 'can_edit');
            try {
                list($n, $bad) = $this->class_results->save_scores($exam, (int) $tt_id, (array) $this->input->post('m'), array_keys($students));
                if ($bad) {
                    $lines = array_map(function ($b) use ($students) { return (isset($students[$b[0]]) ? $students[$b[0]]['name'] : 'Student') . ': ' . $b[1]; }, $bad);
                    $out = array('status' => 'fail', 'message' => 'Nothing was saved. Above the maximum or not a score:<br>' . implode('<br>', array_map('html_escape', array_slice($lines, 0, 12))));
                } else {
                    $out = array('status' => 'success', 'message' => $n ? $n . ' score' . ($n === 1 ? '' : 's') . ' saved.' : 'No changes.');
                }
            } catch (RuntimeException $e) {
                $out = array('status' => 'fail', 'message' => $e->getMessage());
            }
            return $this->output->set_content_type('application/json')->set_output(json_encode($out));
        }
        $marks = array();
        if ($students) {
            foreach ($this->db->where('cbse_exam_timetable_id', (int) $tt_id)->where_in('cbse_exam_student_id', array_keys($students))->get('cbse_student_subject_marks')->result_array() as $m) {
                $marks[(int) $m['cbse_exam_student_id']][(int) $m['cbse_exam_assessment_type_id']] = (int) $m['is_absent'] ? 'ABS' : ($m['marks'] === null ? '' : rtrim(rtrim(number_format((float) $m['marks'], 2, '.', ''), '0'), '.'));
            }
        }
        $tt = $this->cbse_marks_guard->subject((int) $tt_id);
        $this->page('scores', array('title' => $subjects[(int) $tt_id]['name'] . ' scores', 'exam' => $exam, 'class' => $class, 'subject' => $subjects[(int) $tt_id],
            'students' => $students, 'marks' => $marks, 'tt' => $tt, 'lock_reason' => $this->cbse_marks_guard->lock_reason($tt), 'can_approve' => $this->cbse_marks_guard->can_approve(),
            'term_name' => $this->db->select('name')->where('id', (int) $exam['cbse_term_id'])->get('cbse_terms')->row('name')));
    }

    /** POST attendance (days present), class teacher's and principal's comments. */
    public function comments($exam_id = 0, $class_id = 0)
    {
        $this->need('cbse_exam_teacher_remark', 'can_edit');
        list($exam) = $this->load($exam_id, $class_id);
        $students = $this->class_results->exam_students($exam, (int) $class_id);
        $present = (array) $this->input->post('present');
        $teacher = (array) $this->input->post('teacher');
        $principal = (array) $this->input->post('principal');
        foreach (array_keys($students) as $es) {
            $set = array();
            if (array_key_exists($es, $teacher)) {
                $set['remark'] = mb_substr(trim((string) $teacher[$es]), 0, 1000);
                $set['staff_id'] = (int) $this->customlib->getStaffID() ?: null;
            }
            if (array_key_exists($es, $principal)) {
                $set['principal_remark'] = mb_substr(trim((string) $principal[$es]), 0, 1000);
            }
            if (array_key_exists($es, $present) && $this->rbac->hasPrivilege('cbse_exam_attendance', 'can_edit')) {
                $set['total_present_days'] = $present[$es] === '' ? null : max(0, (int) $present[$es]);
            }
            if ($set) {
                $this->db->where(array('id' => $es, 'cbse_exam_id' => (int) $exam['id']))->update('cbse_exam_students', $set);
            }
        }
        $this->session->set_flashdata('cr_msg', array('success', 'Attendance and comments saved.'));
        $this->back($exam, $class_id, 'comments');
    }

    // ------------------------------------------------------------------ results and report cards

    /** POST: approve and lock every subject's scores (exam managers). */
    public function approve_all($exam_id = 0, $class_id = 0)
    {
        $this->need('cbse_exam', 'can_edit');
        list($exam) = $this->load($exam_id, $class_id);
        $ids = array_map('intval', array_column($this->db->select('id')->where('cbse_exam_id', (int) $exam['id'])->where('marks_status !=', 'approved')->get('cbse_exam_timetable')->result_array(), 'id'));
        list($n) = $ids ? $this->cbse_marks_guard->set_status($ids, 'approve', '') : array(0);
        $this->session->set_flashdata('cr_msg', array('success', $n ? $n . ' subject(s) approved and locked.' : 'Every subject was already approved.'));
        $this->back($exam, $class_id, 'results');
    }

    /** POST publish = 1 / 0: report cards visible to students and parents (portal, result checker). */
    public function publish($exam_id = 0, $class_id = 0)
    {
        $this->need('cbse_exam', 'can_edit');
        list($exam) = $this->load($exam_id, $class_id);
        $on = (int) $this->input->post('publish') === 1;
        $this->db->where('id', (int) $exam['id'])->update('cbse_exams', array('is_publish' => $on ? 1 : 0));
        $this->cbse_marks_guard->log(array('cbse_exam_id' => (int) $exam['id'], 'action' => $on ? 'publish' : 'unpublish', 'note' => $on ? 'Report cards published' : 'Report cards withdrawn'));
        $this->session->set_flashdata('cr_msg', array('success', $on ? 'Published: students and parents can see the report cards in their portal.' : 'Report cards withdrawn from the portal.'));
        $this->back($exam, $class_id, 'results');
    }

    /** The data of report cards: one student (es) or the class (or one section). */
    public function card_data(array $exam, array $class, $es = 0, $section_id = 0)
    {
        $r = $this->class_results->results($exam, (int) $class['id'], (int) $es);
        if ($section_id) {
            $r['students'] = array_filter($r['students'], function ($s) use ($section_id) { return (int) $s['section_id'] === (int) $section_id; });
        }
        return array(
            'exam' => $exam, 'class' => $class, 'r' => $r, 'brand' => $this->customlib->brand(),
            'term_name' => $this->db->select('name')->where('id', (int) $exam['cbse_term_id'])->get('cbse_terms')->row('name'),
            'session_name' => $this->db->select('session')->where('id', (int) $exam['session_id'])->get('sessions')->row('session'),
        );
    }

    /** Report cards to view and print (es = one student; section = one arm). */
    public function card($exam_id = 0, $class_id = 0, $es = 0)
    {
        $this->can_view();
        list($exam, $class) = $this->load($exam_id, $class_id);
        $data = $this->card_data($exam, $class, (int) $es, (int) $this->input->get('section'));
        $data['print'] = true;
        $this->load->view('cbseexam/results/cards', $data);
    }

    /** PDF of report cards (es = one student). */
    public function pdf($exam_id = 0, $class_id = 0, $es = 0)
    {
        $this->can_view();
        list($exam, $class) = $this->load($exam_id, $class_id);
        $data = $this->card_data($exam, $class, (int) $es, (int) $this->input->get('section'));
        $name = preg_replace('/[^A-Za-z0-9]+/', '-', $class['class'] . ' ' . $data['term_name'] . ($es && $data['r']['students'] ? ' ' . reset($data['r']['students'])['name'] : '')) . '.pdf';
        $this->output_pdf($data, $name, 'I');
    }

    private function output_pdf(array $data, $name, $dest)
    {
        $data['print'] = false;
        $data['pdf'] = true;
        $html = $this->load->view('cbseexam/results/cards', $data, true);
        $this->load->library('m_pdf');
        $mpdf = $this->m_pdf->load('P');
        $mpdf->SetMargins(8, 8, 8);
        $mpdf->WriteHTML($html);
        return $mpdf->Output($name, $dest);
    }

    /** POST: each parent with an email gets their child's report card PDF. */
    public function email($exam_id = 0, $class_id = 0)
    {
        $this->need('cbse_exam', 'can_edit');
        list($exam, $class) = $this->load($exam_id, $class_id);
        if (!(int) $exam['is_publish']) {
            $this->session->set_flashdata('cr_msg', array('danger', 'Publish the report cards first, then email them.'));
            $this->back($exam, $class_id, 'results');
        }
        $this->load->library('mailer');
        $all = $this->card_data($exam, $class);
        $sent = 0;
        $missing = array();
        $failed = 0;
        foreach ($all['r']['students'] as $es => $st) {
            $to = trim((string) ($st['guardian_email'] ?: $st['email']));
            if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $missing[] = $st['name'];
                continue;
            }
            $one = $all;
            $one['r']['students'] = array($es => $st);
            $pdf = $this->output_pdf($one, 'report-card.pdf', 'S');
            $body = '<p>Dear Parent/Guardian,</p><p>Please find attached the ' . html_escape($all['term_name']) . ' report card of <b>' . html_escape($st['name']) . '</b> (' . html_escape($class['class']) . ').</p><p>' . html_escape($all['brand']['name']) . '</p>';
            if ($this->mailer->send_mail_marksheet($to, $all['term_name'] . ' report card: ' . $st['name'], $body, $pdf, preg_replace('/[^A-Za-z0-9]+/', '-', $st['name'] . ' ' . $all['term_name']))) {
                $sent++;
            } else {
                $failed++;
            }
        }
        $msg = $sent . ' report card(s) emailed.' . ($failed ? ' ' . $failed . ' could not be sent (check Settings > Email Settings).' : '') . ($missing ? ' No parent email for: ' . implode(', ', array_slice($missing, 0, 10)) . (count($missing) > 10 ? '…' : '') . '.' : '');
        $this->session->set_flashdata('cr_msg', array($sent ? 'success' : 'warning', $msg));
        $this->back($exam, $class_id, 'results');
    }
}
