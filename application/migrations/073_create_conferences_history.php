<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `conferences_history`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: conferences, staff, students, school.
 */
class Migration_Create_conferences_history extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `conferences_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `conference_id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `student_id` int DEFAULT NULL,
  `total_hit` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conferences_history_ibfk_1` (`conference_id`),
  KEY `conferences_history_ibfk_2` (`staff_id`),
  KEY `conferences_history_ibfk_3` (`student_id`),
  KEY `idx_conferences_history_school_id` (`school_id`),
  CONSTRAINT `conferences_history_ibfk_1` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conferences_history_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conferences_history_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_conferences_history_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `conferences_history`');
    }
}
