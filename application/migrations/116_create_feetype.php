<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `feetype`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_feetype extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `feetype` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `is_system` int NOT NULL DEFAULT '0',
  `feecategory_id` int DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `code` varchar(100) NOT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_feetype_school_id` (`school_id`),
  CONSTRAINT `fk_feetype_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `feetype`');
    }
}
