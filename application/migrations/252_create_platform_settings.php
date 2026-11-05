<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `platform_settings`: the platform admin's own settings (name in emails, support
 * contacts, renewal reminder days, extra reserved subdomains, last cron runs), as key => value.
 *
 * Platform table: one shared copy, no school_id.
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_platform_settings extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `platform_settings` (
  `name` varchar(100) NOT NULL,
  `value` text,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_settings`');
    }
}
