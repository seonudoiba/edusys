<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `online_course_lesson`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: online_course_section, school.
 */
class Migration_Create_online_course_lesson extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `online_course_lesson` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `course_section_id` int DEFAULT NULL,
  `lesson_title` varchar(255) DEFAULT NULL,
  `lesson_type` varchar(20) DEFAULT NULL,
  `thumbnail` varchar(100) DEFAULT NULL,
  `summary` text,
  `attachment` varchar(200) DEFAULT NULL,
  `video_provider` varchar(20) DEFAULT NULL,
  `video_url` text,
  `video_id` varchar(50) DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `course_section_id` (`course_section_id`) USING BTREE,
  KEY `idx_online_course_lesson_school_id` (`school_id`),
  CONSTRAINT `online_course_lesson_ibfk_1` FOREIGN KEY (`course_section_id`) REFERENCES `online_course_section` (`id`),
  CONSTRAINT `fk_online_course_lesson_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `online_course_lesson`');
    }
}
