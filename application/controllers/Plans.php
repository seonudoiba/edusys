<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class plans extends Front_Controller
{
    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->thumbnail_path = $this->config->item('course_thumbnail_path');
        $this->load->library('cart');
        $this->load->library('captchalib');
        $this->load->library('module_lib');
        $this->load->model(array('subscription_model', 'plan_model', 'setting_model', 'plan_type_model'));
    }

    public function index()
    {

        // $this->load->library('email');

        // // $config = array(
        // //     'protocol' => $this->config->item('protocol'),
        // //     'smtp_host' => $this->config->item('smtp_host'),
        // //     'smtp_user' => $this->config->item('smtp_user'),
        // //     'smtp_pass' => $this->config->item('smtp_pass'),
        // //     'smtp_port' => $this->config->item('smtp_port'),
        // //     'mailtype' => $this->config->item('mailtype'),
        // //     'charset' => $this->config->item('charset'),
        // //     'wordwrap' => $this->config->item('wordwrap'),
        // //     'newline' => $this->config->item('newline'),
        // // );
        // $config = array(
        //     'protocol' => 'smtp',
        //     'smtp_host' => 'smtp.mailgun.org',
        //     'smtp_user' => 'postmaster@mail.educatechild.in',
        //     'smtp_pass' => '',
        //     'smtp_port' => '587',
        //     'mailtype' => $this->config->item('mailtype'),
        //     'charset' => $this->config->item('charset'),
        //     'wordwrap' => $this->config->item('wordwrap'),
        //     'newline' => $this->config->item('newline'),
        // );
        // // Initialize the email library with the config settings
        // $this->email->initialize($config);

        // // Sender's email address

        // //user's mail
        // // $from_email = $this->config->item('from_mail');
        // $from_email = 'vraj92291@gmail.com';
        // $this->email->from($from_email, 'smartinnovation');

        // // // Recipient's email address
        // $to_email = 'sejalsabhaya36.hitinfotech@gmail.com';
        // // $to_email = $subscription_data['email_address'];
        // $this->email->to($to_email);

        // // // Subject of the email
        // $this->email->subject('School Register And School Create.');

        // // // Body of the email
        // // $message = '<p>This is a test email sent from CodeIgniter.</p>';
        // $message = '<DOCTYPE html>
        //     <html lang="en-US">

        //     <head>
        //         <meta charset="UTF-8">
        //         <meta name="viewport" content="width=device-width, initial-scale=1.0">
        //     </head>

        //     <body>
        //         <p>Dear sir/medam,</p>
        //         <p>Please wait while we prepare your resources.This may take a few moments.</p>
        //         <p>Below are the details associated with your account:</p>
        //         <ul>
        //             <li><b>School Name: </b>test </li>
        //             <li><b>Phone Number: </b>test</li>

        //             <p><b>Please note the following important details for login:</b></p>

        //             <li><b>Email: </b>test</li>
        //             <li><b>Password: </b>test</li>
        //         </ul>                            
        //         <p>Best regards,</p>
        //         <p>The Smartinnovation Team</p>
        //     </body>

        //     </html>';
        // $this->email->message($message);

        // // Send the email
        // if ($this->email->send()) {
        //     echo 'Email sent successfully!';
        //     die;
        // } else {
        //     echo 'Email sending failed. Please check your configuration.';
        //     die;
        // }

        
        // $this->load->library('mails');
        // $test = $this->mails->send(array(
        //     'to' => 'bhavhirani007@gmail.com',
        //     'subject' => 'School Register And School Create.',
        //     'html' => "hello". date('Y-M-d H:i:s a'),
        // ));
        // var_dump($test);
        // die;

        // $this->load->library('mails');
        // $test = $this->mails->send(array(
        //     'to' => 'vrajpatel38.hitinfotech@gmail.com',
        //     'from' => 'support@innovationcode.in',
        //     'sender' => 'hitinfotech',
        //     'subject' => 'School Register And School Create.',
        //     'text' => 'School Register And School Create.',
        //     'html' => "hello". date('Y-M-d H:i:s a'),
        // ));

    //     $this->load->library('mails'); 
    // $this->mails->to = 'vrajpatel38.hitinfotech@gmail.com'; // Set recipient
    // $this->mails->from = 'sejalsabhaya36.hitinfotech@gmail.com'; // Set the sender
    // $this->mails->sender = 'your-name'; // Set the sender's name or email
    // $this->mails->subject = 'School Register And School Create.'; // Set the subject
    // $this->mails->html = "hello " . date('Y-M-d H:i:s a'); // HTML message content

        // var_dump($test);
        // die;

        $this->data['active_menu'] = 'Plans';
        $page = array('title' => 'Plans', 'meta_title' => '', 'meta_keyword' => '', 'meta_description' => '');
        $this->data['page_side_bar'] = false;
        $this->data['featured_image'] = false;
        $this->data['page'] = $page;

        $this->data['setting_data'] = $this->setting_model->get();

        $this->data['plan_price'] = array();

        $this->data['plans'] = $this->plan_model->get('', true);

        if(!empty($this->data['plans'])){
            foreach($this->data['plans'] as $key => $plans){
                $list = $plans['list'];
                unset($this->data['plans'][$key]['list']);  
                $this->data['plans'][$key]['list'] = array();
                $this->data['plans'][$key]['list'] = $this->plan_type_model->get_plan_type($list);  // Assign the value to 'listed'
            }
        }
        // $this->data['plan_list'] = $this->plan_type_model->get_plan_type();
        $this->load_theme('plan/plans');
    }

}