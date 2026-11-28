<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * onlineexam.calculator: a calculator on the CBT exam screen (user/onlineexam/take), switched on per
 * CBT by the staff who make it (the CBT form, or Settings on a CBT card). Off by default.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Cbt_calculator extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('calculator', 'onlineexam')) {
            $this->db->query('ALTER TABLE `onlineexam` ADD COLUMN `calculator` TINYINT(1) NOT NULL DEFAULT 0'); // tenancy-lint: ok schema change
        }
    }

    public function down()
    {
        if ($this->db->field_exists('calculator', 'onlineexam')) {
            $this->db->query('ALTER TABLE `onlineexam` DROP COLUMN `calculator`'); // tenancy-lint: ok schema change
        }
    }
}
