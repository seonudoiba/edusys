<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class Subscription extends Admin_Controller
{
    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();

        $this->load->model('Subscription');
    }
    public function index()
    {
        $data['title'] = 'Subscription List';
        // $plan_data = $this->Plan_model->get();
        // $data['schools_data'] = $plan_data;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/subscription/subscription_list', $data);
        $this->load->view('layout/footer', $data);
    }
    public function create()
    {
        $data = array();

        $this->load->view('layout/header');
        $this->load->view('admin/subscription/subscription_create', $data);
        $this->load->view('layout/footer');
    }

    public function save($id = null)
    {
        $this->load->helper('form');

        $post_data = $this->input->post();

        $data = array(
            'owner_name' => $post_data['owner_name']
        );

        if ($id) {
            $this->db->where('id', $id);
            $this->db->update('plan', $data);

            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('message', 'Plan record updated successfully!');
            } else {
                $this->session->set_flashdata('message', 'No changes made to the plan record.');
            }
        } else {
            $this->db->insert('school', $data);

            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('message', 'New plan record inserted successfully!');
            } else {
                $this->session->set_flashdata('message', 'Failed to insert plan record.');
            }
        }

        redirect('admin/plans');
    }

    public function edit($id)
    {
        $data['plan'] = $this->School_model->get($id);
        if (empty($data)) {
            $this->session->set_flashdata('message', 'Plan not found!');
            redirect('admin/plan');
        }

        $data['title'] = 'Edit Plan';

        $this->load->view('layout/header', $data);
        $this->load->view('admin/school/schoolform', $data);
        $this->load->view('layout/footer');
    }

    public function delete($id)
    {
        if (empty($id)) {
            $this->session->set_flashdata('message', 'Invalid school ID!');
            redirect('admin/schools');
        }

        // Call the model to delete the school
        $this->load->model('School_model');
        $result = $this->School_model->delete($id);

        // Check if the deletion was successful
        if ($result) {
            $this->session->set_flashdata('message', 'School record deleted successfully!');
        } else {
            $this->session->set_flashdata('message', 'Failed to delete school record!');
        }

        // Redirect to the schools list page
        redirect('admin/schools');
    }
}