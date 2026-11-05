<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `student_incidents`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: students, student_behaviour, school.
 */
class Migration_Create_student_incidents extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `student_incidents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `session_id` int NOT NULL,
  `student_id` int NOT NULL,
  `incident_id` int NOT NULL,
  `assign_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_incidents_ibfk_1` (`student_id`),
  KEY `student_incidents_ibfk_2` (`incident_id`),
  KEY `idx_student_incidents_school_id` (`school_id`),
  CONSTRAINT `student_incidents_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_incidents_ibfk_2` FOREIGN KEY (`incident_id`) REFERENCES `student_behaviour` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_student_incidents_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `student_incidents`');
    }
}
