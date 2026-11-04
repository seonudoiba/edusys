<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_student_subject_result`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_cbse_student_subject_result extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_student_subject_result` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `cbse_exam_timetable_id` int DEFAULT NULL,
  `cbse_exam_student_id` int DEFAULT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cbse_student_subject_result_school_id` (`school_id`),
  CONSTRAINT `fk_cbse_student_subject_result_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_student_subject_result`');
    }
}
