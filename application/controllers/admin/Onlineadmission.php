<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Onlineadmission extends Admin_Controller
{
    public $sch_setting_detail = array();

    public function __construct()
    {
        parent::__construct();

        $this->load->library('media_storage');
        $this->load->model("onlinestudent_model");
        $this->config->load("app-config");
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->role;
    }

    public function admissionsetting()
    {
        if (!$this->rbac->hasPrivilege('online_admission', 'can_view')) {
            access_denied();
        }
        
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'System Settings/onlineadmissionsetting');
        $data                       = array();
        $data['result']             = $this->setting_model->getSetting();
        $data['fields']             = get_onlineadmission_editable_fields();
        $data['inserted_fields']    = $this->onlinestudent_model->getformfields();
        $data['sch_setting_detail'] = $this->sch_setting_detail;
        $data['custom_fields']      = $this->onlinestudent_model->getcustomfields();
        $this->load->library('Admission_form');
        $this->load->model('class_model');
        $data['adm']       = $this->admission_form;
        $data['classes']   = $this->class_model->getAll();
        $data['questions'] = $this->db->where('belong_to', 'students')->order_by('weight')->order_by('id')->get('custom_fields')->result_array();

        if (!empty($this->input->post('submitbtn'))) {

            $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');

            if ($this->input->post('online_admission_payment') == 'yes') {

                $this->form_validation->set_rules('online_admission_amount', $this->lang->line('amount'), array('required', 'xss_clean', array('check_exists', array($this->onlinestudent_model, 'validate_paymentamount')),
                )
                );

                if ($this->form_validation->run() == true) {
                    $data = array(
                        'online_admission'             => $this->input->post('online_admission'),
                        'online_admission_payment'     => $this->input->post('online_admission_payment'),
                        'online_admission_amount'      => convertCurrencyFormatToBaseAmount($this->input->post('online_admission_amount')),
                        'online_admission_instruction' => $this->input->post('online_admission_instruction'),
                        'online_admission_conditions'  => $this->input->post('online_admission_conditions'),
                        'id'                           => 1,
                    );

                    if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {

                        $img_name = $this->media_storage->fileupload("file", upload_root() . "admission_form/");
                    } else {
                        $img_name = $this->sch_setting_detail->online_admission_application_form;
                    }

                    if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {

                        $this->media_storage->filedelete($this->sch_setting_detail->online_admission_application_form, "uploads/admission_form");
                    }
                    $data['online_admission_application_form'] = $img_name;

                    $this->setting_model->add($data);
                    $this->save_dates();
                    
                    $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">'. $this->lang->line('record_updated_successfully') . '</div>');
                    redirect('admin/onlineadmission/admissionsetting');
                } else {

                    $this->load->view("layout/header");
                    $this->load->view("admin/onlineadmission/onlineadmission_setting", $data);
                    $this->load->view("layout/footer");
                }

            } else {

                $data = array(
                    'online_admission'             => $this->input->post('online_admission'),
                    'online_admission_payment'     => 'no',
                    'online_admission_instruction' => $this->input->post('online_admission_instruction'),
                    'online_admission_conditions'  => $this->input->post('online_admission_conditions'),
                    'id'                           => 1,
                );

                if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {

                    $img_name = $this->media_storage->fileupload("file", upload_root() . "admission_form/");
                } else {
                    $img_name = $this->sch_setting_detail->online_admission_application_form;
                }

                if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {

                    $this->media_storage->filedelete($this->sch_setting_detail->online_admission_application_form, "uploads/admission_form");
                }
                $data['online_admission_application_form'] = $img_name;

                $this->setting_model->add($data);
                $this->save_dates();
                redirect('admin/onlineadmission/admissionsetting');
            }

        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/onlineadmission/onlineadmission_setting", $data);
            $this->load->view("layout/footer");
        }

    }

    /** Opening and closing dates and the closed message, from the General tab. */
    private function save_dates()
    {
        $date = function ($v) {
            $v = trim((string) $v);
            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v) ? $v : null;
        };
        $opens = $date($this->input->post('online_admission_opens'));
        $closes = $date($this->input->post('online_admission_closes'));
        if ($opens && $closes && $closes < $opens) {
            list($opens, $closes) = array($closes, $opens);
        }
        $row = $this->db->select('id')->order_by('id')->limit(1)->get('sch_settings')->row();
        $this->db->where('id', $row->id)->update('sch_settings', array('online_admission_opens' => $opens, 'online_admission_closes' => $closes));
        $config = $this->admission_form->config();
        $config['closed_message'] = (string) $this->input->post('online_admission_closed_message');
        $this->admission_form->save($config);
    }

    private function can_edit()
    {
        if (!$this->rbac->hasPrivilege('online_admission', 'can_view') || $this->input->method() !== 'post') {
            $this->output->set_status_header(403);
            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('access_denied')));
            return false;
        }
        $this->load->library('Admission_form');
        return true;
    }

    /** Form fields tab: required, label, help text and order of the built-in fields. */
    public function saveform()
    {
        if (!$this->can_edit()) {
            return;
        }
        $config = $this->admission_form->config();
        $config['fields'] = (array) $this->input->post('fields');
        $config['order'] = (array) $this->input->post('order');
        $this->admission_form->save($config);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('success_message')));
    }

    /** Classes tab: which classes take applications and how many places each has. */
    public function saveclasses()
    {
        if (!$this->can_edit()) {
            return;
        }
        $config = $this->admission_form->config();
        $classes = array();
        foreach ((array) $this->input->post('classes') as $id => $v) {
            $classes[(int) $id] = array('open' => !empty($v['open']) ? 1 : 0, 'places' => isset($v['places']) && trim($v['places']) !== '' ? (int) $v['places'] : null);
        }
        $config['classes'] = $classes;
        // Places count applications from now on, the first time they're set or when asked to restart.
        if ($config['counted_from'] === '' || $this->input->post('restart_count')) {
            $config['counted_from'] = date('Y-m-d H:i:s');
        }
        $this->admission_form->save($config);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('success_message')));
    }

    /** Questions tab: add or change one of the school's own questions (a student custom field). */
    public function savequestion()
    {
        if (!$this->can_edit()) {
            return;
        }
        $types = array('input', 'textarea', 'number', 'select', 'multiselect', 'checkbox', 'date_picker');
        $id = (int) $this->input->post('id');
        $name = trim(strip_tags((string) $this->input->post('name')));
        $type = (string) $this->input->post('type');
        $options = implode(',', array_filter(array_map('trim', preg_split('/[\r\n,]+/', strip_tags((string) $this->input->post('options'))))));
        $errors = array();
        if ($name === '' || mb_strlen($name) > 100) {
            $errors['name'] = 'Write the question (up to 100 characters).';
        }
        if (!in_array($type, $types, true)) {
            $errors['type'] = 'Choose an answer type.';
        }
        if (in_array($type, array('select', 'multiselect', 'checkbox'), true) && $options === '') {
            $errors['options'] = 'List the choices, one per line.';
        }
        $same = $this->db->where('belong_to', 'students')->where('name', $name)->where('id !=', $id)->count_all_results('custom_fields');
        if ($same) {
            $errors['name'] = 'There is already a question with this wording.';
        }
        $old = $id ? $this->db->where(array('id' => $id, 'belong_to' => 'students'))->get('custom_fields')->row_array() : null;
        if ($id && !$old) {
            $errors['name'] = 'That question no longer exists.';
        }
        if ($errors) {
            echo json_encode(array('status' => 0, 'error' => $errors, 'msg' => reset($errors)));
            return;
        }
        $row = array('name' => $name, 'type' => $type, 'field_values' => $options, 'validation' => $this->input->post('required') ? 1 : 0,
            'bs_column' => in_array($type, array('textarea', 'checkbox'), true) ? 12 : 6);
        if ($old) {
            $this->db->where('id', $id)->update('custom_fields', $row);
            if ($old['name'] !== $name) { // the form's on/off switch is keyed by the question
                $this->db->where('name', $old['name'])->update('online_admission_fields', array('name' => $name));
            }
        } else {
            $weight = (int) $this->db->select_max('weight')->where('belong_to', 'students')->get('custom_fields')->row()->weight;
            $this->db->insert('custom_fields', $row + array('belong_to' => 'students', 'visible_on_table' => 0, 'weight' => $weight + 1, 'is_active' => 1));
            $this->onlinestudent_model->addformfields(array('name' => $name, 'status' => 1));
        }
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('success_message')));
    }

    /** Questions tab: the heading the questions appear under. */
    public function savequestionstitle()
    {
        if (!$this->can_edit()) {
            return;
        }
        $config = $this->admission_form->config();
        $config['questions_title'] = (string) $this->input->post('questions_title');
        $this->admission_form->save($config);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('success_message')));
    }

    public function changeformfieldsetting()
    {
        $this->form_validation->set_rules('name', $this->lang->line('student'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('status', $this->lang->line('status'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'status' => form_error('status'),
                'name'   => form_error('name'),
            );

            $array = array('status' => '0', 'error' => $msg, 'msg' => $this->lang->line('something_went_wrong'));

        } else {
            $insert = array(
                'name'   => $this->input->post('name'),
                'status' => $this->input->post('status'),
            );

            $this->onlinestudent_model->addformfields($insert);

            if ($this->input->post('name') == 'if_guardian_is') {
                $status = $this->input->post('status');
                $this->onlinestudent_model->editguardianfield($status);
            }

            $array = array('status' => '1', 'error' => '', 'msg' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function handle_upload()
    {
        $result = $this->filetype_model->get();
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type = $_FILES["file"]['type'];            
            $file_size = $_FILES["file"]["size"];           
            $file_name = $_FILES["file"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

 
            if ($files = filesize($_FILES['file']['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function download($id)
    {
        $settinglist = $this->setting_model->get($id);         
        $this->media_storage->filedownload($settinglist['online_admission_application_form'], upload_root() . "admission_form");
    }

}
