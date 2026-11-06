<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Base of the fee payment pages for the gateways built on Online_fee_payment
 * (user/gateway/paystack, flutterwave, monnify, interswitch, remita):
 *
 *   index()  the summary of the fees being paid, with the payer's email and phone
 *   pay()    starts the payment (redirect to the gateway, or an auto-submitted form)
 *
 * The payer comes back to gateway_return/fees/<gateway>/<reference> (Gateway_return), which
 * doesn't need the session: the gateway's server is asked, and the fees are recorded once.
 */
abstract class Online_fee_controller extends Studentgateway_Controller
{
    /** Driver id: paystack, flutterwave, monnify, interswitch, remita */
    const GATEWAY = '';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Online_fee_payment');
        $this->setting = $this->setting_model->get();
        $this->setting[0]['currency_symbol'] = $this->customlib->getSchoolCurrencyFormat();
    }

    public function index()
    {
        $this->show();
    }

    public function pay()
    {
        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|valid_email|xss_clean');
        if ($this->form_validation->run() == false) {
            return $this->show();
        }
        $params = $this->session->userdata('params');
        if (empty($params['student_fees_master_array'])) {
            redirect('user/user/getfees');
        }
        try {
            $answer = $this->online_fee_payment->start(static::GATEWAY, $params,
                (string) $this->input->post('email'), (string) $this->input->post('phone'));
        } catch (Payment_gateway_error $e) {
            return $this->show($e->getMessage());
        }
        if (isset($answer['redirect'])) {
            redirect($answer['redirect']);
        }
        $this->load->view('user/gateway/online/redirect_post', array(
            'action' => $answer['post']['url'],
            'fields' => $answer['post']['fields'],
            'gateway_name' => Payment_gateways::name(static::GATEWAY),
        ));
    }

    protected function show($error = null)
    {
        $params = $this->session->userdata('params');
        if (empty($params['student_fees_master_array'])) {
            redirect('user/user/getfees');
        }
        $this->load->view('user/gateway/online/index', array(
            'params' => $params,
            'setting' => $this->setting,
            'api_error' => $error,
            'student_data' => $this->student_model->get($params['student_id']),
            'student_fees_master_array' => $params['student_fees_master_array'],
            'gateway_name' => Payment_gateways::name(static::GATEWAY),
            'action' => site_url('user/gateway/' . static::GATEWAY . '/pay'),
        ));
    }
}
