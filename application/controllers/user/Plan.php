<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Plan extends Student_Controller
{

    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();

        $this->load->model(array('subscription_model', 'plan_model', 'setting_model', 'plan_type_model'));
        $this->current_classSection = $this->customlib->getStudentCurrentClsSection();
        $this->result               = $this->customlib->getLoggedInUserData();
        $this->load->library("aws3");
        $this->load->library("media_storage");
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->library(array('enc_lib', 'cart', 'customlib'));
    }

    public function planlist()
    {   
        $this->session->set_userdata('top_menu', 'user/planlist');

        $data = array();
        $data['plans'] = $this->plan_model->get();
        $data['school_id'] = $this->result['school_id'];

        $data['plan_data'] = '';
        
        $student_data = $this->session->userdata('student');
        $school_id = $student_data['school_id'];
        // $school_id = 51;
        
        $this->db->select('subscription.*, school.*, school.id as school_id, subscription.created_at as date');
        $this->db->from('subscription');
        $this->db->join('school', 'subscription.school_id = school.id');
        $this->db->where('subscription.school_id', $school_id);
        $this->db->where('subscription.payment_status', 1);
        $this->db->order_by('subscription.created_at', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        // print_r($school_id);
        if ($query->num_rows() > 0) {
            $subscription = $query->row();
        
            $created_at = $subscription->date;
            $days = $subscription->days;
        
            $created_timestamp = strtotime($created_at);
            $expiration_timestamp = $created_timestamp + ($days * 86400);
            $current_timestamp = time();
        
            $remaining_days = floor(($expiration_timestamp - $current_timestamp) / 86400);
            $status = ($remaining_days <= 0) ? 'Expired' : 'Active';
        
            $data['plan_data'] = [
                'remaining_days' => $remaining_days,
                'status' => $status,
                'created_at' => $created_at,
                'subscription' => $subscription,
                'school_id' => $school_id
            ];
        } else {
            $data['plan_data'] = null;
        }
        
        foreach($data['plans'] as $key => $plans){
            $list = $plans['list'];
            unset($data['plans'][$key]['list']);  
            $data['plans'][$key]['list'] = array();
            $data['plans'][$key]['list'] = $this->plan_type_model->get_plan_type($list);
        }
        // echo '<pre>';print_r($data);echo '</pre>';die();
        $this->load->view('layout/student/header');
        $this->load->view('user/plan/planlist', $data);
        $this->load->view('layout/student/footer');
    }

    public function success(){
        $data = array();

        $this->load->view('layout/student/header');
        $this->load->view('user/subscription/success', $data);
        // $this->load->view('layout/student/footer');
        // print_r('success');die;
    }

    public function failure(){
        $data = array();

        $this->load->view('layout/student/header');
        $this->load->view('user/subscription/failure', $data);

    }
}