<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `onlineexam_students`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: onlineexam, student_session, school.
 */
class Migration_Create_onlineexam_students extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `onlineexam_students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `onlineexam_id` int DEFAULT NULL,
  `student_session_id` int DEFAULT NULL,
  `is_attempted` int NOT NULL DEFAULT '0',
  `rank` int DEFAULT '0',
  `quiz_attempted` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `onlineexam_id` (`onlineexam_id`),
  KEY `student_session_id` (`student_session_id`),
  KEY `idx_onlineexam_students_school_id` (`school_id`),
  CONSTRAINT `onlineexam_students_ibfk_1` FOREIGN KEY (`onlineexam_id`) REFERENCES `onlineexam` (`id`) ON DELETE CASCADE,
  CONSTRAINT `onlineexam_students_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_onlineexam_students_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `onlineexam_students`');
    }
}
