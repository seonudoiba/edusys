<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `student_incident_comments`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: student_incidents, school.
 */
class Migration_Create_student_incident_comments extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `student_incident_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `student_incident_id` int NOT NULL,
  `comment` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `staff_id` int NOT NULL,
  `student_id` int NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_incident_comments_ibfk_1` (`student_incident_id`),
  KEY `idx_student_incident_comments_school_id` (`school_id`),
  CONSTRAINT `student_incident_comments_ibfk_1` FOREIGN KEY (`student_incident_id`) REFERENCES `student_incidents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_student_incident_comments_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `student_incident_comments`');
    }
}
