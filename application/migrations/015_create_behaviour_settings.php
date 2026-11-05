<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `behaviour_settings`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_behaviour_settings extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `behaviour_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `comment_option` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_behaviour_settings_school_id` (`school_id`),
  CONSTRAINT `fk_behaviour_settings_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `behaviour_settings`');
    }
}
