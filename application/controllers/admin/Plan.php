<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class plan extends Admin_Controller
{
    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();

        $this->load->model('plan_model');
        $this->load->model('plan_type_model');
    }

    public function index()
    {
        $data = array();

        $data['plan_type_list'] = $this->plan_type_model->get_plan_type();

        $data['plan_list'] = $this->plan_model->get();

        $this->load->view('layout/header');
        $this->load->view('admin/plan/planlist', $data);
        $this->load->view('layout/footer');
    }

    public function add()
    {
        // if (!$this->rbac->hasPrivilege('admission_enquiry', 'can_add')) {
        //     access_denied();
        // }
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('text', $this->lang->line('text'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('days', $this->lang->line('days'), 'trim|required|xss_clean');
        // $this->form_validation->set_rules('plan_type_list', $this->lang->line('plan_type_list'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'title' => form_error('title'),
                'text' => form_error('text'),
                'days' => form_error('days'),
                // 'plan_type_list' => "The plan feature is required.",
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $status = 1;
            if($this->input->post('status')){
                $status = $this->input->post('status');
            }
            $plan = array(
                'title' => $this->input->post('title'),
                'text' => $this->input->post('text'),
                'list' => $this->input->post('plan_type_list') ? json_encode($this->input->post('plan_type_list')) : [],
                'status' => $status,
                'price' => $this->input->post('price') ? $this->input->post('price') : '1',
                'oprice' => $this->input->post('oprice'),
                'days' => $this->input->post('days'),
            );
            $this->plan_model->add($plan);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function details($id)
    {
        $data['plan_data'] = $this->plan_model->get($id);
        // $data['plan_data']['list'] = is_array($data['plan_data']['list']) ? $data['plan_data']['list'] : explode(',', $data['plan_data']['list']);
        $data['list'] = json_decode($data['plan_data']['list'], true);
        $data['plan_type_list'] = $this->plan_type_model->get_plan_type();

        $this->load->view('admin/plan/planeditmodalview', $data);
    }

    public function editpost($id)
    {
        // if (!$this->rbac->hasPrivilege('admission_enquiry', 'can_edit')) {
        //     access_denied();
        // }
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('text', $this->lang->line('text'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('days', $this->lang->line('days'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('plan_type_list[]', $this->lang->line('plan_type_list'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'title' => form_error('title'),
                'text' => form_error('text'),
                'days' => form_error('days'),
                'plan_type_list' => "The plan feature is required.",
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $plan_type_update = array(
                'title' => $this->input->post('title'),
                'text' => $this->input->post('text'),
                'status' => $this->input->post('status'),
                'list' => $this->input->post('plan_type_list') ? json_encode($this->input->post('plan_type_list')) : [],
                'price' => $this->input->post('price'),
                'oprice' => $this->input->post('oprice'),
                'days' => $this->input->post('days'),
            );
            $this->plan_model->plan_update($id, $plan_type_update);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
        die;
    }

    public function delete()
    {
        $array = array();
        $id = $this->input->post('plan_id');
        if (!empty($id)) {
            $this->plan_model->delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        }
        echo json_encode($array);
        die;
    }
}