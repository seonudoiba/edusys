<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_exam_students`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: cbse_exams, student_session, school.
 */
class Migration_Create_cbse_exam_students extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_exam_students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `cbse_exam_id` int NOT NULL,
  `student_session_id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `roll_no` varchar(20) DEFAULT NULL,
  `remark` text,
  `total_present_days` int DEFAULT NULL,
  `delete_student_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cbse_exam_id` (`cbse_exam_id`),
  KEY `student_session_id` (`student_session_id`),
  KEY `idx_cbse_exam_students_school_id` (`school_id`),
  CONSTRAINT `cbse_exam_students_ibfk_1` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_exam_students_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cbse_exam_students_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_exam_students`');
    }
}
