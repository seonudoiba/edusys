<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `course_category`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_course_category extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `course_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `is_active` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_course_category_school_id` (`school_id`),
  CONSTRAINT `fk_course_category_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `course_category`');
    }
}
