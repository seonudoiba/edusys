<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `student_applyleave`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: student_session, staff, school.
 */
class Migration_Create_student_applyleave extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `student_applyleave` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `student_session_id` int NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `apply_date` date NOT NULL,
  `status` int NOT NULL,
  `docs` varchar(200) DEFAULT NULL,
  `reason` text NOT NULL,
  `approve_by` int DEFAULT NULL,
  `approve_date` date DEFAULT NULL,
  `request_type` int NOT NULL COMMENT '0 student,1 staff',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_session_id` (`student_session_id`),
  KEY `approve_by` (`approve_by`),
  KEY `idx_student_applyleave_school_id` (`school_id`),
  CONSTRAINT `student_applyleave_ibfk_1` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_applyleave_ibfk_2` FOREIGN KEY (`approve_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_student_applyleave_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `student_applyleave`');
    }
}
