<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Adds `subscription.coupon_code` (the coupon used at sign-up; its discount is included in
 * discount_amt) and `subscription.coupon_counted` (the coupon's use was counted, once).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_subscription_coupon extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `subscription`
            ADD COLUMN `coupon_code` varchar(40) DEFAULT NULL AFTER `discount_amt`,
            ADD COLUMN `coupon_counted` tinyint(1) NOT NULL DEFAULT 0 AFTER `coupon_code`");
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `subscription` DROP COLUMN `coupon_code`, DROP COLUMN `coupon_counted`');
    }
}
