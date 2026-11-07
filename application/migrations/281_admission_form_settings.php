<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Online admission settings (libraries/Admission_form.php): opening and closing dates, and the
 * form's field settings, questions title, closed message and classes as JSON.
 */
class Migration_Admission_form_settings extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('online_admission_opens', 'sch_settings')) {
            $this->db->query('ALTER TABLE `sch_settings` ADD COLUMN `online_admission_opens` DATE NULL AFTER `online_admission_conditions`, ADD COLUMN `online_admission_closes` DATE NULL AFTER `online_admission_opens`, ADD COLUMN `online_admission_form` MEDIUMTEXT NULL AFTER `online_admission_closes`'); // tenancy-lint: ok schema change
        }
    }

    public function down()
    {
        if ($this->db->field_exists('online_admission_opens', 'sch_settings')) {
            $this->db->query('ALTER TABLE `sch_settings` DROP COLUMN `online_admission_opens`, DROP COLUMN `online_admission_closes`, DROP COLUMN `online_admission_form`'); // tenancy-lint: ok schema change
        }
    }
}
