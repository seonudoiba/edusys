<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * School onboarding (admin/onboarding, libraries/School_onboarding.php): sch_settings.onboarding holds
 * JSON with the setup steps the school has finished or skipped, and whether the dashboard card is hidden.
 */
class Migration_School_onboarding extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('onboarding', 'sch_settings')) {
            $this->db->query('ALTER TABLE `sch_settings` ADD COLUMN `onboarding` TEXT NULL'); // tenancy-lint: ok schema change
        }
    }

    public function down()
    {
        if ($this->db->field_exists('onboarding', 'sch_settings')) {
            $this->db->query('ALTER TABLE `sch_settings` DROP COLUMN `onboarding`'); // tenancy-lint: ok schema change
        }
    }
}
