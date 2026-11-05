<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `homework_evaluation`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: homework, students, student_session, school.
 */
class Migration_Create_homework_evaluation extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `homework_evaluation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `homework_id` int NOT NULL,
  `student_id` int NOT NULL,
  `student_session_id` int DEFAULT NULL,
  `marks` float(10,2) DEFAULT NULL,
  `note` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `homework_id` (`homework_id`),
  KEY `student_id` (`student_id`),
  KEY `student_session_id` (`student_session_id`),
  KEY `idx_homework_evaluation_school_id` (`school_id`),
  CONSTRAINT `homework_evaluation_ibfk_1` FOREIGN KEY (`homework_id`) REFERENCES `homework` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homework_evaluation_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homework_evaluation_ibfk_3` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_homework_evaluation_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `homework_evaluation`');
    }
}
