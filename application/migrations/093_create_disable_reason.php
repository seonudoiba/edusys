<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `disable_reason`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_disable_reason extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `disable_reason` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `reason` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_disable_reason_school_id` (`school_id`),
  CONSTRAINT `fk_disable_reason_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `disable_reason`');
    }
}
