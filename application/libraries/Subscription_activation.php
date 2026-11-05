<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Activates a paid (or free) subscription: the one place that does it, for a verified Paystack
 * payment, an approved bank transfer and a free plan.
 *
 * For a new registration it creates the school's registry row (waiting for the platform admin
 * to set its subdomain and approve it) and the owner's platform guest account, then emails the
 * owner and the platform. For a renewal ($renewal = true) it only marks the subscription paid.
 *
 * Runs on the platform host (the guest account belongs to the platform school).
 */
class Subscription_activation
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model(array('subscription_model', 'setting_model', 'studentcourse_model'));
        $this->CI->load->library('Enc_lib');
    }

    /**
     * @param int $subscription_id
     * @param bool $renewal
     * @return bool false when the subscription doesn't exist or is already paid
     */
    public function activate($subscription_id, $renewal = false)
    {
        $db = $this->CI->db;
        $subscription = $db->where('id', (int) $subscription_id)->get('subscription')->row_array();
        if (!$subscription || (int) $subscription['payment_status'] === 1) {
            return false;
        }
        $db->where('id', (int) $subscription_id)->update('subscription', array('payment_status' => 1, 'status' => 1));

        $guest_password = '';
        $update_data = array();
        if (!$renewal) {
            $db->insert('school', array(
                'school_name' => $subscription['school_name'],
                'email_address' => $subscription['email_address'],
                'phone_number' => $subscription['phone_number'],
                'payment_status' => 1,
                'cron_flag' => 0,
            ));
            $school_id = $db->insert_id();
            $update_data = array('school_id' => $school_id, 'status' => 1);
            $guest_password = $this->create_guest($subscription, $school_id);
        }
        if ($update_data) {
            $db->where('id', (int) $subscription_id)->update('subscription', $update_data);
        }
        $this->send_emails($subscription, $guest_password);
        return true;
    }

    /** The owner's platform guest account (account status, days left, renewals). Returns its password. */
    private function create_guest(array $subscription, $school_id)
    {
        $db = $this->CI->db;
        $guest_password = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $encrypt_password = $this->CI->enc_lib->passHashEnc($guest_password);

        $sql = "SELECT * FROM online_course_settings WHERE " . $db->school_filter('online_course_settings') . " ORDER BY id";
        $course_setting = $db->query($sql)->row();

        $guest_unique_id = $course_setting->guest_prefix . '' . $course_setting->guest_id_start_from;
        $last_guest = $db->select('*')->order_by('id', 'desc')->limit(1)->get('guest')->row();
        if (!empty($last_guest)) {
            $last_guest_id = str_replace($course_setting->guest_prefix, '', $last_guest->guest_unique_id);
            $guest_unique_id = $course_setting->guest_prefix . ($last_guest_id + 1);
        }

        $setting_result = $this->CI->setting_model->get();
        $this->CI->studentcourse_model->addguest(array(
            'guest_unique_id' => $guest_unique_id,
            'guest_name' => $subscription['school_name'],
            'email' => $subscription['email_address'],
            'password' => $encrypt_password,
            'created_at' => date('Y-m-d'),
            'is_active' => 'yes',
            'lang_id' => $setting_result[0]['lang_id'],
            'currency_id' => '4',
            // The guest row belongs to the platform school (school_id is set by the tenant layer);
            // the school this owner registered is linked separately.
            'registered_school_id' => $school_id,
            'mobileno' => $subscription['phone_number'],
        ));
        return $guest_password;
    }

    /** The owner's welcome (with the account login) and the platform's notice, from the templates. */
    private function send_emails(array $subscription, $guest_password)
    {
        $CI = $this->CI;
        $CI->load->library('Platform_mailer');
        $plan = $CI->db->select('title')->where('plan_id', (int) $subscription['plan_id'])->get('plan')->row();
        $vars = array(
            'school_name' => $subscription['school_name'],
            'owner_name' => $subscription['owner_name'],
            'email' => $subscription['email_address'],
            'phone' => $subscription['phone_number'],
            'plan' => $plan ? $plan->title : '',
            'amount' => number_format((float) $subscription['total'], 2),
            'account_password' => $guest_password !== '' ? $guest_password : '(unchanged)',
            'account_url' => site_url('course/guestlogin'),
        );
        $school_id = !empty($subscription['school_id']) ? (int) $subscription['school_id'] : null;
        $fresh = $CI->db->select('school_id')->where('id', (int) $subscription['id'])->get('subscription')->row();
        if ($fresh && $fresh->school_id) {
            $school_id = (int) $fresh->school_id;
        }
        $CI->platform_mailer->send('registration_welcome', $subscription['email_address'], $vars, $school_id);
        $vars['review_url'] = $school_id ? site_url('admin/schools/view/' . $school_id) : site_url('admin/schools');
        $CI->platform_mailer->send('platform_new_registration', $CI->platform_settings->get('notify_email'), $vars, $school_id);
    }
}
