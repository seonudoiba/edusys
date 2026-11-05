<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Adds `school.status_reason`: why a school is disabled ("expired" when the expiry policy
 * disabled it after the grace period; it is enabled again automatically when the school renews).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_school_status_reason extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `school` ADD COLUMN `status_reason` varchar(30) DEFAULT NULL AFTER `status`");
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `school` DROP COLUMN `status_reason`');
    }
}
