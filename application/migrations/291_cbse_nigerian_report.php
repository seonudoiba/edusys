<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Nigerian report cards on the CBSE Examination module (libraries/Cbse_nigeria.php):
 *
 *   cbse_exam_students.principal_remark   the principal's remark, entered with the class teacher's
 *   cbse_template.next_term_begins        printed on the report ("Next term begins")
 *   onlineexam.marks_target               now text: "<id>" (Examinations subject) or
 *                                         "cbse:<timetable id>:<assessment type id>" (CBSE assessment)
 */
class Migration_Cbse_nigerian_report extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('principal_remark', 'cbse_exam_students')) {
            $this->db->query('ALTER TABLE `cbse_exam_students` ADD COLUMN `principal_remark` TEXT NULL AFTER `remark`'); // tenancy-lint: ok schema change
        }
        if (!$this->db->field_exists('next_term_begins', 'cbse_template')) {
            $this->db->query('ALTER TABLE `cbse_template` ADD COLUMN `next_term_begins` DATE NULL'); // tenancy-lint: ok schema change
        }
        $this->db->query("ALTER TABLE `onlineexam` MODIFY `marks_target` VARCHAR(40) NULL COMMENT 'where the scores were last sent: <exam subject id> or cbse:<timetable id>:<assessment type id>'"); // tenancy-lint: ok schema change
    }

    public function down()
    {
        if ($this->db->field_exists('principal_remark', 'cbse_exam_students')) {
            $this->db->query('ALTER TABLE `cbse_exam_students` DROP COLUMN `principal_remark`'); // tenancy-lint: ok schema change
        }
        if ($this->db->field_exists('next_term_begins', 'cbse_template')) {
            $this->db->query('ALTER TABLE `cbse_template` DROP COLUMN `next_term_begins`'); // tenancy-lint: ok schema change
        }
        $this->db->query("UPDATE `onlineexam` SET `marks_target` = NULL WHERE `marks_target` LIKE 'cbse:%'"); // tenancy-lint: ok whole table, all schools
        $this->db->query('ALTER TABLE `onlineexam` MODIFY `marks_target` INT NULL'); // tenancy-lint: ok schema change
    }
}
