<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `platform_school_notes`: the platform team's internal notes on a school (calls,
 * agreements, follow-ups). Never shown to the school.
 *
 * Platform table: one shared copy, no school_id (school_ref = the school the note is about).
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_platform_school_notes extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `platform_school_notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_ref` int NOT NULL,
  `note` text NOT NULL,
  `created_by` varchar(255) NOT NULL COMMENT 'platform admin (name and email)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_platform_school_notes_school` (`school_ref`),
  CONSTRAINT `fk_platform_school_notes_school` FOREIGN KEY (`school_ref`) REFERENCES `school` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_school_notes`');
    }
}
