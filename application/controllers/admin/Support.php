<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Help & Support for a school: its support tickets with the platform team.
 * School hosts only (the platform team answers tickets in admin/support_desk).
 */
class Support extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!Tenant::has() || Tenant::is_platform()) {
            show_404();
        }
        $this->load->library(array('Form_token', 'Support_tickets', 'Platform_settings'));
    }

    public function index()
    {
        $this->require_access();
        $this->menu();
        $status = (string) $this->input->get('status');
        $data = array(
            'tickets' => $this->support_tickets->search(array('school_ref' => Tenant::id(), 'status' => $status)),
            'counts' => $this->support_tickets->counts(Tenant::id()),
            'status' => $status,
            'support_email' => $this->platform_settings->get('support_email'),
            'support_phone' => $this->platform_settings->get('support_phone'),
            'message' => $this->session->flashdata('support_msg'),
        );
        $this->load->view('layout/header');
        $this->load->view('admin/support/index', $data);
        $this->load->view('layout/footer');
    }

    public function create()
    {
        $this->require_access();
        $this->menu();
        $errors = array();
        if ($this->input->method() === 'post') {
            $this->form_token->require_post();
            $subject = trim((string) $this->input->post('subject'));
            $message = trim((string) $this->input->post('message'));
            if ($subject === '' || mb_strlen($subject) > 255) {
                $errors[] = 'Enter a subject (up to 255 characters).';
            }
            if ($message === '' || mb_strlen($message) > 20000) {
                $errors[] = 'Describe the problem or question.';
            }
            if (!$errors) {
                $id = $this->support_tickets->open(Tenant::id(), $this->staff(), $subject,
                    (string) $this->input->post('category'), (string) $this->input->post('priority'), $message);
                $this->session->set_flashdata('support_msg', 'Your request was sent. The platform team will reply here and by email.');
                redirect('admin/support/view/' . $id);
            }
        }
        $this->load->view('layout/header');
        $this->load->view('admin/support/create', array('errors' => $errors, 'token' => $this->form_token->field()));
        $this->load->view('layout/footer');
    }

    public function view($id = null)
    {
        $this->require_access();
        $this->menu();
        $ticket = $this->ticket_or_404($id);
        $this->load->view('layout/header');
        $this->load->view('admin/support/conversation', array(
            'ticket' => $ticket,
            'messages' => $this->support_tickets->messages($ticket['id']),
            'token' => $this->form_token->field(),
            'reply_url' => site_url('admin/support/reply/' . (int) $ticket['id']),
            'back_url' => site_url('admin/support'),
            'side' => 'school',
            'message' => $this->session->flashdata('support_msg'),
            'error' => $this->session->flashdata('support_error'),
        ));
        $this->load->view('layout/footer');
    }

    public function reply($id = null)
    {
        $this->require_access();
        $this->form_token->require_post();
        $ticket = $this->ticket_or_404($id);
        if ($this->input->post('close')) {
            $this->support_tickets->set_status($ticket, 'closed');
            $this->session->set_flashdata('support_msg', 'The request is closed. Reply any time to reopen it.');
            redirect('admin/support/view/' . (int) $ticket['id']);
        }
        $message = trim((string) $this->input->post('message'));
        if ($message === '' || mb_strlen($message) > 20000) {
            $this->session->set_flashdata('support_error', 'Write a message first.');
        } else {
            $staff = $this->staff();
            $this->support_tickets->school_reply($ticket, $staff['name'], $message);
            $this->session->set_flashdata('support_msg', 'Reply sent.');
        }
        redirect('admin/support/view/' . (int) $ticket['id']);
    }

    private function ticket_or_404($id)
    {
        $ticket = ctype_digit((string) $id) ? $this->support_tickets->ticket((int) $id, Tenant::id()) : null;
        if (!$ticket) {
            show_404();
        }
        return $ticket;
    }

    private function staff()
    {
        $admin = (array) $this->session->userdata('admin');
        return array(
            'id' => isset($admin['id']) ? (int) $admin['id'] : 0,
            'name' => isset($admin['username']) ? (string) $admin['username'] : '',
            'email' => isset($admin['email']) ? (string) $admin['email'] : '',
        );
    }

    private function require_access()
    {
        if (!$this->rbac->hasPrivilege('general_setting', 'can_view')) {
            access_denied();
        }
    }

    private function menu()
    {
        $this->session->set_userdata('top_menu', 'help_support');
        $this->session->set_userdata('sub_menu', 'admin/support');
    }
}
