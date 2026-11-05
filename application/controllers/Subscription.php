<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class subscription extends Front_Controller
{
    /** @var array the plan being signed up for (coupon validation) */
    private $signup_plan = array();

    /** payment_method value for a manual bank transfer (receipt reviewed by the platform admin) */
    const BANK_TRANSFER = 'bank_transfer';

    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->thumbnail_path = $this->config->item('course_thumbnail_path');
        $this->load->library('cart');
        $this->load->library('captchalib');
        $this->load->library('module_lib');
        $this->load->library('Enc_lib');
        $this->load->model('studentcourse_model');
        $this->load->model(array('subscription_model', 'plan_model', 'setting_model', 'subscription_bank_transfer_model'));
        $this->load->library('Platform_coupons');
        $this->load->library('School_provisioner');
    }

    public function add()
    {
        //$data = array();
        $this->data['active_menu'] = 'plans';
        $page = array('title' => '', 'meta_title' => '', 'meta_keyword' => '', 'meta_description' => '');
        $this->data['page_side_bar'] = false;
        $this->data['featured_image'] = false;
        $this->data['page'] = $page;

        $plan_id = $this->input->get('plan_id');
        if (!$plan_id) {
            $plan_id = $this->session->flashdata('plan_id');
        }
        $this->data['plan_id'] = $plan_id;

        $plan = $this->plan_model->get($plan_id);

        $discount = 0;
        $this->data['amount'] = $plan['price'];
        $this->data['days'] = $plan['days'];
        if ($plan['oprice'] > 0) {
            $discount = ($plan['price'] - $plan['oprice']);
            $this->data['amount'] = ($plan['price'] - $discount);
        }

        $this->data['setting_data'] = $this->setting_model->get();

        $this->load_theme('subscription/subscriptionform');
    }

    public function save()
    {

        $plan_id = (int) $this->input->post('plan_id');
        $plan = $plan_id > 0 ? $this->plan_model->get($plan_id) : null;
        if (empty($plan) || !isset($plan['plan_id']) || (int) $plan['status'] !== 1) {
            show_404(); // only a plan shown on the sign-up page can be chosen
        }
        $discount = 0;
        if ($plan['oprice'] > 0) {
            $discount = ($plan['price'] - $plan['oprice']);
        }
        $total = $plan['price'] - $discount;
        // A coupon (checked again here, whatever the page showed).
        $this->signup_plan = $plan;
        $coupon = null;
        $code = Platform_coupons::normalize($this->input->post('coupon_code'));
        if ($code !== '') {
            $coupon = $this->platform_coupons->find($code);
            if ($this->platform_coupons->error($coupon, $plan) === null) {
                $off = $this->platform_coupons->discount($coupon, $total);
                $discount += $off;
                $total -= $off;
            }
        }
        $this->form_validation->set_rules('coupon_code', 'Coupon', 'trim|callback_valid_coupon');
        $this->form_validation->set_rules('email_address', $this->lang->line('email_address'), 'trim|required|xss_clean|valid_email|callback_check_email_exists', [
            'required' => 'The %s is a required field.',
            'valid_email' => 'Please enter a valid %s.'
        ]);

        if ($total > 0) {
            $this->form_validation->set_rules('payment_method', $this->lang->line('payment_method'), 'trim|required|xss_clean|in_list[paystack,' . self::BANK_TRANSFER . ']');
        }
        $this->form_validation->set_rules('phone_number', $this->lang->line('phone_number'), 'trim|required|xss_clean|regex_match[/^\+?[0-9]{1,4}?[-.\s]?[0-9]+[-.\s]?[0-9]+[-.\s]?[0-9]+$/]');
        $this->form_validation->set_rules('school_name', $this->lang->line('school_name'), 'trim|required|xss_clean');
        $_POST['subdomain'] = School_provisioner::normalize_subdomain($this->input->post('subdomain'));
        $this->form_validation->set_rules('subdomain', 'School address', 'required|callback_valid_subdomain', array('required' => 'Choose an address for your school.'));
        $this->form_validation->set_rules('owner_name', $this->lang->line('owner_name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('plan_id', $this->input->post('plan_id'));

            $msg = array(
                'title' => form_error('title'),
                'text' => form_error('text'),
                'plan_type_list' => form_error('plan_type_list'),
            );

            $data = array(
                'error_messages' => $msg,
                'status' => 'fail',
                'message' => 'There were some validation errors.'
            );

            $plan = $this->plan_model->get($this->input->post('plan_id'));
            $this->data['amount'] = $plan['price'];
            $this->data['active_menu'] = 'online_admission';
            $this->data['setting_data'] = $this->setting_model->get();
            $this->data['page_side_bar'] = false;
            $this->data['featured_image'] = false;
            $page = array('title' => '', 'meta_title' => '', 'meta_keyword' => '', 'meta_description' => '');
            $this->data['page'] = $page;

            $this->load_theme('subscription/subscriptionform');
        } else {
            $currentDate = new DateTime();
            $currentDate->modify('+1 year');
            $end_date = $currentDate->format('Y-m-d');

            // $plan = $this->plan_model->get($this->input->post('plan_id'));
            // $discount = 0;
            // if ($plan['oprice'] > 0) {
            //     $discount = ($plan['price'] - $plan['oprice']);
            // }
            // $total = $plan['price'] - $discount;

            $data = array(
                'plan_id' => $this->input->post('plan_id'),
                'end_date' => $end_date,
                'price' => $plan['price'],
                'days' => $plan['days'],
                'discount_amt' => $discount,
                'coupon_code' => $coupon && $this->platform_coupons->error($coupon, $plan) === null ? $coupon['code'] : null,
                'payment_method' => ($total > 0) ? $this->input->post('payment_method') : '',
                'status' => 0,
                'total' => $total,
                'school_name' => $this->input->post('school_name'),
                'subdomain' => $this->input->post('subdomain'),
                'email_address' => $this->input->post('email_address'),
                'phone_number' => $this->input->post('phone_number'),
                'owner_name' => $this->input->post('owner_name'),
                'cron_flag' => 0,
            );

            $subscription_id = $this->subscription_model->add($data);

            $this->session->set_flashdata('msg', 'Subscription added successfully');
            if ($total > 0) {
                if ($this->input->post('payment_method') == 'paystack') {
                    $this->initialize_payment($subscription_id);
                } elseif ($this->input->post('payment_method') == self::BANK_TRANSFER) {
                    $token = $this->subscription_bank_transfer_model->create($subscription_id);
                    redirect('subscription/bank_transfer/' . $token);
                }
            } else {
                $this->data_set($subscription_id);
            }

        }
    }

    public function edit()
    {
        $student_data = $this->session->userdata('student');
        $plan_id = $this->input->get('plan_id');
        $school_id = $student_data['school_id'];
        $this->db->where('id', $school_id);
        $query = $this->db->get('school');
        $row = $query->row();

        $plan = $this->plan_model->get($plan_id);
        $currentDate = new DateTime();
        $currentDate->modify('+1 year');
        $end_date = $currentDate->format('Y-m-d');
        $discount = 0;
        if ($plan['oprice'] > 0) {
            $discount = ($plan['price'] - $plan['oprice']);
        }
        $total = $plan['price'] - $discount;

        $data = array(
            'plan_id' => $plan_id,
            'end_date' => $end_date,
            'price' => $plan['price'],
            'discount_amt' => $discount,
            'payment_method' => 'paystack',
            // 'payment_status' => 0,
            'status' => 0,
            'total' => $total,
            'school_name' => $row->school_name,
            'email_address' => $row->email_address,
            'phone_number' => $row->phone_number,
            'cron_flag' => 1,
            'school_id' => $school_id,
            'days' => 365
        );

        $subscription_id = $this->subscription_model->add($data);

        $this->initialize_payment($subscription_id, true);
    }

    /**
     * Activates a subscription after payment (or for a free plan) and shows the result.
     * Private: it must only run after the payment has been verified.
     */
    private function data_set($subscription_id, $edit = false)
    {
        $this->load->library('Subscription_activation');
        $this->subscription_activation->activate((int) $subscription_id, (bool) $edit);
        if ($edit) {
            redirect('user/plan/success');
        } else {
            $this->success();
        }
    }

    private function initialize_payment($subscription_id, $edit = false)
    {
        $callback_url = site_url('subscription/verifyPayment') . "?subscription_id={$subscription_id}";
        if ($edit) {
            $callback_url .= '&edit=true';
        }
        $subscription_data = $this->subscription_model->get($subscription_id);

        $fields = [
            'email' => $subscription_data['email_address'],
            'amount' => (int) round($subscription_data['total'] * 100),
            'callback_url' => $callback_url,
        ];
        $paymentResponse = $this->paystack('transaction/initialize', $fields);

        $return = array();
        if (isset($paymentResponse['status']) && $paymentResponse['status'] == true) {
            // Only this reference can later confirm this subscription (see verifyPayment()).
            $this->db->where('id', (int) $subscription_id)->update('subscription', array('reference' => $paymentResponse['data']['reference']));
            redirect($paymentResponse['data']['authorization_url']);
        } else {
            $return = array(
                'status' => 'fail',
                'error' => $paymentResponse['message'],
            );
        }
    }

    public function verifyPayment()
    {
        $reference = (string) $this->input->get('reference');
        $subscription_id = (int) $this->input->get('subscription_id');
        $edit = (bool) $this->input->get('edit');
        $subscription = $this->db->where('id', $subscription_id)->get('subscription')->row_array();

        // The callback must carry the reference this subscription's payment was started with.
        if (!$subscription || $reference === '' || !hash_equals((string) $subscription['reference'], $reference)) {
            return $edit ? redirect('user/plan/failure') : $this->failure();
        }
        if ((int) $subscription['payment_status'] === 1) {
            return $edit ? redirect('user/plan/failure') : $this->alreadyComplete();
        }

        $paymentResponse = $this->paystack('transaction/verify/' . rawurlencode($reference));
        $data = isset($paymentResponse['data']) && is_array($paymentResponse['data']) ? $paymentResponse['data'] : array();
        $paid = !empty($paymentResponse['status'])
            && isset($data['status'], $data['reference'], $data['amount']) && $data['status'] === 'success'
            && $data['reference'] === $reference
            && (int) $data['amount'] === (int) round($subscription['total'] * 100)
            && isset($data['customer']['email']) && strcasecmp($data['customer']['email'], $subscription['email_address']) === 0;
        if (!$paid) {
            log_message('error', 'Paystack verification failed for subscription ' . $subscription_id . ' reference ' . $reference);
            return $edit ? redirect('user/plan/failure') : $this->failure();
        }
        $this->data_set($subscription_id, $edit);
    }

    /**
     * Calls the Paystack API with the platform's secret key (EDUSYS_PAYSTACK_SECRET_KEY in the
     * server environment; it is never stored in the code or the database).
     *
     * @param string $path e.g. 'transaction/initialize'
     * @param array|null $post form fields for a POST request
     * @return array decoded response
     */
    private function paystack($path, $post = null)
    {
        $secret = getenv('EDUSYS_PAYSTACK_SECRET_KEY');
        if ($secret === false || trim($secret) === '') {
            show_error('Online payment is not configured on this server.', 503, 'Payment unavailable');
        }
        $ch = curl_init('https://api.paystack.co/' . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . trim($secret), 'Cache-Control: no-cache'));
        if ($post !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        $response = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode((string) $response, true);
        return is_array($decoded) ? $decoded : array();
    }

    /**
     * The school's bank transfer page (private link from save()): the platform's bank details,
     * the amount, the review status, and the receipt upload form while a receipt is due.
     */
    public function bank_transfer($token = '')
    {
        $transfer = $this->bank_transfer_or_404($token);
        $this->show_bank_transfer($transfer);
    }

    /** Receipt upload (POST from the bank transfer page). */
    public function submit_receipt($token = '')
    {
        $transfer = $this->bank_transfer_or_404($token);
        if ($this->input->method() !== 'post' || !in_array($transfer['status'], array('awaiting_receipt', 'rejected'), true)) {
            redirect('subscription/bank_transfer/' . $transfer['token']);
        }

        $this->form_validation->set_rules('payer_name', 'Account name', 'trim|required|max_length[200]|xss_clean');
        $this->form_validation->set_rules('payer_bank', 'Bank', 'trim|required|max_length[200]|xss_clean');
        $this->form_validation->set_rules('payment_date', 'Payment date', 'trim|required|callback_valid_payment_date');
        $this->form_validation->set_rules('amount', 'Amount paid', 'trim|required|numeric|greater_than[0]');
        $this->form_validation->set_rules('reference', 'Transaction reference', 'trim|max_length[200]|xss_clean');
        $upload_error = $this->subscription_bank_transfer_model->receipt_upload_error('receipt');
        if (!$this->form_validation->run() || $upload_error !== null) {
            return $this->show_bank_transfer($transfer, $upload_error);
        }

        $stored = $this->subscription_bank_transfer_model->store_receipt('receipt');
        if ($stored === null) {
            return $this->show_bank_transfer($transfer, 'The receipt could not be saved. Please try again.');
        }
        $this->subscription_bank_transfer_model->submit($transfer['id'], array(
            'payer_name' => $this->input->post('payer_name'),
            'payer_bank' => $this->input->post('payer_bank'),
            'payment_date' => $this->input->post('payment_date'),
            'amount' => $this->input->post('amount'),
            'reference' => $this->input->post('reference'),
            'receipt_file' => $stored['file'],
            'receipt_name' => $stored['name'],
        ));
        $this->notify_platform_of_receipt($transfer);
        redirect('subscription/bank_transfer/' . $transfer['token']);
    }

    /** Form validation callback: the address the school chose is free. */
    public function valid_subdomain($subdomain)
    {
        $status = $this->school_provisioner->subdomain_status((string) $subdomain);
        if ($status !== 'ok') {
            $this->form_validation->set_message('valid_subdomain', School_provisioner::subdomain_message($status, (string) $subdomain));
            return false;
        }
        return true;
    }

    /** The sign-up page's live check of the address (JSON): available, or why not, with suggestions. */
    public function check_subdomain()
    {
        $subdomain = School_provisioner::normalize_subdomain($this->input->get('name'));
        $status = $this->school_provisioner->subdomain_status($subdomain);
        $out = array(
            'ok' => $status === 'ok',
            'status' => $status,
            'subdomain' => $subdomain,
            'address' => $subdomain === '' ? '' : $subdomain . '.' . $this->config->item('tenancy_base_domain'),
            'message' => School_provisioner::subdomain_message($status, $subdomain),
            'suggestions' => in_array($status, array('taken', 'held', 'reserved'), true) ? $this->school_provisioner->subdomain_suggestions($subdomain) : array(),
        );
        $this->output->set_content_type('application/json')->set_header('Cache-Control: no-store')->set_output(json_encode($out));
    }

    /** Form validation callback: an empty coupon, or one valid for the chosen plan. */
    public function valid_coupon($code)
    {
        if (Platform_coupons::normalize($code) === '') {
            return true;
        }
        $error = $this->platform_coupons->error($this->platform_coupons->find($code), $this->signup_plan);
        if ($error !== null) {
            $this->form_validation->set_message('valid_coupon', $error);
            return false;
        }
        return true;
    }

    /** The sign-up page's "Apply" button: the price with a coupon (JSON). */
    public function check_coupon()
    {
        $plan_id = (int) $this->input->get('plan_id');
        $plan = $plan_id > 0 ? $this->plan_model->get($plan_id) : null;
        $out = array('ok' => false);
        if (!empty($plan) && isset($plan['plan_id']) && (int) $plan['status'] === 1) {
            $base = (float) ((int) $plan['oprice'] > 0 ? $plan['oprice'] : $plan['price']);
            $coupon = $this->platform_coupons->find($this->input->get('code'));
            $error = $this->platform_coupons->error($coupon, $plan);
            if ($error !== null) {
                $out['message'] = $error;
            } else {
                $off = $this->platform_coupons->discount($coupon, $base);
                $out = array('ok' => true, 'discount' => $off, 'total' => $base - $off,
                    'message' => 'Coupon applied: ' . number_format($off, 2) . ' off.' . ($base - $off <= 0 ? ' This plan is now free.' : ''));
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($out));
    }

    /** An invoice, for the owner (private link in the receipt email). */
    public function invoice($token = '')
    {
        $invoice = preg_match('/^[0-9a-f]{40}$/', (string) $token) ? $this->db->where('token', $token)->get('platform_invoices')->row_array() : null;
        if (!$invoice) {
            show_404();
        }
        $this->load->library('Platform_invoices');
        $pdf = $this->platform_invoices->pdf($invoice);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $invoice['invoice_no'] . '.pdf"');
        header('Cache-Control: private, no-store');
        header('X-Content-Type-Options: nosniff');
        echo $pdf;
        exit;
    }

    /** Form validation callback: a real date, not in the future. */
    public function valid_payment_date($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', (string) $date);
        if (!$d || $d->format('Y-m-d') !== $date || $date > date('Y-m-d')) {
            $this->form_validation->set_message('valid_payment_date', 'Enter the payment date (not in the future).');
            return false;
        }
        return true;
    }

    private function bank_transfer_or_404($token)
    {
        $transfer = $this->subscription_bank_transfer_model->get_by_token((string) $token);
        if (!$transfer) {
            show_404();
        }
        return $transfer;
    }

    private function show_bank_transfer(array $transfer, $upload_error = null)
    {
        $subscription = $this->subscription_model->get($transfer['subscription_id']);
        $plan = $this->plan_model->get($subscription['plan_id']);
        $settings = $this->setting_model->getSetting();
        $this->data['transfer'] = $transfer;
        $this->data['subscription'] = $subscription;
        $this->data['plan'] = $plan;
        $this->data['bank_instructions'] = $settings ? (string) $settings->offline_bank_payment_instruction : '';
        $this->data['currency_symbol'] = $this->customlib->getSchoolCurrencyFormat();
        $this->data['upload_error'] = $upload_error;
        $this->data['active_menu'] = 'plans';
        $this->data['setting_data'] = $this->setting_model->get();
        $this->data['page_side_bar'] = false;
        $this->data['featured_image'] = false;
        $this->data['page'] = array('title' => 'Bank transfer', 'meta_title' => '', 'meta_keyword' => '', 'meta_description' => '');
        $this->load_theme('subscription/bank_transfer');
    }

    /** Tells the platform a receipt is waiting for review (template receipt_submitted). */
    private function notify_platform_of_receipt(array $transfer)
    {
        $subscription = $this->subscription_model->get($transfer['subscription_id']);
        if (!$subscription) {
            return;
        }
        $fresh = $this->subscription_bank_transfer_model->get_by_token($transfer['token']);
        $this->load->library('Platform_mailer');
        $this->platform_mailer->send('receipt_submitted', $this->platform_settings->get('notify_email'), array(
            'school_name' => $subscription['school_name'],
            'amount' => $fresh ? number_format((float) $fresh['amount'], 2) : '',
            'review_url' => site_url('admin/subscription_bank_transfers/view/' . (int) $transfer['id']),
        ), $subscription['school_id'] ?: null);
    }

    public function success()
    {
        $this->data['active_menu'] = 'online_admission';
        $this->data['setting_data'] = $this->setting_model->get();
        $this->data['page_side_bar'] = false;
        $this->data['featured_image'] = false;
        $page = array('title' => '', 'meta_title' => '', 'meta_keyword' => '', 'meta_description' => '');
        $this->data['page'] = $page;
        $this->data['edit'] = '/';
        $this->load_theme('subscription/success');
    }

    public function failure()
    {
        $this->data['active_menu'] = 'online_admission';
        $this->data['setting_data'] = $this->setting_model->get();
        $this->data['page_side_bar'] = false;
        $this->data['featured_image'] = false;
        $page = array('title' => '', 'meta_title' => '', 'meta_keyword' => '', 'meta_description' => '');
        $this->data['page'] = $page;
        $this->data['edit'] = '/';
        $this->load_theme('subscription/failure');
    }

    public function alreadyComplete()
    {
        $this->data['active_menu'] = 'online_admission';
        $this->data['setting_data'] = $this->setting_model->get();
        $this->data['page_side_bar'] = false;
        $this->data['featured_image'] = false;
        $page = array('title' => '', 'meta_title' => '', 'meta_keyword' => '', 'meta_description' => '');
        $this->data['page'] = $page;
        $this->data['edit'] = '/';
        $this->load_theme('subscription/alreadycomplete');
    }

    public function check_email_exists($email)
    {
        $this->load->model('School_model');

        if ($this->School_model->is_email_taken($email)) {
            $this->form_validation->set_message('check_email_exists', 'The %s is already exist.');
            return false;
        }

        return true;
    }
}