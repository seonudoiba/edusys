<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `sessions`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_sessions extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `session` varchar(60) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sessions_school_id` (`school_id`),
  CONSTRAINT `fk_sessions_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `sessions`');
    }
}
