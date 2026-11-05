<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Adds `subscription.subdomain`: the address a school chooses when it registers
 * (<subdomain>.<base domain>). It is copied to `school.subdomain` when the subscription is paid;
 * until then an unpaid registration holds the name for School_provisioner::HOLD_DAYS days.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_subscription_subdomain extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('subdomain', 'subscription')) {
            $this->db->query("ALTER TABLE `subscription`
                ADD COLUMN `subdomain` varchar(63) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL
                    COMMENT 'address chosen at registration, copied to school.subdomain when paid' AFTER `school_name`,
                ADD KEY `idx_subscription_subdomain` (`subdomain`)");
        }
    }

    public function down()
    {
        if ($this->db->field_exists('subdomain', 'subscription')) {
            $this->db->query('ALTER TABLE `subscription` DROP KEY `idx_subscription_subdomain`, DROP COLUMN `subdomain`');
        }
    }
}
