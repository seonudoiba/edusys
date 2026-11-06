<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class subscription extends Front_Controller
{
    /** @var array the plan being signed up for (coupon validation) */
    private $signup_plan = array();
    private $signup_price = array();

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
        $this->load->library(array('Payment_gateways', 'Platform_settings', 'Plan_pricing'));
        $this->theme_path = 'platform'; // the platform's own design, whatever theme is selected
    }

    /** The registration form: school details, students, how often to pay, and the live price. */
    public function add()
    {
        $quote = $this->plan_pricing->quote($this->input->get('quote'));
        if ($this->input->get('quote') && !$quote) {
            show_error('This quote has expired or has already been used. Please contact us for a new one.', 410, 'Quote not available');
        }
        $students = $quote ? (int) $quote['students'] : max(0, (int) $this->input->get('students'));
        if (!$quote && $students > 0 && $this->plan_pricing->price($students, 'year')['needs_quote']) {
            redirect('plans/quote?students=' . $students);
        }
        $this->show_form($quote, $students, (string) $this->input->get('period'));
    }

    private function show_form($quote, $students, $period)
    {
        $page = array('title' => 'Register your school', 'meta_title' => 'Register your school', 'meta_keyword' => '', 'meta_description' => '');
        $this->data['active_menu'] = 'plans';
        $this->data['page_side_bar'] = false;
        $this->data['featured_image'] = false;
        $this->data['page'] = $page;
        $this->data['setting_data'] = $this->setting_model->get();
        $this->data['online_methods'] = $this->payment_gateways->platform_enabled();
        $this->data['bands'] = $this->plan_pricing->bands();
        $this->data['quote'] = $quote;
        $this->data['students'] = $students;
        $this->data['period'] = isset(Plan_pricing::PERIODS[$period]) ? $period : 'year';
        $this->load_theme('subscription/subscriptionform');
    }

    public function save()
    {
        $plan = $this->plan_pricing->plan();
        if (empty($plan)) {
            show_error('Pricing is not set up on this server.', 503, 'Registration unavailable');
        }
        $token = (string) $this->input->post('quote');
        $quote = $token !== '' ? $this->plan_pricing->quote($token) : null;
        if ($token !== '' && !$quote) {
            show_error('This quote has expired or has already been used. Please contact us for a new one.', 410, 'Quote not available');
        }
        $students = (int) $this->input->post('student_count');
        $period = (string) $this->input->post('billing_period');
        $this->signup_price = $this->plan_pricing->price($students, $period, true, $quote);
        if ($this->signup_price['needs_quote']) {
            redirect('plans/quote?students=' . $students);
        }
        $total = $this->signup_price['total'];
        $discount = 0;
        // A coupon (checked again here, whatever the page showed).
        $this->signup_plan = $plan;
        $coupon = null;
        $code = Platform_coupons::normalize($this->input->post('coupon_code'));
        if ($code !== '') {
            $coupon = $this->platform_coupons->find($code);
            if ($this->platform_coupons->error($coupon, $plan) === null) {
                $discount = $this->platform_coupons->discount($coupon, $total);
            }
        }
        $payable = max(0, $total - $discount);
        $this->form_validation->set_rules('student_count', 'Number of students', 'callback_valid_price');
        $this->form_validation->set_rules('coupon_code', 'Coupon', 'trim|callback_valid_coupon');
        $this->form_validation->set_rules('email_address', $this->lang->line('email_address'), 'trim|required|xss_clean|valid_email|callback_check_email_exists', [
            'required' => 'The %s is a required field.',
            'valid_email' => 'Please enter a valid %s.'
        ]);
        if ($payable > 0) {
            $this->form_validation->set_rules('payment_method', $this->lang->line('payment_method'), 'trim|required|xss_clean|in_list[' . implode(',', array_merge(array_keys($this->payment_gateways->platform_enabled()), array(self::BANK_TRANSFER))) . ']');
        }
        $this->form_validation->set_rules('phone_number', $this->lang->line('phone_number'), 'trim|required|xss_clean|regex_match[/^\+?(?=(?:\D*\d){7,15}\D*$)[0-9\s().\-]+$/]');
        $this->form_validation->set_rules('school_name', $this->lang->line('school_name'), 'trim|required|xss_clean');
        $_POST['subdomain'] = School_provisioner::normalize_subdomain($this->input->post('subdomain'));
        $this->form_validation->set_rules('subdomain', 'School address', 'required|callback_valid_subdomain', array('required' => 'Choose an address for your school.'));
        $this->form_validation->set_rules('owner_name', $this->lang->line('owner_name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            return $this->show_form($quote, $students, $period);
        }
        $p = $this->signup_price;
        $subscription_id = $this->subscription_model->add(array(
            'plan_id' => $plan['plan_id'],
            'kind' => 'new',
            'student_count' => $p['students'],
            'billing_period' => $p['period'],
            'rate_per_term' => $p['rate'],
            'terms' => $p['terms'],
            'setup_fee' => $p['setup'],
            'quote_id' => $quote ? (int) $quote['id'] : null,
            'end_date' => date('Y-m-d', strtotime('+' . $p['days'] . ' days')),
            'price' => $total,
            'days' => $p['days'],
            'discount_amt' => $discount,
            'coupon_code' => $coupon && $this->platform_coupons->error($coupon, $plan) === null ? $coupon['code'] : null,
            'payment_method' => ($payable > 0) ? $this->input->post('payment_method') : '',
            'status' => 0,
            'total' => $payable,
            'school_name' => $this->input->post('school_name'),
            'subdomain' => $this->input->post('subdomain'),
            'email_address' => $this->input->post('email_address'),
            'phone_number' => $this->input->post('phone_number'),
            'owner_name' => $this->input->post('owner_name'),
            'cron_flag' => 0,
        ));
        if ($payable <= 0) {
            return $this->data_set($subscription_id);
        }
        if (Payment_gateways::known($this->input->post('payment_method'))) {
            return $this->initialize_payment($subscription_id);
        }
        if ($this->input->post('payment_method') == self::BANK_TRANSFER) {
            $token = $this->subscription_bank_transfer_model->create($subscription_id);
            redirect('subscription/bank_transfer/' . $token);
        }
    }

    /** Form validation callback: the number of students has a price. */
    public function valid_price($value)
    {
        if (empty($this->signup_price['ok'])) {
            $this->form_validation->set_message('valid_price', $this->signup_price['error'] ?: 'Check the number of students.');
            return false;
        }
        return true;
    }

    /** Renewal from the owner's account: the next period for the students the school has now. No setup fee. */
    public function edit()
    {
        list($school_id, $row, $current) = $this->renewal_context();
        $method = $this->chosen_method();
        $used = $this->active_students($school_id);
        $students = (int) $this->input->get('students');
        if ($students < $used) {
            $this->renewal_error('The school has ' . number_format($used) . ' active students: pay for at least that many, or deactivate some first.');
        }
        // A rate agreed in a quote stays agreed.
        $agreed = $current && !empty($current['quote_id']) ? $current['rate_per_term'] : null;
        $p = $this->plan_pricing->price($students, (string) $this->input->get('period'), false, null, $agreed);
        if (!$p['ok']) {
            $this->renewal_error($p['error'] . ($p['needs_quote'] ? ' Please contact us to renew.' : ''));
        }
        $plan = $this->plan_pricing->plan();
        $id = $this->subscription_model->add(array(
            'plan_id' => $plan['plan_id'],
            'kind' => 'renewal',
            'student_count' => $p['students'],
            'billing_period' => $p['period'],
            'rate_per_term' => $p['rate'],
            'terms' => $p['terms'],
            'setup_fee' => 0,
            'end_date' => date('Y-m-d', strtotime('+' . $p['days'] . ' days')),
            'price' => $p['total'],
            'discount_amt' => 0,
            'payment_method' => $method,
            'status' => 0,
            'total' => $p['total'],
            'school_name' => $row->school_name,
            'email_address' => $row->email_address,
            'phone_number' => $row->phone_number,
            'cron_flag' => 1,
            'school_id' => $school_id,
            'days' => $p['days'],
        ));
        $this->initialize_payment($id, true);
    }

    /** More students before the period ends: the extra students for the terms left, same end date. */
    public function topup()
    {
        list($school_id, $row, $current) = $this->renewal_context();
        $method = $this->chosen_method();
        if (!$current) {
            $this->renewal_error('There is no subscription to top up.');
        }
        $t = $this->plan_pricing->topup($current, (int) $this->input->get('students'));
        if (!$t['ok']) {
            $this->renewal_error($t['error']);
        }
        $id = $this->subscription_model->add(array(
            'plan_id' => $current['plan_id'],
            'kind' => 'topup',
            'student_count' => $t['students'],
            'billing_period' => $current['billing_period'],
            'rate_per_term' => $t['rate'],
            'terms' => $t['terms'],
            'setup_fee' => 0,
            'quote_id' => $current['quote_id'],
            'end_date' => date('Y-m-d', strtotime('+' . $t['days'] . ' days')),
            'price' => $t['total'],
            'discount_amt' => 0,
            'payment_method' => $method,
            'status' => 0,
            'total' => $t['total'],
            'school_name' => $row->school_name,
            'email_address' => $row->email_address,
            'phone_number' => $row->phone_number,
            'cron_flag' => 1,
            'school_id' => $school_id,
            'days' => $t['days'], // from today: the end date stays where it is
        ));
        $this->initialize_payment($id, true);
    }

    /** [school id, school row, latest paid subscription] of the logged-in owner, or back to login. */
    private function renewal_context()
    {
        $student_data = $this->session->userdata('student');
        $school_id = isset($student_data['school_id']) ? (int) $student_data['school_id'] : 0;
        $row = $school_id ? $this->db->where('id', $school_id)->get('school')->row() : null;
        if (!$row) {
            redirect('course/guestlogin');
        }
        $current = $this->db->where(array('school_id' => $school_id, 'payment_status' => 1))->order_by('id', 'desc')->limit(1)->get('subscription')->row_array();
        return array($school_id, $row, $current);
    }

    private function chosen_method()
    {
        $methods = $this->payment_gateways->platform_enabled();
        $method = (string) $this->input->get('method');
        if (!isset($methods[$method])) {
            $this->renewal_error('Choose a payment method. If none is offered, please contact us to pay by bank transfer.');
        }
        return $method;
    }

    private function active_students($school_id)
    {
        return (int) $this->db->without_tenant(function ($db) use ($school_id) {
            return $db->where(array('school_id' => (int) $school_id, 'is_active' => 'yes'))->count_all_results('students');
        });
    }

    private function renewal_error($message)
    {
        $this->session->set_flashdata('plan_error', $message);
        redirect('user/plan/planlist');
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

    /**
     * Starts the online payment of a subscription with the platform's keys (server environment,
     * see Payment_gateways::PLATFORM_ENV): Paystack, Flutterwave, Monnify, Interswitch or Remita.
     */
    private function initialize_payment($subscription_id, $edit = false)
    {
        $s = $this->subscription_model->get($subscription_id);
        $gateway = (string) $s['payment_method'];
        $methods = $this->payment_gateways->platform_enabled();
        if (!isset($methods[$gateway])) {
            show_error('This payment method is not available on this server.', 503, 'Payment unavailable');
        }
        $reference = 'SUB' . (int) $subscription_id . 'T' . date('ymdHis') . strtoupper(bin2hex(random_bytes(3)));
        try {
            $answer = $this->payment_gateways->for_platform($gateway)->start(array(
                'reference' => $reference,
                'amount' => (float) $s['total'],
                'currency' => self::platform_currency(),
                'email' => $s['email_address'],
                'name' => (string) ($s['owner_name'] ?: $s['school_name']),
                'phone' => (string) $s['phone_number'],
                'description' => $this->platform_settings->get('platform_name') . ' subscription: ' . $s['school_name'],
                'return_url' => site_url('subscription/verify/' . $gateway . '/' . (int) $subscription_id) . ($edit ? '?edit=1' : ''),
            ));
        } catch (Payment_gateway_error $e) {
            show_error(html_escape($e->getMessage()), 502, 'The payment could not start');
        }
        // Only this reference can later confirm this subscription (see verify()).
        $this->db->where('id', (int) $subscription_id)->update('subscription', array(
            'reference' => $reference,
            'gateway_ref' => isset($answer['gateway_ref']) ? $answer['gateway_ref'] : null,
        ));
        if (isset($answer['redirect'])) {
            redirect($answer['redirect']);
        }
        $this->load->view('user/gateway/online/redirect_post', array(
            'action' => $answer['post']['url'],
            'fields' => $answer['post']['fields'],
            'gateway_name' => Payment_gateways::name($gateway),
        ));
    }

    /**
     * Where the gateway sends the payer back (GET or POST; no session needed, Interswitch comes
     * back with a cross-site POST). The gateway's server is asked about the reference this
     * subscription's payment was started with; only the full amount activates it, once.
     */
    public function verify($gateway = '', $subscription_id = 0)
    {
        $edit = (bool) $this->input->get('edit');
        $s = $this->db->where('id', (int) $subscription_id)->get('subscription')->row_array();
        if (!$s || !Payment_gateways::known($gateway) || $s['payment_method'] !== $gateway || (string) $s['reference'] === '') {
            return $edit ? redirect('user/plan/failure') : $this->failure();
        }
        if ((int) $s['payment_status'] === 1) {
            return $edit ? redirect('user/plan/success') : $this->alreadyComplete();
        }
        $currency = self::platform_currency();
        try {
            $r = $this->payment_gateways->for_platform($gateway)->check(array(
                'reference' => $s['reference'],
                'gateway_ref' => $s['gateway_ref'],
                'amount' => (float) $s['total'],
                'currency' => $currency,
                'email' => $s['email_address'],
            ), array_merge((array) $this->input->get(), (array) $this->input->post()));
        } catch (Payment_gateway_error $e) {
            log_message('error', 'Subscription ' . (int) $subscription_id . ': ' . $e->getMessage());
            return $edit ? redirect('user/plan/failure') : $this->failure();
        }
        $paid = !empty($r['paid']) && $r['amount'] !== null && $r['amount'] + 0.009 >= (float) $s['total']
            && (!$r['currency'] || strtoupper($r['currency']) === $currency);
        if (!$paid) {
            log_message('error', Payment_gateways::name($gateway) . ' did not confirm subscription ' . (int) $subscription_id
                . ' (' . $s['reference'] . '): ' . (isset($r['message']) ? $r['message'] : '') . ', amount ' . var_export($r['amount'], true));
            return $edit ? redirect('user/plan/failure') : $this->failure();
        }
        $this->data_set($subscription_id, $edit);
    }

    /** Paystack's callback URL before verify() existed (payments started before this version). */
    public function verifyPayment()
    {
        $id = (int) $this->input->get('subscription_id');
        $s = $id ? $this->db->where('id', $id)->get('subscription')->row_array() : null;
        if ($s && $s['payment_method'] === 'paystack' && hash_equals((string) $s['reference'], (string) $this->input->get('reference'))) {
            $_GET['edit'] = $this->input->get('edit') ? '1' : '';
            return $this->verify('paystack', $id);
        }
        return $this->failure();
    }

    /** Currency of the plans (EDUSYS_PLATFORM_CURRENCY, default NGN). */
    private static function platform_currency()
    {
        $c = strtoupper(trim((string) getenv('EDUSYS_PLATFORM_CURRENCY')));
        return $c !== '' ? $c : 'NGN';
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
        $quote = $this->plan_pricing->quote($this->input->get('quote'));
        $p = $this->plan_pricing->price((int) $this->input->get('students'), (string) $this->input->get('period'), true, $quote);
        $plan = $this->plan_pricing->plan();
        $out = array('ok' => false, 'message' => $p['error']);
        if ($p['ok'] && $plan) {
            $coupon = $this->platform_coupons->find($this->input->get('code'));
            $error = $this->platform_coupons->error($coupon, $plan);
            if ($error !== null) {
                $out['message'] = $error;
            } else {
                $off = $this->platform_coupons->discount($coupon, $p['total']);
                $out = array('ok' => true, 'discount' => $off, 'total' => $p['total'] - $off,
                    'message' => 'Coupon applied: ' . number_format($off, 2) . ' off.');
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