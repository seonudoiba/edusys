<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Checkout extends OnlineAdmission_Controller
{
    public $pay_method;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->setting = $this->setting_model->getSetting();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
    }

    /** Pay the admission form fee for the application posted from the review page. */
    public function index()
    {
        $id = (int) $this->input->post('admission_id');
        $application = $id ? $this->db->where('id', $id)->get('online_admissions')->row_array() : null;
        if (!$application) {
            show_404();
        }
        $back = 'welcome/online_admission_review/' . $application['reference_no'];
        if ((int) $application['paid_status'] === 1) {
            redirect($back);
        }
        $this->session->set_userdata('reference', $id);

        // Paystack, Flutterwave, Monnify, Interswitch, Remita: the shared drivers (school keys or subaccount).
        $this->load->library('Admission_payment');
        if ($this->admission_payment->gateway()) {
            try {
                $answer = $this->admission_payment->start($application, $this->setting->online_admission_amount, $this->setting->currency);
            } catch (Payment_gateway_error $e) {
                $this->session->set_flashdata('payment_error', $e->getMessage());
                redirect($back);
            }
            if (isset($answer['redirect'])) {
                redirect($answer['redirect']);
            }
            $this->load->view('user/gateway/online/redirect_post', array(
                'action' => $answer['post']['url'],
                'fields' => $answer['post']['fields'],
                'gateway_name' => Payment_gateways::name($this->admission_payment->gateway()),
            ));
            return;
        }

        // Older gateways keep their own pages.
        $older = array('payu', 'stripe', 'ccavenue', 'paypal', 'instamojo', 'paytm', 'razorpay', 'midtrans', 'ipayafrica', 'jazzcash',
            'pesapal', 'billplz', 'sslcommerz', 'walkingm', 'mollie', 'cashfree', 'payfast', 'toyyibpay', 'twocheckout', 'skrill', 'payhere', 'onepay');
        if (!empty($this->pay_method) && in_array($this->pay_method->payment_type, $older, true)) {
            redirect(base_url('onlineadmission/' . $this->pay_method->payment_type));
        }
        $this->session->set_flashdata('payment_error', 'Online payment is not set up for this school yet. Please contact the school to pay the form fee.');
        redirect($back);
    }

    public function successinvoice($reference_no){
        $data['setting'] = $this->setting;
        $data["reference_no"] = $reference_no;
        $this->load->view("onlineadmission/success_invoice",$data);
    }

    public function processinginvoice($reference_no=null){
       $data['setting'] = $this->setting;
       $data["reference_no"] = $reference_no;
       $this->load->view("onlineadmission/processing_invoice",$data);  
    }

    public function paymentfailed($reference_no=null){
        $data['setting'] = $this->setting;
        $data["reference_no"] = $reference_no;
        $this->load->view("onlineadmission/payment_failed",$data);
    }    

}