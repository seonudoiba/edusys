<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `online_course_section`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: online_courses, school.
 */
class Migration_Create_online_course_section extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `online_course_section` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `online_course_id` int DEFAULT NULL,
  `section_title` varchar(255) DEFAULT NULL,
  `order` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `online_course_id` (`online_course_id`),
  KEY `idx_online_course_section_school_id` (`school_id`),
  CONSTRAINT `online_course_section_ibfk_1` FOREIGN KEY (`online_course_id`) REFERENCES `online_courses` (`id`),
  CONSTRAINT `fk_online_course_section_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `online_course_section`');
    }
}
