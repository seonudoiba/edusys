<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class plan_type extends Admin_Controller
{
    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();

        $this->load->model('plan_type_model');
    }

    public function index()
    {
        $data = array();

        $data['plan_list'] = $this->plan_type_model->get();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/plan_type', $data);
        $this->load->view('layout/footer');
    }

    public function add()
    {
        // if (!$this->rbac->hasPrivilege('admission_enquiry', 'can_add')) {
        //     access_denied();
        // }
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sort_order', $this->lang->line('sort_order'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
                'sort_order' => form_error('sort_order')
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $plan_type = array(
                'name' => $this->input->post('name'),
                'sort_order' => $this->input->post('sort_order'),
                'status' => $this->input->post('status'),
            );
            $this->plan_type_model->add($plan_type);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function details($id)
    {
        $data['plan_type_data'] = $this->plan_type_model->get($id);
        $this->load->view('admin/frontoffice/plantypeeditmodalview', $data);
    }

    public function editpost($id)
    {
        // if (!$this->rbac->hasPrivilege('admission_enquiry', 'can_edit')) {
        //     access_denied();
        // }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sort_order', $this->lang->line('sort_order'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
                'sort_order' => form_error('sort_order')
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $plan_type_update = array(
                'name' => $this->input->post('name'),
                'sort_order' => $this->input->post('sort_order'),
                'status' => $this->input->post('status'),
            );
            $this->plan_type_model->plan_type_update($id, $plan_type_update);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        // if (!$this->rbac->hasPrivilege('admission_enquiry', 'can_delete')) {
        //     access_denied();
        // }
        if (!empty($id)) {
            $this->plan_type_model->plan_type_delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        }
        echo json_encode($array);
    }
}