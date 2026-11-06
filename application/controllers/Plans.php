<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The platform's public sales pages: /product, /plans (pricing) and /plans/quote (large schools).
 * Always in the platform's own design, whatever theme is selected.
 */
class Plans extends Front_Controller
{
    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();
        $this->load->library(array('Plan_pricing', 'Platform_settings', 'Platform_mailer', 'captchalib', 'School_provisioner'));
        $this->theme_path = 'platform';
    }

    private function page($title, $description, $active)
    {
        $this->data['active_menu'] = $active;
        $this->data['page_side_bar'] = false;
        $this->data['featured_image'] = false;
        $this->data['page'] = array('title' => $title, 'meta_title' => $title, 'meta_keyword' => '', 'meta_description' => $description);
        $this->data['setting_data'] = $this->setting_model->get();
    }

    public function index()
    {
        $this->page('Pricing', 'EduSYS is priced per student, per term, with a one-time implementation and training fee.', 'plans');
        $this->data['bands'] = $this->plan_pricing->bands();
        $this->load_theme('plan/plans');
    }

    public function product()
    {
        $this->page('The product', 'Everything a school runs on: admissions, fees, attendance, examinations, staff, communication and its own website.', 'product');
        $this->load_theme('product');
    }

    /** Where school owners sign in: renewals, top-ups and their subscription. */
    public function account()
    {
        $this->page('School owners', 'Sign in to see your EduSYS subscription, renew it, or pay for more students.', 'account');
        $this->load_theme('plan/account');
    }

    /** Schools above the largest band: the request lands in Support > Website Enquiries. */
    public function quote()
    {
        $this->page('Request a quote', 'A custom quote for schools with more than 3,000 students.', 'plans');
        $this->data['errors'] = array();
        $this->data['sent'] = (bool) $this->session->flashdata('quote_sent');
        if ($this->input->method() === 'post') {
            $in = function ($k, $max) { return mb_substr(trim(strip_tags((string) $this->input->post($k))), 0, $max); };
            $row = array('school_name' => $in('school_name', 255), 'name' => $in('name', 255), 'email' => $in('email', 255), 'phone' => $in('phone', 50),
                'students' => (int) $this->input->post('students'), 'message' => $in('message', 5000));
            $errors = array();
            // A hidden field: people leave it empty, form-filling robots don't.
            if (trim((string) $this->input->post('website')) !== '') {
                $this->session->set_flashdata('quote_sent', 1);
                redirect('plans/quote');
            }
            if ($row['school_name'] === '') { $errors[] = 'Enter the name of your school.'; }
            if ($row['name'] === '') { $errors[] = 'Enter your name.'; }
            if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid email address.'; }
            if ($row['students'] < 1) { $errors[] = 'Enter the number of students.'; }
            if (!$errors) {
                $this->db->insert('platform_enquiries', $row + array('kind' => 'quote', 'subject' => 'Quote request: ' . $row['school_name']));
                $id = (int) $this->db->insert_id();
                $this->platform_mailer->send('quote_request', $this->platform_settings->get('notify_email'), array(
                    'school_name' => $row['school_name'], 'students' => number_format($row['students']), 'name' => $row['name'], 'email' => $row['email'],
                    'phone' => $row['phone'], 'message' => $row['message'],
                    'enquiry_url' => $this->school_provisioner->url_for(array('is_platform' => 1)) . 'admin/enquiries/view/' . $id,
                ));
                $this->session->set_flashdata('quote_sent', 1);
                redirect('plans/quote');
            }
            $this->data['errors'] = $errors;
        }
        $this->data['students'] = (int) ($this->input->post('students') ?: $this->input->get('students'));
        $this->load_theme('plan/quote');
    }
}
