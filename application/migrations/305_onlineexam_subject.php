<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * An online exam (CBT) can have a subject (onlineexam.subject_id, optional): shown in the exam
 * list, the question picker opens filtered to it, and Send scores to report card picks that
 * subject's Report Cards assessment first.
 */
class Migration_Onlineexam_subject extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('subject_id', 'onlineexam')) {
            $this->db->query('ALTER TABLE `onlineexam` ADD COLUMN `subject_id` INT NULL AFTER `exam`'); // tenancy-lint: ok schema change
        }
    }

    public function down()
    {
        if ($this->db->field_exists('subject_id', 'onlineexam')) {
            $this->db->query('ALTER TABLE `onlineexam` DROP COLUMN `subject_id`'); // tenancy-lint: ok schema change
        }
    }
}
