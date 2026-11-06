<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Skrill extends Front_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('gateway_ins_model'));
    }

    public function index()
    {
        // Skrill signs its status notification: md5sig = MD5(merchant_id . transaction_id . MD5(secret word)
        // . mb_amount . mb_currency . status), upper case. Anything else is refused (it could be forged).
        $details = $this->gateway_ins_model->get_gateway_details('skrill');
        $secret = $details ? trim((string) $details->salt) : '';
        $p = function ($k) { return isset($_POST[$k]) ? (string) $_POST[$k] : ''; };
        $expected = strtoupper(md5($p('merchant_id') . $p('transaction_id') . strtoupper(md5($secret)) . $p('mb_amount') . $p('mb_currency') . $p('status')));
        if ($secret === '' || !hash_equals($expected, strtoupper($p('md5sig')))
            || ($details && $details->api_email !== '' && strcasecmp($p('pay_to_email'), $details->api_email) !== 0)) {
            log_message('error', 'Skrill notification refused: bad signature (transaction ' . $p('transaction_id') . ')');
            set_status_header(403);
            exit('refused');
        }
        $refno    = $_POST['transaction_id'];
       $order_id = $_POST['transaction_id'];
       $status   = $_POST['status'];
	
        if ($status == 2) {
            $para_amount    = $this->gateway_ins_model->get_gateway_ins($order_id, 'skrill');
            $parameter_data = $this->gateway_ins_model->get($order_id, 'skrill');
            $bulk_fees      = array();
            if ($para_amount['module_type'] == 'fees') {
                foreach ($parameter_data as $fee_key => $fee_value) {
                    $amount_detail                = json_decode($fee_value['amount_detail'], true);
                    $amount_detail['description'] = "Online fees deposit through skrill TXN ID:" . $refno;
                    $insert_fee_data              = array(
                        'fee_category'             => $fee_value['fee_category'],
                        'student_transport_fee_id' => $fee_value['student_transport_fee_id'],
                        'student_fees_master_id'   => $fee_value['student_fees_master_id'],
                        'fee_groups_feetype_id'    => $fee_value['fee_groups_feetype_id'],
                        'amount_detail'            => $amount_detail,
                    );
                    $bulk_fees[] = $insert_fee_data;
                    //========
                }
                $insert_id = $this->gateway_ins_model->fee_deposit_bulk($bulk_fees);
                if ($insert_id) {
                    $response = "success";
                    $this->gateway_ins_model->deleteBygateway_ins_id($para_amount['id']);
                } else {
                    $response = "quiry_failed";
                }
            }

            if ($para_amount['module_type'] == 'online_course') {
                $online_course                   = $this->gateway_ins_model->get_processing_payment($para_amount['id']);
                $online_course['transaction_id'] = $pfData['pf_payment_id'];
                $online_course['note']           = "Online course fees processing skrill Txn ID: " . $pfData['pf_payment_id'];
                unset($online_course['id']);
                unset($online_course['gateway_ins_id']);
                $response = "success";
                $this->gateway_ins_model->deleteprocessingpaymentByid($para_amount['id']);
            }
        }

        $get_statusByUnique_id = $this->gateway_ins_model->get_statusByUnique_id($order_id, 'skrill');
        $gateway_ins_response  = json_encode($_POST);
        $gateway_ins_add       = array('gateway_ins_id' => $get_statusByUnique_id['id'], 'posted_data' => $gateway_ins_response, 'response
        '                                       => $status);

        $this->gateway_ins_model->add_gateway_ins_response($gateway_ins_add);

        $this->gateway_ins_model->update_gateway_ins(array('id' => $get_statusByUnique_id['id'], 'payment_status' => $status));

    }
}
