<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `roles`.
 *
 * Hybrid table: school_id NULL = system row shared by every school; otherwise owned by that school.
 * References: school.
 */
class Migration_Create_roles extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int DEFAULT NULL COMMENT 'NULL = system row shared by all schools',
  `name` varchar(100) DEFAULT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `is_active` int DEFAULT '0',
  `is_system` int NOT NULL DEFAULT '0',
  `is_superadmin` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_roles_school_id` (`school_id`),
  CONSTRAINT `fk_roles_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `roles`');
    }
}
