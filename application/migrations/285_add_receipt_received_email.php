<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Adds `receipt_received` to `platform_email_templates`: the school owner's confirmation that their
 * bank transfer receipt arrived and is being reviewed (before, only the platform was told).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_receipt_received_email extends CI_Migration
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
            'receipt_received' => array(
                'Bank transfer receipt received (to the owner)', 'owner',
                'Receipt received: {school_name}',
                "<p>Dear {owner_name},</p>\n<p>We have received your bank transfer receipt of {amount} for <b>{school_name}</b> ({plan}).</p>\n"
                . "<p>We will check it and email you once it is confirmed, usually within one working day. You can see its status here: "
                . "<a href=\"{status_url}\">{status_url}</a></p>\n<p>The {platform_name} team</p>",
                '{owner_name}, {school_name}, {plan}, {amount}, {status_url}, ' . $common),
        );
    }
}
