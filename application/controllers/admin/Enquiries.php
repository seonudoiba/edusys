<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The platform's inbox for its website's contact and complaint forms (platform_enquiries):
 * read, reply by email (template enquiry_reply) and close.
 */
class Enquiries extends Admin_Controller
{
    const PERMISSION = 'platform_settings';
    const STATUSES = array('new' => array('New', 'label-warning'), 'replied' => array('Replied', 'label-info'), 'closed' => array('Closed', 'label-default'));

    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();
        $this->load->library(array('Form_token', 'Platform_mailer', 'Platform_audit'));
    }

    public function index()
    {
        $this->require_privilege('can_view');
        $this->menu();
        $status = $this->input->get('status') === null ? 'new' : (string) $this->input->get('status');
        $kind = (string) $this->input->get('kind');
        $q = trim((string) $this->input->get('q'));
        $db = $this->db;
        if (isset(self::STATUSES[$status])) {
            $db->where('status', $status);
        }
        if (in_array($kind, array('contact', 'complaint', 'quote'), true)) {
            $db->where('kind', $kind);
        }
        if ($q !== '') {
            $db->group_start()->like('name', $q)->or_like('email', $q)->or_like('subject', $q)->or_like('message', $q)->group_end();
        }
        $rows = $db->order_by('id', 'DESC')->limit(500)->get('platform_enquiries')->result_array();
        $counts = array_fill_keys(array_keys(self::STATUSES), 0);
        foreach ($this->db->select('status, COUNT(*) AS n')->group_by('status')->get('platform_enquiries')->result_array() as $r) {
            $counts[$r['status']] = (int) $r['n'];
        }
        $this->load->view('layout/header');
        $this->load->view('admin/enquiries/index', array('rows' => $rows, 'counts' => $counts, 'status' => $status, 'kind' => $kind, 'q' => $q));
        $this->load->view('layout/footer');
    }

    public function view($id = null)
    {
        $this->require_privilege('can_view');
        $this->menu();
        $row = $this->row_or_404($id);
        $this->load->view('layout/header');
        $this->load->view('admin/enquiries/view', array(
            'r' => $row,
            'token' => $this->form_token->field(),
            'can_edit' => $this->rbac->hasPrivilege(self::PERMISSION, 'can_edit'),
            'message' => $this->session->flashdata('enquiry_msg'),
            'error' => $this->session->flashdata('enquiry_error'),
        ));
        $this->load->view('layout/footer');
    }

    public function reply($id = null)
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $row = $this->row_or_404($id);
        $message = trim((string) $this->input->post('message'));
        if ($message === '' || mb_strlen($message) > 20000) {
            $this->back($row['id'], null, 'Write a reply first.');
        }
        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            $this->back($row['id'], null, 'This enquiry has no valid email address to reply to.');
        }
        $ok = $this->platform_mailer->send('enquiry_reply', $row['email'], array(
            'name' => $row['name'],
            'subject' => $row['subject'],
            'message' => $message,
            'original' => mb_strimwidth($row['message'], 0, 1000, '…'),
        ));
        if (!$ok) {
            $this->back($row['id'], null, 'The email could not be sent (see Communication > Email log). Nothing was saved.');
        }
        $admin = (array) $this->session->userdata('admin');
        $this->db->where('id', (int) $row['id'])->update('platform_enquiries', array(
            'status' => $this->input->post('close') ? 'closed' : 'replied',
            'reply' => $message,
            'replied_by' => isset($admin['username']) ? $admin['username'] : '',
            'replied_at' => date('Y-m-d H:i:s'),
        ));
        $this->platform_audit->record('enquiry.reply', 'Replied to the website enquiry #' . (int) $row['id'] . ' from ' . $row['email']);
        $this->back($row['id'], 'Reply sent to ' . $row['email'] . '.');
    }

    /** A price for a large school: emails a private link it registers and pays with. */
    public function send_quote($id = null)
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $row = $this->row_or_404($id);
        $this->load->library('Plan_pricing');
        $students = (int) $this->input->post('students');
        $rate = (float) $this->input->post('rate');
        $setup = (float) $this->input->post('setup');
        if ($students < 1 || $rate <= 0 || $setup < 0) {
            $this->back($row['id'], null, 'Enter the students, the rate per student per term, and the setup fee.');
        }
        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            $this->back($row['id'], null, 'This enquiry has no valid email address.');
        }
        $token = bin2hex(random_bytes(20));
        $expires = date('Y-m-d', strtotime('+' . Plan_pricing::QUOTE_VALID_DAYS . ' days'));
        $this->db->insert('platform_quotes', array('token' => $token, 'enquiry_id' => (int) $row['id'], 'school_name' => $row['school_name'] ?: $row['name'],
            'contact_name' => $row['name'], 'email' => $row['email'], 'phone' => $row['phone'], 'students' => $students, 'rate_per_term' => $rate,
            'setup_fee' => $setup, 'expires_at' => $expires));
        $url = site_url('subscription/add?quote=' . $token);
        $ok = $this->platform_mailer->send('quote_ready', $row['email'], array('name' => $row['name'], 'school_name' => $row['school_name'] ?: $row['name'],
            'students' => number_format($students), 'rate' => 'NGN ' . number_format($rate, 2), 'setup' => 'NGN ' . number_format($setup, 2),
            'expires' => date('j F Y', strtotime($expires)), 'quote_url' => $url));
        $admin = (array) $this->session->userdata('admin');
        $this->db->where('id', (int) $row['id'])->update('platform_enquiries', array('status' => 'replied', 'reply' => 'Quote: ' . number_format($students)
            . ' students at NGN ' . number_format($rate, 2) . ' per term, setup NGN ' . number_format($setup, 2) . ". Link: $url",
            'replied_by' => isset($admin['username']) ? $admin['username'] : '', 'replied_at' => date('Y-m-d H:i:s')));
        $this->platform_audit->record('enquiry.quote', 'Sent a quote to ' . $row['email']);
        $this->back($row['id'], $ok ? 'Quote sent to ' . $row['email'] . '.' : 'Saved, but the email could not be sent. Send the school this link: ' . $url);
    }

    public function status($id = null)
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $row = $this->row_or_404($id);
        $status = (string) $this->input->post('status');
        if (isset(self::STATUSES[$status])) {
            $this->db->where('id', (int) $row['id'])->update('platform_enquiries', array('status' => $status));
        }
        $this->back($row['id'], 'Status changed.');
    }

    public function delete($id = null)
    {
        $this->require_privilege('can_delete');
        $this->form_token->require_post();
        $row = $this->row_or_404($id);
        $this->db->where('id', (int) $row['id'])->delete('platform_enquiries');
        $this->platform_audit->record('enquiry.delete', 'Deleted the website enquiry #' . (int) $row['id'] . ' from ' . $row['email']);
        $this->session->set_flashdata('enquiry_msg', 'Enquiry deleted.');
        redirect('admin/enquiries');
    }

    private function row_or_404($id)
    {
        $row = ctype_digit((string) $id) ? $this->db->where('id', (int) $id)->get('platform_enquiries')->row_array() : null;
        if (!$row) {
            show_404();
        }
        return $row;
    }

    private function back($id, $message = null, $error = null)
    {
        if ($message !== null) {
            $this->session->set_flashdata('enquiry_msg', $message);
        }
        if ($error !== null) {
            $this->session->set_flashdata('enquiry_error', $error);
        }
        redirect('admin/enquiries/view/' . (int) $id);
    }

    private function menu()
    {
        $this->session->set_userdata('top_menu', 'platform_admin');
        $this->session->set_userdata('sub_menu', 'admin/enquiries');
    }

    private function require_privilege($action)
    {
        if (!$this->rbac->hasPrivilege(self::PERMISSION, $action)) {
            access_denied();
        }
    }
}
