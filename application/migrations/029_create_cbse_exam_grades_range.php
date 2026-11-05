<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_exam_grades_range`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: cbse_exam_grades, school.
 */
class Migration_Create_cbse_exam_grades_range extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_exam_grades_range` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `cbse_exam_grade_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `minimum_percentage` float NOT NULL,
  `maximum_percentage` float NOT NULL,
  `description` mediumtext NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cbse_exam_grade_id` (`cbse_exam_grade_id`),
  KEY `idx_name` (`name`),
  KEY `idx_cbse_exam_grades_range_school_id` (`school_id`),
  CONSTRAINT `cbse_exam_grades_range_ibfk_1` FOREIGN KEY (`cbse_exam_grade_id`) REFERENCES `cbse_exam_grades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cbse_exam_grades_range_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_exam_grades_range`');
    }
}
