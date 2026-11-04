<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `visitors_purpose`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_visitors_purpose extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `visitors_purpose` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `visitors_purpose` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_visitors_purpose_school_id` (`school_id`),
  CONSTRAINT `fk_visitors_purpose_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `visitors_purpose`');
    }
}
