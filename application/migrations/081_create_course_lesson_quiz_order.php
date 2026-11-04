<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `course_lesson_quiz_order`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: online_course_section, school.
 */
class Migration_Create_course_lesson_quiz_order extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `course_lesson_quiz_order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `type` varchar(10) DEFAULT NULL,
  `course_section_id` int DEFAULT NULL,
  `lesson_quiz_id` int DEFAULT NULL,
  `order` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `section_id` (`course_section_id`),
  KEY `idx_course_lesson_quiz_order_school_id` (`school_id`),
  CONSTRAINT `course_lesson_quiz_order_ibfk_1` FOREIGN KEY (`course_section_id`) REFERENCES `online_course_section` (`id`),
  CONSTRAINT `fk_course_lesson_quiz_order_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `course_lesson_quiz_order`');
    }
}
