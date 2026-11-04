<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class subscription extends Front_Controller
{
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
        $this->load->model(array('subscription_model', 'plan_model', 'setting_model'));
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

        $plan = $this->plan_model->get($this->input->post('plan_id'));
        $discount = 0;
        if ($plan['oprice'] > 0) {
            $discount = ($plan['price'] - $plan['oprice']);
        }
        $total = $plan['price'] - $discount;
        $this->form_validation->set_rules('email_address', $this->lang->line('email_address'), 'trim|required|xss_clean|valid_email|callback_check_email_exists', [
            'required' => 'The %s is a required field.',
            'valid_email' => 'Please enter a valid %s.'
        ]);

        if ($total > 0) {
            $this->form_validation->set_rules('payment_method', $this->lang->line('payment_method'), 'trim|required|xss_clean');
        }
        $this->form_validation->set_rules('phone_number', $this->lang->line('phone_number'), 'trim|required|xss_clean|regex_match[/^\+?[0-9]{1,4}?[-.\s]?[0-9]+[-.\s]?[0-9]+[-.\s]?[0-9]+$/]');
        $this->form_validation->set_rules('school_name', $this->lang->line('school_name'), 'trim|required|xss_clean');
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
                'payment_method' => ($total > 0) ? $this->input->post('payment_method') : '',
                'status' => 0,
                'total' => $total,
                'school_name' => $this->input->post('school_name'),
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

    public function data_set($subscription_id, $edit = false)
    {
        $update_data = array(
            'payment_status' => 1,
            'status' => 1,
        );
        $this->db->where('id', $subscription_id);
        $this->db->update('subscription', $update_data);

        $subscription_data = $this->subscription_model->get($subscription_id);

        if (!$edit) {
            $school_data = array(
                'school_name' => $subscription_data['school_name'],
                'email_address' => $subscription_data['email_address'],
                'phone_number' => $subscription_data['phone_number'],
                'payment_status' => 1,
                'cron_flag' => 0,
            );

            $this->db->insert('school', $school_data);
            $school_id = $this->db->insert_id();
            $update_data = array(
                'school_id' => $school_id,
                'status' => 1,
            );
            //guest add.
            $guest_password = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $encrypt_password = $this->enc_lib->passHashEnc($guest_password);


            $sql = "SELECT * FROM online_course_settings WHERE " . $this->db->school_filter('online_course_settings') . " ORDER BY id";
            $query = $this->db->query($sql);

            $course_setting = $query->row();

            $guest_unique_id = $course_setting->guest_prefix . '' . $course_setting->guest_id_start_from;
            $last_guest = $this->db->select('*')->order_by('id', "desc")->limit(1)->get('guest')->row();


            if (!empty($last_guest)) {
                $last_guest_id = str_replace($course_setting->guest_prefix, "", $last_guest->guest_unique_id);
                $guest_unique_id = $course_setting->guest_prefix . ($last_guest_id + 1);
            }
            $this->db->where(array('guest_unique_id' => $guest_unique_id));
            $query = $this->db->get('guest');

            $setting_result = $this->setting_model->get();
            $default = $setting_result[0]['lang_id'];

            $data = array(
                'guest_unique_id' => $guest_unique_id,
                'guest_name' => $subscription_data['school_name'],
                'email' => $subscription_data['email_address'],
                'password' => $encrypt_password,
                'created_at' => date("Y-m-d"),
                'is_active' => 'yes',
                'lang_id' => $default,
                'currency_id' => '4',
                // The guest row belongs to the platform school (school_id is set by the tenant layer);
                // the school this owner registered is linked separately.
                'registered_school_id' => $school_id,
                'mobileno' => $subscription_data['phone_number'],
                // 'currency_id'  => $this->customlib->getSchoolCurrency(),
            );

            $this->studentcourse_model->addguest($data);

        } else {
            $this->db->where('id', $subscription_id);
            $query = $this->db->get('subscription');
            $row = $query->row();
            $school_id = $row->school_id;
        }

        $this->db->where('id', $subscription_id);
        $this->db->update('subscription', $update_data);

        //mail send for school create
        $this->load->library('email');
        $config = array(
            'protocol' => $this->config->item('protocol'),
            'smtp_host' => $this->config->item('smtp_host'),
            'smtp_user' => $this->config->item('smtp_user'),
            'smtp_pass' => $this->config->item('smtp_pass'),
            'smtp_port' => $this->config->item('smtp_port'),
            'mailtype' => $this->config->item('mailtype'),
            'charset' => $this->config->item('charset'),
            'wordwrap' => $this->config->item('wordwrap'),
            'newline' => $this->config->item('newline'),
        );
        $this->email->initialize($config);
        $from_email = $this->config->item('from_email');
        $this->email->from($from_email, 'Edusys');

        // Recipient's email address
        $to_email = $subscription_data['email_address'];
        $this->email->to($to_email);
        $this->email->subject('Notice of  School registration on EduSyS ERP');

        $message = '<DOCTYPE html>
                <html lang="en-US">
                <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
                <body>
                    <p>Dear sir/medam,</p>
                    <br>
                    <p>Please wait while we prepare your resources. This may take a few moments.</p>
                    <br>
                    <p>Below are the details associated with your account:</p>
                    <br>
                    <b>School Name: </b>' . $subscription_data['school_name'] . ' <br>
                    <b>Phone Number: </b>' . $subscription_data['phone_number'] . '
                    <br>
                    <p><b>Please note the following important details for login:</b></p>
                    <br>
                    <b>Email: </b>' . $subscription_data['email_address'] . ' <br>
                    <b>Password: </b>' . $guest_password . '
                    <br>
                    <p>you can check your "account status, remaing days and renew payment using this login" at <a href="edusys.ng" target="_blank">login</a></p>
                    <p>Best regards,</p>
                    <p>The Edusys Team</p>
                </body>
            </html>';
        $this->email->message($message);
        $this->email->send();
        // End Mail

        $this->email->from($from_email, 'Edusys');
        $this->email->to($from_email);
        $this->email->subject('Buy Plan');
        $message = '<DOCTYPE html>
            <html lang="en-US">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
            </head>
            <body>
                <p>Dear admin,</p>
                <p>I hope you\'re doing well. This email serves to inform you that a customer has successfully purchased a plan through our system.</p>
                <p>Customer Details:</p>
                <ul>
                    <li>School Name:' . $subscription_data['school_name'] . '  </li>
                    <li>Email:' . $subscription_data['email_address'] . ' </li>
                    <li>Phone Number:' . $subscription_data['phone_number'] . ' </li>
                </ul>
                Please take note of the purchase and ensure the necessary steps are followed to fulfill the customer\'s request.

                If you need any further details or assistance regarding this transaction, feel free to reach out.

                Best regards,
            </body>
            </html>';
        $this->email->message($message);
        $this->email->send();
        if ($edit) {
            redirect('user/plan/success');
        } else {
            $this->success();
        }
    }

    public function initialize_payment($subscription_id, $edit = false)
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