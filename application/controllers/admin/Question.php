<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Question extends Admin_Controller
{
    /**
     * The permission each page or action needs: alternatives, any one is enough. A method missing
     * from this list is refused. add(): a new question needs can_add, an edit (recordid) can_edit.
     */
    const PERMISSIONS = array(
        'read' => array(array('question_bank', 'can_view')),
        'index' => array(array('question_bank', 'can_view')),
        'getdatatable' => array(array('question_bank', 'can_view')),
        'getquestionbyid' => array(array('question_bank', 'can_view')),
        'getrecord' => array(array('question_bank', 'can_view')),
        'questionsearchvalidation' => array(array('question_bank', 'can_view')),
        'exportformat' => array(array('import_question', 'can_view'), array('question_bank', 'can_add')),
        'uploadfile' => array(array('import_question', 'can_view')),
        'addform' => array(array('question_bank', 'can_add')),
        'bulk' => array(array('question_bank', 'can_add')),
        'bulk_save' => array(array('question_bank', 'can_add')),
        'editform' => array(array('question_bank', 'can_edit')),
        'add' => array(),
        'delete' => array(array('question_bank', 'can_delete')),
        'bulkdelete' => array(array('question_bank', 'can_delete')),
        'getimages' => null, // the image picker, also used by the syllabus pages: any signed-in staff
    );

    public function __construct()
    {
        parent::__construct();
        $method = strtolower($this->router->fetch_method());
        if (!array_key_exists($method, self::PERMISSIONS)) {
            show_404(); // form-validation callbacks and helpers are not pages
        }
        $needs = self::PERMISSIONS[$method];
        if ($method === 'add') {
            $needs = array(array('question_bank', $this->input->post('recordid') ? 'can_edit' : 'can_add'));
        }
        if ($needs !== null) {
            $allowed = false;
            foreach ($needs as $need) {
                $allowed = $allowed || $this->rbac->hasPrivilege($need[0], $need[1]);
            }
            if (!$allowed) {
                access_denied();
            }
        }
        $this->load->library('media_storage');
        $this->sch_setting_detail = $this->setting_model->getSetting();

    }
    public function read($id)
    {
        if (!$this->rbac->hasPrivilege('question_bank', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Online_Examinations');
        $this->session->set_userdata('sub_menu', 'Online_Examinations/question');
        $question                    = $this->question_model->get($id);
        $data['question']            = $question;
        $data['question_type']       = $this->config->item('question_type');
        $data['question_level']      = $this->config->item('question_level');
        $data['question_true_false'] = $this->config->item('question_true_false');
        $questionOpt                 = $this->customlib->getQuesOption();
        $data['questionOpt']         = $questionOpt;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/question/read', $data);
        $this->load->view('layout/footer', $data);
    }

    public function index($offset = 0)
    {
        if (!$this->rbac->hasPrivilege('question_bank', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Online_Examinations');
        $this->session->set_userdata('sub_menu', 'Online_Examinations/question');
        $data                   = array();
        $class                  = $this->class_model->get();
        $data['classlist']      = $class;
        $subjectlist            = $this->subject_model->get();
        $data['subjectlist']    = $subjectlist;
        $data['question_type']  = $this->config->item('question_type');
        $data['question_level'] = $this->config->item('question_level');
        $questionList           = $this->question_model->getquestioncreatedstaff();
        $data['staff_list']     = $questionList;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/question/question', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getQuestionByID()
    {
        $id = $this->input->post('recordid');
        $question_result = $this->question_model->get($id);
        echo json_encode(array('status' => 1, 'result' => $question_result));
    }

    /** The import template: Excel (.xlsx), or the CSV with ?format=csv. */
    public function exportformat()
    {
        $this->load->helper('download');
        $xlsx = $this->input->get('format') !== 'csv' && is_file('./backend/import/import_question_template.xlsx');
        $name = $xlsx ? 'import_question_template.xlsx' : 'import_question_sample_file.csv';
        force_download($name, file_get_contents('./backend/import/' . $name));
    }

    public function bulkdelete()
    {
        $question_array  = $this->input->post('recordid');
        $question_result = $this->question_model->bulkdelete($question_array);
        echo json_encode(array('status' => 1, 'message' => $this->lang->line('delete_message')));
    }

    /** Excel (.xlsx) or CSV questions (libraries/Question_import.php): imported rows, and problems by row. */
    public function uploadfile()
    {
        if (!$this->rbac->hasPrivilege('import_question', 'can_view') || !$this->rbac->hasPrivilege('question_bank', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject_id', $this->lang->line('subject'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'subject_id' => form_error('subject_id'),
                'class_id'   => form_error('class_id'),
                'section_id' => form_error('section_id'),
                'file'       => form_error('file'),
            );
            echo json_encode(array('status' => 0, 'error' => $data));
            return;
        }
        $this->load->library('Question_import');
        list($rows, $error) = Question_import::read($_FILES['file']['tmp_name'], pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if ($error) {
            echo json_encode(array('status' => 0, 'error' => array('file' => $error)));
            return;
        }
        list($count, $problems) = $this->question_import->import($rows, array(
            'staff_id' => $this->customlib->getStaffID(),
            'subject_id' => $this->input->post('subject_id'),
            'class_id' => $this->input->post('class_id'),
            'section_id' => $this->input->post('section_id'),
        ));
        $message = $count . ' ' . $this->lang->line('questions_are_successfully_imported');
        if ($problems) {
            $shown = array_slice($problems, 0, 20);
            $message .= '<br><b>' . count($problems) . ' row(s) skipped:</b><br>' . implode('<br>', array_map('html_escape', $shown))
                . (count($problems) > 20 ? '<br>...' : '');
        }
        echo json_encode(array('status' => '1', 'error' => '', 'message' => $message));
    }

    public function handle_upload()
    {
        $this->load->library('Import_sheet');
        $problem = Import_sheet::upload_problem('file');
        if ($problem !== '') {
            $this->form_validation->set_message('handle_upload', $problem);
            return false;
        }
        return true;
    }

    public function add()
    {

        $this->form_validation->set_rules('subject_id', $this->lang->line('subject'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('question', $this->lang->line('question'), 'trim|required');
        $this->form_validation->set_rules('question_type', $this->lang->line('question_type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('question_level', $this->lang->line('question_level'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        if ($this->input->post('question_type') == "singlechoice") {
            $this->form_validation->set_rules('opt_a', $this->lang->line('option_a'), 'trim|required');
            $this->form_validation->set_rules('opt_b', $this->lang->line('option_b'), 'trim|required');
            $this->form_validation->set_rules('correct', $this->lang->line('answer'), 'trim|required');
        } elseif ($this->input->post('question_type') == "true_false") {
            $this->form_validation->set_rules('correct_true_false', $this->lang->line('answer'), 'trim|required|xss_clean');
        } elseif ($this->input->post('question_type') == "multichoice") {
            $this->form_validation->set_rules('opt_a', $this->lang->line('option_a'), 'trim|required');
            $this->form_validation->set_rules('opt_b', $this->lang->line('option_b'), 'trim|required');
            $this->form_validation->set_rules('ans[]', $this->lang->line('answer'), 'trim|required|xss_clean');
        }

        if ($this->form_validation->run() == false) {

            $msg = array(
                'subject_id'     => form_error('subject_id'),
                'question'       => form_error('question'),
                'question_type'  => form_error('question_type'),
                'question_level' => form_error('question_level'),
                'class_id'       => form_error('class_id'),
            );

            if ($this->input->post('question_type') == "singlechoice") {
                $msg['opt_a']   = form_error('opt_a');
                $msg['opt_b']   = form_error('opt_b');
                $msg['correct'] = form_error('correct');
            } elseif ($this->input->post('question_type') == "true_false") {
                $msg['correct_true_false'] = form_error('correct_true_false');
            } elseif ($this->input->post('question_type') == "multichoice") {
                $msg['opt_a'] = form_error('opt_a');
                $msg['opt_b'] = form_error('opt_b');
                $msg['ans']   = form_error('ans[]');
            }

            $array = array('status' => 0, 'error' => $msg, 'message' => '');

        } else {

            $insert_data = array(
                'subject_id'    => $this->input->post('subject_id'),
                'question'      => $this->input->post('question'),
                'question_type' => $this->input->post('question_type'),
                'level'         => $this->input->post('question_level'),
                'class_id'      => $this->input->post('class_id'),
                'staff_id'      => $this->customlib->getStaffID(),
                'passage_id'    => (int) $this->input->post('passage_id') ?: null,
            );

            $section_id = $this->input->post('section_id');
            if (isset($section_id) && $section_id != "") {
                $insert_data['section_id'] = $this->input->post('section_id');
            }

            if ($this->input->post('question_type') == "singlechoice") {
                $insert_data['opt_a']   = $this->input->post('opt_a');
                $insert_data['opt_b']   = $this->input->post('opt_b');
                $insert_data['opt_c']   = $this->input->post('opt_c');
                $insert_data['opt_d']   = $this->input->post('opt_d');
                $insert_data['opt_e']   = $this->input->post('opt_e');
                $insert_data['correct'] = $this->input->post('correct');
            } elseif ($this->input->post('question_type') == "true_false") {
                $insert_data['opt_a']   = "";
                $insert_data['opt_b']   = "";
                $insert_data['opt_c']   = "";
                $insert_data['opt_d']   = "";
                $insert_data['opt_e']   = "";
                $insert_data['correct'] = $this->input->post('correct_true_false');
            } elseif ($this->input->post('question_type') == "multichoice") {
                $insert_data['opt_a']   = $this->input->post('opt_a');
                $insert_data['opt_b']   = $this->input->post('opt_b');
                $insert_data['opt_c']   = $this->input->post('opt_c');
                $insert_data['opt_d']   = $this->input->post('opt_d');
                $insert_data['opt_e']   = $this->input->post('opt_e');
                $insert_data['correct'] = json_encode($this->input->post('ans'));
            } elseif ($this->input->post('question_type') == "descriptive") {
                $insert_data['opt_a']   = "";
                $insert_data['opt_b']   = "";
                $insert_data['opt_c']   = "";
                $insert_data['opt_d']   = "";
                $insert_data['opt_e']   = "";
                $insert_data['correct'] = "";
            }

            $id = $this->input->post('recordid');
            if ($id != 0) {
                $insert_data['id'] = $id;
            }

            // the same question already in the bank is refused; a very similar one is pointed out
            $this->load->library('question_dedupe');
            $dup = $this->question_dedupe->find($insert_data['subject_id'], $insert_data['question'], (int) $id);
            if ($dup && $dup[0] === 'same') {
                echo json_encode(array('status' => 0, 'error' => array('question' => 'This question is already in the question bank for this subject (question #' . $dup[1] . ').'), 'message' => ''));
                return;
            }
            $this->question_model->add($insert_data);
            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message')
                . ($dup ? ' Note: question #' . $dup[1] . ' in the bank is ' . $dup[2] . '% the same. Check it is not a repeat.' : ''));
        }

        echo json_encode($array);
    }

    /**
     * Add questions: one class (and section) and subject, then as many questions as needed on one
     * page, saved together. ?exam=<online exam id> also puts them in that CBT (with its marks).
     */
    public function bulk()
    {
        $this->session->set_userdata('top_menu', 'Online_Examinations');
        $this->session->set_userdata('sub_menu', 'Online_Examinations/question');
        $exam_id = (int) $this->input->get('exam');
        $data = array(
            'classList' => $this->class_model->get(),
            'subjectlist' => $this->subject_model->get(),
            'question_level' => $this->config->item('question_level'),
            'exams' => $this->db->select('id, exam, subject_id')->where('exam_to >=', date('Y-m-d H:i:s'))->order_by('exam_from', 'desc')->get('onlineexam')->result_array(),
            'exam_id' => $exam_id,
            'subject_id' => (int) $this->input->get('subject'),
            'return' => preg_match('#^(cbseexam/results/|admin/cbtexams)[A-Za-z0-9/_?=&%.-]*$#', (string) $this->input->get('return')) ? (string) $this->input->get('return') : '',
        );
        if ($exam_id && !in_array($exam_id, array_map('intval', array_column($data['exams'], 'id')), true)) {
            $row = $this->db->select('id, exam, subject_id')->where('id', $exam_id)->get('onlineexam')->row_array();
            if ($row) {
                $data['exams'][] = $row;
            }
        }
        $this->load->view('layout/header', $data);
        $this->load->view('admin/question/bulk', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * POST class_id, section_id, subject_id, level, exam_id, marks, neg_marks, q[n][type|text|opt_a..opt_e|correct|ans[]].
     * All or nothing: any question with a problem saves none, and each problem is returned by number.
     */
    public function bulk_save()
    {
        $class_id = (int) $this->input->post('class_id');
        $subject_id = (int) $this->input->post('subject_id');
        $section_id = (int) $this->input->post('section_id');
        $level = (string) $this->input->post('level');
        $levels = array_keys((array) $this->config->item('question_level'));
        $errors = array();
        if (!in_array($class_id, array_map('intval', array_column($this->class_model->get(), 'id')), true)) {
            $errors['head'][] = 'Choose the class.';
        }
        if (!in_array($subject_id, array_map('intval', array_column($this->subject_model->get(), 'id')), true)) {
            $errors['head'][] = 'Choose the subject.';
        }
        if (!in_array($level, $levels, true)) {
            $level = 'medium';
        }
        $exam_id = (int) $this->input->post('exam_id');
        $marks = (float) $this->input->post('marks');
        $neg = max(0, (float) $this->input->post('neg_marks'));
        if ($exam_id) {
            if (!$this->db->where('id', $exam_id)->count_all_results('onlineexam')) {
                $errors['head'][] = 'That online exam no longer exists.';
            }
            if ($marks <= 0) {
                $errors['head'][] = 'Enter the marks for each question (more than 0).';
            }
        }
        $opts = array('opt_a', 'opt_b', 'opt_c', 'opt_d', 'opt_e');
        $rows = array();
        foreach ((array) $this->input->post('q') as $n => $q) {
            $no = count($rows) + count(isset($errors['q']) ? $errors['q'] : array()) + 1;
            $type = isset($q['type']) ? (string) $q['type'] : '';
            $text = isset($q['text']) ? trim((string) $q['text']) : '';
            if ($text === '' && $type !== '' && trim(implode('', array_map(function ($k) use ($q) { return isset($q[$k]) ? (string) $q[$k] : ''; }, $opts))) === '') {
                continue; // an empty card left at the end
            }
            $problems = array();
            if ($text === '') {
                $problems[] = 'type the question';
            }
            $row = array('question_type' => $type, 'question' => nl2br(html_escape($text), false), 'level' => $level, 'class_id' => $class_id,
                'subject_id' => $subject_id, 'staff_id' => $this->customlib->getStaffID(), 'opt_a' => '', 'opt_b' => '', 'opt_c' => '', 'opt_d' => '', 'opt_e' => '', 'correct' => '');
            if ($section_id) {
                $row['section_id'] = $section_id;
            }
            if ($type === 'singlechoice' || $type === 'multichoice') {
                $filled = array();
                foreach ($opts as $k) {
                    $v = isset($q[$k]) ? trim((string) $q[$k]) : '';
                    $row[$k] = $v === '' ? '' : html_escape($v);
                    if ($v !== '') {
                        $filled[] = $k;
                    }
                }
                if (count($filled) < 2 || $row['opt_a'] === '' || $row['opt_b'] === '') {
                    $problems[] = 'fill in at least options A and B';
                }
                if ($type === 'singlechoice') {
                    $c = isset($q['correct']) ? (string) $q['correct'] : '';
                    if (!in_array($c, $filled, true)) {
                        $problems[] = 'choose the right answer (an option that has text)';
                    }
                    $row['correct'] = $c;
                } else {
                    $ans = array_values(array_intersect((array) (isset($q['ans']) ? $q['ans'] : array()), $filled));
                    if (!$ans) {
                        $problems[] = 'tick the right answers';
                    }
                    $row['correct'] = json_encode($ans);
                }
            } elseif ($type === 'true_false') {
                $c = isset($q['correct']) ? (string) $q['correct'] : '';
                if (!in_array($c, array('true', 'false'), true)) {
                    $problems[] = 'choose True or False';
                }
                $row['correct'] = $c;
            } elseif ($type !== 'descriptive') {
                $problems[] = 'choose the question type';
            }
            if ($problems) {
                $errors['q'][$n] = 'Question ' . $no . ': ' . implode(', ', $problems) . '.';
            } else {
                $rows[$n] = $row;
            }
        }
        // the same question twice (in the bank or on this page) is refused
        $this->load->library('question_dedupe');
        foreach ($rows as $n => $row) {
            $dup = $this->question_dedupe->find($subject_id, $row['question']);
            if ($dup && $dup[0] === 'same') {
                $errors['q'][$n] = 'Question ' . (array_search($n, array_keys((array) $this->input->post('q')), true) + 1) . ': this question is already in the question bank' . (is_int($dup[1]) ? ' (#' . $dup[1] . ')' : ' (twice on this page)') . '.';
                unset($rows[$n]);
                continue;
            }
            $this->question_dedupe->remember($subject_id, 0, $row['question']);
        }
        if (!$rows && empty($errors['q'])) {
            $errors['head'][] = 'Write at least one question.';
        }
        if ($errors) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => 0, 'errors' => $errors)));
        }
        $this->db->trans_start();
        $added_to_exam = 0;
        foreach ($rows as $row) {
            $this->db->insert('questions', $row);
            $qid = (int) $this->db->insert_id();
            if ($exam_id) {
                $this->db->insert('onlineexam_questions', array('onlineexam_id' => $exam_id, 'question_id' => $qid, 'marks' => $marks, 'neg_marks' => $neg));
                $added_to_exam++;
            }
        }
        $this->db->trans_complete();
        $msg = count($rows) . ' question(s) saved to the question bank' . ($added_to_exam ? ' and added to the online exam' : '') . '.';
        $this->session->set_flashdata('bulk_questions', $msg);
        $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => 1, 'message' => $msg,
            'redirect' => preg_match('#^(cbseexam/results/|admin/cbtexams)[A-Za-z0-9/_?=&%.-]*$#', (string) $this->input->post('return')) ? site_url($this->input->post('return')) : ($exam_id ? site_url('admin/onlineexam') : site_url('admin/question')))));
    }

    public function getRecord($id)
    {
        $result            = $this->question_model->get_result($id);
        $result['options'] = $this->question_model->get_option($id);
        $result['ans']     = $this->question_model->get_answer($id);
        echo json_encode($result);
    }

    public function addform()
    {
        $data                        = array();
        $data['classList']           = $this->class_model->get();
        $subject_result              = $this->subject_model->get();
        $data['subjectlist']         = $subject_result;
        $data['question_true_false'] = $this->config->item('question_true_false');
        $data['question_type']       = $this->config->item('question_type');
        $data['question_level']      = $this->config->item('question_level');
        $questionOpt                 = $this->customlib->getQuesOption();
        $data['questionOpt']         = $questionOpt;
        $data['recordid']            = $this->input->post('recordid');
        $data['passages']            = $this->db->select('id, title')->order_by('title')->get('question_passages')->result_array();
        $page                        = $this->load->view('admin/question/_addform', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function editform()
    {
        $data                        = array();
        $data['recordid']            = $this->input->post('recordid');
        $question_result             = $this->question_model->get($data['recordid']);
        $data['question_result']     = $question_result;
        $data['passages']            = $this->db->select('id, title')->order_by('title')->get('question_passages')->result_array();
        $data['classList']           = $this->class_model->get();
        $data['sectionList']         = $this->section_model->getClassBySection($question_result->class_id);
        $subject_result              = $this->subject_model->get();
        $data['subjectlist']         = $subject_result;
        $data['question_true_false'] = $this->config->item('question_true_false');
        $data['question_type']       = $this->config->item('question_type');
        $data['question_level']      = $this->config->item('question_level');
        $questionOpt                 = $this->customlib->getQuesOption();
        $data['questionOpt']         = $questionOpt;
        $page = $this->load->view('admin/question/_editform', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('question_bank', 'can_delete')) {
            access_denied();
        }
        $this->question_model->remove($id);
        redirect('admin/question', 'refresh');
    }

    public function getimages()
    {
        $keyword         = "";
        $page            = $this->input->post('page');
        $keyword         = $this->input->post('query');
        $per_page_record = 12;
        if ($page > 1) {
            $start = (($page - 1) * $per_page_record);
            $page  = $page;
        } else {
            $start = 0;
        }
        $file_type = "image";
        $result       = $this->cms_media_model->fetch_details($per_page_record, $start, $keyword, $file_type);
        $result_count = $this->cms_media_model->count_all($keyword, $file_type);

        $data['result']       = $result;
        $data['result_count'] = $result_count;
        $data['pagination']   = $this->getpagination($result_count, $per_page_record, $page);
        $page = $this->load->view('admin/question/_getimages', $data, true);
        echo json_encode(array('page' => $page, 'count' => $data['result_count'], 'pagination' => $data['pagination']));
    }

    public function getpagination($total_data, $limit, $page)
    {
        $output = "";
        $output .= '<ul class="pagination">';
        $total_links   = ceil($total_data / $limit);
        $previous_link = '';
        $next_link     = '';
        $page_link     = '';

        $page_array = array();
        if ($total_links > 4) {
            if ($page < 5) {
                for ($count = 1; $count <= 5; $count++) {
                    $page_array[] = $count;
                }
                $page_array[] = '...';
                $page_array[] = $total_links;
            } else {
                $end_limit = $total_links - 5;
                if ($page > $end_limit) {
                    $page_array[] = 1;
                    $page_array[] = '...';
                    for ($count = $end_limit; $count <= $total_links; $count++) {
                        $page_array[] = $count;
                    }
                } else {
                    $page_array[] = 1;
                    $page_array[] = '...';
                    for ($count = $page - 1; $count <= $page + 1; $count++) {
                        $page_array[] = $count;
                    }
                    $page_array[] = '...';
                    $page_array[] = $total_links;
                }
            }
        } else {

            for ($count = 1; $count <= $total_links; $count++) {
                $page_array[] = $count;
            }
        }

        for ($count = 0; $count < count($page_array); $count++) {
            if ($page == $page_array[$count]) {
                $page_link .= '
    <li class="page-item active">
      <a class="page-link" href="#">' . $page_array[$count] . ' <span class="sr-only">(current)</span></a>
    </li>
    ';

                $previous_id = $page_array[$count] - 1;
                if ($previous_id > 0) {
                    $previous_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">Previous</a></li>';
                } else {
                    $previous_link = '
      <li class="page-item disabled">
        <a class="page-link" href="#">Previous</a>
      </li>
      ';
                }
                $next_id = $page_array[$count] + 1;
                if ($next_id >= $total_links) {
                    $next_link = '
      <li class="page-item disabled">
        <a class="page-link" href="#">Next</a>
      </li>
        ';
                } else {
                    $next_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">Next</a></li>';
                }
            } else {
                if ($page_array[$count] == '...') {
                    $page_link .= '
      <li class="page-item disabled">
          <a class="page-link" href="#">...</a>
      </li>
      ';
                } else {
                    $page_link .= '
      <li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a></li>
      ';
                }
            }
        }

        $output .= $previous_link . $page_link . $next_link;
        $output .= '</ul>';

        return $output;
    }

    public function getDatatable()
    {
        $getStaffRole            = $this->customlib->getStaffRole();
        $staffrole               = json_decode($getStaffRole);
        $superadmin_restriction   = $this->session->userdata['admin']['superadmin_restriction'];
        
        
        $class_id              = $this->input->post('class_id');
        $section_id            = $this->input->post('section_id');
        $subject               = $this->input->post('subject');
        $search_question_type  = $this->input->post('question_type');
        $search_question_level = $this->input->post('question_level');
        $created_by            = $this->input->post('created_by');
        
        $question_type  = $this->config->item('question_type');
        $question_level = $this->config->item('question_level');
        $question_dt = $this->question_model->getAllRecord($class_id, $section_id, $subject, $search_question_type, $search_question_level,$created_by);
        $question_dt = json_decode($question_dt);
        $dt_data = array();
        $recordsTotal_flter = "";
        $userdata           = $this->customlib->getUserData();
        $role_id            = $userdata["role_id"];
        if (!empty($question_dt->data)) {
            foreach ($question_dt->data as $key => $value) {

                $delete       = "'" . $this->lang->line("delete_confirm") . "'";
                $delete_title = "'" . $this->lang->line("delete") . "'";
                $editbtn      = "";
                $deletebtn    = "";
                $del_checkbox = "";

                if ($this->rbac->hasPrivilege('question_bank', 'can_delete')) {
                    $deletebtn    = '<a href="' . base_url() . 'admin/question/delete/' . $value->id . '" class="btn btn-default btn-xs"  data-toggle="tooltip" title=' . $delete_title . ' onclick="return confirm(' . $delete . ')"><i class="fa fa-remove"></i></a>';
                    $del_checkbox = "<input type='checkbox' name='question_" . $value->id . "' data-question-id='" . $value->id . "' value='" . $value->id . "'>";
                }

                if ($this->rbac->hasPrivilege('question_bank', 'can_view')) {

                    $viewbtn = '<a target="_blank" href="' . site_url('admin/question/read/' . $value->id) . '" class="btn btn-default btn-xs"  data-toggle="tooltip" title=' . $this->lang->line("view") . ' ><i class="fa fa-eye"></i></a>';
                }
                
                if ($this->rbac->hasPrivilege('question_bank', 'can_edit')) {                
                    $editbtn = '<button type="button" class="btn btn-default btn-xs question-btn-edit" data-toggle="tooltip" id="load" data-recordid="' . $value->id . '" title="' . $this->lang->line("edit") . '" ><i class="fa fa-pencil"></i></button>';
                }
                
                $code = '';
                if($value->code){
                    $code = ' ('.$value->code.')';
                }
                $row = array();

                $row[] = $del_checkbox;
                $row[] = $value->id;
                $row[] = $value->name .' '. $code ;
                $row[] = ($value->question_type != "") ? $question_type[$value->question_type] : "";
                $row[] = ($value->level != "") ? $question_level[$value->level] : "";
                $row[] = readmorelink($value->question, site_url('admin/question/read/' . $value->id));                
                
                if($superadmin_restriction == 'disabled'){
                    if($staffrole->id == 7){
                        $row[] = $value->staff_name. ' ' .$value->staff_surname. ' (' .$value->employee_id. ')';
                    }else{
                        if($value->created_role != 7){
                            $row[] = $value->staff_name. ' ' .$value->staff_surname. ' (' .$value->employee_id. ')'; 
                        }else{
                            $row[] = '';
                        } 
                    }
                }else{
                    $row[] = $value->staff_name. ' ' .$value->staff_surname. ' (' .$value->employee_id. ')';
                }              
                
                $row[] = $viewbtn . ' ' . $editbtn . ' ' . $deletebtn;

                if ($role_id == 2) {
                    $my_section = array();
                    if ($this->sch_setting_detail->class_teacher == 'yes' && $this->sch_setting_detail->my_question == '1') {
                        $my_class = $this->class_model->get();
                        foreach ($my_class as $class_key => $class_value) {
                            $section_id = $this->teacher_model->get_teacherrestricted_modesections($this->customlib->getStaffID(), $class_value['id']);
                            foreach ($section_id as $section_idkey => $section_idvalue) {
                                $my_section[] = $section_idvalue['section_id'];
                            }

                            if (in_array($value->section_id, $my_section, true) && $class_value['id'] == $value->class_id) {
                                $dt_data[] = $row;

                            } elseif (($class_value['id'] == $value->class_id) && $value->section_id == '0') {
                                $dt_data[] = $row;

                            }
                        }

                    } elseif ($this->sch_setting_detail->class_teacher == 'yes' && $this->sch_setting_detail->my_question == '0') {
                        $my_class = $this->class_model->get();
                        foreach ($my_class as $class_key => $class_value) {
                            $section_id = $this->teacher_model->get_teacherrestricted_modesections($this->customlib->getStaffID(), $class_value['id']);
                            foreach ($section_id as $section_idkey => $section_idvalue) {
                                $my_section[] = $section_idvalue['section_id'];
                            }

                            if (in_array($value->section_id, $my_section, true) && $class_value['id'] == $value->class_id) {
                                $dt_data[] = $row;

                            } elseif (($class_value['id'] == $value->class_id) && $value->section_id == '0') {
                                $dt_data[] = $row;

                            }
                        }

                    } elseif ($this->sch_setting_detail->class_teacher == 'no' && $this->sch_setting_detail->my_question == '1') {
                        if ($this->customlib->getStaffID() == $value->staff_id) {
                            $dt_data[] = $row;

                        }
                    } else {
                        $dt_data[] = $row;

                    }

                } else {
                    $dt_data[] = $row;

                }
            }
        }

        $json_data = array(
            "draw"            => intval($question_dt->draw),
            "recordsTotal"    => intval($question_dt->recordsTotal),
            "recordsFiltered" => intval($question_dt->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function questionsearchvalidation()
    {
        $class_id       = $this->input->post('class');
        $section_id     = $this->input->post('section');
        $subject        = $this->input->post('subject');
        $question_type  = $this->input->post('question_type');
        $question_level = $this->input->post('question_level');
        $created_by = $this->input->post('created_by');
        $srch_type      = $this->input->post('search_type');         

        $params = array('srch_type' => $srch_type, 'class_id' => $class_id, 'section_id' => $section_id, 'subject' => $subject, 'question_type' => $question_type, 'question_level' => $question_level, 'created_by' => $created_by);
            $array  = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);        

    }

}
