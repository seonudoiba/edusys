<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `student_fees_master`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: fee_session_groups, student_session, school.
 */
class Migration_Create_student_fees_master extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `student_fees_master` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `is_system` int NOT NULL DEFAULT '0',
  `student_session_id` int DEFAULT NULL,
  `fee_session_group_id` int DEFAULT NULL,
  `amount` float(10,2) DEFAULT '0.00',
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_session_id` (`student_session_id`),
  KEY `fee_session_group_id` (`fee_session_group_id`),
  KEY `idx_student_fees_master_school_id` (`school_id`),
  CONSTRAINT `student_fees_master_ibfk_1` FOREIGN KEY (`fee_session_group_id`) REFERENCES `fee_session_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_fees_master_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_student_fees_master_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `student_fees_master`');
    }
}
