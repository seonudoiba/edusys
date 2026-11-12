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

    /** Every menu a school gets, what it does and which staff roles have it (views/themes/platform/features.php). */
    public function features()
    {
        $this->page('Features', 'Every EduSYS module and tool, what it does, and which staff roles use it: admissions, fees, report cards, CBT, payroll, website and more.', 'features');
        $this->load_theme('features');
    }

    /** Where school owners sign in: renewals, top-ups and their subscription. */
    public function account()
    {
        $this->page('School owners', 'Sign in to see your EduSYS subscription, renew it, or pay for more students.', 'account');
        $this->load_theme('plan/account');
    }

    /**
     * Find your school (/find-school): staff, students and parents type their school's name and
     * go to its own sign-in page. Nothing is looked up by email or username: the page only says
     * which schools exist, not who belongs to them.
     */
    public function find()
    {
        $this->page('Find your school', 'Find your school and go to its sign-in page.', 'find-school');
        $this->load_theme('plan/find');
    }

    /** GET q (2+ characters): up to 8 schools that are set up and open, as {name, place, url}. */
    public function find_search()
    {
        $q = trim(preg_replace('/\s+/', ' ', (string) $this->input->get('q')));
        $out = array();
        if (mb_strlen($q) >= 2 && mb_strlen($q) <= 100) {
            $rows = $this->db->select('id, school_name, subdomain, school_address, is_platform')
                ->where(array('is_platform' => 0, 'status' => 1))
                ->where('subdomain IS NOT NULL', null, false)->where('subdomain <>', '')
                ->like('school_name', $q)
                ->order_by('school_name')->limit(8)->get('school')->result_array();
            foreach ($rows as $r) {
                $out[] = array(
                    'name' => $r['school_name'],
                    'place' => mb_substr(trim((string) $r['school_address']), 0, 80),
                    'url' => $this->school_provisioner->url_for($r) . 'site/login',
                );
            }
        }
        $this->output->set_content_type('application/json')
            ->set_header('Cache-Control: no-store')
            ->set_output(json_encode(array('schools' => $out)));
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
