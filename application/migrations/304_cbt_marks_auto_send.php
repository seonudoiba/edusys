<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Online exam scores sent to the report card automatically (Cbt_scoring::auto_send): the exam
 * keeps where its scores go (marks_target, already there) and how.
 *
 *   onlineexam.marks_auto     1 = each student's score goes in as soon as it is final (submitted,
 *                             written answers marked)
 *   onlineexam.marks_scale    1 = as a share of the assessment's maximum, 0 = the score as it is
 *   onlineexam.marks_replace  1 = overwrite a mark already there
 */
class Migration_Cbt_marks_auto_send extends CI_Migration
{
    public function up()
    {
        $add = function ($column, $definition) {
            if (!$this->db->field_exists($column, 'onlineexam')) {
                $this->db->query("ALTER TABLE `onlineexam` ADD COLUMN `$column` $definition"); // tenancy-lint: ok schema change
            }
        };
        $add('marks_auto', 'TINYINT(1) NOT NULL DEFAULT 0');
        $add('marks_scale', 'TINYINT(1) NOT NULL DEFAULT 1');
        $add('marks_replace', 'TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down()
    {
        foreach (array('marks_auto', 'marks_scale', 'marks_replace') as $c) {
            if ($this->db->field_exists($c, 'onlineexam')) {
                $this->db->query("ALTER TABLE `onlineexam` DROP COLUMN `$c`"); // tenancy-lint: ok schema change
            }
        }
    }
}
