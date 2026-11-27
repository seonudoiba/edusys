<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * school.pay_before_portal: a school added by platform staff (Schools > Add school) is staff only
 * until a subscription is paid: students and parents can't sign in (Plan_limits, the same rule as a
 * school that paid only its setup fee). Schools > a school > "Let students and parents in without
 * payment" clears it. Existing schools keep 0: nothing changes for them.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Pay_before_portal extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('pay_before_portal', 'school')) {
            $this->db->query('ALTER TABLE `school` ADD COLUMN `pay_before_portal` TINYINT(1) NOT NULL DEFAULT 0'); // tenancy-lint: ok schema change (platform table)
        }
    }

    public function down()
    {
        if ($this->db->field_exists('pay_before_portal', 'school')) {
            $this->db->query('ALTER TABLE `school` DROP COLUMN `pay_before_portal`'); // tenancy-lint: ok schema change (platform table)
        }
    }
}
