<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Platform jobs (CLI only, run once a day from cron, not per school):
 *
 *   php index.php platform_cron reminders    # subscription renewal reminders and expiry notices
 *
 * A school gets one reminder for each configured day before its subscription ends (Platform
 * settings, e.g. 30, 7 and 1 days) and one notice when it has ended. platform_reminder_log keeps
 * what was sent, so a reminder is never sent twice; if the job missed some days, only the
 * nearest reminder is sent.
 */
class Platform_cron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        $this->load->database();
        $this->load->library(array('Platform_settings', 'Platform_mailer', 'School_provisioner'));
    }

    public function reminders()
    {
        $settings = $this->platform_settings;
        $days = $settings->reminder_days();
        $remind = $settings->get('reminders_enabled') === '1' && $days;
        $expired_notice = $settings->get('expired_notice') === '1';
        $platform = $this->db->where('is_platform', 1)->get('school')->row_array();
        $account_url = $platform ? $this->school_provisioner->url_for($platform) . 'course/guestlogin' : '';

        $schools = $this->db->where(array('is_platform' => 0, 'cron_flag' => 1, 'status' => 1))->get('school')->result_array();
        $sent = 0;
        $disabled = 0;
        foreach ($schools as $school) {
            $sub = $this->db->select('subscription.*, plan.title AS plan_title')
                ->from('subscription')->join('plan', 'plan.plan_id = subscription.plan_id', 'left')
                ->where(array('subscription.school_id' => (int) $school['id'], 'subscription.payment_status' => 1))
                ->order_by('subscription.id', 'desc')->limit(1)->get()->row_array();
            if (!$sub) {
                continue;
            }
            $expires = strtotime($sub['created_at']) + (int) $sub['days'] * 86400;
            $days_left = (int) floor(($expires - time()) / 86400);
            $logged = array_column($this->db->select('reminder')->where('subscription_id', (int) $sub['id'])->get('platform_reminder_log')->result_array(), 'reminder');
            $vars = array(
                'owner_name' => $school['owner_name'],
                'school_name' => $school['school_name'],
                'plan' => $sub['plan_title'],
                'expires_on' => date('d M Y', $expires),
                'days_left' => max(0, $days_left),
                'renew_url' => $account_url,
            );
            $to = $school['email_address'] ?: $sub['email_address'];

            if ($days_left < 0) {
                if ($expired_notice && !in_array('expired', $logged, true)) {
                    $this->platform_mailer->send('subscription_expired', $to, $vars, $school['id']);
                    $this->db->insert('platform_reminder_log', array('subscription_id' => (int) $sub['id'], 'reminder' => 'expired'));
                    $sent++;
                }
                // Expiry policy "disable": after the grace period the school is disabled until it renews.
                $grace_ends = $expires + max(0, (int) $settings->get('grace_days')) * 86400;
                if ($settings->get('expiry_action') === 'disable' && time() > $grace_ends) {
                    $this->db->where('id', (int) $school['id'])->update('school', array('status' => 0, 'status_reason' => 'expired'));
                    $this->platform_mailer->send('school_disabled', $to, $vars, $school['id']);
                    $this->load->library('Platform_audit');
                    $this->platform_audit->record('school.expired', 'Disabled ' . $school['school_name'] . ': subscription ended ' . date('d M Y', $expires) . ', grace period over', $school['id']);
                    $disabled++;
                }
                continue;
            }
            if (!$remind) {
                continue;
            }
            // The nearest configured day that has been reached and not sent yet.
            $due = null;
            foreach ($days as $d) { // largest first
                if ($days_left <= $d) {
                    $due = $d;
                }
            }
            if ($due === null || in_array((string) $due, $logged, true)) {
                continue;
            }
            $this->platform_mailer->send('renewal_reminder', $to, $vars, $school['id']);
            foreach ($days as $d) {
                if ($d >= $due && !in_array((string) $d, $logged, true)) {
                    $this->db->insert('platform_reminder_log', array('subscription_id' => (int) $sub['id'], 'reminder' => (string) $d));
                }
            }
            $sent++;
        }
        $settings->set('cron_last_reminders', date('Y-m-d H:i:s'));
        echo "reminders: $sent sent for " . count($schools) . " schools, $disabled disabled after the grace period\n";
    }
}
