<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `questions`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: subjects, staff, classes, sections, class_sections, school.
 */
class Migration_Create_questions extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  `question_type` varchar(100) NOT NULL,
  `level` varchar(10) NOT NULL,
  `class_id` int NOT NULL,
  `section_id` int DEFAULT NULL,
  `class_section_id` int DEFAULT NULL,
  `question` text,
  `opt_a` text,
  `opt_b` text,
  `opt_c` text,
  `opt_d` text,
  `opt_e` text,
  `correct` text,
  `descriptive_word_limit` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  KEY `staff_id` (`staff_id`),
  KEY `class_id` (`class_id`),
  KEY `section_id` (`section_id`),
  KEY `class_section_id` (`class_section_id`),
  KEY `idx_questions_school_id` (`school_id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `questions_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `questions_ibfk_4` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `questions_ibfk_5` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `questions_ibfk_6` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_questions_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `questions`');
    }
}
