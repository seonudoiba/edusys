<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `platform_reminder_log`: renewal reminders already sent (one per subscription and
 * reminder day), so the daily job never sends the same reminder twice.
 *
 * Platform table: one shared copy, no school_id.
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_platform_reminder_log extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `platform_reminder_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subscription_id` int NOT NULL,
  `reminder` varchar(20) NOT NULL COMMENT 'days before expiry, or "expired"',
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_platform_reminder` (`subscription_id`, `reminder`),
  CONSTRAINT `fk_platform_reminder_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `subscription` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_reminder_log`');
    }
}
