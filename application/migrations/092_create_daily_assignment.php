<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `daily_assignment`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: student_session, staff, subject_group_subjects, school.
 */
class Migration_Create_daily_assignment extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `daily_assignment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `student_session_id` int NOT NULL,
  `subject_group_subject_id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `attachment` varchar(255) DEFAULT NULL,
  `evaluated_by` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `evaluation_date` date DEFAULT NULL,
  `remark` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_session_id` (`student_session_id`),
  KEY `evaluated_by` (`evaluated_by`),
  KEY `subject_group_subject_id` (`subject_group_subject_id`),
  KEY `idx_daily_assignment_school_id` (`school_id`),
  CONSTRAINT `daily_assignment_ibfk_1` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_assignment_ibfk_2` FOREIGN KEY (`evaluated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_assignment_ibfk_3` FOREIGN KEY (`subject_group_subject_id`) REFERENCES `subject_group_subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_daily_assignment_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `daily_assignment`');
    }
}
