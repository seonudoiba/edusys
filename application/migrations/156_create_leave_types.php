<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `leave_types`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_leave_types extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `leave_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `type` varchar(200) NOT NULL,
  `is_active` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`school_id`,`type`),
  KEY `idx_leave_types_school_id` (`school_id`),
  CONSTRAINT `fk_leave_types_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `leave_types`');
    }
}
