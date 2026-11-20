<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Onlineexam extends Student_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->config->load("mailsms");
        $this->load->library("datatables");
        $this->load->library('Exam_auto_publish');
        $this->exam_auto_publish->publish_due(); // results whose Auto Result Publish Date has passed
    }

    public function index()
    {
        $data = array();
        $this->session->set_userdata('top_menu', 'Onlineexam');
        $student_current_class = $this->customlib->getStudentCurrentClsSection();
        $student_session_id    = $student_current_class->student_session_id;
        $onlineexam            = $this->onlineexam_model->getStudentexam($student_session_id);
        $data['onlineexam']    = $onlineexam;
        $this->load->view('layout/student/header');
        $this->load->view('user/onlineexam/onlineexamlist', $data);
        $this->load->view('layout/student/footer');
    }

    public function view($id)
    {
        $data = array();
        $this->session->set_userdata('top_menu', 'Onlineexam');
        $data['sch_setting']         = $this->sch_setting_detail;
        $role                        = $this->customlib->getUserRole();
        $data['role']                = $role;
        $student_current_class       = $this->customlib->getStudentCurrentClsSection();
        $student_session_id          = $student_current_class->student_session_id;
        $online_exam_validate        = $this->onlineexam_model->examstudentsID($student_session_id, $id);
        $student                     = $this->student_model->getByStudentSession($student_session_id);
        $data['question_true_false'] = $this->config->item('question_true_false');
        $exam                        = $this->onlineexam_model->getexamdetails($id);
        $data['exam']                = $exam;
        $data['student']             = $student;
        $questionOpt                 = $this->customlib->getQuesOption();
        $data['questionOpt']         = $questionOpt;
        if (!empty($online_exam_validate)) {
            $data['question_result'] = $this->onlineexamresult_model->getResultByStudent($online_exam_validate->id, $online_exam_validate->onlineexam_id);
            $data['result_prepare']  = $this->onlineexamresult_model->checkResultPrepare($online_exam_validate->id);
        }
        $data['online_exam_validate'] = $online_exam_validate;
        $data['cbt_state'] = null;
        if (!empty($online_exam_validate) && $exam) {
            $this->load->library('Cbt_attempt');
            $found = $this->cbt_attempt->enrolment((int) $exam->id, (int) $student_session_id);
            if ($found) {
                $data['cbt_state'] = $this->cbt_attempt->state($found[0], $found[1]); // also closes an attempt whose time ran out
                $data['online_exam_validate'] = $online_exam_validate = $this->onlineexam_model->examstudentsID($student_session_id, $id);
                $data['question_result'] = $this->onlineexamresult_model->getResultByStudent($online_exam_validate->id, $online_exam_validate->onlineexam_id);
            }
        }
        if ($this->session->flashdata('msg')) {
            $data['flash'] = $this->session->flashdata('msg');
        }
        $filetype                     = $this->filetype_model->get();
        $data['allowed_extension']    = array_map('trim', array_map('strtolower', explode(',', $filetype->file_extension)));
        $data['allowed_mime_type']    = array_map('trim', array_map('strtolower', explode(',', $filetype->file_mime)));
        $data['allowed_upload_size']  = $filetype->file_size;
        $this->load->view('layout/student/header');
        $this->load->view('user/onlineexam/view', $data);
        $this->load->view('layout/student/footer');
    }

    function print() {
        $data                        = array();
        $data['sch_setting']         = $this->sch_setting_detail;
        $exam_id                     = $this->input->post('exam_id');
        $role                        = $this->customlib->getUserRole();
        $data['role']                = $role;
        $student_current_class       = $this->customlib->getStudentCurrentClsSection();
        $student_session_id          = $student_current_class->student_session_id;
        $online_exam_validate        = $this->onlineexam_model->examstudentsID($student_session_id, $exam_id);
        $data['question_true_false'] = $this->config->item('question_true_false');
        $exam                        = $this->onlineexam_model->printstudentexamdetails($exam_id);
        $data['exam']                = $exam;
        $questionOpt                 = $this->customlib->getQuesOption();
        $data['questionOpt']         = $questionOpt;
        $student                     = $this->student_model->getByStudentSession($student_session_id);
        $data['student']             = $student;

        if (!empty($online_exam_validate)) {

            $data['question_result'] = $this->onlineexamresult_model->getResultByStudent($online_exam_validate->id, $online_exam_validate->onlineexam_id);
            $data['result_prepare']  = $this->onlineexamresult_model->checkResultPrepare($online_exam_validate->id);

        }
        $data['online_exam_validate'] = $online_exam_validate;
        $data['page']                 = $this->load->view('user/onlineexam/_print', $data, true);
        echo json_encode(array('status' => 1, 'page' => $data['page']));
    }

    /** The old one-form submit: answers now save as they are chosen (take/answer/submit). */
    public function save()
    {
        $this->session->set_flashdata('msg', '<div class="alert alert-info">Please open the exam again: answers now save as you go.</div>');
        redirect('user/onlineexam');
    }

    /** The old start (exam in a pop-up): the exam now opens on its own page. */
    public function getExamForm()
    {
        $id = (int) $this->input->post('recordid');
        echo json_encode(array('status' => 0, 'redirect' => site_url('user/onlineexam/take/' . $id), 'question_status' => 1));
    }

    // ------------------------------------------------------------------ CBT (libraries/Cbt_attempt.php)

    /** The exam screen: questions, navigator, timer. The questions load from start(). */
    public function take($id = null)
    {
        list($exam, $os) = $this->cbt_enrolment($id);
        list($state, $message) = $this->cbt_attempt->state($exam, $os);
        if (!in_array($state, array('ready', 'running'), true)) {
            $this->session->set_flashdata('msg', '<div class="alert alert-info">' . html_escape($message) . '</div>');
            redirect('user/onlineexam/view/' . (int) $exam['id']);
        }
        $student = $this->student_model->getByStudentSession($os['student_session_id']);
        $this->load->view('user/onlineexam/take', array(
            'exam' => $exam,
            'state' => $state,
            'student' => $student,
            'sch_setting' => $this->sch_setting_detail,
            'question_count' => $this->db->where('onlineexam_id', (int) $exam['id'])->count_all_results('onlineexam_questions'),
        ));
    }

    /** POST exam_id: starts or resumes the attempt; the paper, saved answers and seconds left. */
    public function start()
    {
        list($exam, $os) = $this->cbt_enrolment($this->input->post('exam_id'), true);
        try {
            $attempt = $this->cbt_attempt->start($exam, $os);
        } catch (RuntimeException $e) {
            return $this->cbt_json(array('ok' => false, 'error' => $e->getMessage()));
        }
        $paper = $this->cbt_attempt->paper($exam, $os, $attempt);
        $this->cbt_json(array('ok' => true, 'attempt' => (int) $attempt['id'], 'seconds_left' => $this->cbt_attempt->seconds_left($attempt),
            'questions' => $paper['questions'], 'passages' => $paper['passages']));
    }

    /** POST exam_id, question_id, answer (string, or answer[] for multiple choice): saves one answer. */
    public function answer()
    {
        list($exam, $os) = $this->cbt_enrolment($this->input->post('exam_id'), true);
        $attempt = $this->cbt_attempt->running($exam, $os);
        if (!$attempt) {
            return $this->cbt_json(array('ok' => false, 'ended' => true, 'error' => 'This exam has ended. Your saved answers were submitted.'));
        }
        $value = $this->input->post('answer');
        if ($value === null) {
            $value = $this->input->post('answer[]');
        }
        try {
            $this->cbt_attempt->save($exam, $os, $attempt, (int) $this->input->post('question_id'), $value);
        } catch (RuntimeException $e) {
            return $this->cbt_json(array('ok' => false, 'error' => $e->getMessage(), 'seconds_left' => $this->cbt_attempt->seconds_left($attempt)));
        }
        $this->cbt_json(array('ok' => true, 'seconds_left' => $this->cbt_attempt->seconds_left($attempt)));
    }

    /** POST exam_id, question_id, file: a file for a written answer. */
    public function upload()
    {
        list($exam, $os) = $this->cbt_enrolment($this->input->post('exam_id'), true);
        $attempt = $this->cbt_attempt->running($exam, $os);
        if (!$attempt) {
            return $this->cbt_json(array('ok' => false, 'ended' => true, 'error' => 'This exam has ended.'));
        }
        try {
            $name = $this->cbt_attempt->upload($exam, $os, $attempt, (int) $this->input->post('question_id'), isset($_FILES['file']) ? $_FILES['file'] : array());
        } catch (RuntimeException $e) {
            return $this->cbt_json(array('ok' => false, 'error' => $e->getMessage()));
        }
        $this->cbt_json(array('ok' => true, 'file' => $name, 'seconds_left' => $this->cbt_attempt->seconds_left($attempt)));
    }

    /** POST exam_id: submits (also when time is up). */
    public function submit()
    {
        list($exam, $os) = $this->cbt_enrolment($this->input->post('exam_id'), true);
        $attempt = $this->cbt_attempt->running($exam, $os); // a passed deadline is closed (submitted) here already
        if ($attempt) {
            $this->cbt_attempt->submit($exam, $os, $attempt);
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Your exam has been submitted.</div>');
        $this->cbt_json(array('ok' => true, 'redirect' => site_url('user/onlineexam/view/' . (int) $exam['id'])));
    }

    /**
     * POST exam_id, away (1 = the student left the exam page): counts tab switches; at the exam's
     * limit the attempt is submitted. Also a heartbeat: returns the seconds left (extra time shows).
     */
    public function presence()
    {
        list($exam, $os) = $this->cbt_enrolment($this->input->post('exam_id'), true);
        $attempt = $this->cbt_attempt->running($exam, $os);
        if (!$attempt) {
            return $this->cbt_json(array('ok' => false, 'ended' => true));
        }
        $max = (int) (isset($exam['max_tab_switches']) ? $exam['max_tab_switches'] : 0);
        $count = (int) (isset($attempt['tab_switches']) ? $attempt['tab_switches'] : 0);
        if ($this->input->post('away')) {
            $count++;
            $this->db->where('id', (int) $attempt['id'])->update('onlineexam_attempts', array('tab_switches' => $count));
            if ($max > 0 && $count >= $max) {
                $this->db->where('id', (int) $attempt['id'])->update('onlineexam_attempts', array('forced_submit' => 'tab_switches'));
                $this->cbt_attempt->submit($exam, $os, $attempt);
                $this->session->set_flashdata('msg', '<div class="alert alert-warning">Your exam was submitted because you left the exam page ' . $count . ' times.</div>');
                return $this->cbt_json(array('ok' => true, 'ended' => true, 'forced' => true, 'count' => $count, 'max' => $max, 'redirect' => site_url('user/onlineexam/view/' . (int) $exam['id'])));
            }
        }
        $this->cbt_json(array('ok' => true, 'count' => $count, 'max' => $max, 'seconds_left' => $this->cbt_attempt->seconds_left($attempt)));
    }

    /** The exam and this student's place in it; only the student (not a parent) and only their own. */
    private function cbt_enrolment($exam_id, $json = false)
    {
        $this->load->library('Cbt_attempt');
        $fail = function ($message) use ($json) {
            if ($json) {
                $this->cbt_json(array('ok' => false, 'error' => $message));
                $this->output->_display();
                exit;
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">' . html_escape($message) . '</div>');
            redirect('user/onlineexam');
        };
        if ($this->customlib->getUserRole() !== 'student') {
            $fail('Only the student can sit an exam.');
        }
        $class = $this->customlib->getStudentCurrentClsSection();
        $found = $class ? $this->cbt_attempt->enrolment((int) $exam_id, (int) $class->student_session_id) : null;
        if (!$found) {
            $fail('This exam is not assigned to you.');
        }
        return $found;
    }

    private function cbt_json(array $data)
    {
        $this->output->set_content_type('application/json')->set_header('Cache-Control: no-store')->set_output(json_encode($data));
    }

    public function downloadattachment($doc)
    {
        $this->load->helper('download');
        $filepath = upload_root() . "onlinexam_images/" . $doc;
        $data     = file_get_contents($filepath);
        $name     = $doc;
        force_download($name, $data);
    }

    public function getexamlist()
    {
        $student_current_class = $this->customlib->getStudentCurrentClsSection();
        $student_session_id    = $student_current_class->student_session_id;
        $questionList          = $this->onlineexam_model->getstudentexamlist($student_session_id);
        $m                     = json_decode($questionList);
        $currency_symbol       = $this->customlib->getSchoolCurrencyFormat();
        $dt_data               = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $viewbtn = '';

                $title = "<a href='#' data-toggle='popover' class='detail_popover'>" . $value->exam . "</a>";

                $title = "<a href='#' data-toggle='popover' class='detail_popover'>" . $value->exam . "</a>";

                if ($value->description == "") {
                    $description = "<div class='fee_detail_popover' style='display: none'><p class='text text-danger'>" . $this->lang->line('no_description') . "</div></p>";
                } else {
                    $description = "<div class='fee_detail_popover' style='display: none'><p class='text text-danger'>" . $value->description . "</div></p>";
                }
                if ($value->is_quiz) {
                    $is_quiz = "<i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $is_quiz = "<i class='fa fa-exclamation-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                }

                $viewbtn = " <a href=" . base_url() . 'user/onlineexam/view/' . $value->id . " class='btn btn-default btn-xs' data-toggle='tooltip'  title=" . $this->lang->line('view') . " '   ><i class='fa fa fa-eye'></i></a>";

                $row   = array();
                $row[] = $title . $description;
                $row[] = $is_quiz;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->exam_from, false);
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->exam_to, false);
                $row[] = $value->duration;
                $row[] = $value->attempt;
                $row[] = $value->counter;

                if ($value->publish_result) {
                    $row[] = $this->lang->line('result_published');
                } else {
                    $row[] = $this->lang->line('available');
                }
                $row[] = $viewbtn;

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getclosedexamlist()
    {
        $student_current_class = $this->customlib->getStudentCurrentClsSection();
        $student_session_id    = $student_current_class->student_session_id;
        $questionList          = $this->onlineexam_model->getstudentclosedexamlist($student_session_id);
        $m                     = json_decode($questionList);
        $currency_symbol       = $this->customlib->getSchoolCurrencyFormat();
        $dt_data               = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $viewbtn = '';

                $title = "<a href='#' data-toggle='popover' class='detail_popover'>" . $value->exam . "</a>";

                $title = "<a href='#' data-toggle='popover' class='detail_popover'>" . $value->exam . "</a>";

                if ($value->description == "") {
                    $description = "<div class='fee_detail_popover' style='display: none'><p class='text text-danger'>" . $this->lang->line('no_description') . "</div></p>";
                } else {
                    $description = "<div class='fee_detail_popover' style='display: none'><p class='text text-danger'>" . $value->description . "</div></p>";
                }
                if ($value->is_quiz) {
                    $is_quiz = "<i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $is_quiz = "<i class='fa fa-exclamation-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                }

                $viewbtn = " <a href=" . base_url() . 'user/onlineexam/view/' . $value->id . " class='btn btn-default btn-xs' data-toggle='tooltip'  title=" . $this->lang->line('view') . " '   ><i class='fa fa fa-eye'></i></a>";
                $row     = array();
                $row[]   = $title . $description;
                $row[]   = $is_quiz;
                $row[]   = $this->customlib->dateyyyymmddToDateTimeformat($value->exam_from, false);
                $row[]   = $this->customlib->dateyyyymmddToDateTimeformat($value->exam_to, false);
                $row[]   = $value->duration;
                $row[]   = $value->attempt;
                $row[]   = $value->counter;

                if ($value->publish_result) {
                    $row[] = $this->lang->line('result_published');
                } else {
                    $row[] = $this->lang->line('available');
                }
                $row[] = $viewbtn;

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }
}
