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
        // Claim it: if two confirmations arrive at once (return page + reload), only one activates.
        $db->where('id', (int) $subscription_id)->where('payment_status', 0)->update('subscription', array('payment_status' => 1, 'status' => 1));
        if ($db->affected_rows() !== 1) {
            return false;
        }

        // The period starts when it is paid (an invoice or a bank transfer can be paid days after it
        // was issued); a top-up keeps the end date it was priced for. renewed() moves the start
        // to the end of a period still running.
        if (!isset($subscription['kind']) || $subscription['kind'] !== 'topup') {
            $now = date('Y-m-d H:i:s');
            $db->where('id', (int) $subscription_id)->update('subscription', array(
                'created_at' => $now, 'end_date' => date('Y-m-d H:i:s', time() + (int) $subscription['days'] * 86400)));
            $subscription['created_at'] = $now;
        }
        // A subscription billed to a school already registered (the platform's invoice): no new
        // school, the school's period is extended as for a renewal, and the receipt is the email.
        $billed = !$renewal && !empty($subscription['school_id']);
        if ($billed) {
            $renewal = true;
        }
        $guest_password = '';
        $update_data = array();
        if (!$renewal) {
            // The address chosen at registration, if it is still free (the platform can change it before approving).
            $subdomain = isset($subscription['subdomain']) ? (string) $subscription['subdomain'] : '';
            if ($subdomain !== '') {
                $this->CI->load->library('School_provisioner');
                if ($this->CI->school_provisioner->subdomain_status($subdomain, null, (int) $subscription['id']) !== 'ok') {
                    $subdomain = '';
                }
            }
            $db->insert('school', array(
                'school_name' => $subscription['school_name'],
                'subdomain' => $subdomain !== '' ? $subdomain : null,
                'email_address' => $subscription['email_address'],
                'phone_number' => $subscription['phone_number'],
                'payment_status' => 1,
                'cron_flag' => 0,
            ));
            $school_id = $db->insert_id();
            $update_data = array('school_id' => $school_id, 'status' => 1);
            $guest_password = $this->create_guest($subscription, $school_id);
        }
        if ($renewal && !empty($subscription['school_id'])) {
            $this->renewed((int) $subscription['school_id'], $subscription);
        }
        if ($update_data) {
            $db->where('id', (int) $subscription_id)->update('subscription', $update_data);
        }
        // A coupon's use counts once, when the subscription is paid (abandoned sign-ups don't use it up).
        if (!empty($subscription['coupon_code']) && !(int) $subscription['coupon_counted']) {
            $db->where('code', $subscription['coupon_code'])->set('used_count', 'used_count + 1', false)->update('platform_coupons');
            $db->where('id', (int) $subscription_id)->update('subscription', array('coupon_counted' => 1));
        }
        if (!empty($subscription['school_discount_id'])) {
            $this->CI->load->library('School_discounts');
            $this->CI->school_discounts->used((int) $subscription['school_discount_id'], (int) $subscription_id);
        }
        if (!empty($subscription['quote_id'])) {
            $db->where('id', (int) $subscription['quote_id'])->update('platform_quotes', array('status' => 'used'));
        }
        if ($billed) {
            $db->where('id', (int) $subscription['school_id'])->update('school', array('payment_status' => 1));
        } else {
            $this->send_emails($subscription, $guest_password);
        }
        $this->CI->load->library('Platform_invoices');
        $this->CI->platform_invoices->issue((int) $subscription_id);
        return true;
    }

    /**
     * A renewal of a school already set up: the new period starts when the current one ends (an
     * early renewal doesn't lose the days left); the plan's modules are applied (newly included
     * ones switched on when the plan changed); a school disabled for expiry is enabled again.
     */
    private function renewed($school_id, array $subscription)
    {
        $db = $this->CI->db;
        if (isset($subscription['kind']) && $subscription['kind'] === 'topup') {
            return; // more students in the same period: the end date doesn't move
        }
        $previous = $db->where(array('school_id' => $school_id, 'payment_status' => 1, 'id <>' => (int) $subscription['id']))
            ->order_by('id', 'desc')->limit(1)->get('subscription')->row_array();
        if ($previous) {
            $ends = strtotime($previous['created_at']) + (int) $previous['days'] * 86400;
            if ($ends > time()) {
                $db->where('id', (int) $subscription['id'])->update('subscription', array('created_at' => date('Y-m-d H:i:s', $ends)));
            }
        }
        $this->CI->load->library('Plan_limits');
        $plan_changed = !$previous || (int) $previous['plan_id'] !== (int) $subscription['plan_id'];
        $this->CI->plan_limits->apply_modules($school_id, $plan_changed);
        $school = $db->where('id', $school_id)->get('school')->row_array();
        if ($school && (int) $school['status'] !== 1 && $school['status_reason'] === 'expired') {
            $db->where('id', $school_id)->update('school', array('status' => 1, 'status_reason' => null));
        }
    }

    /** The owner's platform guest account (account status, days left, renewals). Returns its password. */
    private function create_guest(array $subscription, $school_id)
    {
        $db = $this->CI->db;
        $guest_password = bin2hex(random_bytes(5));
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
            'must_change_password' => 1,
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
            'plan' => isset($subscription['kind']) && $subscription['kind'] === 'setup'
                ? 'Setup fee only: pay the subscription from your account when you are ready for students and parents to sign in'
                : ($plan ? $plan->title : ''),
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
