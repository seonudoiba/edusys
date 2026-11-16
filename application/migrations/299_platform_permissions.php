<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Platform admin:
 *  - Plan > Plan opens the pricing bands (libraries/Platform_admin_setup.php does the platform part);
 *  - finer permissions (approve schools, enable/disable, sign in as a school, modules, domains, notes,
 *    export/purge, subscriptions and discounts of a school, bank transfers, pricing, invoices, coupons,
 *    billing reports, email templates, support desk, enquiries). Roles that had the broad permission get
 *    the matching finer ones, so nobody loses access;
 *  - a built-in "Platform Manager" role: every platform permission except roles and permissions and
 *    purging a school's data;
 *  - platform_school_discounts: a discount the platform gives a school (libraries/School_discounts.php).
 * Schools: online_admissions gets a review status (approved / rejected, with a note) for
 * Online Admission > Review.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Platform_permissions extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('platform_school_discounts')) {
            $this->db->query("CREATE TABLE `platform_school_discounts` (
                `id` int NOT NULL AUTO_INCREMENT,
                `school_id` int NOT NULL,
                `type` varchar(10) NOT NULL DEFAULT 'percent',
                `value` decimal(12,2) NOT NULL DEFAULT 0,
                `applies` varchar(10) NOT NULL DEFAULT 'once',
                `note` varchar(255) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `used_subscription_id` int DEFAULT NULL,
                `created_by` int DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `used_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `school_id` (`school_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"); // tenancy-lint: ok platform table
        }
        if (!$this->db->field_exists('school_discount_id', 'subscription')) {
            $this->db->query("ALTER TABLE `subscription` ADD `school_discount_id` int DEFAULT NULL"); // tenancy-lint: ok platform table
        }
        if (!$this->db->field_exists('review_status', 'online_admissions')) {
            $this->db->query("ALTER TABLE `online_admissions` ADD `review_status` varchar(10) DEFAULT NULL, ADD `review_note` varchar(500) DEFAULT NULL,
                ADD `reviewed_by` int DEFAULT NULL, ADD `reviewed_at` datetime DEFAULT NULL"); // tenancy-lint: ok schema change
        }
        $this->db->without_tenant(function ($db) {
            $this->load->library('Platform_admin_setup');
            foreach ($db->select('id')->where('is_platform', 1)->get('school')->result_array() as $school) {
                $this->platform_admin_setup->install((int) $school['id']);
            }
        });
    }

    public function down()
    {
        $this->load->library('Platform_admin_setup');
        $this->db->without_tenant(function ($db) {
            foreach ($db->select('id')->where('is_platform', 1)->get('school')->result_array() as $school) {
                $sid = (int) $school['id'];
                $db->where(array('school_id' => $sid, 'name' => Platform_admin_setup::MANAGER))->delete('roles');
                $db->where('school_id', $sid)->where_in('short_code', array_keys(Platform_admin_setup::CATEGORIES))->delete('permission_category');
            }
        });
    }
}
