<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Bank transfer payments for subscriptions (platform admin): review the receipt a school
 * uploaded, then approve it (the subscription is activated exactly as after a verified
 * Paystack payment) or reject it with a reason (the school can upload a new receipt).
 */
class Subscription_bank_transfers extends Admin_Controller
{
    const PERMISSION = 'subscription_list';

    private $statuses = array('pending', 'awaiting_receipt', 'approved', 'rejected');

    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();
        $this->load->model('subscription_bank_transfer_model');
        $this->load->library(array('Platform_audit', 'Platform_mailer'));
    }

    public function index()
    {
        $this->require_privilege();
        $status = (string) $this->input->get('status');
        $status = in_array($status, $this->statuses, true) || $status === 'all' ? $status : 'pending';
        $this->session->set_userdata('top_menu', 'subscription');
        $this->session->set_userdata('sub_menu', 'admin/subscription_bank_transfers');

        $data = array(
            'status' => $status,
            'transfers' => $this->subscription_bank_transfer_model->get_list($status === 'all' ? null : $status),
            'pending_count' => $this->subscription_bank_transfer_model->count_pending(),
            'currency_symbol' => $this->customlib->getSchoolCurrencyFormat(),
        );
        $this->load->view('layout/header');
        $this->load->view('admin/subscription/bank_transfers', $data);
        $this->load->view('layout/footer');
    }

    public function view($id = null)
    {
        $this->require_privilege();
        $transfer = $this->transfer_or_404($id);
        $this->session->set_userdata('top_menu', 'subscription');
        $this->session->set_userdata('sub_menu', 'admin/subscription_bank_transfers');

        $data = array(
            'transfer' => $transfer,
            'reviewer' => $transfer['reviewed_by'] ? $this->db->select('name, surname')->where('id', (int) $transfer['reviewed_by'])->get('staff')->row_array() : null,
            'currency_symbol' => $this->customlib->getSchoolCurrencyFormat(),
            'form_token' => $this->form_token(),
            'message' => $this->session->flashdata('bank_transfer_msg'),
            'error' => $this->session->flashdata('bank_transfer_error'),
        );
        $this->load->view('layout/header');
        $this->load->view('admin/subscription/bank_transfer_view', $data);
        $this->load->view('layout/footer');
    }

    /** Streams the receipt (the receipts folder refuses direct web access). */
    public function receipt($id = null)
    {
        $this->require_privilege();
        $transfer = $this->transfer_or_404($id);
        $path = $this->subscription_bank_transfer_model->receipt_path($transfer['receipt_file']);
        if ($path === null) {
            show_404();
        }
        $mime = $this->subscription_bank_transfer_model->receipt_mime($transfer['receipt_file']);
        $name = $transfer['receipt_name'] ?: basename($path);
        $disposition = $this->input->get('download') ? 'attachment' : 'inline';
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: ' . $disposition . '; filename="' . str_replace('"', '', $name) . '"');
        header('X-Content-Type-Options: nosniff');
        header("Content-Security-Policy: default-src 'none'; img-src 'self'; style-src 'unsafe-inline'; sandbox");
        header('Cache-Control: private, no-store');
        readfile($path);
        exit;
    }

    public function approve($id = null)
    {
        $this->require_privilege();
        $transfer = $this->checked_post($id);

        $this->db->trans_begin();
        $reviewed = $this->subscription_bank_transfer_model->review($transfer['id'], 'approved', $this->customlib->getStaffID());
        if (!$reviewed) {
            $this->db->trans_rollback();
            return $this->back($transfer['id'], null, 'This payment was already reviewed.');
        }
        $this->load->library('Subscription_activation');
        $this->subscription_activation->activate((int) $transfer['subscription_id']);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return $this->back($transfer['id'], null, 'Approving the payment failed; nothing was changed.');
        }
        $this->db->trans_commit();
        $school_id = (int) $this->db->select('school_id')->where('id', (int) $transfer['subscription_id'])->get('subscription')->row()->school_id;
        $this->platform_audit->record('payment.approve', 'Approved the bank transfer of ' . $transfer['school_name'] . ' (' . number_format((float) $transfer['amount'], 2) . ')', $school_id ?: null);
        $this->platform_mailer->send('receipt_approved', $transfer['email_address'], $this->mail_vars($transfer), $school_id ?: null);
        $this->back($transfer['id'], 'Payment approved. The school is now in the Schools list: set its subdomain there and approve it.');
    }

    public function reject($id = null)
    {
        $this->require_privilege();
        $transfer = $this->checked_post($id);
        $note = trim((string) $this->input->post('review_note'));
        if ($note === '') {
            return $this->back($transfer['id'], null, 'Give the reason for rejecting: the school sees it.');
        }
        if (!$this->subscription_bank_transfer_model->review($transfer['id'], 'rejected', $this->customlib->getStaffID(), mb_substr($note, 0, 2000))) {
            return $this->back($transfer['id'], null, 'This payment was already reviewed.');
        }
        $this->platform_audit->record('payment.reject', 'Rejected the bank transfer of ' . $transfer['school_name'] . ': ' . $note, $transfer['registered_school_id'] ?: null);
        $this->platform_mailer->send('receipt_rejected', $transfer['email_address'], array_merge($this->mail_vars($transfer), array('reason' => $note)), $transfer['registered_school_id'] ?: null);
        $this->back($transfer['id'], 'Payment rejected. The school can upload a new receipt from its payment link.');
    }

    private function mail_vars(array $transfer)
    {
        return array(
            'school_name' => $transfer['school_name'],
            'owner_name' => $transfer['owner_name'],
            'plan' => $transfer['plan_title'],
            'amount' => number_format((float) $transfer['amount'], 2),
            'payment_page_url' => site_url('subscription/bank_transfer/' . $transfer['token']),
        );
    }

    private function require_privilege()
    {
        if (!$this->rbac->hasPrivilege(self::PERMISSION, 'can_view')) {
            access_denied();
        }
    }

    private function transfer_or_404($id)
    {
        $transfer = ctype_digit((string) $id) ? $this->subscription_bank_transfer_model->get_detail((int) $id) : null;
        if (!$transfer) {
            show_404();
        }
        return $transfer;
    }

    /** A POST from the review page with its form token, for a transfer that is waiting for review. */
    private function checked_post($id)
    {
        $transfer = $this->transfer_or_404($id);
        $token = (string) $this->input->post('form_token');
        $expected = (string) $this->session->userdata('bank_transfer_form_token');
        if ($this->input->method() !== 'post' || $expected === '' || !hash_equals($expected, $token)) {
            show_error('This form has expired. Open the payment again and retry.', 403, 'Request refused');
        }
        if ($transfer['status'] !== 'pending') {
            $this->back($transfer['id'], null, 'Only a payment with an uploaded receipt can be reviewed.');
        }
        return $transfer;
    }

    /** Per-session token for the approve/reject forms (the application's CSRF protection is off). */
    private function form_token()
    {
        $token = (string) $this->session->userdata('bank_transfer_form_token');
        if ($token === '') {
            $token = bin2hex(random_bytes(20));
            $this->session->set_userdata('bank_transfer_form_token', $token);
        }
        return $token;
    }

    private function back($id, $message = null, $error = null)
    {
        if ($message !== null) {
            $this->session->set_flashdata('bank_transfer_msg', $message);
        }
        if ($error !== null) {
            $this->session->set_flashdata('bank_transfer_error', $error);
        }
        redirect('admin/subscription_bank_transfers/view/' . (int) $id);
    }
}
