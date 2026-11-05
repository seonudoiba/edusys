<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Adds the support emails to `platform_email_templates`: a new ticket or a school's reply (to the
 * platform), the platform's reply (to the school), and the reply to a website enquiry.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_support_email_templates extends CI_Migration
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
            'support_new_message' => array(
                'Support ticket: new message from a school (to the platform)', 'platform',
                '[Ticket #{ticket_id}] {subject} ({school_name})',
                "<p><b>{school_name}</b> ({author_name}) wrote:</p>\n<blockquote>{message}</blockquote>\n<p>Reply: <a href=\"{ticket_url}\">{ticket_url}</a></p>",
                '{ticket_id}, {subject}, {school_name}, {author_name}, {message}, {ticket_url}, ' . $common),
            'support_reply' => array(
                'Support ticket: reply from the platform (to the school)', 'owner',
                '[Ticket #{ticket_id}] {subject}',
                "<p>Hello {name},</p>\n<p>The {platform_name} team replied to your support request:</p>\n<blockquote>{message}</blockquote>\n"
                . "<p>See the conversation and reply: <a href=\"{ticket_url}\">{ticket_url}</a></p>\n<p>The {platform_name} team</p>",
                '{name}, {ticket_id}, {subject}, {message}, {ticket_url}, ' . $common),
            'enquiry_reply' => array(
                'Reply to a website enquiry', 'owner',
                'Re: {subject}',
                "<p>Hello {name},</p>\n<p>{message}</p>\n<hr>\n<p style=\"color:#888\">You wrote: {original}</p>\n<p>The {platform_name} team</p>",
                '{name}, {subject}, {message}, {original}, ' . $common),
        );
    }
}
