<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class Subscription_list extends Admin_Controller
{
    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();

        $this->load->model('Subscription_model', 'Course_model');
        $this->load->model('Plan_model');
        $this->load->model('School_model');
        $this->load->library('Enc_lib');
        $this->load->model('studentcourse_model');
        $this->load->model('setting_model');
    }

    public function index()
    {
        $data = array();
        $data['subscription_list'] = array();

        $this->db->select('*')->from('subscription');

        if (isset($id) && $id != null) {
            $this->db->where('subscription.id', $id);
        } else {
            $this->db->order_by('subscription.created_at', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            $subscription_list =  $query->row_array();
        } else {
            $subscription_list = $query->result_array();
        }

        $plan_list = $this->Plan_model->get();
        

        $school_list = $this->School_model->get();

        $plan_lookup = [];
        foreach ($plan_list as $plan) {
            $plan_lookup[$plan['plan_id']] = $plan['title'];
        }

        $school_lookup = [];
        foreach ($school_list as $school) {
            $school_lookup[$school['id']] = $school['school_name'];
        }

        foreach ($subscription_list as $subscription) {
            $plan_name = '';
            $school_name = '';
            if (isset($plan_lookup[$subscription['plan_id']])) {
                $plan_name = $plan_lookup[$subscription['plan_id']];
            }

            if (isset($school_lookup[$subscription['school_id']])) {
                $school_name = $school_lookup[$subscription['school_id']];
            }

            if ($subscription['discount_amt'] > 0) {
                $subscription['total'] = ($subscription['price'] - $subscription['discount_amt']);
            }

            $data['subscription_list'][] = array(
                'id' => $subscription['id'],
                'plan_id' => $subscription['plan_id'],
                'payment_method' => $subscription['payment_method'],
                'payment_status' => ($subscription['payment_status'] == 1) ? 'Complete' : 'Pending',
                'total' => $subscription['total'],
                'plan_name' => $plan_name,
                'school_name' => $subscription['school_name'],
                'owner_name' => $subscription['owner_name'],
                'created_at' => $subscription['created_at'],
                'school_id' => $subscription['school_id'],
            );
        }

        $this->load->view('layout/header');
        $this->load->view('admin/subscription/subscriptionlist', $data);
        $this->load->view('layout/footer');
    }

    public function delete($id)
    {
        $array = array();
        
        if (!empty($id)) {
            $this->db->where('id', $id);
            $query = $this->db->get('subscription');
            if ($query->num_rows() > 0) {
                $result = $query->row_array();
                if(isset($result['school_id']) && $result['school_id']){
                    $this->db->where('id', $result['school_id']);
                    $query = $this->db->get('school');
                    if ($query->num_rows() > 0) {
                        $array = array('status' => 'fail', 'error' => '', 'message' => 'Your school is exist, please delete school first.');
                        echo json_encode($array);
                        die;
                    }
                }    
            }
            
            $this->db->where('id', $id);
            $this->db->delete('subscription');

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        }
        echo json_encode($array);
        die;
    }
}