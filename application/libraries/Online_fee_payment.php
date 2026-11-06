<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Online school fee payments through the gateway drivers (Paystack, Flutterwave, Monnify,
 * Interswitch, Remita), with the school's own keys: the money goes to the school's account.
 *
 *   start()     saves a pending row in online_fee_payments (amount, currency, the fees being paid)
 *               and asks the gateway for its payment page
 *   complete()  asks the gateway's server about that reference, checks the amount and currency,
 *               and records the fees ONCE (a reload or a replayed return URL does nothing)
 *
 * complete() doesn't need the payer's session (the row holds everything), so it also works when
 * the gateway returns the payer with a cross-site POST (Interswitch) or a session has expired.
 */
class Online_fee_payment
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('Payment_gateways');
        $this->CI->load->model(array('paymentsetting_model', 'studentfeemaster_model', 'feegrouptype_model', 'studenttransportfee_model', 'student_model'));
    }

    /** The school's active gateway row if it is $gateway, else null. */
    public function school_setting($gateway)
    {
        $row = $this->CI->paymentsetting_model->getActiveMethod();
        return ($row && $row->payment_type === $gateway) ? $row : null;
    }

    /** @return Payment_gateway|null the driver with the school's keys (null: not the active gateway) */
    public function driver($gateway)
    {
        $row = $this->school_setting($gateway);
        return $row ? $this->CI->payment_gateways->for_school($gateway, $row) : null;
    }

    /**
     * Saves the pending payment and starts it at the gateway.
     * @param array $params the fee payment session ('params' set by user/gateway/payment)
     * @return array the driver's answer: ['redirect' => url] or ['post' => [url, fields]]
     * @throws Payment_gateway_error
     */
    public function start($gateway, array $params, $email, $phone)
    {
        $driver = $this->driver($gateway);
        if (!$driver) {
            throw new Payment_gateway_error(Payment_gateways::name($gateway) . ' is not this school\'s payment method.');
        }
        if ($driver->missing()) {
            throw new Payment_gateway_error(Payment_gateways::name($gateway) . ' is not fully set up (System Settings > Payment Methods).');
        }
        $amount = round((float) convertBaseAmountCurrencyFormat($params['fine_amount_balance'] + $params['total']), 2);
        if ($amount <= 0) {
            throw new Payment_gateway_error('There is nothing to pay.');
        }
        $currency = strtoupper((string) (isset($params['invoice']->currency_name) ? $params['invoice']->currency_name : 'NGN'));
        $reference = 'EF' . Tenant::id() . 'T' . date('ymdHis') . strtoupper(bin2hex(random_bytes(3)));
        $this->CI->db->insert('online_fee_payments', array(
            'gateway' => $gateway,
            'reference' => $reference,
            'student_id' => (int) $params['student_id'],
            'student_session_id' => (int) $params['student_session_id'],
            'amount' => $amount,
            'currency' => $currency,
            'payer_email' => $email,
            'payer_phone' => $phone,
            'fees' => json_encode($params['student_fees_master_array']),
            'status' => 'pending',
        ));
        $id = (int) $this->CI->db->insert_id();
        $answer = $driver->start(array(
            'reference' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'email' => $email,
            'name' => (string) $params['name'],
            'phone' => $phone,
            'description' => $this->CI->customlib->getAppName() . ' school fees',
            'return_url' => site_url('gateway_return/fees/' . $gateway . '/' . $reference),
        ));
        if (!empty($answer['gateway_ref'])) {
            $this->CI->db->where('id', $id)->update('online_fee_payments', array('gateway_ref' => $answer['gateway_ref']));
        }
        return $answer;
    }

    /**
     * Confirms a payment with the gateway and records the fees once.
     * @param Payment_gateway|null $driver normally null (the school's keys); tests pass their own
     * @return array ['ok' => bool, 'message' => string, 'student_id' => int|null]
     */
    public function complete($gateway, $reference, array $request = array(), Payment_gateway $driver = null)
    {
        $db = $this->CI->db;
        $row = $db->where(array('gateway' => (string) $gateway, 'reference' => (string) $reference))->get('online_fee_payments')->row_array();
        if (!$row) {
            return array('ok' => false, 'message' => 'Unknown payment reference.', 'student_id' => null);
        }
        if ($row['status'] === 'paid') {
            return array('ok' => true, 'message' => 'This payment is already recorded.', 'student_id' => (int) $row['student_id']);
        }
        if ($driver === null) {
            $driver = $this->CI->payment_gateways->for_school($gateway, $this->setting_row($gateway));
        }
        try {
            $result = $driver->check(array(
                'reference' => $row['reference'],
                'amount' => (float) $row['amount'],
                'currency' => $row['currency'],
                'gateway_ref' => $row['gateway_ref'],
                'email' => $row['payer_email'],
            ), $request);
        } catch (Payment_gateway_error $e) {
            return array('ok' => false, 'message' => $e->getMessage(), 'student_id' => (int) $row['student_id']);
        }
        if (empty($result['paid'])) {
            $db->where('id', (int) $row['id'])->update('online_fee_payments', array('message' => substr((string) $result['message'], 0, 255)));
            return array('ok' => false, 'message' => $result['message'], 'student_id' => (int) $row['student_id']);
        }
        if ($result['amount'] === null || $result['amount'] + 0.009 < (float) $row['amount']
            || ($result['currency'] && strtoupper($result['currency']) !== strtoupper($row['currency']))) {
            $msg = 'Amount or currency mismatch: expected ' . $row['currency'] . ' ' . $row['amount'] . ', the gateway reports '
                . ($result['currency'] ?: '?') . ' ' . ($result['amount'] === null ? '?' : $result['amount']) . '.';
            log_message('error', 'Online fee payment ' . $row['reference'] . ': ' . $msg);
            $db->where('id', (int) $row['id'])->update('online_fee_payments', array('status' => 'failed', 'message' => $msg));
            return array('ok' => false, 'message' => 'The payment could not be matched to this invoice. The school will check it.', 'student_id' => (int) $row['student_id']);
        }

        // Claim the row: only one request can move it from pending to paid.
        $db->where(array('id' => (int) $row['id'], 'status' => 'pending'))->update('online_fee_payments', array(
            'status' => 'paid', 'paid_at' => date('Y-m-d H:i:s'), 'message' => 'Paid',
            'gateway_ref' => $result['id'] ?: $row['gateway_ref'],
        ));
        if ($db->affected_rows() !== 1) {
            return array('ok' => true, 'message' => 'This payment is already recorded.', 'student_id' => (int) $row['student_id']);
        }
        $txn = $result['id'] ? $result['id'] : $row['reference'];
        $bulk = array();
        foreach ((array) json_decode($row['fees'], true) as $fee) {
            $bulk[] = array(
                'fee_category' => $fee['fee_category'],
                'student_transport_fee_id' => $fee['student_transport_fee_id'],
                'student_fees_master_id' => $fee['student_fees_master_id'],
                'fee_groups_feetype_id' => $fee['fee_groups_feetype_id'],
                'amount_detail' => array(
                    'amount' => $fee['amount_balance'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $fee['fine_balance'],
                    'description' => 'Online fees deposit through ' . Payment_gateways::name($gateway) . ' Txn ID: ' . $txn . ' (ref ' . $row['reference'] . ')',
                    'received_by' => '',
                    'payment_mode' => Payment_gateways::name($gateway),
                ),
            );
        }
        $response = $this->CI->studentfeemaster_model->fee_deposit_bulk($bulk, null);
        if (!$response) {
            // Not recorded: put it back so the school (or a retry) can complete it.
            $db->where('id', (int) $row['id'])->update('online_fee_payments', array('status' => 'pending', 'paid_at' => null,
                'message' => 'Paid at the gateway, but the fees could not be recorded'));
            log_message('error', 'Online fee payment ' . $row['reference'] . ': paid, fee_deposit_bulk failed');
            return array('ok' => false, 'message' => 'The payment went through but could not be recorded yet. The school will record it.', 'student_id' => (int) $row['student_id']);
        }
        $db->where('id', (int) $row['id'])->update('online_fee_payments', array('result' => json_encode($response)));
        try {
            $this->notify((int) $row['student_id'], (int) $row['student_session_id'], $response);
        } catch (Throwable $e) {
            log_message('error', 'Online fee payment ' . $row['reference'] . ': notification failed: ' . $e->getMessage());
        }
        return array('ok' => true, 'message' => 'Payment recorded.', 'student_id' => (int) $row['student_id']);
    }

    /** The school's settings row for $gateway, active or not (a gateway switched off after a payment started still completes it). */
    private function setting_row($gateway)
    {
        return $this->CI->db->where('payment_type', (string) $gateway)->get('payment_settings')->row();
    }

    /** The fee_submission email/SMS, as the other gateways send it (without relying on the session). */
    private function notify($student_id, $student_session_id, array $response)
    {
        $ss = $this->CI->db->where('id', $student_session_id)->get('student_session')->row();
        if (!$ss) {
            return;
        }
        $student = $this->CI->student_model->getStudentByClassSectionID($ss->class_id, $ss->section_id, $student_id);
        $fee_group_name = $type = $code = $amount = $fine_type = $due_date = $fine_percentage = $fine_amount = $invoice = array();
        $fee_category = '';
        foreach ($response as $r) {
            $fee_category = $r['fee_category'];
            $invoice[] = array('invoice_id' => $r['invoice_id'], 'sub_invoice_id' => $r['sub_invoice_id'], 'fee_category' => $fee_category);
            if ($r['student_transport_fee_id'] != 0 && $r['fee_category'] === 'transport') {
                $m = $this->CI->studenttransportfee_model->getTransportFeeMasterByStudentTransportID($r['student_transport_fee_id']);
                $fee_group_name[] = $this->CI->lang->line('transport_fees');
                $type[] = $m->month;
                $code[] = '-';
                $amount[] = $m->amount;
            } else {
                $m = $this->CI->feegrouptype_model->getFeeGroupByIDAndStudentSessionID($r['fee_groups_feetype_id'], $student_session_id);
                $fee_group_name[] = $m->fee_group_name;
                $type[] = $m->type;
                $code[] = $m->code;
                $amount[] = $m->is_system ? $m->balance_fee_master_amount : $m->amount;
            }
            $fine_type[] = $m->fine_type;
            $due_date[] = $m->due_date;
            $fine_percentage[] = $m->fine_percentage;
            $fine_amount[] = $m->fine_amount;
        }
        $wrap = function ($a) { return '(' . implode(',', $a) . ')'; };
        $this->CI->load->library('mailsmsconf');
        $this->CI->mailsmsconf->mailsms('fee_submission', array(
            'student_id' => $student_id,
            'student_session_id' => $student_session_id,
            'invoice' => $invoice,
            'contact_no' => $student ? $student['guardian_phone'] : '',
            'email' => $student ? $student['email'] : '',
            'parent_app_key' => $student ? $student['parent_app_key'] : '',
            'amount' => $wrap($amount),
            'fine_type' => $wrap($fine_type),
            'due_date' => $wrap($due_date),
            'fine_percentage' => $wrap($fine_percentage),
            'fine_amount' => $wrap($fine_amount),
            'fee_group_name' => $wrap($fee_group_name),
            'type' => $wrap($type),
            'code' => $wrap($code),
            'fee_category' => $fee_category,
            'send_type' => 'group',
        ));
    }
}
