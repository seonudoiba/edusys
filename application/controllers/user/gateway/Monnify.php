<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/payments/Online_fee_controller.php';

/**
 * School fees through Monnify, with the school's own keys (System Settings > Payment Methods).
 * See Online_fee_controller and Online_fee_payment; the payer returns to Gateway_return.
 */
class Monnify extends Online_fee_controller
{
    const GATEWAY = 'monnify';
}
