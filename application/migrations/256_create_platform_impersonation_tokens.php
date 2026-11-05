<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `platform_impersonation_tokens`: one-time links the platform admin uses to open a
 * school's admin panel as its super admin (support). Only the token's hash is stored; a token
 * works once, on that school's host, within two minutes.
 *
 * Platform table: one shared copy, no school_id (school_ref = the school it opens).
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_platform_impersonation_tokens extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `platform_impersonation_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token_hash` char(64) NOT NULL,
  `school_ref` int NOT NULL,
  `staff_id` int NOT NULL COMMENT 'the school staff account opened',
  `created_by` varchar(255) NOT NULL COMMENT 'platform admin (name and email)',
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_platform_impersonation_token` (`token_hash`),
  CONSTRAINT `fk_platform_impersonation_school` FOREIGN KEY (`school_ref`) REFERENCES `school` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_impersonation_tokens`');
    }
}
