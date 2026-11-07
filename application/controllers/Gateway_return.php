<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Where payment gateways send the payer back (GET or POST), without needing a logged-in session:
 * a gateway may come back with a cross-site POST (Interswitch), which carries no session cookie.
 * Nothing the browser sends is trusted: the payment is looked up by our reference (in the school of
 * the host), and the gateway's server is asked whether it was paid.
 *
 *   gateway_return/fees/<gateway>/<reference>        school fees (Online_fee_payment)
 *   gateway_return/admission/<gateway>/<reference>   admission form fees (Admission_payment)
 */
class Gateway_return extends MY_Controller
{
    public function fees($gateway = '', $reference = '')
    {
        $this->load->library('Online_fee_payment');
        if (!Payment_gateways::known($gateway) || !preg_match('/^[A-Za-z0-9]{6,100}$/', (string) $reference)) {
            show_404();
        }
        $request = array_merge((array) $this->input->get(), (array) $this->input->post());
        $result = $this->online_fee_payment->complete($gateway, $reference, $request);
        if ($result['ok']) {
            redirect('user/gateway/payment/successinvoice');
        }
        $this->session->set_flashdata('payment_error', $result['message']);
        redirect('user/gateway/payment/paymentfailed');
    }

    public function admission($gateway = '', $reference = '')
    {
        $this->load->library('Admission_payment');
        if (!Payment_gateways::known($gateway) || !preg_match('/^[A-Za-z0-9]{6,100}$/', (string) $reference)) {
            show_404();
        }
        $request = array_merge((array) $this->input->get(), (array) $this->input->post());
        $result = $this->admission_payment->complete($gateway, $reference, $request);
        if (!$result['reference_no']) {
            show_404();
        }
        // The applicant may come back without their session (cross-site POST): let them see the application.
        $this->session->set_userdata('validlogin', $result['reference_no']);
        $this->session->set_flashdata($result['ok'] ? 'payment_ok' : 'payment_error', $result['message']);
        redirect('welcome/online_admission_review/' . $result['reference_no']);
    }
}
