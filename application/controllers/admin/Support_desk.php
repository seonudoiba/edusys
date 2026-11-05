<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The platform team's support desk: every school's tickets, replies and status changes.
 */
class Support_desk extends Admin_Controller
{
    const PERMISSION = 'platform_settings';

    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();
        $this->load->library(array('Form_token', 'Support_tickets', 'Platform_audit'));
    }

    public function index()
    {
        $this->require_privilege('can_view');
        $this->menu();
        $f = array(
            'status' => (string) $this->input->get('status'),
            'q' => trim((string) $this->input->get('q')),
            'school_ref' => (int) $this->input->get('school'),
        );
        if ($this->input->get('status') === null) {
            $f['status'] = 'open';
        }
        $this->load->view('layout/header');
        $this->load->view('admin/support/desk', array(
            'tickets' => $this->support_tickets->search($f, 500),
            'counts' => $this->support_tickets->counts(),
            'f' => $f,
        ));
        $this->load->view('layout/footer');
    }

    public function view($id = null)
    {
        $this->require_privilege('can_view');
        $this->menu();
        $ticket = $this->ticket_or_404($id);
        $this->load->view('layout/header');
        $this->load->view('admin/support/conversation', array(
            'ticket' => $ticket,
            'messages' => $this->support_tickets->messages($ticket['id']),
            'token' => $this->form_token->field(),
            'reply_url' => site_url('admin/support_desk/reply/' . (int) $ticket['id']),
            'back_url' => site_url('admin/support_desk'),
            'side' => 'platform',
            'can_reply' => $this->rbac->hasPrivilege(self::PERMISSION, 'can_edit'),
            'message' => $this->session->flashdata('support_msg'),
            'error' => $this->session->flashdata('support_error'),
        ));
        $this->load->view('layout/footer');
    }

    public function reply($id = null)
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $ticket = $this->ticket_or_404($id);
        $status = (string) $this->input->post('status');
        $message = trim((string) $this->input->post('message'));
        if ($message === '' && isset(Support_tickets::STATUSES[$status])) {
            $this->support_tickets->set_status($ticket, $status);
            $this->platform_audit->record('support.status', 'Set ticket #' . (int) $ticket['id'] . ' to ' . $status, $ticket['school_ref']);
            $this->session->set_flashdata('support_msg', 'Status changed.');
        } elseif ($message === '' || mb_strlen($message) > 20000) {
            $this->session->set_flashdata('support_error', 'Write a reply first.');
        } else {
            $admin = (array) $this->session->userdata('admin');
            $name = isset($admin['username']) ? $admin['username'] : 'Support';
            $this->support_tickets->platform_reply($ticket, $name, $message, $status === 'closed');
            $this->platform_audit->record('support.reply', 'Replied to ticket #' . (int) $ticket['id'], $ticket['school_ref']);
            $this->session->set_flashdata('support_msg', 'Reply sent to ' . $ticket['opened_by_email'] . '.');
        }
        redirect('admin/support_desk/view/' . (int) $ticket['id']);
    }

    private function ticket_or_404($id)
    {
        $ticket = ctype_digit((string) $id) ? $this->support_tickets->ticket((int) $id) : null;
        if (!$ticket) {
            show_404();
        }
        return $ticket;
    }

    private function menu()
    {
        $this->session->set_userdata('top_menu', 'platform_admin');
        $this->session->set_userdata('sub_menu', 'admin/support_desk');
    }

    private function require_privilege($action)
    {
        if (!$this->rbac->hasPrivilege(self::PERMISSION, $action)) {
            access_denied();
        }
    }
}
