<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_exams`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: cbse_terms, cbse_exam_grades, cbse_exam_assessments, sessions, school.
 */
class Migration_Create_cbse_exams extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_exams` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `total_working_days` int DEFAULT '0',
  `cbse_term_id` int DEFAULT NULL,
  `cbse_exam_assessment_id` int DEFAULT NULL,
  `cbse_exam_grade_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `exam_code` varchar(200) DEFAULT NULL,
  `session_id` int NOT NULL,
  `description` mediumtext NOT NULL,
  `is_publish` int NOT NULL,
  `is_active` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `use_exam_roll_no` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cbse_term_id` (`cbse_term_id`),
  KEY `cbse_exam_grade_id` (`cbse_exam_grade_id`),
  KEY `cbse_exam_assessment_id` (`cbse_exam_assessment_id`),
  KEY `session_id` (`session_id`),
  KEY `idx_name` (`name`),
  KEY `idx_exam_code` (`exam_code`),
  KEY `idx_cbse_exams_school_id` (`school_id`),
  CONSTRAINT `cbse_exams_ibfk_1` FOREIGN KEY (`cbse_term_id`) REFERENCES `cbse_terms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_exams_ibfk_2` FOREIGN KEY (`cbse_exam_grade_id`) REFERENCES `cbse_exam_grades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_exams_ibfk_3` FOREIGN KEY (`cbse_exam_assessment_id`) REFERENCES `cbse_exam_assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_exams_ibfk_4` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cbse_exams_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_exams`');
    }
}
