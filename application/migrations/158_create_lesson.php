<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `lesson`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: sessions, subject_group_subjects, subject_group_class_sections, school.
 */
class Migration_Create_lesson extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `lesson` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `session_id` int NOT NULL,
  `subject_group_subject_id` int NOT NULL,
  `subject_group_class_sections_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `subject_group_subject_id` (`subject_group_subject_id`),
  KEY `subject_group_class_sections_id` (`subject_group_class_sections_id`),
  KEY `idx_lesson_school_id` (`school_id`),
  CONSTRAINT `lesson_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lesson_ibfk_2` FOREIGN KEY (`subject_group_subject_id`) REFERENCES `subject_group_subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lesson_ibfk_3` FOREIGN KEY (`subject_group_class_sections_id`) REFERENCES `subject_group_class_sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lesson_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `lesson`');
    }
}
