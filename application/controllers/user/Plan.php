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
        $this->load->library(array('enc_lib', 'cart', 'customlib', 'Plan_pricing', 'Payment_gateways', 'Platform_settings'));
    }

    public function planlist()
    {
        $this->session->set_userdata('top_menu', 'user/planlist');
        $student_data = $this->session->userdata('student');
        $school_id = (int) $student_data['school_id'];
        $current = $this->db->where(array('school_id' => $school_id, 'payment_status' => 1))->order_by('id', 'desc')->limit(1)->get('subscription')->row_array();
        $ends = $current ? strtotime($current['created_at']) + (int) $current['days'] * 86400 : 0;
        $days_left = $current ? (int) floor(($ends - time()) / 86400) : -1;
        $onboarding = $current && $current['kind'] === 'setup'; // only the setup fee paid so far
        $topup = !$onboarding && $current && !empty($current['student_count']) ? $this->plan_pricing->topup($current, (int) $current['student_count'] + 1) : null;
        $this->load->library('School_discounts');
        $this->load->view('layout/student/header');
        $this->load->view('user/plan/planlist', array(
            'current' => $current,
            'ends' => $ends,
            'days_left' => $days_left,
            'status' => $onboarding ? 'Setup fee paid: subscription not paid yet' : ($days_left >= 0 ? 'Active' : 'Expired'),
            'onboarding' => $onboarding,
            'discount' => $this->school_discounts->active($school_id),
            'active' => (int) $this->db->without_tenant(function ($db) use ($school_id) {
                return $db->where(array('school_id' => $school_id, 'is_active' => 'yes'))->count_all_results('students');
            }),
            'bands' => $this->plan_pricing->bands(),
            'methods' => $this->payment_gateways->platform_enabled(),
            'topup_terms' => $topup && $topup['ok'] ? $topup['terms'] : 0,
            'support' => $this->platform_settings->get('support_email'),
        ));
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