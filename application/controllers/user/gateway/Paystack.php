<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/payments/Online_fee_controller.php';

/**
 * School fees through Paystack, with the school's own keys (System Settings > Payment Methods).
 * See Online_fee_controller and Online_fee_payment; the payer returns to Gateway_return.
 */
class Paystack extends Online_fee_controller
{
    const GATEWAY = 'paystack';

    /** The old form posted here. */
    public function paystack_pay()
    {
        $this->pay();
    }

    /** Old return URL: payments started before this version can't be matched (no pending row), so they are shown as failed for the school to check. */
    public function verify_payment($ref = null)
    {
        redirect("user/gateway/payment/paymentfailed");
    }
}
