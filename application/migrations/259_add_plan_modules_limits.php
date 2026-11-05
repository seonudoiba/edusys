<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Adds what a plan includes to `plan`:
 *   modules         JSON list of module codes (permission_group.short_code) the plan includes;
 *                   NULL = every module
 *   max_students    active students allowed (0 = no limit)
 *   max_staff       active staff allowed (0 = no limit)
 *   max_storage_mb  upload storage allowed in MB (0 = no limit)
 * Enforced by Plan_limits (hook Plan_policy_hook, Modules page, school setup and payments).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_plan_modules_limits extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `plan`
            ADD COLUMN `modules` text NULL COMMENT 'JSON module codes; NULL = all' AFTER `list`,
            ADD COLUMN `max_students` int NOT NULL DEFAULT 0 AFTER `modules`,
            ADD COLUMN `max_staff` int NOT NULL DEFAULT 0 AFTER `max_students`,
            ADD COLUMN `max_storage_mb` int NOT NULL DEFAULT 0 AFTER `max_staff`");
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `plan` DROP COLUMN `modules`, DROP COLUMN `max_students`, DROP COLUMN `max_staff`, DROP COLUMN `max_storage_mb`');
    }
}
