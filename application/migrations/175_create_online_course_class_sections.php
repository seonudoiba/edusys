<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `online_course_class_sections`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: online_courses, class_sections, school.
 */
class Migration_Create_online_course_class_sections extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `online_course_class_sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `course_id` int DEFAULT NULL,
  `class_section_id` int DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `class_section_id` (`class_section_id`),
  KEY `idx_online_course_class_sections_school_id` (`school_id`),
  CONSTRAINT `online_course_class_sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `online_courses` (`id`),
  CONSTRAINT `online_course_class_sections_ibfk_2` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`),
  CONSTRAINT `fk_online_course_class_sections_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `online_course_class_sections`');
    }
}
