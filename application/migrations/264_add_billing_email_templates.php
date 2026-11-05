<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Adds the billing emails to `platform_email_templates`: the payment receipt (with the invoice
 * link) and the notice that a school was disabled after its grace period.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_billing_email_templates extends CI_Migration
{
    public function up()
    {
        foreach (self::defaults() as $key => $t) {
            if ($this->db->where('template_key', $key)->count_all_results('platform_email_templates') > 0) {
                continue;
            }
            $this->db->insert('platform_email_templates', array('template_key' => $key, 'name' => $t[0], 'recipient' => $t[1],
                'subject' => $t[2], 'body' => $t[3], 'placeholders' => $t[4], 'is_active' => 1));
        }
    }

    public function down()
    {
        $this->db->where_in('template_key', array_keys(self::defaults()))->delete('platform_email_templates');
    }

    public static function defaults()
    {
        $common = '{platform_name}, {support_email}, {support_phone}';
        return array(
            'payment_receipt' => array(
                'Payment receipt and invoice (to the owner)', 'owner',
                'Receipt {invoice_no}: {school_name}',
                "<p>Dear {owner_name},</p>\n<p>Thank you for your payment of <b>{amount}</b> for <b>{school_name}</b> ({plan}).</p>\n"
                . "<p>Invoice {invoice_no}: <a href=\"{invoice_url}\">download it here</a>.</p>\n<p>The {platform_name} team</p>",
                '{owner_name}, {school_name}, {plan}, {amount}, {invoice_no}, {invoice_url}, ' . $common),
            'school_disabled' => array(
                'School disabled after expiry (to the owner)', 'owner',
                '{school_name} has been disabled',
                "<p>Dear {owner_name},</p>\n<p>The subscription of <b>{school_name}</b> ended on {expires_on} and was not renewed, so the school has been disabled. Its data is kept.</p>\n"
                . "<p>Renew to enable it again straight away: <a href=\"{renew_url}\">{renew_url}</a></p>\n<p>Questions? Write to {support_email}.</p>\n<p>The {platform_name} team</p>",
                '{owner_name}, {school_name}, {expires_on}, {renew_url}, ' . $common),
        );
    }
}
