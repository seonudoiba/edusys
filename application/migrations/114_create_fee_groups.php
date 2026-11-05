<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `fee_groups`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_fee_groups extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `fee_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `is_system` int NOT NULL DEFAULT '0',
  `description` text,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fee_groups_school_id` (`school_id`),
  CONSTRAINT `fk_fee_groups_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `fee_groups`');
    }
}
