<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . 'libraries/payments/Payment_gateway.php';

class Paymentsettings extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('payment_methods', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'admin/paymentsettings');
        $data['title']  = 'SMS Config List';     

        $data['statuslist']  = $this->customlib->getStatus();
        $data['paymentlist'] = $this->paymentsetting_model->get();
        $this->load->library('School_bank_transfer');
        $data['bank_transfer'] = $this->school_bank_transfer->details();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/payment_setting/paymentsettingList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function paypal()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('paypal_username', $this->lang->line('username'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paypal_password', $this->lang->line('password'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paypal_signature', $this->lang->line('signature'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {
            $data = array(
                'payment_type'  => 'paypal',
                'api_username'  => $this->input->post('paypal_username'),
                'api_password'  => $this->input->post('paypal_password'),
                'api_signature' => $this->input->post('paypal_signature'),
                'paypal_demo'   => 'TRUE',
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'paypal_username'  => form_error('paypal_username'),
                'paypal_password'  => form_error('paypal_password'),
                'paypal_signature' => form_error('paypal_signature'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function stripe()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('api_secret_key', $this->lang->line('secret_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('api_publishable_key', $this->lang->line('publishable_key'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'      => $this->input->post('api_secret_key'),
                'api_publishable_key' => $this->input->post('api_publishable_key'),
                'payment_type'        => 'stripe',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'api_secret_key'      => form_error('api_secret_key'),
                'api_publishable_key' => form_error('api_publishable_key'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function payu()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('key', $this->lang->line('key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('salt', $this->lang->line('salt'), 'trim|required|xss_clean');
        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key' => $this->input->post('key'),
                'salt'           => $this->input->post('salt'),
                'payment_type'   => 'payu',
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'key'  => form_error('key'),
                'salt' => form_error('salt'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function twocheckout()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('twocheckout_api_publishable_key', $this->lang->line('merchant_code'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('twocheckout_api_secret_key', $this->lang->line('secret_key'), 'trim|required|xss_clean');
        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'      => $this->input->post('twocheckout_api_secret_key'),
                'api_publishable_key' => $this->input->post('twocheckout_api_publishable_key'),
                'payment_type'        => 'twocheckout',
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'twocheckout_api_secret_key'      => form_error('twocheckout_api_secret_key'),
                'twocheckout_api_publishable_key' => form_error('twocheckout_api_publishable_key'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function ccavenue()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('ccavenue_secret', $this->lang->line('key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ccavenue_salt', $this->lang->line('salt'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ccavenue_api_publishable_key', $this->lang->line('access_code'), 'trim|required|xss_clean');
        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'      => $this->input->post('ccavenue_secret'),
                'salt'                => $this->input->post('ccavenue_salt'),
                'api_publishable_key' => $this->input->post('ccavenue_api_publishable_key'),
                'payment_type'        => 'ccavenue',
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'ccavenue_secret'              => form_error('ccavenue_secret'),
                'ccavenue_salt'                => form_error('ccavenue_salt'),
                'ccavenue_api_publishable_key' => form_error('ccavenue_api_publishable_key'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function paystack()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('paystack_secretkey', $this->lang->line('key'), 'trim|required|xss_clean');
        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key' => $this->input->post('paystack_secretkey'),
                'payment_type'   => 'paystack',
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'paystack_secretkey' => form_error('paystack_secretkey'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function instamojo()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('instamojo_apikey', $this->lang->line('key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('instamojo_authtoken', $this->lang->line('key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('instamojo_salt', $this->lang->line('key'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'api_secret_key'      => $this->input->post('instamojo_apikey'),
                'api_publishable_key' => $this->input->post('instamojo_authtoken'),
                'salt'                => $this->input->post('instamojo_salt'),
                'payment_type'        => 'instamojo',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'instamojo_apikey'    => form_error('instamojo_apikey'),
                'instamojo_authtoken' => form_error('instamojo_authtoken'),
                'instamojo_salt'      => form_error('instamojo_salt'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function razorpay()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('razorpay_keyid', $this->lang->line('key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('razorpay_secretkey', $this->lang->line('key'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'api_secret_key'      => $this->input->post('razorpay_secretkey'),
                'api_publishable_key' => $this->input->post('razorpay_keyid'),
                'payment_type'        => 'razorpay',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'razorpay_keyid'     => form_error('razorpay_keyid'),
                'razorpay_secretkey' => form_error('razorpay_secretkey'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function paytm()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('paytm_merchantid', $this->lang->line('key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paytm_merchantkey', $this->lang->line('key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paytm_website', $this->lang->line('key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paytm_industrytype', $this->lang->line('key'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'api_secret_key'      => $this->input->post('paytm_merchantkey'),
                'api_publishable_key' => $this->input->post('paytm_merchantid'),
                'paytm_website'       => $this->input->post('paytm_website'),
                'paytm_industrytype'  => $this->input->post('paytm_industrytype'),
                'payment_type'        => 'paytm',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'paytm_merchantkey'  => form_error('paytm_merchantkey'),
                'paytm_merchantid'   => form_error('paytm_merchantid'),
                'paytm_website'      => form_error('paytm_website'),
                'paytm_industrytype' => form_error('paytm_industrytype'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function midtrans()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('midtrans_serverkey', $this->lang->line('key'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'api_secret_key' => $this->input->post('midtrans_serverkey'),
                'payment_type'   => 'midtrans',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'midtrans_serverkey' => form_error('midtrans_serverkey'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function pesapal()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('pesapal_consumer_key', $this->lang->line('consumer_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('pesapal_consumer_secret', $this->lang->line('consumer_secret'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'api_secret_key'      => $this->input->post('pesapal_consumer_secret'),
                'api_publishable_key' => $this->input->post('pesapal_consumer_key'),
                'payment_type'        => 'pesapal',
            );

            $this->paymentsetting_model->add($data);

            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'pesapal_consumer_key'    => form_error('pesapal_consumer_key'),
                'pesapal_consumer_secret' => form_error('pesapal_consumer_secret'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function ipayafrica()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('ipayafrica_vendorid', $this->lang->line('vendorid'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ipayafrica_hashkey', $this->lang->line('hashkey'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'api_secret_key'      => $this->input->post('ipayafrica_hashkey'),
                'api_publishable_key' => $this->input->post('ipayafrica_vendorid'),
                'payment_type'        => 'ipayafrica',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'ipayafrica_vendorid' => form_error('ipayafrica_vendorid'),
                'ipayafrica_hashkey'  => form_error('ipayafrica_hashkey'),

            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function flutterwave()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('public_key', $this->lang->line('public_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('secret_key', $this->lang->line('secret_key'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(

                'api_publishable_key' => $this->input->post('public_key'),
                'api_secret_key'      => $this->input->post('secret_key'),
                'payment_type'        => 'flutterwave',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'public_key' => form_error('public_key'),
                'secret_key' => form_error('secret_key'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function jazzcash()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('jazzcash_pp_MerchantID', $this->lang->line('pp_MerchantID'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('jazzcash_pp_Password', $this->lang->line('pp_password'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'api_secret_key' => $this->input->post('jazzcash_pp_MerchantID'),
                'api_password'   => $this->input->post('jazzcash_pp_Password'),
                'payment_type'   => 'jazzcash',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'jazzcash_pp_MerchantID' => form_error('jazzcash_pp_MerchantID'),
                'jazzcash_pp_Password'   => form_error('jazzcash_pp_Password'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function billplz()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('billplz_api_key', $this->lang->line('api_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('billplz_customer_service_email', $this->lang->line('customer_service_email'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'api_secret_key' => $this->input->post('billplz_api_key'),
                'api_email'      => $this->input->post('billplz_customer_service_email'),
                'payment_type'   => 'billplz',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'billplz_api_key'                => form_error('billplz_api_key'),
                'billplz_customer_service_email' => form_error('billplz_customer_service_email'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function sslcommerz()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('sslcommerz_api_key', $this->lang->line('sslcommerz_api_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sslcommerz_store_password', $this->lang->line('sslcommerz_store_password'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'api_password'        => $this->input->post('sslcommerz_store_password'),
                'api_publishable_key' => $this->input->post('sslcommerz_api_key'),
                'payment_type'        => 'sslcommerz',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'sslcommerz_store_password' => form_error('sslcommerz_store_password'),
                'sslcommerz_api_key'        => form_error('sslcommerz_api_key'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function walkingm()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('walkingm_client_id', $this->lang->line('client_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('walkingm_client_secret', $this->lang->line('client_secret'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'api_publishable_key' => $this->input->post('walkingm_client_id'),
                'api_secret_key'      => $this->input->post('walkingm_client_secret'),
                'payment_type'        => 'walkingm',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'walkingm_client_id'     => form_error('walkingm_client_id'),
                'walkingm_client_secret' => form_error('walkingm_client_secret'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function mollie()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('mollie_api_key', $this->lang->line('api_key'), 'trim|required|xss_clean');
        if ($this->form_validation->run()) {

            $data = array(
                'api_publishable_key' => $this->input->post('mollie_api_key'),
                'payment_type'        => 'mollie',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'mollie_api_key' => form_error('mollie_api_key'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function cashfree()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('cashfree_app_id', $this->lang->line('app_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('cashfree_secret_key', $this->lang->line('secret_key'), 'trim|required|xss_clean');
        if ($this->form_validation->run()) {

            $data = array(
                'api_publishable_key' => $this->input->post('cashfree_app_id'),
                'api_secret_key'      => $this->input->post('cashfree_secret_key'),
                'payment_type'        => 'cashfree',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'cashfree_app_id'     => form_error('cashfree_app_id'),
                'cashfree_secret_key' => form_error('cashfree_secret_key'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function payfast()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('payfast_api_publishable_key', $this->lang->line('merchant_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payfast_api_secret_key', $this->lang->line('merchant_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payfast_salt', $this->lang->line('security_passphrase'), 'trim|required|xss_clean');
        if ($this->form_validation->run()) {

            $data = array(
                'api_publishable_key' => $this->input->post('payfast_api_publishable_key'),
                'api_secret_key'      => $this->input->post('payfast_api_secret_key'),
                'salt'                => $this->input->post('payfast_salt'),
                'payment_type'        => 'payfast',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'payfast_api_publishable_key' => form_error('payfast_api_publishable_key'),
                'payfast_api_secret_key'      => form_error('payfast_api_secret_key'),
                'payfast_salt'                => form_error('payfast_salt'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function toyyibPay()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('toyyibpay_api_secret_key', $this->lang->line('secret_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('toyyibpay_category_code', $this->lang->line('category_code'), 'trim|required|xss_clean');
        if ($this->form_validation->run()) {

            $data = array(
                'api_secret_key' => $this->input->post('toyyibpay_api_secret_key'),
                'api_signature'  => $this->input->post('toyyibpay_category_code'),
                'payment_type'   => 'toyyibpay',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'toyyibpay_api_secret_key' => form_error('toyyibpay_api_secret_key'),
                'toyyibpay_category_code'  => form_error('toyyibpay_category_code'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function skrill()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('skrill_api_email', $this->lang->line('merchant_account_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('skrill_salt', $this->lang->line('merchant_secret_salt'), 'trim|required|xss_clean');
        if ($this->form_validation->run()) {

            $data = array(
                'api_email'    => $this->input->post('skrill_api_email'),
                'salt'         => $this->input->post('skrill_salt'),
                'payment_type' => 'skrill',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'skrill_api_email' => form_error('skrill_api_email'),
                'skrill_salt'      => form_error('skrill_salt'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function payhere()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('payhere_api_publishable_key', $this->lang->line('merchant_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payhere_api_secret_key', $this->lang->line('merchant_secret'), 'trim|required|xss_clean');
        if ($this->form_validation->run()) {

            $data = array(
                'api_publishable_key' => $this->input->post('payhere_api_publishable_key'),
                'api_secret_key'      => $this->input->post('payhere_api_secret_key'),
                'payment_type'        => 'payhere',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'payhere_api_publishable_key' => form_error('payhere_api_publishable_key'),
                'payhere_api_secret_key'      => form_error('payhere_api_secret_key'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));

        }
    }

public function onepay() 
{     
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('onepay_merchant_id', $this->lang->line('onepay_merchant_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('onepay_salt', $this->lang->line('access_code'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('onepay_api_signature', $this->lang->line('hash_key'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'api_publishable_key' => $this->input->post('onepay_merchant_id'),
                 'salt' => $this->input->post('onepay_salt'),
                 'api_signature' => $this->input->post('onepay_api_signature'),
                'payment_type' => 'onepay',
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'onepay_merchant_id' => form_error('onepay_merchant_id'),
                'onepay_salt' => form_error('onepay_salt'),
                'onepay_api_signature' => form_error('onepay_api_signature'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }
    
    // ------------------------------------------------------------------ subaccounts (no keys for the school)

    /**
     * Banks for the subaccount form (JSON). The platform's key is used: the school has none.
     * /admin/paymentsettings/banks/<paystack|flutterwave>
     */
    public function banks($gateway = '')
    {
        $driver = $this->subaccount_driver($gateway);
        try {
            $out = array('ok' => true, 'banks' => $driver->banks());
        } catch (Payment_gateway_error $e) {
            $out = array('ok' => false, 'message' => $e->getMessage());
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($out));
    }

    /** The account holder's name for a bank and account number (JSON, POST gateway, bank, account). */
    public function resolve_account()
    {
        $driver = $this->subaccount_driver((string) $this->input->post('gateway'));
        list($bank, $account) = $this->bank_and_account();
        try {
            $out = array('ok' => true, 'name' => $driver->resolve_account($bank, $account));
        } catch (Payment_gateway_error $e) {
            $out = array('ok' => false, 'message' => $e->getMessage());
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($out));
    }

    /**
     * Creates the school's subaccount on the platform's Paystack/Flutterwave account and saves it
     * (JSON, POST gateway, bank, account, business_name). Payments then settle to that bank account.
     */
    public function subaccount()
    {
        $gateway = (string) $this->input->post('gateway');
        $driver = $this->subaccount_driver($gateway);
        list($bank, $account) = $this->bank_and_account();
        $setting = $this->setting_model->get();
        $school = $setting ? $setting[0] : array();
        $business = trim((string) $this->input->post('business_name'));
        if ($business === '') {
            $business = isset($school['name']) ? $school['name'] : 'School';
        }
        $business = mb_substr($business, 0, 100);
        $email = isset($school['email']) ? (string) $school['email'] : '';
        $phone = isset($school['phone']) ? preg_replace('/[^0-9+]/', '', (string) $school['phone']) : '';
        try {
            $name = $driver->resolve_account($bank, $account);
            $percent = Payment_gateways::platform_split_percent();
            $code = $gateway === 'flutterwave'
                ? $driver->create_subaccount($business, $bank, $account, $percent, $email, $phone)
                : $driver->create_subaccount($business, $bank, $account, $percent, $email);
        } catch (Payment_gateway_error $e) {
            return $this->json(array('st' => 1, 'msg' => array('subaccount' => $e->getMessage())));
        }
        $this->paymentsetting_model->add(array(
            'payment_type' => $gateway,
            'api_secret_key' => '',          // no own keys: the subaccount is used (Payment_gateways::for_school)
            'api_publishable_key' => '',
            'api_signature' => $code,
            'api_username' => $bank,
            'account_no' => $account,
            'api_email' => mb_substr($name, 0, 200),
            'salt' => '', 'paypal_demo' => '', 'paytm_website' => '', 'paytm_industrytype' => '',
        ));
        $this->json(array('st' => 0, 'msg' => 'Done: payments will settle to ' . $name . ' (' . $account . '). Now choose '
            . Payment_gateways::name($gateway) . ' under Select payment gateway.'));
    }

    /** The platform's driver for subaccount pages, or 404 when the platform has no key for it. */
    private function subaccount_driver($gateway)
    {
        if (!$this->rbac->hasPrivilege('payment_methods', 'can_edit')) {
            access_denied();
        }
        $this->load->library('Payment_gateways');
        if (!$this->payment_gateways->subaccounts_available($gateway)) {
            show_404();
        }
        return $this->payment_gateways->for_platform($gateway);
    }

    /** [bank code, 10-digit account number] from the POST, or a JSON error. */
    private function bank_and_account()
    {
        $bank = preg_replace('/[^0-9A-Za-z]/', '', (string) $this->input->post('bank'));
        $account = preg_replace('/\D/', '', (string) $this->input->post('account'));
        if ($bank === '' || strlen($account) !== 10) {
            $this->json(array('ok' => false, 'st' => 1, 'message' => 'Choose the bank and enter the 10-digit account number (NUBAN).',
                'msg' => array('subaccount' => 'Choose the bank and enter the 10-digit account number (NUBAN).')));
            exit;
        }
        return array($bank, $account);
    }

    private function json(array $data)
    {
        $this->output->set_content_type('application/json')->set_output(json_encode($data))->_display();
        exit;
    }
    // ------------------------------------------------------------------ Nigerian gateways (Online_fee_payment)

    /** Monnify: API key, secret key, contract code, test/live. */
    /** Bank transfer: the school's bank details, for the admission form fee and school fees (Offline Bank Payments). */
    public function bank_transfer()
    {
        if (!$this->rbac->hasPrivilege('payment_methods', 'can_edit') || $this->input->method() !== 'post') {
            access_denied();
        }
        $this->load->library('School_bank_transfer');
        $post = array(
            'bank_name' => $this->input->post('bank_name'),
            'account_name' => $this->input->post('account_name'),
            'account_number' => $this->input->post('account_number'),
            'note' => $this->input->post('note'),
            'admission' => $this->input->post('admission'),
            'school_fees' => $this->input->post('school_fees'),
        );
        $on = !empty($post['admission']) || !empty($post['school_fees']);
        $missing = array();
        foreach (array('bank_name' => 'bank name', 'account_name' => 'account name', 'account_number' => 'account number') as $key => $label) {
            if ($on && trim(strip_tags((string) $post[$key])) === '') {
                $missing[] = $label;
            }
        }
        if ($missing) {
            $this->session->set_flashdata('bank_transfer_msg', array('danger', 'Fill in the ' . implode(', ', $missing) . ' before switching bank transfer on.'));
        } else {
            $this->school_bank_transfer->save($post);
            $this->session->set_flashdata('bank_transfer_msg', array('success', 'Bank transfer settings saved.'));
        }
        redirect('admin/paymentsettings#tab_bank');
    }

    public function monnify()
    {
        $this->save_gateway('monnify', array(
            'monnify_api_key' => array('API key', 'api_publishable_key'),
            'monnify_secret_key' => array('Secret key', 'api_secret_key'),
            'monnify_contract_code' => array('Contract code', 'account_no'),
        ));
    }

    /** Interswitch Web Checkout: merchant code, pay item id, test/live. */
    public function interswitch()
    {
        $this->save_gateway('interswitch', array(
            'interswitch_merchant_code' => array('Merchant code', 'api_username'),
            'interswitch_pay_item_id' => array('Pay item ID', 'account_no'),
        ));
    }

    /** Remita: merchant ID, service type ID, API key, test/live. */
    public function remita()
    {
        $this->save_gateway('remita', array(
            'remita_merchant_id' => array('Merchant ID', 'api_username'),
            'remita_service_type_id' => array('Service type ID', 'account_no'),
            'remita_api_key' => array('API key', 'api_secret_key'),
        ));
    }

    /**
     * Saves a gateway's keys (AJAX, same answers as the other gateways). $fields maps
     * form field => [label, payment_settings column]; "<gateway>_mode" (0 test, 1 live) goes to gateway_mode.
     */
    private function save_gateway($gateway, array $fields)
    {
        if (!$this->rbac->hasPrivilege('payment_methods', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_error_delimiters('', '');
        foreach ($fields as $field => $spec) {
            $this->form_validation->set_rules($field, $spec[0], 'trim|required|max_length[200]|xss_clean');
        }
        $this->form_validation->set_rules($gateway . '_mode', 'Mode', 'trim|in_list[0,1]');
        if (!$this->form_validation->run()) {
            $errors = array();
            foreach ($fields as $field => $spec) {
                $errors[$field] = form_error($field);
            }
            echo json_encode(array('st' => 1, 'msg' => $errors));
            return;
        }
        $data = array('payment_type' => $gateway, 'gateway_mode' => (int) $this->input->post($gateway . '_mode') === 1 ? 1 : 0);
        foreach ($fields as $field => $spec) {
            $data[$spec[1]] = $this->input->post($field);
        }
        foreach (array('api_secret_key', 'salt', 'api_publishable_key', 'paypal_demo', 'account_no', 'paytm_website', 'paytm_industrytype') as $not_null) {
            if (!isset($data[$not_null])) {
                $data[$not_null] = ''; // NOT NULL columns without a default
            }
        }
        $this->paymentsetting_model->add($data);
        echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
    }

    public function setting()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('payment_setting', $this->lang->line('payment_setting'), array('required',
            array('paymentsetting', array($this->paymentsetting_model, 'valid_paymentsetting')),
        )
        );
        if ($this->form_validation->run()) {
            $paymentsetting = $this->input->post('payment_setting');
            $other          = false;
            if ($paymentsetting == "none") {
                $other = true;
                $data  = array(
                    'is_active' => 'no',
                );
            } else {
                $data = array(
                    'payment_type' => $this->input->post('payment_setting'),
                    'is_active'    => 'yes',
                );
            }
            $this->paymentsetting_model->active($data, $other);

            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'payment_setting' => form_error('payment_setting'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

}
