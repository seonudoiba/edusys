<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `online_course_quiz`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: online_course_section, staff, school.
 */
class Migration_Create_online_course_quiz extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `online_course_quiz` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `course_section_id` int DEFAULT NULL,
  `quiz_title` varchar(255) DEFAULT NULL,
  `quiz_instruction` text,
  `created_by` int DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `course_section_id` (`course_section_id`) USING BTREE,
  KEY `idx_online_course_quiz_school_id` (`school_id`),
  CONSTRAINT `online_course_quiz_ibfk_1` FOREIGN KEY (`course_section_id`) REFERENCES `online_course_section` (`id`),
  CONSTRAINT `online_course_quiz_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_online_course_quiz_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `online_course_quiz`');
    }
}
