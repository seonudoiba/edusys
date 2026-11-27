<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Onlineexam extends Admin_Controller
{
    public $sch_setting_detail = array();
    /**
     * The permission each page or action needs: alternatives, any one is enough. A method missing
     * from this list is refused, so a new public method has to be added here to be reachable.
     */
    const PERMISSIONS = array(
        'index' => array(array('online_examination', 'can_view')),
        'getexamlist' => array(array('online_examination', 'can_view')),
        'getclosedexamlist' => array(array('online_examination', 'can_view')),
        'getonlineexambyid' => array(array('online_examination', 'can_view')),
        'getrecord' => array(array('online_examination', 'can_view')),
        'downloadattachment' => array(array('online_examination', 'can_view'), array('online_exam_wise_report', 'can_view')),
        'evalution' => array(array('online_examination', 'can_view')),
        'getdescques' => array(array('online_examination', 'can_view')),
        'fillmarks' => array(array('online_examination', 'can_edit')),
        'assign' => array(array('online_assign_view_student', 'can_view')),
        'addstudent' => array(array('online_assign_view_student', 'can_edit')),
        'assign_classes' => array(array('online_assign_view_student', 'can_edit')),
        'searchquestionbyexamid' => array(array('add_questions_in_exam', 'can_view')),
        'getexamquestions' => array(array('add_questions_in_exam', 'can_view')),
        'download_exam' => array(array('add_questions_in_exam', 'can_view')),
        'questionadd' => array(array('add_questions_in_exam', 'can_edit')),
        'deleteexamquestions' => array(array('add_questions_in_exam', 'can_edit')),
        'rankgenerate' => array(array('online_examination', 'can_edit')),
        'saverank' => array(array('online_examination', 'can_edit')),
        'delete' => array(array('online_examination', 'can_delete')),
        'ajax_delete' => array(array('online_examination', 'can_delete')),
        'send_marks' => array(array('online_examination', 'can_edit')),
        'results' => array(array('online_examination', 'can_view')),
        'invigilate' => array(array('online_examination', 'can_view')),
        'random_draw' => array(array('add_questions_in_exam', 'can_edit')),
        'extra_time' => array(array('online_examination', 'can_edit')),
        'release_device' => array(array('online_examination', 'can_edit')),
        'photos' => array(array('online_examination', 'can_view')),
        'photo' => array(array('online_examination', 'can_view')),
        'results_export' => array(array('online_examination', 'can_view')),
        'report' => array(array('online_exam_wise_report', 'can_view')),
        'dtreportlist' => array(array('online_exam_wise_report', 'can_view')),
        'searchloginvalidation' => array(array('online_exam_wise_report', 'can_view')),
        'getstudentresult' => array(array('online_examination', 'can_view'), array('online_exam_wise_report', 'can_view')),
        // add(): a new exam needs can_add, an edit (recordid posted) can_edit; checked below.
        'add' => array(),
    );

    public function __construct()
    {
        parent::__construct();
        $this->require_permission();
        $this->config->load('app-config');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->library('mailsmsconf');
        $this->load->library('media_storage');
        $this->load->library('Exam_auto_publish');
        $this->load->library('Cbt_integrity');
        $this->exam_auto_publish->publish_due(); // results whose Auto Result Publish Date has passed
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('online_examination', 'can_view')) {
            access_denied();
        }
        $data = array();
        $this->session->set_userdata('top_menu', 'Online_Examinations');
        $this->session->set_userdata('sub_menu', 'Online_Examinations/Onlineexam');
        $questionList           = $this->onlineexam_model->get();

        $data['questionList']   = $questionList;
        $subject_result         = $this->subject_model->get();
        $data['subjectlist']    = $subject_result;
        $questionOpt            = $this->customlib->getQuesOption();
        $data['questionOpt']    = $questionOpt;
        $data['question_type']  = $this->config->item('question_type');
        $data['question_level'] = $this->config->item('question_level');
        $data['classList']      = $this->class_model->get();
        // CBTs by class: a card per class on top of the list (admin/cbtexams opens a class)
        $this->load->library(array('cbt_admin', 'class_results'));
        $cbt_session = (int) $this->setting_model->getCurrentSession();
        list($data['cbt_per_class'], $data['cbt_no_class']) = $this->cbt_admin->per_class($cbt_session);
        $data['cbt_classes'] = $this->class_results->classes($cbt_session);

        $this->load->view('layout/header', $data);
        $this->load->view('admin/onlineexam/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getexamlist()
    {
        $questionList = $this->onlineexam_model->getexamlist();
        //echo $this->db->last_query();die;
        $subject_result = $this->subject_model->get();
        $subjectlist    = $subject_result;
        $questionOpt    = $this->customlib->getQuesOption();
        $questionOpt    = $questionOpt;
        $question_type  = $this->config->item('question_type');
        $question_level = $this->config->item('question_level');
        $classList      = $this->class_model->get();
        $m              = json_decode($questionList);
        $subject_names = array_column($this->subject_model->get(), 'name', 'id'); // the exam's subject under its title

        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $subject_value) {
                $assign          = '';
                $addquestion_btn = '';
                $editbtn         = '';
                $deletebtn       = '';
                $question_list   = '';
                $set_enable      = false;
                if ((($subject_value->publish_result == 0) && (strtotime($subject_value->exam_to) >= strtotime(date('Y-m-d H:i:s')))) && (($subject_value->auto_publish_date == "0000-00-00" || $subject_value->auto_publish_date == "" || $subject_value->auto_publish_date == null) || strtotime($subject_value->auto_publish_date) >= strtotime(date('Y-m-d H:i:s')))) {
                    $set_enable = true;
                }
                $title = "<a href='#' data-toggle='popover' class='detail_popover'>" . $subject_value->exam . "</a>";
                if (!empty($subject_value->subject_id) && isset($subject_names[(int) $subject_value->subject_id])) {
                    $title .= "<br><small class='text-muted'>" . html_escape($subject_names[(int) $subject_value->subject_id]) . "</small>";
                }

                if ($subject_value->description == "") {
                    $description = "<div class='fee_detail_popover' style='display: none'><p class='text text-danger'>" . $this->lang->line('no_description') . "</div></p>";
                } else {
                    $description = "<div class='fee_detail_popover' style='display: none'><p class='text text-danger'>" . $subject_value->description . "</div></p>";
                }

                if ($subject_value->is_quiz) {
                    $is_quiz = "<i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $is_quiz = "<i class='fa fa-exclamation-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                }

                $descriptive_ques = $subject_value->total_ques . "<br /><span>(" . $this->lang->line('descriptive') . ':' . $subject_value->total_descriptive_ques . ")</span>";
                if ($subject_value->is_active == 1) {
                    $is_active = "<i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $is_active = "<i class='fa fa-exclamation-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                }

                if ($subject_value->publish_result == 1) {
                    $publish_result = "<i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $publish_result = "<i class='fa fa-exclamation-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                }

                if ($this->rbac->hasPrivilege('online_assign_view_student', 'can_view') && $set_enable) {

                    $assign = "<a href=" . base_url() . 'admin/onlineexam/assign/' . $subject_value->id . " data-toggle='tooltip' class='btn btn-default btn-xs' title='" . $this->lang->line('assign_view_student') . "'  ><i class='fa fa-tag'></i></a>";
                }
                if ($this->rbac->hasPrivilege('add_questions_in_exam', 'can_view')) {
                    $addquestion_btn = " <button type='button' class='btn btn-default btn-xs' data-recordid=" . $subject_value->id . " data-is_quiz=" . $subject_value->is_quiz . " data-subject_id='" . (int) $subject_value->subject_id . "' data-toggle='modal' data-target='#myQuestionModal' title='" . $this->lang->line('add_question') . "' ><i class='fa fa-question-circle'></i></button>";
                }

                if ($this->rbac->hasPrivilege('add_questions_in_exam', 'can_view')) {
                    $download_btn = " <button type='button' data-toggle='tooltip' class='btn btn-default btn-xs download_exam' data-recordid=" . $subject_value->id . "  title=" . $this->lang->line('print') . " data-loading-text='<i class=" . '" fa fa-spinner fa-spin"' . "  ></i>'><i class='fa fa fa-print'></i></button>";
                } else {
                    $download_btn = "";
                }

                if ($this->rbac->hasPrivilege('online_examination', 'can_edit')) {
                    $editbtn = " <button type='button' data-toggle='tooltip' class='btn btn-default btn-xs question-btn-edit' data-recordid=" . $subject_value->id . "  title='" . $this->lang->line('edit_exam') . "'  ><i class='fa fa fa-pencil'></i></button>";
                } else {
                    $editbtn = "";
                }

                if ($this->rbac->hasPrivilege('online_examination', 'can_delete')) {
                    $deletebtn = " <form method='post' action='" . site_url('admin/onlineexam/delete/' . (int) $subject_value->id) . "' style='display:inline' onsubmit='return confirm(" . htmlspecialchars(json_encode($this->lang->line('delete_confirm')), ENT_QUOTES) . ")'><button type='submit' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa fa-remove'></i></button></form>";
                }

                if ($this->rbac->hasPrivilege('add_questions_in_exam', 'can_view')) {

                    $question_list = "<button class='btn btn-default btn-xs exam_ques_list' data-toggle='tooltip' data-recordid=" . $subject_value->id . "    data-loading-text='<i class=" . '" fa fa-spinner fa-spin"' . "  ></i>' title='" . $this->lang->line('exam_questions_list') . "' ><i class='fa fa-file-text-o'></i></button>" . " ";

                    $question_list .= "<a href=" . base_url() . 'admin/onlineexam/evalution/' . $subject_value->id . " class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('exam_evaluation') . "'>  <i class='fa fa-newspaper-o'></i></a>" . " ";
                    $question_list .= "<a href='" . site_url('admin/onlineexam/results/' . (int) $subject_value->id) . "' class='btn btn-default btn-xs' data-toggle='tooltip' title='Results'><i class='fa fa-bar-chart'></i></a> ";
                    $question_list .= "<a href='" . site_url('admin/onlineexam/invigilate/' . (int) $subject_value->id) . "' class='btn btn-default btn-xs' data-toggle='tooltip' title='Invigilate: live status, extra time'><i class='fa fa-eye'></i></a> ";
                    if ($this->rbac->hasPrivilege('online_examination', 'can_edit')) {
                        $auto_on = !empty($subject_value->marks_auto) && (string) $subject_value->marks_target !== '';
                        $question_list .= "<a href='" . site_url('admin/onlineexam/send_marks/' . (int) $subject_value->id) . "' class='btn btn-xs " . ($auto_on ? 'btn-success' : 'btn-default') . "' data-toggle='tooltip' title='" . ($auto_on ? 'Scores go to the report card automatically' : 'Send scores to report card') . "'><i class='fa fa-share-square-o'></i></a> ";
                    }

                    if ($subject_value->publish_result || ($subject_value->auto_publish_date != "0000-00-00" && $subject_value->auto_publish_date != "" && $subject_value->auto_publish_date != null && (strtotime($subject_value->auto_publish_date) <= strtotime(date('Y-m-d H:i:s'))))) {

                        $question_list .= "<button class='btn btn-default btn-xs generate_rank' data-exam-title=" . $subject_value->exam . " data-recordid=" . $subject_value->id . " data-toggle='tooltip' title='" . $this->lang->line('generate_rank') . "' > <i class='fa fa-list-alt'></i></button> ";
                    }
                }

                // The exam link to share with students (cbt/<id>): sign in, then the exam opens.
                $share_btn = " <button type='button' class='btn btn-default btn-xs share_exam_link' data-toggle='tooltip' title='Share exam link'"
                    . " data-link='" . html_escape(site_url('cbt/' . (int) $subject_value->id)) . "'"
                    . " data-title='" . html_escape($subject_value->exam) . "'"
                    . " data-from='" . html_escape($this->customlib->dateyyyymmddToDateTimeformat($subject_value->exam_from, false)) . "'"
                    . " data-to='" . html_escape($this->customlib->dateyyyymmddToDateTimeformat($subject_value->exam_to, false)) . "'"
                    . " data-active='" . (int) $subject_value->is_active . "'><i class='fa fa-link'></i></button>";

                $row = array();

                $row[]     = $title . $description;
                $row[]     = $is_quiz;
                $row[]     = $descriptive_ques;
                $row[]     = $subject_value->attempt;
                $row[]     = $this->customlib->dateyyyymmddToDateTimeformat($subject_value->exam_from, false);
                $row[]     = $this->customlib->dateyyyymmddToDateTimeformat($subject_value->exam_to, false);
                $row[]     = $subject_value->duration;
                $row[]     = $is_active;
                $row[]     = $publish_result;
                $row[]     = $subject_value->description;
                $row[]     = $share_btn . " " . $download_btn . " " . $assign . " " . $addquestion_btn . " " . $editbtn . " " . $question_list . " " . $deletebtn;
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
        $questionList = $this->onlineexam_model->getclosedexamlist();

        $subject_result = $this->subject_model->get();
        $subjectlist    = $subject_result;
        $questionOpt    = $this->customlib->getQuesOption();
        $questionOpt    = $questionOpt;
        $question_type  = $this->config->item('question_type');
        $question_level = $this->config->item('question_level');
        $classList      = $this->class_model->get();
        $m              = json_decode($questionList);
        $subject_names = array_column($this->subject_model->get(), 'name', 'id'); // the exam's subject under its title

        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $subject_value) {
                $assign          = '';
                $addquestion_btn = '';
                $editbtn         = '';
                $deletebtn       = '';
                $question_list   = '';
                $set_enable      = false;
                if ((($subject_value->publish_result == 0) && (strtotime($subject_value->exam_to) >= strtotime(date('Y-m-d H:i:s')))) && (($subject_value->auto_publish_date == "0000-00-00" || $subject_value->auto_publish_date == "" || $subject_value->auto_publish_date == null) || strtotime($subject_value->auto_publish_date) >= strtotime(date('Y-m-d H:i:s')))) {
                    $set_enable = true;
                }
                $title = "<a href='#' data-toggle='popover' class='detail_popover'>" . $subject_value->exam . "</a>";
                if (!empty($subject_value->subject_id) && isset($subject_names[(int) $subject_value->subject_id])) {
                    $title .= "<br><small class='text-muted'>" . html_escape($subject_names[(int) $subject_value->subject_id]) . "</small>";
                }

                if ($subject_value->description == "") {
                    $description = "<div class='fee_detail_popover' style='display: none'><p class='text text-danger'>" . $this->lang->line('no_description') . "</div></p>";
                } else {
                    $description = "<div class='fee_detail_popover' style='display: none'><p class='text text-danger'>" . $subject_value->description . "</div></p>";
                }
                if ($subject_value->is_quiz) {
                    $is_quiz = "<i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $is_quiz = "<i class='fa fa-exclamation-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                }
                $descriptive_ques = $subject_value->total_ques . "<br /><span>(" . $this->lang->line('descriptive') . ':' . $subject_value->total_descriptive_ques . ")</span>";
                if ($subject_value->is_active == 1) {
                    $is_active = "<i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $is_active = "<i class='fa fa-exclamation-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                }
                if ($subject_value->publish_result == 1) {
                    $publish_result = "<i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $publish_result = "<i class='fa fa-exclamation-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                }
                if ($this->rbac->hasPrivilege('online_assign_view_student', 'can_view') && $set_enable) {

                    $assign = "<a href=" . base_url() . 'admin/onlineexam/assign/' . $subject_value->id . " data-toggle='tooltip' class='btn btn-default btn-xs' title=" . $this->lang->line('assign_view') . "  ><i class='fa fa-tag'></i></a>";
                }
                if ($this->rbac->hasPrivilege('add_questions_in_exam', 'can_view')) {
                    $addquestion_btn = " <button type='button' class='btn btn-default btn-xs' data-recordid=" . $subject_value->id . " data-is_quiz=" . $subject_value->is_quiz . " data-subject_id='" . (int) $subject_value->subject_id . "' data-toggle='modal' data-target='#myQuestionModal' title='" . $this->lang->line('add_question') . "' ><i class='fa fa-question-circle'></i></button>";
                }
                if ($this->rbac->hasPrivilege('add_questions_in_exam', 'can_view')) {
                    $download_btn = " <button type='button' data-toggle='tooltip' class='btn btn-default btn-xs download_exam' data-recordid=" . $subject_value->id . "  title=" . $this->lang->line('print') . " data-loading-text='<i class=" . '" fa fa-spinner fa-spin"' . "  ></i>'><i class='fa fa fa-print'></i></button>";
                } else {
                    $download_btn = "";
                }
                if ($this->rbac->hasPrivilege('online_examination', 'can_edit')) {
                    $editbtn = " <button type='button' data-toggle='tooltip' class='btn btn-default btn-xs question-btn-edit' data-recordid=" . $subject_value->id . "  title='" . $this->lang->line('edit_exam') . "'  ><i class='fa fa fa-pencil'></i></button>";
                } else {
                    $editbtn = "";
                }
                if ($this->rbac->hasPrivilege('online_examination', 'can_delete')) {
                    $deletebtn = " <form method='post' action='" . site_url('admin/onlineexam/delete/' . (int) $subject_value->id) . "' style='display:inline' onsubmit='return confirm(" . htmlspecialchars(json_encode($this->lang->line('delete_confirm')), ENT_QUOTES) . ")'><button type='submit' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa fa-remove'></i></button></form>";
                } else {
                    $deletebtn = "";
                }

                if ($this->rbac->hasPrivilege('add_questions_in_exam', 'can_view')) {

                    $question_list = "<button type='button'class='btn btn-default btn-xs exam_ques_list' data-toggle='tooltip' data-recordid=" . $subject_value->id . "    data-loading-text='<i class=" . '" fa fa-spinner fa-spin"' . "  ></i>' title='" . $this->lang->line('exam_questions_list') . "' ><i class='fa fa-file-text-o'></i></button>" . " ";

                    $question_list .= "<a href=" . base_url() . 'admin/onlineexam/evalution/' . $subject_value->id . " class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('exam_evaluation') . "'>  <i class='fa fa-newspaper-o'></i></a>" . " ";
                    $question_list .= "<a href='" . site_url('admin/onlineexam/results/' . (int) $subject_value->id) . "' class='btn btn-default btn-xs' data-toggle='tooltip' title='Results'><i class='fa fa-bar-chart'></i></a> ";
                    $question_list .= "<a href='" . site_url('admin/onlineexam/invigilate/' . (int) $subject_value->id) . "' class='btn btn-default btn-xs' data-toggle='tooltip' title='Invigilate: live status, extra time'><i class='fa fa-eye'></i></a> ";
                    if ($this->rbac->hasPrivilege('online_examination', 'can_edit')) {
                        $auto_on = !empty($subject_value->marks_auto) && (string) $subject_value->marks_target !== '';
                        $question_list .= "<a href='" . site_url('admin/onlineexam/send_marks/' . (int) $subject_value->id) . "' class='btn btn-xs " . ($auto_on ? 'btn-success' : 'btn-default') . "' data-toggle='tooltip' title='" . ($auto_on ? 'Scores go to the report card automatically' : 'Send scores to report card') . "'><i class='fa fa-share-square-o'></i></a> ";
                    }

                    if ($subject_value->publish_result || ($subject_value->auto_publish_date != "0000-00-00" && $subject_value->auto_publish_date != "" && $subject_value->auto_publish_date != null && (strtotime($subject_value->auto_publish_date) <= strtotime(date('Y-m-d H:i:s'))))) {

                        $question_list .= "<button type='button' class='btn btn-default btn-xs generate_rank' data-exam-title=" . $subject_value->exam . " data-recordid=" . $subject_value->id . " data-toggle='tooltip' title='" . $this->lang->line('generate_rank') . "' ><i class='fa fa-list-alt'></i></button> ";
                    }
                }
                $deleteexambtn = "<input type='checkbox' name='exam[]'' value=" . $subject_value->id . ">";

                $row       = array();
                $row[]     = $deleteexambtn;
                $row[]     = $title . $description;
                $row[]     = $is_quiz;
                $row[]     = $descriptive_ques;
                $row[]     = $subject_value->attempt;
                $row[]     = $this->customlib->dateyyyymmddToDateTimeformat($subject_value->exam_from, false);
                $row[]     = $this->customlib->dateyyyymmddToDateTimeformat($subject_value->exam_to, false);
                $row[]     = $subject_value->duration;
                $row[]     = $is_active;
                $row[]     = $publish_result;
                $row[]     = $subject_value->description;
                $row[]     = $download_btn . " " . $assign . " " . $addquestion_btn . " " . $editbtn . " " . $question_list . " " . $deletebtn;
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
    public function evalution($id)
    {
        $this->session->set_userdata('top_menu', 'Online_Examinations');
        $this->session->set_userdata('sub_menu', 'Online_Examinations/Onlineexam');
        $data['id']         = $id;
        $data['title']      = 'student fees';
        $this->cbt_refresh($id);
        $class              = $this->class_model->get();
        $data['classlist']  = $class;
        $onlineexam         = $this->onlineexam_model->get($id);
        $data['onlineexam'] = $onlineexam;

        $onlineexam_questions         = $this->onlineexamquestion_model->getByExamNoLimit($id, 'descriptive');
        $data['onlineexam_questions'] = $onlineexam_questions;

        $data['sch_setting'] = $this->sch_setting_detail;
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $data['class_id']      = $this->input->post('class_id');
            $data['section_id']    = $this->input->post('section_id');
            $data['onlineexam_id'] = $this->input->post('onlineexam_id');
            $resultlist            = $this->onlineexam_model->searchOnlineExamStudents($data['class_id'], $data['section_id'], $data['onlineexam_id']);

            $data['resultlist'] = $resultlist;
        }
        $data['sch_setting'] = $this->sch_setting_detail;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/onlineexam/evalution', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getDescQues()
    {

        $pag_content    = '';
        $pag_navigation = '';

        if (isset($_POST['data']['page'])) {

            $page = $_POST['data']['page']; /* The page we are currently at */

            $max      = $_POST['data']['post_max']; /* Number of items to display per page */
            $cur_page = $page;
            $page -= 1;
            $per_page     = $max ? $max : 40;
            $previous_btn = true;
            $next_btn     = true;
            $first_btn    = true;
            $last_btn     = true;
            $start        = $page * $per_page;
            $count        = 0;

            $where_search = array();

            /* Check if there is a string inputted on the search box */
            if (!empty($_POST['data']['class_id'])) {
                $where_search['class_id'] = $_POST['data']['class_id'];
            }
            if (!empty($_POST['data']['section_id'])) {
                $where_search['section_id'] = $_POST['data']['section_id'];
            }
            if (!empty($_POST['data']['question_id'])) {
                $where_search['question_id'] = $_POST['data']['question_id'];
            }

            /* Retrieve all the posts */
            $all_items = $this->onlineexamresult_model->getDescriptionRecord($per_page, $start, $where_search, $_POST['data']['onlineexam_id']);

            /* Check if our query returns anything. */
            if ($all_items) {
                $result              = json_decode($all_items);
                $data['result']      = $result;
                $data['total_row']   = $result->total_row;
                $data['start']       = ($cur_page * $per_page) - $per_page + 1;
                $data['upto']        = ($result->total_row < ($cur_page * $per_page)) ? $result->total_row : ($cur_page * $per_page);
                $data['sch_setting'] = $this->sch_setting_detail;
                $pag_content         = $this->load->view('admin/onlineexam/_getDescQues', $data, true);

                /* If the query returns nothing, we throw an error message */
            } else {
                $pag_content = '';
            }
            $no_of_paginations = ceil($result->total_row / $per_page);

            if ($cur_page >= 7) {
                $start_loop = $cur_page - 3;
                if ($no_of_paginations > $cur_page + 3) {
                    $end_loop = $cur_page + 3;
                } else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
                    $start_loop = $no_of_paginations - 6;
                    $end_loop   = $no_of_paginations;
                } else {
                    $end_loop = $no_of_paginations;
                }
            } else {
                $start_loop = 1;
                if ($no_of_paginations > 7) {
                    $end_loop = 7;
                } else {
                    $end_loop = $no_of_paginations;
                }
            }

            $pag_navigation .= "<ul class='pagination'>";

            if ($first_btn && $cur_page > 1) {
                $pag_navigation .= "<li p='1' class='active_v'><a>" . $this->lang->line('first') . "</a></li>";
            } else if ($first_btn) {
                $pag_navigation .= "<li p='1' class='disabled'><a>" . $this->lang->line('first') . "</a></li>";
            }

            if ($previous_btn && $cur_page > 1) {
                $pre = $cur_page - 1;
                $pag_navigation .= "<li p='$pre' class='active_v'><a>" . $this->lang->line('previous') . "</a></li>";
            } else if ($previous_btn) {
                $pag_navigation .= "<li class='disabled'><a>" . $this->lang->line('previous') . "</a></li>";
            }
            for ($i = $start_loop; $i <= $end_loop; $i++) {

                if ($cur_page == $i) {
                    $pag_navigation .= "<li p='$i' class = 'active' ><a href='#'>{$i}</a></li>";
                } else {
                    $pag_navigation .= "<li p='$i' class='active_v'><a>{$i}</a></li>";
                }
            }

            if ($next_btn && $cur_page < $no_of_paginations) {
                $nex = $cur_page + 1;
                $pag_navigation .= "<li p='$nex' class='active_v'><a>" . $this->lang->line('next') . "</a></li>";
            } else if ($next_btn) {
                $pag_navigation .= "<li class='disabled'><a>" . $this->lang->line('next') . "</a></li>";
            }

            if ($last_btn && $cur_page < $no_of_paginations) {
                $pag_navigation .= "<li p='$no_of_paginations' class='active_v'><a>" . $this->lang->line('last') . "</a></li>";
            } else if ($last_btn) {
                $pag_navigation .= "<li p='$no_of_paginations' class='disabled'><a>" . $this->lang->line('last') . "</a></li>";
            }

            $pag_navigation = $pag_navigation . "</ul>";
        }

        $response = array(
            'content'    => $pag_content,
            'navigation' => $pag_navigation,
        );

        echo json_encode($response);

        exit();
    }

    public function assign($id)
    {
        if (!$this->rbac->hasPrivilege('online_assign_view_student', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Online_Examinations');
        $this->session->set_userdata('sub_menu', 'Online_Examinations/Onlineexam');
        $data['id']          = $id;
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $onlineexam          = $this->onlineexam_model->get($id);
        $data['onlineexam']  = $onlineexam;
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['class_summary'] = $onlineexam ? $this->class_summary((int) $id, $class) : array();
        $data['class_msg']     = $this->session->flashdata('class_msg');
        $data['assigned']      = $onlineexam ? $this->assigned_students((int) $id) : array();

        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/onlineexam/assign', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
                $this->load->view('layout/header', $data);
                $this->load->view('admin/onlineexam/assign', $data);
                $this->load->view('layout/footer', $data);
            } else {
                $data['class_id']      = $this->input->post('class_id');
                $data['section_id']    = $this->input->post('section_id');
                $data['onlineexam_id'] = $this->input->post('onlineexam_id');
                $resultlist            = $this->onlineexam_model->searchOnlineExamStudents($data['class_id'], $data['section_id'], $data['onlineexam_id']);
                $data['resultlist']    = $resultlist;
                $data['sch_setting']   = $this->sch_setting_detail;
                $this->load->view('layout/header', $data);
                $this->load->view('admin/onlineexam/assign', $data);
                $this->load->view('layout/footer', $data);
            }
        }
    }

    public function addstudent()
    {
        $this->form_validation->set_rules('onlineexam_id', $this->lang->line('exam_id'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'onlineexam_id' => form_error('onlineexam_id'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {

            $array_insert  = array();
            $array_delete  = array();
            $class_id      = $this->input->post('post_class_id');
            $section_id    = $this->input->post('post_section_id');
            $onlineexam_id = $this->input->post('onlineexam_id');
            $resultlist    = $this->onlineexam_model->searchOnlineExamStudents($class_id, $section_id, $onlineexam_id);
            $all_students  = array();
            if (!empty($resultlist)) {

                foreach ($resultlist as $each_student_key => $each_student_value) {
                    if ($each_student_value['onlineexam_student_session_id'] != 0) {
                        $all_students[] = $each_student_value['onlineexam_student_session_id'];
                    }
                }
            }

            $students_id = $this->input->post('students_id');
            $students    = array();
            if (!isset($students_id)) {
                $students_id = array();
            }
            if (!empty($all_students)) {
                $array_delete = array_diff($all_students, $students_id);
            }
            if (!empty($students_id)) {
                $student_session_array = array();
                foreach ($students_id as $student_key => $student_value) {
                    $student_session_array[] = $student_value;
                }

                $student_array = array_diff($student_session_array, $all_students);
                if (!empty($student_array)) {
                    foreach ($student_array as $insert_key => $insert_value) {
                        $array_insert[] = array(
                            'onlineexam_id'      => $onlineexam_id,
                            'student_session_id' => $insert_value,
                        );
                    }
                }
            }

            $this->onlineexam_model->addStudents($array_insert, $array_delete, $onlineexam_id);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    /**
     * Whole classes at once (every section): POST onlineexam_id, class_ids[] assigns all the
     * classes' active students of this session not yet assigned; do=remove with class_id takes
     * a class off again, except students who have started or sat the exam.
     */
    public function assign_classes()
    {
        $exam_id = (int) $this->input->post('onlineexam_id');
        $exam = $exam_id ? $this->db->select('id')->where('id', $exam_id)->get('onlineexam')->row() : null;
        if (!$exam) {
            show_404();
        }
        $allowed = array_map('intval', array_column($this->class_model->get(), 'id'));
        $names = array_column($this->class_model->get(), 'class', 'id');
        $session = (int) $this->setting_model->getCurrentSession();

        if ($this->input->post('do') === 'remove_student') {
            $os_id = (int) $this->input->post('onlineexam_student_id');
            $row = $this->db->select('id, is_attempted')->where(array('id' => $os_id, 'onlineexam_id' => $exam_id))->get('onlineexam_students')->row_array();
            $started = $row ? $this->db->where('onlineexam_student_id', $os_id)->count_all_results('onlineexam_attempts') : 0;
            if (!$row) {
                $msg = array('type' => 'danger', 'text' => 'That student is not on this exam.');
            } elseif ((int) $row['is_attempted'] === 1 || $started) {
                $msg = array('type' => 'danger', 'text' => 'This student has started or sat the exam, so they stay on it.');
            } else {
                $this->db->where(array('id' => $os_id, 'onlineexam_id' => $exam_id))->delete('onlineexam_students');
                $msg = array('type' => 'success', 'text' => 'Student taken off this exam.');
            }
            $this->session->set_flashdata('class_msg', $msg);
            redirect('admin/onlineexam/assign/' . $exam_id . '#assigned_box');
        }
        if ($this->input->post('do') === 'remove') {
            $class_id = (int) $this->input->post('class_id');
            if (!in_array($class_id, $allowed, true)) {
                access_denied();
            }
            $removed = $this->unassign_class_students($exam_id, $class_id);
            $msg = $removed['removed'] . ' student(s) of ' . (isset($names[$class_id]) ? $names[$class_id] : 'the class') . ' taken off this exam.'
                . ($removed['kept'] ? ' ' . $removed['kept'] . ' who started or sat it were kept.' : '');
            $this->session->set_flashdata('class_msg', array('type' => 'success', 'text' => $msg));
            redirect('admin/onlineexam/assign/' . $exam_id);
        }

        $class_ids = array_values(array_intersect(array_map('intval', (array) $this->input->post('class_ids')), $allowed));
        if (!$class_ids) {
            $this->session->set_flashdata('class_msg', array('type' => 'danger', 'text' => 'Tick at least one class.'));
            redirect('admin/onlineexam/assign/' . $exam_id);
        }
        $new = $this->db->select('ss.id')->from('student_session ss')
            ->join('students s', 's.id = ss.student_id')
            ->join('onlineexam_students oes', 'oes.student_session_id = ss.id AND oes.onlineexam_id = ' . $exam_id, 'left')
            ->where('ss.session_id', $session)->where('s.is_active', 'yes')->where_in('ss.class_id', $class_ids)
            ->where('oes.id IS NULL', null, false)->get()->result_array();
        $rows = array();
        foreach (array_unique(array_map('intval', array_column($new, 'id'))) as $ss_id) {
            $rows[] = array('onlineexam_id' => $exam_id, 'student_session_id' => $ss_id);
        }
        if ($rows) {
            $this->db->insert_batch('onlineexam_students', $rows);
        }
        $labels = array();
        foreach ($class_ids as $c) {
            $labels[] = isset($names[$c]) ? $names[$c] : $c;
        }
        $this->session->set_flashdata('class_msg', array(
            'type' => 'success',
            'text' => count($rows) . ' student(s) assigned from ' . implode(', ', $labels) . ' (all sections).' . (count($rows) ? '' : ' They were already assigned.')
        ));
        redirect('admin/onlineexam/assign/' . $exam_id);
    }

    /**
     * Everyone assigned to the exam (the classes and sections this staff member may see), sorted by
     * class, section and name, with where each one is: not started, in progress or submitted.
     */
    private function assigned_students($exam_id)
    {
        $rows = $this->onlineexam_model->searchAllOnlineExamStudents($exam_id);
        $ids = array_map('intval', array_column($rows, 'onlineexam_student_id'));
        $started = $ids ? array_map('intval', array_column($this->db->select('onlineexam_student_id')->distinct()
            ->where_in('onlineexam_student_id', $ids)->get('onlineexam_attempts')->result_array(), 'onlineexam_student_id')) : array();
        foreach ($rows as &$r) {
            $r['status'] = (int) $r['is_attempted'] === 1 ? 'submitted' : (in_array((int) $r['onlineexam_student_id'], $started, true) ? 'started' : 'not_started');
        }
        unset($r);
        usort($rows, function ($a, $b) {
            return array((int) $a['class_id'], $a['section'], $a['firstname'], $a['lastname']) <=> array((int) $b['class_id'], $b['section'], $b['firstname'], $b['lastname']);
        });
        return $rows;
    }

    /** Per class of this session: active students and how many are assigned to the exam. */
    private function class_summary($exam_id, array $classlist)
    {
        $ids = array_map('intval', array_column($classlist, 'id'));
        if (!$ids) {
            return array();
        }
        $session = (int) $this->setting_model->getCurrentSession(); // before the query: it runs one of its own
        $rows = $this->db->select('ss.class_id, COUNT(*) AS total, SUM(CASE WHEN oes.id IS NULL THEN 0 ELSE 1 END) AS assigned', false)
            ->from('student_session ss')
            ->join('students s', 's.id = ss.student_id')
            ->join('onlineexam_students oes', 'oes.student_session_id = ss.id AND oes.onlineexam_id = ' . (int) $exam_id, 'left')
            ->where('ss.session_id', $session)->where('s.is_active', 'yes')
            ->where_in('ss.class_id', $ids)->group_by('ss.class_id')->get()->result_array();
        $out = array();
        foreach ($rows as $r) {
            $out[(int) $r['class_id']] = array('total' => (int) $r['total'], 'assigned' => (int) $r['assigned']);
        }
        return $out;
    }

    /** Class ids that already have at least one student on this exam (current session). */
    private function assigned_class_ids($exam_id)
    {
        $session = (int) $this->setting_model->getCurrentSession();
        $rows = $this->db->select('ss.class_id')->from('onlineexam_students oes')
            ->join('student_session ss', 'ss.id = oes.student_session_id')
            ->where('oes.onlineexam_id', (int) $exam_id)
            ->where('ss.session_id', $session)
            ->group_by('ss.class_id')->get()->result_array();
        return array_map('intval', array_column($rows, 'class_id'));
    }

    /**
     * Takes a class off an exam, except students who have started or sat it.
     * Returns how many were removed and how many were kept.
     */
    private function unassign_class_students($exam_id, $class_id)
    {
        $exam_id = (int) $exam_id;
        $class_id = (int) $class_id;
        $session = (int) $this->setting_model->getCurrentSession();
        $rows = $this->db->select('oes.id, oes.is_attempted')->from('onlineexam_students oes')
            ->join('student_session ss', 'ss.id = oes.student_session_id')
            ->where(array('oes.onlineexam_id' => $exam_id, 'ss.class_id' => $class_id, 'ss.session_id' => $session))
            ->get()->result_array();
        $ids = array();
        $kept = 0;
        $started = $rows ? array_map('intval', array_column($this->db->select('onlineexam_student_id')
            ->where_in('onlineexam_student_id', array_map('intval', array_column($rows, 'id')))->get('onlineexam_attempts')->result_array(), 'onlineexam_student_id')) : array();
        foreach ($rows as $r) {
            if ((int) $r['is_attempted'] === 1 || in_array((int) $r['id'], $started, true)) {
                $kept++;
            } else {
                $ids[] = (int) $r['id'];
            }
        }
        if ($ids) {
            $this->db->where('onlineexam_id', $exam_id)->where_in('id', $ids)->delete('onlineexam_students');
        }
        return array('removed' => count($ids), 'kept' => $kept);
    }

    public function getOnlineExamByID()
    {
        $id = (int) $this->input->post('recordid');
        $question_result = $this->onlineexam_model->get($id);
        if ($question_result) {
            $question_result->class_ids = $this->assigned_class_ids($id);
        }
        echo json_encode(array('status' => 1, 'result' => $question_result));
    }

    public function searchQuestionByExamID()
    {
        $recordsTotal_flter = "";
        $userdata           = $this->customlib->getUserData();
        $role_id            = $userdata["role_id"];
        $data               = array();
        $pag_content        = '';
        $pag_navigation     = '';
        $is_quiz            = $this->input->post('is_quiz');
        $page               = $this->input->post('page');
        $exam_id            = $this->input->post('exam_id');
        $keyword            = $this->input->post('keyword');
        $question_type      = $this->input->post('question_type');
        $question_level     = $this->input->post('question_level');
        $class_id           = $this->input->post('class_id');
        $section_id         = $this->input->post('section_id');

        if (isset($page)) {
            $max      = 100;
            $cur_page = $page;
            $page -= 1;
            $per_page      = $max ? $max : 40;
            $previous_btn  = true;
            $next_btn      = true;
            $first_btn     = true;
            $last_btn      = true;
            $start         = $page * $per_page;
            $where_search  = array();
            $show_from     = ($cur_page * $max) - ($max - 1);
            $show_to       = $cur_page * $max;
            $total_display = 0;

            /* Check if there is a string inputted on the search box */
            if (($_POST['search'] != "") ||
                ($_POST['keyword'] != "") ||
                ($_POST['question_level'] != "") ||
                ($_POST['question_type'] != "") ||
                ($_POST['class_id'] != "") ||
                ($_POST['section_id'] != "")
            ) {
                $search                         = $this->input->post('search');
                $where_search['subject']        = $search;
                $where_search['keyword']        = $keyword;
                $where_search['question_level'] = $question_level;
                $where_search['question_type']  = $question_type;
                $where_search['class_id']       = $class_id;
                $where_search['section_id']     = $section_id;
            }
            $where_search['is_quiz'] = $is_quiz;
            $data['question_type']   = $this->config->item('question_type');
            $data['question_level']  = $this->config->item('question_level');
            $questionList            = $this->onlineexamquestion_model->getByExamID($exam_id, $per_page, $start, $where_search);

            $dt_data = array();
            if ($role_id == 2) {
                foreach ($questionList as $questionList_key => $questionList_value) {

                    $my_section = array();

                    if ($this->sch_setting_detail->class_teacher == 'yes' && $this->sch_setting_detail->my_question == '1') {

                        $my_class = $this->class_model->get();
                        foreach ($my_class as $class_key => $class_value) {

                            $section_id = $this->teacher_model->get_teacherrestricted_modesections($this->customlib->getStaffID(), $class_value['id']);

                            foreach ($section_id as $section_idkey => $section_idvalue) {
                                $my_section[] = $section_idvalue['section_id'];
                            }

                            if (in_array($questionList_value->section_id, $my_section, true) && $class_value['id'] == $questionList_value->class_id) {

                                $dt_data[]          = $questionList_value;
                                $recordsTotal_flter = count($dt_data);
                            }
                        }
                    } elseif ($this->sch_setting_detail->class_teacher == 'yes' && $this->sch_setting_detail->my_question == '0') {

                        $my_class = $this->class_model->get();
                        foreach ($my_class as $class_key => $class_value) {

                            $section_id = $this->teacher_model->get_teacherrestricted_modesections($this->customlib->getStaffID(), $class_value['id']);
                            foreach ($section_id as $section_idkey => $section_idvalue) {
                                $my_section[] = $section_idvalue['section_id'];
                            }

                            if (in_array($questionList_value->section_id, $my_section, true) && $class_value['id'] == $questionList_value->class_id) {
                                $dt_data[]          = $questionList_value;
                                $recordsTotal_flter = count($dt_data);
                            }
                        }
                    } elseif ($this->sch_setting_detail->class_teacher == 'no' && $this->sch_setting_detail->my_question == '1') {

                        if ($this->customlib->getStaffID() == $questionList_value->staff_id) {
                            $dt_data[]          = $questionList_value;
                            $recordsTotal_flter = count($dt_data);
                        }
                    } else {

                        $dt_data[]          = $questionList_value;
                        $recordsTotal_flter = count($dt_data);
                    }
                }
                $data['questionList'] = $dt_data;
            } else {
                $data['questionList'] = $questionList;
                $recordsTotal_flter   = count($questionList);
            }

            $count = $this->onlineexamquestion_model->getCountByExamID($exam_id, $where_search);

            $total_display = $recordsTotal_flter;
            /* Check if our query returns anything. */
            if ($count) {
                $pag_content = $this->load->view('admin/onlineexam/_searchQuestionByExamID', $data, true);
                /* If the query returns nothing, we throw an error message */
            }

            $no_of_paginations = ceil($count / $per_page);

            if ($cur_page >= 7) {
                $start_loop = $cur_page - 3;
                if ($no_of_paginations > $cur_page + 3) {
                    $end_loop = $cur_page + 3;
                } else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
                    $start_loop = $no_of_paginations - 6;
                    $end_loop   = $no_of_paginations;
                } else {
                    $end_loop = $no_of_paginations;
                }
            } else {
                $start_loop = 1;
                if ($no_of_paginations > 7) {
                    $end_loop = 7;
                } else {
                    $end_loop = $no_of_paginations;
                }
            }

            $pag_navigation .= "<ul class='pagination pull-right'>";

            if ($first_btn && $cur_page > 1) {
                $pag_navigation .= "<li p='1' class='activee'><a href='#'><i class='fa fa-angle-double-left'></i></a></li>";
            } else if ($first_btn) {

                $pag_navigation .= "<li p='1' class='disabled'><a href='#'><i class='fa fa-angle-double-left'></i></a></li>";
            }

            if ($previous_btn && $cur_page > 1) {
                $pre = $cur_page - 1;
                $pag_navigation .= "<li p='$pre' class='activee'><a href='#'><i class='fa fa-angle-left'></i></a></li>";
            } else if ($previous_btn) {
                $pag_navigation .= "<li  class='disabled'><a href='#'><i class='fa fa-angle-left'></i></a></li>";
            }

            for ($i = $start_loop; $i <= $end_loop; $i++) {
                if ($cur_page == $i) {
                    $pag_navigation .= "<li p='$i' class='active'><a href='#'>{$i}</a></li>";
                } else {
                    $pag_navigation .= "<li p='$i'  class='activee'><a href='#'>{$i}</a></li>";
                }
            }

            if ($next_btn && $cur_page < $no_of_paginations) {
                $nex = $cur_page + 1;
                $pag_navigation .= "<li p='$nex' class='activee'><a href='#'><i class='fa fa-angle-right'></i></a></li>";
            } else if ($next_btn) {
                $pag_navigation .= "<li class='disabled'><a href='#'><i class='fa fa-angle-right'></i></a></li>";
            }

            if ($last_btn && $cur_page < $no_of_paginations) {
                $pag_navigation .= "<li p='$no_of_paginations'  class='activee'><a href='#'><i class='fa fa-angle-double-right'></i></a></li>";
            } else if ($last_btn) {
                $pag_navigation .= "<li p='$no_of_paginations' class='disabled'><a href='#'><i class='fa fa-angle-double-right'></i></a></li>";
            }

            $pag_navigation = $pag_navigation . "</ul>";
        }

        $response = array(
            'content'       => $pag_content,
            'navigation'    => $pag_navigation,
            'show_from'     => ($total_display <= 0) ? 0 : $show_from,
            'show_to'       => ($show_to > $total_display) ? $total_display : $show_to,
            'total_display' => $total_display,
        );

        echo json_encode($response);
    }

    /** Refuses the request unless the staff member has one of the permissions its method needs (PERMISSIONS). */
    private function require_permission()
    {
        $method = strtolower($this->router->fetch_method());
        if (!array_key_exists($method, self::PERMISSIONS)) {
            show_404(); // form-validation callbacks and anything not listed are not pages
        }
        $needs = self::PERMISSIONS[$method];
        if ($method === 'add') {
            $needs = array(array('online_examination', $this->input->post('recordid') ? 'can_edit' : 'can_add'));
        }
        foreach ($needs as $need) {
            if ($this->rbac->hasPrivilege($need[0], $need[1])) {
                return;
            }
        }
        access_denied();
    }

    /**
     * Before results are shown: attempts whose time ran out are closed (submitted), and students
     * who submitted without a stored score (before scores were stored) get one.
     */
    private function cbt_refresh($exam_id)
    {
        $this->load->library(array('Cbt_attempt', 'Cbt_scoring'));
        $this->cbt_attempt->close_expired((int) $exam_id);
        $ids = array_column($this->db->select('id')->where(array('onlineexam_id' => (int) $exam_id, 'is_attempted' => 1))
            ->where('scored_at IS NULL', null, false)->get('onlineexam_students')->result_array(), 'id');
        foreach ($ids as $id) {
            $this->cbt_scoring->score((int) $id);
        }
    }

    /** One exam's results: who sat it, scores, what each student missed, and how each question went (Cbt_report). */
    public function results($id = null)
    {
        $this->cbt_refresh((int) $id);
        $this->load->library('Cbt_report');
        $report = $this->cbt_report->build((int) $id);
        if (!$report) {
            show_404();
        }
        $this->session->set_userdata('top_menu', 'Online_Examinations');
        $this->session->set_userdata('sub_menu', 'Online_Examinations/Onlineexam');
        $data = array('report' => $report, 'can_send' => $this->rbac->hasPrivilege('online_examination', 'can_edit'));
        $this->load->view('layout/header', $data);
        $this->load->view('admin/onlineexam/results', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * POST exam_id, subject_id, class_id (optional), levels[level] = how many, marks, neg_marks:
     * adds that many random questions of each level from the bank (not already in the exam).
     */
    public function random_draw()
    {
        $exam = $this->db->where('id', (int) $this->input->post('exam_id'))->get('onlineexam')->row_array();
        $subject_id = (int) $this->input->post('subject_id');
        $marks = (float) $this->input->post('marks');
        $neg = max(0, (float) $this->input->post('neg_marks'));
        $out = function ($status, $message) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => $status, 'message' => $message)));
        };
        if (!$exam || !$subject_id || $marks <= 0) {
            return $out('fail', !$exam ? 'That exam no longer exists.' : ($subject_id ? 'Marks for each question must be more than 0.' : 'Choose the subject.'));
        }
        $levels = array_keys((array) $this->config->item('question_level'));
        $in_exam = array_map('intval', array_column($this->db->select('question_id')->where('onlineexam_id', (int) $exam['id'])->get('onlineexam_questions')->result_array(), 'question_id'));
        $added = 0;
        $short = array();
        $this->db->trans_start();
        foreach ((array) $this->input->post('levels') as $level => $n) {
            $n = max(0, min(200, (int) $n));
            if (!$n || !in_array($level, $levels, true)) {
                continue;
            }
            $this->db->select('id')->from('questions')->where(array('subject_id' => $subject_id, 'level' => $level));
            if ((int) $this->input->post('class_id')) {
                $this->db->where('class_id', (int) $this->input->post('class_id'));
            }
            if ($in_exam) {
                $this->db->where_not_in('id', $in_exam);
            }
            if (!empty($exam['is_quiz'])) {
                $this->db->where('question_type !=', 'descriptive');
            }
            $ids = array_map('intval', array_column($this->db->order_by('RAND()', '', false)->limit($n)->get()->result_array(), 'id'));
            foreach ($ids as $qid) {
                $this->db->insert('onlineexam_questions', array(
                    'onlineexam_id' => (int) $exam['id'],
                    'question_id' => $qid,
                    'marks' => $marks,
                    'neg_marks' => $neg,
                    'session_id' => $exam['session_id'],
                    'is_active' => 1
                ));
                $in_exam[] = $qid;
                $added++;
            }
            if (count($ids) < $n) {
                $short[] = ucfirst($level) . ': only ' . count($ids) . ' of ' . $n . ' available';
            }
        }
        $this->db->trans_complete();
        $msg = $added . ' question(s) added to the exam.' . ($short ? ' ' . implode('; ', $short) . '.' : '');
        return $out($added ? 'success' : 'fail', $added ? $msg : 'No questions found. ' . implode('; ', $short));
    }

    /**
     * Invigilation: every student of the exam with the state of their attempt (not started, in
     * progress with time left, submitted), how often they left the exam page, and extra time given.
     */
    public function invigilate($id = null)
    {
        $this->cbt_refresh((int) $id);
        $exam = $this->db->where('id', (int) $id)->get('onlineexam')->row_array();
        if (!$exam) {
            show_404();
        }
        $rows = $this->db->select("os.id AS os_id, st.admission_no, CONCAT_WS(' ', st.firstname, st.middlename, st.lastname) AS name, c.class, s.section,
                a.id AS attempt_id, a.started_at, a.ends_at, a.submitted_at, a.tab_switches, a.extra_minutes, a.extra_reason, a.forced_submit, os.percentage,
                a.ip, a.user_agent, a.device_key", false)
            ->from('onlineexam_students os')
            ->join('student_session ss', 'ss.id = os.student_session_id')
            ->join('students st', 'st.id = ss.student_id')
            ->join('classes c', 'c.id = ss.class_id', 'left')->join('sections s', 's.id = ss.section_id', 'left')
            ->join('onlineexam_attempts a', 'a.id = (SELECT MAX(a2.id) FROM onlineexam_attempts a2 WHERE a2.onlineexam_student_id = os.id)', 'left', false) // tenancy-lint: ok correlated to os
            ->where('os.onlineexam_id', (int) $id)->order_by('c.class')->order_by('s.section')->order_by('st.firstname')->get()->result_array();
        $this->session->set_userdata('top_menu', 'Online_Examinations');
        $this->session->set_userdata('sub_menu', 'Online_Examinations/Onlineexam');
        $photos = array();
        if ($rows) {
            foreach (
                $this->db->select('onlineexam_student_id AS os, COUNT(*) AS n, MAX(id) AS last', false)->where_in('onlineexam_student_id', array_map('intval', array_column($rows, 'os_id')))
                    ->group_by('onlineexam_student_id')->get('onlineexam_snapshots')->result_array() as $p
            ) {
                $photos[(int) $p['os']] = array((int) $p['n'], (int) $p['last']);
            }
        }
        $data = array('exam' => $exam, 'rows' => $rows, 'photos' => $photos, 'can_edit' => $this->rbac->hasPrivilege('online_examination', 'can_edit'));
        $this->load->view('layout/header', $data);
        $this->load->view('admin/onlineexam/invigilate', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * POST os_id, minutes, reason: more time for one student (technical problem, power cut...). A
     * running attempt gets later deadline; a submitted one is opened again for that many minutes
     * (answers kept). The reason and who gave it are kept on the attempt.
     */
    public function extra_time()
    {
        $os_id = (int) $this->input->post('os_id');
        $minutes = (int) $this->input->post('minutes');
        $reason = trim((string) $this->input->post('reason'));
        $os = $this->db->where('id', $os_id)->get('onlineexam_students')->row_array();
        $back = 'admin/onlineexam/invigilate/' . ($os ? (int) $os['onlineexam_id'] : 0);
        $attempt = $os ? $this->db->where('onlineexam_student_id', $os_id)->order_by('id', 'desc')->limit(1)->get('onlineexam_attempts')->row_array() : null;
        if (!$attempt || $minutes < 1 || $minutes > 300 || $reason === '') {
            $this->session->set_flashdata('inv_msg', array('danger', !$attempt ? 'That student has not started the exam: no extra time is needed.' : 'Give 1 to 300 minutes and a reason.'));
            redirect($back);
        }
        $now = time();
        $set = array(
            'extra_minutes' => (int) $attempt['extra_minutes'] + $minutes,
            'extra_by' => (int) $this->customlib->getStaffID() ?: null,
            'extra_reason' => mb_substr(trim(($attempt['extra_reason'] ? $attempt['extra_reason'] . ' | ' : '') . $minutes . ' min: ' . $reason), 0, 255)
        );
        if ($attempt['submitted_at']) {
            $set['submitted_at'] = null;
            $set['forced_submit'] = null;
            $set['ends_at'] = date('Y-m-d H:i:s', $now + $minutes * 60);
            $msg = 'The exam is open again for this student for ' . $minutes . ' minutes. Their answers are kept; they continue from the exam page.';
        } else {
            $set['ends_at'] = date('Y-m-d H:i:s', max($now, strtotime($attempt['ends_at'])) + $minutes * 60);
            $msg = $minutes . " minutes added. The student's timer updates within a minute.";
        }
        $this->db->where('id', (int) $attempt['id'])->update('onlineexam_attempts', $set);
        $this->session->set_flashdata('inv_msg', array('success', $msg));
        redirect($back);
    }

    /** POST os_id: the student's running exam moves to whichever device opens it next (theirs stopped working). */
    public function release_device()
    {
        if (!$this->rbac->hasPrivilege('online_examination', 'can_edit')) {
            access_denied();
        }
        $os = $this->db->where('id', (int) $this->input->post('os_id'))->get('onlineexam_students')->row_array();
        $ok = $os && $this->cbt_integrity->release((int) $os['id']);
        $this->session->set_flashdata('inv_msg', $ok ? array('success', 'Done: the student can now continue on another device, with the time they have left.') : array('danger', 'That student has not started the exam.'));
        redirect('admin/onlineexam/invigilate/' . ($os ? (int) $os['onlineexam_id'] : 0));
    }

    /** One student's webcam photos during the exam. */
    public function photos($os_id = null)
    {
        $os = $this->db->where('id', (int) $os_id)->get('onlineexam_students')->row_array();
        $exam = $os ? $this->db->where('id', (int) $os['onlineexam_id'])->get('onlineexam')->row_array() : null;
        if (!$exam) {
            show_404();
        }
        $student = $this->db->select("CONCAT_WS(' ', st.firstname, st.middlename, st.lastname) AS name, st.admission_no", false)->from('student_session ss')
            ->join('students st', 'st.id = ss.student_id')->where('ss.id', (int) $os['student_session_id'])->get()->row_array();
        $this->session->set_userdata('top_menu', 'Online_Examinations');
        $this->session->set_userdata('sub_menu', 'Online_Examinations/Onlineexam');
        $data = array('exam' => $exam, 'student' => $student, 'shots' => $this->cbt_integrity->snapshots((int) $os['id']));
        $this->load->view('layout/header', $data);
        $this->load->view('admin/onlineexam/photos', $data);
        $this->load->view('layout/footer', $data);
    }

    /** One webcam photo (staff only: this controller needs online_examination). */
    public function photo($id = null)
    {
        $path = $this->cbt_integrity->snapshot_file((int) $id);
        if (!$path) {
            show_404();
        }
        $this->output->set_content_type('image/jpeg')->set_header('Cache-Control: private, max-age=86400')->set_output(file_get_contents($path));
    }

    /** The Results page as an Excel file: a Students sheet's rows (one per student) and the questions below them. */
    public function results_export($id = null)
    {
        $this->cbt_refresh((int) $id);
        $this->load->library(array('Cbt_report', 'Import_sheet'));
        $r = $this->cbt_report->build((int) $id);
        if (!$r) {
            show_404();
        }
        $status = array('done' => 'Submitted', 'pending' => 'Waiting for marking', 'not_taken' => 'Not taken');
        $rows = array();
        foreach ($r['students'] as $st) {
            $done = $st['status'] !== 'not_taken';
            $rows[] = array(
                $st['admission_no'],
                $st['name'],
                trim($st['class'] . ' ' . $st['section']),
                $status[$st['status']],
                $done ? (float) $st['score'] . ' / ' . (float) $st['max_score'] : '',
                $done && $st['percentage'] !== null ? (float) $st['percentage'] . '%' : '',
                $st['status'] === 'done' && $st['passed'] !== null ? ((int) $st['passed'] ? 'Pass' : 'Fail') : '',
                $done ? (int) $st['correct_count'] : '',
                $done ? (int) $st['wrong_count'] : '',
                $done ? (int) $st['unanswered_count'] : '',
                implode(', ', array_map(function ($m) {
                    return 'Q' . $m['no'];
                }, $st['missed']))
            );
        }
        $rows[] = array();
        $rows[] = array('Question', 'Text', 'Type', 'Right answer', 'Got it right', 'Got it wrong', 'Left it out', '% right', 'Wrong answer chosen most');
        foreach ($r['questions'] as $q) {
            $rows[] = array(
                'Q' . $q['no'],
                $q['text'],
                $q['question_type'],
                $q['answer_text'],
                $q['stats']['correct'],
                $q['stats']['wrong'],
                $q['stats']['unanswered'],
                $q['stats']['correct_pct'] === null ? '' : $q['stats']['correct_pct'] . '%',
                $q['stats']['top_wrong'] !== '' ? $q['stats']['top_wrong'] . ' (' . $q['stats']['top_wrong_count'] . ')' : ''
            );
        }
        $headings = array('Admission no', 'Student', 'Class', 'Status', 'Score', '%', 'Result', 'Right', 'Wrong', 'Left out', 'Questions missed');
        $first = $rows ? array_shift($rows) : array();
        $this->load->helper('download');
        force_download(preg_replace('/[^A-Za-z0-9 _-]+/', '', $r['exam']['exam']) . ' results.xlsx', Import_sheet::xlsx($headings, $first, $rows));
    }
    /** Send the CBT scores to a subject's marks in Examinations (the report card). GET: the form; POST: send. */
    public function send_marks($id = null)
    {
        if (!$this->rbac->hasPrivilege('online_examination', 'can_edit')) {
            access_denied();
        }
        $exam = $this->db->where('id', (int) $id)->get('onlineexam')->row_array();
        if (!$exam) {
            show_404();
        }
        $this->cbt_refresh($exam['id']);
        $this->load->library('Cbt_scoring');
        $data = array('exam' => $exam, 'result' => null, 'error' => null);
        if ($this->input->method() === 'post') {
            // "cbse:<timetable>:<type>" = a CBSE Examination assessment; a number = an Examinations subject.
            $target = (string) $this->input->post('target_id');
            $scale = $this->input->post('mode') !== 'raw';
            $replace = (bool) $this->input->post('replace');
            $auto = (bool) $this->input->post('auto');
            try {
                $data['result'] = strpos($target, 'cbse:') === 0
                    ? $this->cbt_scoring->send_to_cbse($exam['id'], $target, $scale, $replace)
                    : $this->cbt_scoring->send_to_report_card($exam['id'], (int) $target, $scale, $replace);
                // Remembered for the automatic sending (Cbt_scoring::auto_send) as well.
                $settings = array('marks_auto' => $auto ? 1 : 0, 'marks_scale' => $scale ? 1 : 0, 'marks_replace' => $replace ? 1 : 0);
                $this->db->where('id', (int) $exam['id'])->update('onlineexam', $settings);
                $exam = array_merge($exam, $settings, array('marks_target' => $target));
                $data['exam'] = $exam;
            } catch (RuntimeException $e) {
                $data['error'] = $e->getMessage();
            }
        } elseif ($this->input->get('auto') === 'off') {
            $this->db->where('id', (int) $exam['id'])->update('onlineexam', array('marks_auto' => 0));
            $exam['marks_auto'] = 0;
            $data['exam'] = $exam;
            $data['auto_off'] = true;
        }
        $session = $this->setting_model->getCurrentSession();
        $data['cbse_targets'] = $this->cbt_scoring->cbse_targets($session);
        $data['targets'] = $this->cbt_scoring->report_card_targets($session);
        $data['summary'] = $this->db->select('COUNT(*) AS assigned, SUM(is_attempted = 1) AS sat, SUM(pending_count > 0) AS pending, MAX(max_score) AS max_score', false)
            ->where('onlineexam_id', (int) $exam['id'])->get('onlineexam_students')->row_array();
        $this->session->set_userdata('top_menu', 'Online_Examinations');
        $this->session->set_userdata('sub_menu', 'Online_Examinations/Onlineexam');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/onlineexam/send_marks', $data);
        $this->load->view('layout/footer', $data);
    }

    public function rankgenerate()
    {
        $examid                 = $this->input->post('examid');
        $this->cbt_refresh($examid);
        $data['stored_scores']  = array();
        foreach (
            $this->db->select('id, score')->where('onlineexam_id', (int) $examid)->where('scored_at IS NOT NULL', null, false)
                ->get('onlineexam_students')->result_array() as $row
        ) {
            $data['stored_scores'][(int) $row['id']] = (float) $row['score'];
        }
        $student_data           = $this->onlineexam_model->searchAllOnlineExamStudents($examid, null, null, 1);
        $student_question_array = array();
        if (!empty($student_data)) {
            foreach ($student_data as $student_key => $student_value) {
                $student_question_array[$student_value['onlineexam_student_id']] = $this->onlineexamresult_model->getResultByStudent($student_value['onlineexam_student_id'], $examid);
            }
        }
        $data['onlineexam']             = $this->onlineexam_model->get($examid);
        $data['student_question_array'] = $student_question_array;
        $data['examid']                 = $examid;
        $data['student_data']           = $student_data;
        $data['sch_setting']            = $this->sch_setting_detail;
        $page                           = $this->load->view('admin/onlineexam/_rankgenerate', $data, true);
        $array                          = array('status' => 1, 'page' => $page, 'examid' => $examid, 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
    }

    public function add()
    {
        $this->form_validation->set_rules('word_limit', $this->lang->line('answer_word_limit'), 'trim|xss_clean|callback_validate_word_limit');
        $this->form_validation->set_rules('exam', $this->lang->line('exam_title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('attempt', $this->lang->line('attempt'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_from', $this->lang->line('exam_from'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_to', $this->lang->line('exam_to'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('duration', $this->lang->line('time_duration'), 'trim|required|callback_validate_duration');
        $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required');
        $this->form_validation->set_rules('passing_percentage', $this->lang->line('passing_percentage'), 'trim|required|xss_clean');
        // every CBT has a subject (migration 317)
        $this->form_validation->set_rules('subject_id', $this->lang->line('subject'), 'trim|required|integer|greater_than[0]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'word_limit'         => form_error('word_limit'),
                'exam'               => form_error('exam'),
                'subject_id'         => form_error('subject_id'),
                'attempt'            => form_error('attempt'),
                'exam_from'          => form_error('exam_from'),
                'duration'           => form_error('duration'),
                'exam_to'            => form_error('exam_to'),
                'description'        => form_error('description'),
                'passing_percentage' => form_error('passing_percentage'),
            );

            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {
            $is_active          = 0;
            $publish_result     = 0;
            $is_marks_display   = 0;
            $is_neg_marking     = 0;
            $is_random_question = 0;
            $is_quiz            = 0;
            $auto_publish_date  = "";
            if (isset($_POST['is_active'])) {
                $is_active = 1;
            }
            if (isset($_POST['publish_result'])) {
                $publish_result = 1;
            }
            if (isset($_POST['is_marks_display'])) {
                $is_marks_display = 1;
            }
            if (isset($_POST['is_neg_marking'])) {
                $is_neg_marking = 1;
            }
            if (isset($_POST['is_random_question'])) {
                $is_random_question = 1;
            }
            if (isset($_POST['auto_publish_date']) && $_POST['auto_publish_date'] != "") {

                $auto_publish_date = date('Y-m-d H:i:s', $this->customlib->dateTimeformatTwentyfourhour($this->input->post('auto_publish_date'), false));
            } else {
                $auto_publish_date = null;
            }

            $insert_data = array(
                'answer_word_count'  => trim((string) $this->input->post('word_limit')) === '' ? -1 : (int) $this->input->post('word_limit'),
                'exam'               => $this->input->post('exam'),
                'subject_id'         => (int) $this->input->post('subject_id'),
                'attempt'            => $this->input->post('attempt'),
                'exam_from'          => date('Y-m-d H:i:s', $this->customlib->dateTimeformatTwentyfourhour($this->input->post('exam_from'), false)),
                'exam_to'            => date('Y-m-d H:i:s', $this->customlib->dateTimeformatTwentyfourhour($this->input->post('exam_to'), false)),
                'auto_publish_date'  => $auto_publish_date,
                'duration'           => $this->input->post('duration'),
                'description'        => $this->input->post('description'),
                'session_id'         => $this->setting_model->getCurrentSession(),
                'is_active'          => $is_active,
                'publish_result'     => $publish_result,
                'is_marks_display'   => $is_marks_display,
                'is_neg_marking'     => $is_neg_marking,
                'is_random_question' => $is_random_question,
                'is_random_option'   => isset($_POST['is_random_option']) ? 1 : 0,
                'block_copy'         => isset($_POST['block_copy']) ? 1 : 0,
                'max_tab_switches'   => max(0, min(50, (int) $this->input->post('max_tab_switches'))),
                'lock_device'        => isset($_POST['lock_device']) ? 1 : 0,
                'require_fullscreen' => isset($_POST['require_fullscreen']) ? 1 : 0,
                'questions_per_student' => max(0, min(500, (int) $this->input->post('questions_per_student'))),
                'exam_pin'           => mb_substr(preg_replace('/\s+/', '', (string) $this->input->post('exam_pin')), 0, 12) ?: null,
                'allowed_ips'        => Cbt_integrity::clean_ips($this->input->post('allowed_ips')) ?: null,
                'webcam'             => isset($_POST['webcam']) ? 1 : 0,
                'webcam_minutes'     => max(1, min(30, (int) $this->input->post('webcam_minutes') ?: 3)),
                'passing_percentage' => $this->input->post('passing_percentage'),
            );
            if (isset($_POST['is_quiz']) && $_POST['is_quiz'] != "") {
                $insert_data['publish_result']    = 0;
                $insert_data['auto_publish_date'] = null;
                $insert_data['is_quiz']           = 1;
            } else {
                $insert_data['is_quiz'] = $is_quiz;
            }

            $id = $this->input->post('recordid');
            if ($id != 0) {
                $insert_data['id'] = $id;
            }

            // add() logs a row after the insert, so insert_id() here would be the log id, not the exam.
            $saved_id = $this->onlineexam_model->add($insert_data);
            if (!$saved_id) {
                echo json_encode(array('status' => 0, 'error' => array('exam' => 'The exam could not be saved. Please try again.'), 'message' => ''));
                return;
            }

            $allowed_classes = array_map('intval', array_column($this->class_model->get(), 'id'));
            $class_ids = array_values(array_intersect(array_unique(array_filter(array_map('intval', (array) $this->input->post('class_ids')))), $allowed_classes));
            $previous = array_values(array_intersect(array_unique(array_filter(array_map('intval', (array) $this->input->post('previous_class_ids')))), $allowed_classes));
            // Only classes ticked now fill up: a class already on the exam (even with a few students
            // picked on the Assign page) is left as it is; unticking one takes off who has not started.
            $this->assign_classes_to_exam((int) $saved_id, array_diff($class_ids, $previous));
            foreach (array_diff($previous, $class_ids) as $drop_class) {
                $this->unassign_class_students((int) $saved_id, $drop_class);
            }

            if ($id != 0) {
                $exam_notification = $this->onlineexam_model->get_msnstatusByexam_id($id);
                if ($is_active == 1 && $exam_notification['publish_exam_notification'] == 0) {

                    $sender_details = array(
                        'exam_id'            => $id,
                        'exam_title'         => $this->input->post('exam'),
                        'attempt'            => $this->input->post('attempt'),
                        'time_duration'      => $this->input->post('duration'),
                        'passing_percentage' => $this->input->post('passing_percentage'),
                        'exam_from'          => date('Y-m-d H:i:s', $this->customlib->dateTimeformatTwentyfourhour($this->input->post('exam_from'), false)),
                        'exam_to'            => date('Y-m-d H:i:s', $this->customlib->dateTimeformatTwentyfourhour($this->input->post('exam_to'), false)),
                    );

                    $notification_status = $this->mailsmsconf->mailsms('online_examination_publish_exam', $sender_details);
                    $publish_exam_notification['id']                        = $id;
                    $publish_exam_notification['publish_exam_notification'] = '1';
                    $this->onlineexam_model->add($publish_exam_notification);
                }

                if ($publish_result == 1 && $exam_notification['publish_result_notification'] == 0) {

                    $sender_details = array(
                        'exam_id'            => $id,
                        'exam_title'         => $this->input->post('exam'),
                        'attempt'            => $this->input->post('attempt'),
                        'time_duration'      => $this->input->post('duration'),
                        'passing_percentage' => $this->input->post('passing_percentage'),
                        'exam_from'          => date('Y-m-d H:i:s', $this->customlib->dateTimeformatTwentyfourhour($this->input->post('exam_from'), false)),
                        'exam_to'            => date('Y-m-d H:i:s', $this->customlib->dateTimeformatTwentyfourhour($this->input->post('exam_to'), false)),

                    );

                    $this->mailsmsconf->mailsms('online_examination_publish_result', $sender_details);
                    $publish_result_notification['id']                          = $id;
                    $publish_result_notification['publish_result_notification'] = '1';
                    $this->onlineexam_model->add($publish_result_notification);
                }
            }

            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    /**
     * Adds every active student of the given classes (all sections, current session)
     * to the exam, skipping anyone already on it. Used by add()/edit() and by
     * assign_classes(). Returns how many rows were inserted.
     */
    private function assign_classes_to_exam($exam_id, array $class_ids)
    {
        $exam_id = (int) $exam_id;
        if (!$exam_id || !$class_ids) {
            return 0;
        }
        $allowed = array_map('intval', array_column($this->class_model->get(), 'id'));
        $class_ids = array_values(array_intersect(array_map('intval', $class_ids), $allowed));
        if (!$class_ids) {
            return 0;
        }
        $session = (int) $this->setting_model->getCurrentSession();
        $new = $this->db->select('ss.id')->from('student_session ss')
            ->join('students s', 's.id = ss.student_id')
            ->join('onlineexam_students oes', 'oes.student_session_id = ss.id AND oes.onlineexam_id = ' . $exam_id, 'left')
            ->where('ss.session_id', $session)
            ->where('s.is_active', 'yes')
            ->where_in('ss.class_id', $class_ids)
            ->where('oes.id IS NULL', null, false)
            ->get()->result_array();
        $rows = array();
        foreach (array_unique(array_map('intval', array_column($new, 'id'))) as $ss_id) {
            $rows[] = array('onlineexam_id' => $exam_id, 'student_session_id' => $ss_id);
        }
        if ($rows) {
            $this->db->insert_batch('onlineexam_students', $rows);
        }
        return count($rows);
    }

    public function getRecord($id)
    {
        $result            = $this->onlineexam_model->get_result($id);
        $result['options'] = $this->onlineexam_model->get_option($id);
        $result['ans']     = $this->onlineexam_model->get_answer($id);
        echo json_encode($result);
    }

    public function delete($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Delete exams with the delete button on the exam list.', 405, 'Not allowed');
        }
        $this->onlineexam_model->remove($id);
        redirect('admin/onlineexam', 'refresh');
    }

    public function saverank()
    {
        $this->form_validation->set_rules('row[]', $this->lang->line('row'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'row' => form_error('row[]'),
            );

            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {
            $row     = $this->input->post('row');
            $exam_id = $this->input->post('exam_id');

            if (!empty($row)) {
                $students = array();
                foreach ($row as $row_key => $row_value) {
                    $students[] = array(
                        'id'   => $row_value,
                        'rank' => $this->input->post('onlineexam_student_id_' . $row_value),
                    );
                }

                $this->onlineexam_model->updateStudentRank($students, $exam_id);
            }

            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function fillmarks()
    {
        $this->form_validation->set_rules('onlineexam_student_result_id', $this->lang->line('exam'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('fill_mark', $this->lang->line('marks'), 'trim|xss_clean|callback_validate_marks');
        $this->form_validation->set_rules('question_marks', $this->lang->line('question_marks'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'question_id'    => form_error('onlineexam_student_result_id'),
                'fill_mark'      => form_error('fill_mark'),
                'question_marks' => form_error('question_marks'),
            );

            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {
            $insert_data = array(
                'id'     => $this->input->post('onlineexam_student_result_id'),
                'marks'  => $this->input->post('fill_mark'),
                'remark' => $this->input->post('remark'),
                'marked_at' => date('Y-m-d H:i:s'),

            );
            $this->onlineexamresult_model->update($insert_data);
            // The student's stored score includes the new mark.
            $result = $this->db->select('onlineexam_student_id')->where('id', (int) $insert_data['id'])->get('onlineexam_student_results')->row();
            if ($result) {
                $this->load->library('Cbt_scoring');
                $this->cbt_scoring->score((int) $result->onlineexam_student_id);
            }
            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function validate_duration($str)
    {
        if ($this->input->post('duration') != "") {
            if ($this->input->post('duration') != "00:00:00") {
                if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $str)) {

                    $this->form_validation->set_message('validate_duration', '%s ' . $this->lang->line('field_must_be'));
                    return false;
                }
            } else {
                $this->form_validation->set_message('validate_duration', '%s ' . $this->lang->line('field_can_not_be'));
                return false;
            }
            return true;
        }
        return true;
    }

    /** Empty or -1: no limit; otherwise a whole number of words, 1 or more. */
    public function validate_word_limit($str)
    {
        $str = trim((string) $str);
        if ($str === '' || $str === '-1') {
            return true;
        }
        if (!preg_match('/^\d+$/', $str) || (int) $str < 1) {
            $this->form_validation->set_message('validate_word_limit', '%s: enter a number of words (1 or more), or -1 for no limit.');
            return false;
        }
        return true;
    }

    public function validate_marks($str)
    {
        if (($this->input->post('fill_mark') != "") && ($this->input->post('question_marks') != "")) {
            if (preg_match('/^[+-]?([0-9]*[.])?[0-9]+$/', $str)) {
                if ($this->input->post('question_marks') < $this->input->post('fill_mark')) {
                    $this->form_validation->set_message('validate_marks', 'The %s field must be between 0 and ' . $this->input->post('question_marks'));
                    return false;
                }
                return true;
            } else {
                $this->form_validation->set_message('validate_marks', '%s ' . $this->lang->line('field_can_only_contain_numbers'));
                return false;
            }
        } elseif ($this->input->post('fill_mark') != "") {
            $this->form_validation->set_message('validate_marks', '%s ' . $this->lang->line('field_is_required'));
            return false;
        }
    }

    public function questionAdd()
    {
        $this->form_validation->set_rules('question_id', $this->lang->line('exam'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('onlineexam_id', $this->lang->line('attempt'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ques_mark', $this->lang->line('marks'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ques_neg_mark', $this->lang->line('negative_marks'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'question_id'   => form_error('question_id'),
                'onlineexam_id' => form_error('onlineexam_id'),
                'ques_mark'     => form_error('ques_mark'),
                'ques_neg_mark' => form_error('ques_neg_mark'),
            );

            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } elseif (
            !$this->db->where('id', (int) $this->input->post('onlineexam_id'))->count_all_results('onlineexam')
            || !$this->db->where('id', (int) $this->input->post('question_id'))->count_all_results('questions')
        ) {
            $array = array('status' => 0, 'error' => array('question_id' => 'That exam or question no longer exists. Reload the page.'), 'message' => '');
        } else {
            $insert_data = array(
                'question_id'   => $this->input->post('question_id'),
                'onlineexam_id' => $this->input->post('onlineexam_id'),
                'marks'         => $this->input->post('ques_mark'),
                'neg_marks'     => $this->input->post('ques_neg_mark'),
            );
            $this->onlineexam_model->insertExamQuestion($insert_data);
            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function deleteExamQuestions()
    {
        $this->form_validation->set_rules('question_id', $this->lang->line('question'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'question_id' => form_error('question_id'),
            );

            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {

            $this->onlineexamquestion_model->remove($this->input->post('question_id'));
            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('delete_message'));
        }

        echo json_encode($array);
    }

    public function report()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/online_examinations');
        $this->session->set_userdata('subsub_menu', 'Reports/online_examinations/online_exam_report');
        $examList            = $this->onlineexam_model->get();
        $data['examList']    = $examList;
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/onlineexam/report', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getstudentresult()
    {
        $onlineexam_student_id      = $this->input->post('recordid');
        $examid                     = $this->input->post('examid');
        $student_session_id         = $this->input->post('student_session_id');
        $data['student_session_id'] = $this->input->post('student_session_id');
        $admission_no               = '';

        $student_list = $this->onlineexam_model->getstudentbystudentsessionid($data['student_session_id']);

        if ($student_list['admission_no'] != '') {
            $admission_no = ' (' . $student_list['admission_no'] . ')';
        }

        $data['student_name'] = $student_list['firstname'] . ' ' . $student_list['lastname'] . $admission_no;
        $data['class']        = $student_list['class'];
        $data['section']      = $student_list['section'];
        $data['father_name']  = $student_list['father_name'];

        $this->cbt_refresh($examid);
        $exam         = $this->onlineexam_model->get($examid);
        $data['exam'] = $exam;
        $online_exam_validate = $this->onlineexam_model->examstudentsID($student_session_id, $examid);

        $data['question_result']       = $this->onlineexamresult_model->getResultByStudent($onlineexam_student_id, $examid);
        $data['result_prepare']        = $this->onlineexamresult_model->checkResultPrepare($onlineexam_student_id);
        $data['online_exam_validate']  = $online_exam_validate;
        $questionOpt                   = $this->customlib->getQuesOption();
        $data['questionOpt']           = $questionOpt;
        $data['onlineexam_student_id'] = $onlineexam_student_id;
        $data['question_true_false']   = $this->config->item('question_true_false');

        $print = $this->input->post('print');
        if (isset($print)) {

            $question_result = $this->load->view('admin/onlineexam/_print', $data, true);
        } else {
            $question_result = $this->load->view('admin/onlineexam/_getstudentresult', $data, true);
        }
        echo json_encode(array('status' => 1, 'result' => $question_result));
    }

    public function download_exam()
    {
        $exam_id                     = $this->input->post('recordid');
        $exam                        = $this->onlineexam_model->get($exam_id);
        $data['exam']                = $exam;
        $data['questions']           = $this->onlineexamquestion_model->getExamQuestions($exam_id);
        $data['questionSubjects']    = $this->onlineexamquestion_model->getExamQuestionSubjects($exam_id);
        $data['question_type']       = $this->config->item('question_type');
        $data['question_level']      = $this->config->item('question_level');
        $questionOpt                 = $this->customlib->getQuesOption();
        $data['question_true_false'] = $this->config->item('question_true_false');
        $data['questionOpt']         = $questionOpt;
        $data['onlineexamfooter']    = $this->setting_model->get_onlineexamfooter();

        $page                        = $this->load->view('admin/onlineexam/_download_exam', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'exam' => $exam));
    }

    public function getExamQuestions()
    {
        $exam_id                  = $this->input->post('recordid');
        $exam                     = $this->onlineexam_model->get($exam_id);
        $data['exam']             = $exam;
        $data['questions']        = $this->onlineexamquestion_model->getExamQuestions($exam_id);
        $data['questionSubjects'] = $this->onlineexamquestion_model->getExamQuestionSubjects($exam_id);
        $data['question_type']    = $this->config->item('question_type');
        $data['question_level']   = $this->config->item('question_level');
        $questionList = $this->load->view('admin/onlineexam/_getexamquestions', $data, true);
        echo json_encode(array('status' => 1, 'result' => $questionList, 'exam' => $exam));
    }

    public function downloadattachment($doc)
    {
        $this->load->helper('download');
        $filepath = upload_root() . "onlinexam_images/" . $doc;
        $data     = file_get_contents($filepath);
        $name     = $doc;
        force_download($name, $data);
    }

    public function searchloginvalidation()
    {
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $exam_id    = $this->input->post('exam_id');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $error = array();

            $error['class_id']   = form_error('class_id');
            $error['section_id'] = form_error('section_id');
            $error['exam_id']    = form_error('exam_id');
            $array               = array('status' => 0, 'error' => $error);
            echo json_encode($array);
        } else {
            $params = array('class_id' => $class_id, 'section_id' => $section_id, 'exam_id' => $exam_id);
            $array  = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }

    public function dtreportlist()
    {
        $exam_id     = $this->input->post('exam_id');
        $class_id    = $this->input->post('class_id');
        $section_id  = $this->input->post('section_id');
        $sch_setting = $this->sch_setting_detail;
        $results     = $this->onlineexamresult_model->getStudentByExam($exam_id, $class_id, $section_id);
        $resultlist  = json_decode($results);
        $dt_data     = array();

        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $resultlist_key => $student) {

                $attempt_bn = "";
                $action     = "";
                $viewbtn    = "<a  href='" . base_url() . "student/view/" . $student->id . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";
                if ($student->is_attempted) {
                    $attempt_btn = " <i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $attempt_btn = " <i class='fa fa-remove'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                }
                $action = "<button type='button' title=" . $this->lang->line('view') . " class='btn btn-info btn-xs student_result' data-toggle='tooltip' id='load' data-recordid=" . $student->onlineexam_student_id . " data-student_session_id=" . $student->student_session_id . " data-examid=" . $student->exam_id . " data-loading-text='<i class=fa fa-spinner fa-spin></i>'   ><i class='fa fa-eye'></i></button>";

                $row       = array();
                $row[]     = $student->admission_no;
                $row[]     = $viewbtn;
                $row[]     = $student->class . "(" . $student->section . ")";
                $row[]     = $student->attempt;
                $row[]     = $student->attempt - $student->total_counter;
                $row[]     = $attempt_btn;
                $row[]     = $action;
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function ajax_delete()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('exam[]', $this->lang->line('exam'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'exam[]' => form_error('exam[]'),
            );
            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {
            $exams = $this->input->post('exam');
            $this->onlineexam_model->bulkdelete($exams);

            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('delete_message'));
        }
        echo json_encode($array);
    }
}
