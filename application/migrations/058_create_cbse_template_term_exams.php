<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_template_term_exams`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: cbse_exams, cbse_template_terms, cbse_template, school.
 */
class Migration_Create_cbse_template_term_exams extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_template_term_exams` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `cbse_template_term_id` int DEFAULT NULL,
  `cbse_exam_id` int NOT NULL,
  `cbse_template_id` int NOT NULL,
  `weightage` float NOT NULL DEFAULT '100',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cbse_template_term_id` (`cbse_template_term_id`),
  KEY `cbse_template_term_exams_ibfk_3` (`cbse_exam_id`),
  KEY `cbse_template_term_exams_ibfk_4` (`cbse_template_id`),
  KEY `idx_weightage` (`weightage`),
  KEY `idx_cbse_template_term_exams_school_id` (`school_id`),
  CONSTRAINT `cbse_template_term_exams_ibfk_1` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_template_term_exams_ibfk_2` FOREIGN KEY (`cbse_template_term_id`) REFERENCES `cbse_template_terms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_template_term_exams_ibfk_4` FOREIGN KEY (`cbse_template_id`) REFERENCES `cbse_template` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cbse_template_term_exams_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_template_term_exams`');
    }
}
