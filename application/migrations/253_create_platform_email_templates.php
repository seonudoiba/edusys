<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `platform_email_templates`: the emails the platform sends to school owners and to its
 * own team, editable by the platform admin (Platform_mailer renders them). Placeholders are
 * written {like_this}; values are HTML-escaped when the email is built.
 *
 * Platform table: one shared copy, no school_id.
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_platform_email_templates extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `platform_email_templates` (
  `template_key` varchar(60) NOT NULL,
  `name` varchar(150) NOT NULL,
  `recipient` varchar(20) NOT NULL COMMENT 'owner or platform',
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `placeholders` varchar(500) NOT NULL DEFAULT '',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`template_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
        foreach (self::defaults() as $key => $t) {
            $this->db->insert('platform_email_templates', array(
                'template_key' => $key,
                'name' => $t[0],
                'recipient' => $t[1],
                'subject' => $t[2],
                'body' => $t[3],
                'placeholders' => $t[4],
                'is_active' => 1,
            ));
        }
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_email_templates`');
    }

    /** key => [name, recipient, subject, body, placeholders] */
    public static function defaults()
    {
        $common = '{platform_name}, {support_email}, {support_phone}';
        return array(
            'registration_welcome' => array(
                'Registration received (to the owner)', 'owner',
                'We received your registration: {school_name}',
                "<p>Dear {owner_name},</p>\n<p>Thank you for registering <b>{school_name}</b> on {platform_name} ({plan}).</p>\n"
                . "<p>We are preparing your school's site. When it is ready you will receive a second email with its address and a link to set your admin password.</p>\n"
                . "<p>Meanwhile you can follow your subscription (status, days left, renewals) with this account:</p>\n"
                . "<p>Login: <b>{email}</b><br>Password: <b>{account_password}</b><br><a href=\"{account_url}\">{account_url}</a></p>\n"
                . "<p>Questions? Write to {support_email}.</p>\n<p>The {platform_name} team</p>",
                '{owner_name}, {school_name}, {plan}, {email}, {account_password}, {account_url}, ' . $common),
            'platform_new_registration' => array(
                'New registration (to the platform)', 'platform',
                'New school registration: {school_name}',
                "<p>A school registered and paid (or chose a free plan).</p>\n<ul><li>School: {school_name}</li><li>Owner: {owner_name}</li><li>Email: {email}</li><li>Phone: {phone}</li><li>Plan: {plan}</li><li>Amount: {amount}</li></ul>\n"
                . "<p>Set its subdomain and approve it: <a href=\"{review_url}\">{review_url}</a></p>",
                '{school_name}, {owner_name}, {email}, {phone}, {plan}, {amount}, {review_url}, ' . $common),
            'receipt_submitted' => array(
                'Bank transfer receipt to review (to the platform)', 'platform',
                'Bank transfer receipt to review: {school_name}',
                "<p>{school_name} uploaded a bank transfer receipt ({amount}).</p>\n<p>Review it: <a href=\"{review_url}\">{review_url}</a></p>",
                '{school_name}, {amount}, {review_url}, ' . $common),
            'receipt_approved' => array(
                'Bank transfer approved (to the owner)', 'owner',
                'Payment received: {school_name}',
                "<p>Dear {owner_name},</p>\n<p>We have received your bank transfer of {amount} for <b>{school_name}</b> ({plan}). Thank you.</p>\n"
                . "<p>Your school is being set up; you will receive its address and your admin login shortly.</p>\n<p>The {platform_name} team</p>",
                '{owner_name}, {school_name}, {plan}, {amount}, ' . $common),
            'receipt_rejected' => array(
                'Bank transfer not accepted (to the owner)', 'owner',
                'Your payment receipt needs attention: {school_name}',
                "<p>Dear {owner_name},</p>\n<p>We could not accept the bank transfer receipt you uploaded for <b>{school_name}</b>.</p>\n"
                . "<p>Reason: {reason}</p>\n<p>Please upload a new receipt here: <a href=\"{payment_page_url}\">{payment_page_url}</a></p>\n"
                . "<p>Questions? Write to {support_email}.</p>\n<p>The {platform_name} team</p>",
                '{owner_name}, {school_name}, {reason}, {payment_page_url}, ' . $common),
            'school_ready' => array(
                'School ready, set your password (to the owner)', 'owner',
                'Your school is ready: {school_name}',
                "<p>Hello,</p>\n<p><b>{school_name}</b> is set up at <a href=\"{school_url}\">{school_url}</a>.</p>\n"
                . "<p>Your super admin login is <b>{email}</b>. Choose your password with this one-time link:</p>\n<p><a href=\"{set_password_url}\">{set_password_url}</a></p>\n"
                . "<p>Then log in at <a href=\"{login_url}\">{login_url}</a>.</p>\n<p>The {platform_name} team</p>",
                '{school_name}, {school_url}, {email}, {set_password_url}, {login_url}, ' . $common),
            'password_link' => array(
                'New set-password link (to a school admin)', 'owner',
                'Set your password: {school_name}',
                "<p>Hello,</p>\n<p>A new password link was created for your account at <b>{school_name}</b> (<a href=\"{school_url}\">{school_url}</a>).</p>\n"
                . "<p>Your login is <b>{email}</b>. Choose your password with this one-time link:</p>\n<p><a href=\"{set_password_url}\">{set_password_url}</a></p>\n"
                . "<p>Then log in at <a href=\"{login_url}\">{login_url}</a>.</p>\n<p>The {platform_name} team</p>",
                '{school_name}, {school_url}, {email}, {set_password_url}, {login_url}, ' . $common),
            'renewal_reminder' => array(
                'Subscription renewal reminder (to the owner)', 'owner',
                '{school_name}: your subscription ends in {days_left} days',
                "<p>Dear {owner_name},</p>\n<p>The {plan} subscription of <b>{school_name}</b> ends on <b>{expires_on}</b> ({days_left} days from now).</p>\n"
                . "<p>Renew it to keep your school running: <a href=\"{renew_url}\">{renew_url}</a></p>\n<p>Questions? Write to {support_email}.</p>\n<p>The {platform_name} team</p>",
                '{owner_name}, {school_name}, {plan}, {expires_on}, {days_left}, {renew_url}, ' . $common),
            'subscription_expired' => array(
                'Subscription expired (to the owner)', 'owner',
                '{school_name}: your subscription has ended',
                "<p>Dear {owner_name},</p>\n<p>The {plan} subscription of <b>{school_name}</b> ended on <b>{expires_on}</b>.</p>\n"
                . "<p>Renew it here: <a href=\"{renew_url}\">{renew_url}</a></p>\n<p>Questions? Write to {support_email}.</p>\n<p>The {platform_name} team</p>",
                '{owner_name}, {school_name}, {plan}, {expires_on}, {renew_url}, ' . $common),
        );
    }
}
