<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Pricing by student bands, replacing the fixed-price plans (see Plan_pricing):
 *   platform_plan_bands   per-student rate per term and one-time setup fee, by school size
 *   platform_quotes       custom prices agreed with large schools (a private link to register with)
 *   subscription          student_count (what was paid for: the school's limit), billing_period
 *                         (term or year), rate_per_term, terms, setup_fee, kind (new, renewal, topup)
 *   plan.is_band          the one plan new subscriptions use; the old plans and the trial are retired
 *                         (kept for the records of past subscriptions)
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_student_band_pricing extends CI_Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `platform_plan_bands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `min_students` int NOT NULL,
  `max_students` int DEFAULT NULL COMMENT 'NULL = no upper limit',
  `rate_per_term` decimal(10,2) DEFAULT NULL COMMENT 'naira per student per term; NULL = custom quote',
  `setup_fee` decimal(12,2) NOT NULL DEFAULT 0 COMMENT 'one-time implementation and training (a starting price for custom quotes)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        if ($this->db->count_all('platform_plan_bands') === 0) {
            foreach (array(array(1, 300, 3500, 250000), array(301, 700, 3000, 350000), array(701, 1500, 2500, 450000),
                array(1501, 3000, 2000, 600000), array(3001, null, null, 750000)) as $b) {
                $this->db->insert('platform_plan_bands', array('min_students' => $b[0], 'max_students' => $b[1], 'rate_per_term' => $b[2], 'setup_fee' => $b[3]));
            }
        }
        $this->db->query("CREATE TABLE IF NOT EXISTS `platform_quotes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token` char(40) NOT NULL,
  `enquiry_id` int DEFAULT NULL,
  `school_name` varchar(255) NOT NULL,
  `contact_name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL DEFAULT '',
  `students` int NOT NULL,
  `rate_per_term` decimal(10,2) NOT NULL,
  `setup_fee` decimal(12,2) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'open' COMMENT 'open, used',
  `expires_at` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_platform_quotes_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        if (!$this->db->field_exists('student_count', 'subscription')) {
            // price, discount and total were float/int: too small and imprecise for these amounts.
            $this->db->query("ALTER TABLE `subscription`
                MODIFY `price` decimal(14,2) NOT NULL, MODIFY `discount_amt` decimal(14,2) NOT NULL, MODIFY `total` decimal(14,2) NOT NULL,
                ADD COLUMN `kind` varchar(10) NOT NULL DEFAULT 'new' AFTER `plan_id`,
                ADD COLUMN `student_count` int DEFAULT NULL COMMENT 'students paid for: the school limit',
                ADD COLUMN `billing_period` varchar(5) DEFAULT NULL COMMENT 'term or year',
                ADD COLUMN `rate_per_term` decimal(10,2) DEFAULT NULL,
                ADD COLUMN `terms` int DEFAULT NULL,
                ADD COLUMN `setup_fee` decimal(12,2) NOT NULL DEFAULT 0,
                ADD COLUMN `quote_id` int DEFAULT NULL");
        }
        if (!$this->db->field_exists('is_band', 'plan')) {
            $this->db->query('ALTER TABLE `plan` ADD COLUMN `is_band` tinyint(1) NOT NULL DEFAULT 0');
        }
        if ($this->db->field_exists('students', 'platform_enquiries') === false) {
            $this->db->query('ALTER TABLE `platform_enquiries` ADD COLUMN `students` int DEFAULT NULL AFTER `phone`, ADD COLUMN `school_name` varchar(255) DEFAULT NULL AFTER `students`');
        }
        // The old plans and the trial stop being sold; one plan carries every band.
        $this->db->update('plan', array('status' => 0));
        if ($this->db->where('is_band', 1)->count_all_results('plan') === 0) {
            $this->db->insert('plan', array('title' => 'EduSYS Standard Subscription', 'text' => 'Priced per student, per term', 'list' => '[]',
                'status' => 1, 'price' => 0, 'oprice' => 0, 'days' => 365, 'is_band' => 1, 'created_at' => date('Y-m-d H:i:s')));
        }

        $this->db->without_tenant(function ($db) {
            foreach ($db->select('id')->where('is_platform', 1)->get('school')->result() as $school) {
                $sid = (int) $school->id;
                // The platform's homepage is now the 'platform' theme's own (publishing a layout overrides it).
                $db->where(array('school_id' => $sid, 'is_homepage' => 1))->update('front_cms_pages', array('builder_layout' => null, 'builder_draft' => null));
                $db->where('school_id', $sid)->update('front_cms_settings', array('theme' => 'platform'));
                $menu = $db->where(array('school_id' => $sid, 'lang_key' => 'subscription'))->get('sidebar_menus')->row();
                if ($menu && $db->where(array('school_id' => $sid, 'url' => 'admin/billing/bands'))->count_all_results('sidebar_sub_menus') === 0) {
                    $db->insert('sidebar_sub_menus', array('school_id' => $sid, 'sidebar_menu_id' => $menu->id, 'menu' => 'billing_bands', 'key' => 'billing_bands',
                        'lang_key' => 'pricing_bands', 'url' => 'admin/billing/bands', 'level' => 2, 'access_permissions' => "('subscription_list', 'can_view')",
                        'permission_group_id' => $menu->permission_group_id, 'activate_controller' => 'billing', 'activate_methods' => 'bands', 'addon_permission' => '', 'is_active' => 1));
                }
            }
        });

        foreach (self::templates() as $key => $t) {
            if ($this->db->where('template_key', $key)->count_all_results('platform_email_templates') === 0) {
                $this->db->insert('platform_email_templates', array('template_key' => $key, 'name' => $t[0], 'recipient' => $t[1],
                    'subject' => $t[2], 'body' => $t[3], 'placeholders' => $t[4], 'is_active' => 1));
            }
        }
    }

    public function down()
    {
        $this->db->where_in('template_key', array_keys(self::templates()))->delete('platform_email_templates');
        $this->db->without_tenant(function ($db) {
            $db->where('url', 'admin/billing/bands')->delete('sidebar_sub_menus');
            $db->where('theme', 'platform')->update('front_cms_settings', array('theme' => 'material_pink'));
        });
        $this->db->where('is_band', 1)->delete('plan');
        $this->db->query('ALTER TABLE `plan` DROP COLUMN `is_band`');
        $this->db->update('plan', array('status' => 1));
        $this->db->query('ALTER TABLE `platform_enquiries` DROP COLUMN `students`, DROP COLUMN `school_name`');
        $this->db->query('ALTER TABLE `subscription` DROP COLUMN `kind`, DROP COLUMN `student_count`, DROP COLUMN `billing_period`, DROP COLUMN `rate_per_term`, DROP COLUMN `terms`, DROP COLUMN `setup_fee`, DROP COLUMN `quote_id`');
        $this->db->query('DROP TABLE IF EXISTS `platform_quotes`');
        $this->db->query('DROP TABLE IF EXISTS `platform_plan_bands`');
    }

    public static function templates()
    {
        $common = '{platform_name}, {support_email}, {support_phone}';
        return array(
            'quote_request' => array('Quote request from a large school (to the platform)', 'platform',
                'Quote request: {school_name} ({students} students)',
                "<p><b>{school_name}</b> asked for a quote for {students} students.</p>\n<p>{name}<br>{email}<br>{phone}</p>\n<blockquote>{message}</blockquote>\n<p>Reply and set the price: <a href=\"{enquiry_url}\">{enquiry_url}</a></p>",
                '{school_name}, {students}, {name}, {email}, {phone}, {message}, {enquiry_url}, ' . $common),
            'quote_ready' => array('Your quote is ready (to the school)', 'owner',
                'Your {platform_name} quote: {school_name}',
                "<p>Hello {name},</p>\n<p>Here is your quote for {school_name}:</p>\n<ul><li>{students} students</li><li>{rate} per student, per term</li><li>{setup} one-time implementation and training</li></ul>\n<p>It is valid until {expires}. To accept it and register your school: <a href=\"{quote_url}\">{quote_url}</a></p>\n<p>The {platform_name} team</p>",
                '{name}, {school_name}, {students}, {rate}, {setup}, {expires}, {quote_url}, ' . $common),
        );
    }
}
