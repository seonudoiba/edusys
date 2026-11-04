<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `student_quiz_status`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: students, online_course_quiz, school.
 */
class Migration_Create_student_quiz_status extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `student_quiz_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `guest_id` int DEFAULT NULL,
  `course_quiz_id` int DEFAULT NULL,
  `total_question` int DEFAULT NULL,
  `correct_answer` int DEFAULT NULL,
  `wrong_answer` int DEFAULT NULL,
  `not_answer` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '1-completed,0-incomplete	',
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `course_quiz_id` (`course_quiz_id`) USING BTREE,
  KEY `idx_student_quiz_status_school_id` (`school_id`),
  CONSTRAINT `student_quiz_status_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_quiz_status_ibfk_2` FOREIGN KEY (`course_quiz_id`) REFERENCES `online_course_quiz` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_student_quiz_status_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `student_quiz_status`');
    }
}
