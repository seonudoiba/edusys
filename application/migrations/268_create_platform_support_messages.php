<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `platform_support_messages`: the conversation of a support ticket.
 *   author  school | platform
 *
 * Platform table: one shared copy, no school_id (reached through its ticket).
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_platform_support_messages extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `platform_support_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `author` varchar(10) NOT NULL COMMENT 'school or platform',
  `author_name` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_platform_support_messages_ticket` (`ticket_id`),
  CONSTRAINT `fk_platform_support_messages_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `platform_support_tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_support_messages`');
    }
}
