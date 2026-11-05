<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `permission_student`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: permission_group, school.
 */
class Migration_Create_permission_student extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `permission_student` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `short_code` varchar(100) NOT NULL,
  `system` int NOT NULL,
  `student` int NOT NULL,
  `parent` int NOT NULL,
  `group_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `idx_permission_student_school_id` (`school_id`),
  CONSTRAINT `permission_student_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `permission_group` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_permission_student_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `permission_student`');
    }
}
