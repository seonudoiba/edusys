<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Online admission form fees through the gateway drivers (Paystack, Flutterwave, Monnify,
 * Interswitch, Remita) with the school's keys, or its subaccount, like school fees
 * (Online_fee_payment):
 *
 *   start()     saves a pending row in online_fee_payments (online_admission_id, amount, currency)
 *               and asks the gateway for its payment page
 *   complete()  asks the gateway's server about that reference, checks amount and currency, and
 *               marks the application paid ONCE (a reload or a replayed return URL does nothing)
 *
 * complete() doesn't need the applicant's session, so it also works after a cross-site POST back
 * from the gateway (Interswitch) or an expired session.
 */
class Admission_payment
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('Payment_gateways');
        $this->CI->load->model(array('paymentsetting_model', 'onlinestudent_model'));
    }

    /** The school's active gateway id when it's one of the drivers, else null. */
    public function gateway()
    {
        $row = $this->CI->paymentsetting_model->getActiveMethod();
        return ($row && Payment_gateways::known($row->payment_type)) ? $row->payment_type : null;
    }

    /**
     * Saves the pending payment and starts it at the gateway.
     * @return array the driver's answer: ['redirect' => url] or ['post' => [url, fields]]
     * @throws Payment_gateway_error
     */
    public function start(array $application, $amount, $currency)
    {
        $gateway = $this->gateway();
        if (!$gateway) {
            throw new Payment_gateway_error('Online payment is not set up for this school yet. Please contact the school.');
        }
        $driver = $this->CI->payment_gateways->for_school($gateway, $this->CI->paymentsetting_model->getActiveMethod());
        if ($driver->missing()) {
            throw new Payment_gateway_error(Payment_gateways::name($gateway) . ' is not fully set up yet. Please contact the school.');
        }
        $amount = round((float) $amount, 2);
        if ($amount <= 0) {
            throw new Payment_gateway_error('There is nothing to pay.');
        }
        $email = trim((string) ($application['guardian_email'] ?: $application['email']));
        $phone = trim((string) ($application['guardian_phone'] ?: $application['mobileno']));
        if ($email === '') {
            throw new Payment_gateway_error('An email address is needed to pay online. Please contact the school.');
        }
        $reference = 'AD' . Tenant::id() . 'T' . date('ymdHis') . strtoupper(bin2hex(random_bytes(3)));
        $this->CI->db->insert('online_fee_payments', array(
            'gateway' => $gateway,
            'reference' => $reference,
            'online_admission_id' => (int) $application['id'],
            'amount' => $amount,
            'currency' => strtoupper((string) $currency),
            'payer_email' => $email,
            'payer_phone' => $phone,
            'status' => 'pending',
        ));
        $id = (int) $this->CI->db->insert_id();
        $answer = $driver->start(array(
            'reference' => $reference,
            'amount' => $amount,
            'currency' => strtoupper((string) $currency),
            'email' => $email,
            'name' => trim($application['firstname'] . ' ' . $application['lastname']),
            'phone' => $phone,
            'description' => $this->CI->customlib->getAppName() . ' admission form fee, ref ' . $application['reference_no'],
            'return_url' => site_url('gateway_return/admission/' . $gateway . '/' . $reference),
        ));
        if (!empty($answer['gateway_ref'])) {
            $this->CI->db->where('id', $id)->update('online_fee_payments', array('gateway_ref' => $answer['gateway_ref']));
        }
        return $answer;
    }

    /**
     * Confirms a payment with the gateway and marks the application paid once.
     * @param Payment_gateway|null $driver normally null (the school's keys); tests pass their own
     * @return array ['ok' => bool, 'message' => string, 'reference_no' => string|null]
     */
    public function complete($gateway, $reference, array $request = array(), Payment_gateway $driver = null)
    {
        $db = $this->CI->db;
        $row = $db->where(array('gateway' => (string) $gateway, 'reference' => (string) $reference))
            ->where('online_admission_id IS NOT NULL', null, false)->get('online_fee_payments')->row_array();
        if (!$row) {
            return array('ok' => false, 'message' => 'Unknown payment reference.', 'reference_no' => null);
        }
        $application = $db->where('id', (int) $row['online_admission_id'])->get('online_admissions')->row_array();
        $ref_no = $application ? $application['reference_no'] : null;
        if ($row['status'] === 'paid') {
            return array('ok' => true, 'message' => 'This payment is already recorded.', 'reference_no' => $ref_no);
        }
        if ($driver === null) {
            $setting = $db->where('payment_type', (string) $gateway)->get('payment_settings')->row();
            $driver = $this->CI->payment_gateways->for_school($gateway, $setting);
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
            return array('ok' => false, 'message' => $e->getMessage(), 'reference_no' => $ref_no);
        }
        if (empty($result['paid'])) {
            $db->where('id', (int) $row['id'])->update('online_fee_payments', array('message' => substr((string) $result['message'], 0, 255)));
            return array('ok' => false, 'message' => $result['message'], 'reference_no' => $ref_no);
        }
        if ($result['amount'] === null || $result['amount'] + 0.009 < (float) $row['amount']
            || ($result['currency'] && strtoupper($result['currency']) !== strtoupper($row['currency']))) {
            $msg = 'Amount or currency mismatch: expected ' . $row['currency'] . ' ' . $row['amount'] . ', the gateway reports '
                . ($result['currency'] ?: '?') . ' ' . ($result['amount'] === null ? '?' : $result['amount']) . '.';
            log_message('error', 'Admission fee payment ' . $row['reference'] . ': ' . $msg);
            $db->where('id', (int) $row['id'])->update('online_fee_payments', array('status' => 'failed', 'message' => $msg));
            return array('ok' => false, 'message' => 'The payment could not be matched to this application. The school will check it.', 'reference_no' => $ref_no);
        }

        // Claim the row: only one request can move it from pending to paid.
        $txn = $result['id'] ? $result['id'] : $row['reference'];
        $db->where(array('id' => (int) $row['id'], 'status' => 'pending'))->update('online_fee_payments', array(
            'status' => 'paid', 'paid_at' => date('Y-m-d H:i:s'), 'message' => 'Paid', 'gateway_ref' => $result['id'] ?: $row['gateway_ref'],
        ));
        if ($db->affected_rows() !== 1) {
            return array('ok' => true, 'message' => 'This payment is already recorded.', 'reference_no' => $ref_no);
        }
        $name = Payment_gateways::name($gateway);
        $this->CI->onlinestudent_model->paymentSuccess(array(
            'online_admission_id' => (int) $row['online_admission_id'],
            'paid_amount' => (float) $row['amount'],
            'transaction_id' => substr((string) $txn, 0, 100),
            'payment_mode' => $gateway,
            'payment_type' => 'online',
            'note' => substr('Admission form fee through ' . $name . ', ref ' . $row['reference'], 0, 100),
            'date' => date('Y-m-d H:i:s'),
        ));
        if ($application) {
            try {
                $this->CI->load->library('mailsmsconf');
                $this->CI->mailsmsconf->mailsms('online_admission_fees_submission', array(
                    'firstname' => $application['firstname'], 'lastname' => $application['lastname'], 'email' => $application['email'],
                    'date' => date($this->CI->customlib->getSchoolDateFormat()), 'reference_no' => $application['reference_no'],
                    'mobileno' => $application['mobileno'], 'paid_amount' => (float) $row['amount'],
                    'guardian_email' => $application['guardian_email'], 'guardian_phone' => $application['guardian_phone'],
                ));
            } catch (Throwable $e) {
                log_message('error', 'Admission fee payment ' . $row['reference'] . ': notification failed: ' . $e->getMessage());
            }
        }
        return array('ok' => true, 'message' => 'Payment received. Thank you.', 'reference_no' => $ref_no);
    }
}
