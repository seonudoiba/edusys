<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `multi_branch`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_multi_branch extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `multi_branch` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `branch_name` varchar(200) DEFAULT NULL,
  `branch_url` varchar(500) NOT NULL,
  `hostname` varchar(200) DEFAULT NULL,
  `username` varchar(200) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `database_name` varchar(200) DEFAULT NULL,
  `directory_path` varchar(500) DEFAULT NULL,
  `is_verified` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_multi_branch_school_id` (`school_id`),
  CONSTRAINT `fk_multi_branch_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `multi_branch`');
    }
}
