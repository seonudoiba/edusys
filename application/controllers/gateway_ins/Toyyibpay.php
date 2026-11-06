<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Toyyibpay extends Front_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('gateway_ins_model'));
    }

    /** True when toyyibPay reports a successful payment of $bill_code for $order_id. */
    private function bill_paid($bill_code, $order_id)
    {
        if ($bill_code === '' || $order_id === '') {
            return false;
        }
        $ch = curl_init('https://toyyibpay.com/index.php/api/getBillTransactions');
        curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => array('billCode' => $bill_code, 'billpaymentStatus' => '1')));
        $rows = json_decode((string) curl_exec($ch), true);
        curl_close($ch);
        foreach ((array) $rows as $r) {
            if (is_array($r) && isset($r['billpaymentStatus']) && (string) $r['billpaymentStatus'] === '1'
                && (!isset($r['billExternalReferenceNo']) || (string) $r['billExternalReferenceNo'] === $order_id)) {
                return true;
            }
        }
        return false;
    }

     public function index(){
 
        $refno=$_POST['refno'];
        $order_id=$_POST['order_id'];
        $status=$_POST['status'];
        // Don't trust the posted status: ask toyyibPay about the bill (it must be paid and be this order's).
        if ($status == 1 && !$this->bill_paid(isset($_POST['billcode']) ? (string) $_POST['billcode'] : '', (string) $order_id)) {
            log_message('error', 'toyyibPay callback refused: bill not confirmed as paid (order ' . $order_id . ')');
            set_status_header(403);
            exit('refused');
        }
        if($status==1){
        $para_amount=$this->gateway_ins_model->get_gateway_ins($order_id,'toyyibpay');
        $parameter_data=$this->gateway_ins_model->get($order_id,'toyyibpay');
        $bulk_fees=array();   
        if($para_amount['module_type']=='fees'){
            foreach ($parameter_data as $fee_key => $fee_value) {
                $amount_detail=json_decode($fee_value['amount_detail'],true);
                $amount_detail['description']="Online fees deposit through toyyibPay TXN ID:".$refno;
                $insert_fee_data = array(
                    'fee_category'=>$fee_value['fee_category'],
                    'student_transport_fee_id'=>$fee_value['student_transport_fee_id'],
                    'student_fees_master_id' => $fee_value['student_fees_master_id'],
                    'fee_groups_feetype_id'  => $fee_value['fee_groups_feetype_id'],
                    'amount_detail'          => $amount_detail,
                );                 
               $bulk_fees[]=$insert_fee_data;
                //========
            }  
            $insert_id=$this->gateway_ins_model->fee_deposit_bulk($bulk_fees);
            if($insert_id){
             $response="success"; 
             $this->gateway_ins_model->deleteBygateway_ins_id($para_amount['id']);  
            }else{
             $response="quiry_failed"; 
            }
         }

         if($para_amount['module_type']=='online_course'){
            $online_course=$this->gateway_ins_model->get_processing_payment($para_amount['id']);
            $online_course['transaction_id']=$pfData['pf_payment_id'];
            $online_course['note']="Online course fees processing Toyyibpay TXN ID:".$refno;
            unset($online_course['id']);
            unset($online_course['gateway_ins_id']);
            //$this->gateway_ins_model->add_course_payment($online_course,$para_amount['id']);
            $response="success";

            $this->gateway_ins_model->deleteprocessingpaymentByid($para_amount['id']);
        }

          if($para_amount['module_type']=='online_admission'){
                $this->gateway_ins_model->onlineAdmissionStatus($para_amount['online_admission_id'],1);
            }
        }

        $get_statusByUnique_id=$this->gateway_ins_model->get_statusByUnique_id($order_id,'toyyibpay');
        $gateway_ins_response=json_encode($_POST);
        $gateway_ins_add=array('gateway_ins_id'=>$get_statusByUnique_id['id'],'posted_data'=>$gateway_ins_response,'response
        '=>$status);

        $this->gateway_ins_model->add_gateway_ins_response($gateway_ins_add);

        $this->gateway_ins_model->update_gateway_ins(array('id'=>$get_statusByUnique_id['id'],'payment_status'=>$status));  
               


    }
}
