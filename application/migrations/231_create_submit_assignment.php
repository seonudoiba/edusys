<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `submit_assignment`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: students, homework, school.
 */
class Migration_Create_submit_assignment extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `submit_assignment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `homework_id` int NOT NULL,
  `student_id` int NOT NULL,
  `message` text NOT NULL,
  `docs` varchar(225) NOT NULL,
  `file_name` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `homework_id` (`homework_id`),
  KEY `idx_submit_assignment_school_id` (`school_id`),
  CONSTRAINT `submit_assignment_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submit_assignment_ibfk_2` FOREIGN KEY (`homework_id`) REFERENCES `homework` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_submit_assignment_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `submit_assignment`');
    }
}
