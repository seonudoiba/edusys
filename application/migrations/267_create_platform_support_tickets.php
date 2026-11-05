<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `platform_support_tickets`: help requests a school sends to the platform team
 * (Help & Support in the school's sidebar; Support desk on the platform).
 *   status  open (waiting for the platform), answered (waiting for the school), closed
 *
 * Platform table: one shared copy, no school_id (school_ref = the school that asked). Every
 * query on a school's host filters on school_ref explicitly.
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_platform_support_tickets extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `platform_support_tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_ref` int NOT NULL,
  `subject` varchar(200) NOT NULL,
  `category` varchar(30) NOT NULL DEFAULT 'question' COMMENT 'question, problem, billing, feature',
  `priority` varchar(10) NOT NULL DEFAULT 'normal' COMMENT 'normal, high',
  `status` varchar(10) NOT NULL DEFAULT 'open',
  `opened_by_staff` int NOT NULL COMMENT 'the school staff member who opened it',
  `opened_by_name` varchar(255) NOT NULL,
  `opened_by_email` varchar(255) NOT NULL,
  `last_reply_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_platform_support_tickets_school` (`school_ref`, `status`),
  KEY `idx_platform_support_tickets_status` (`status`, `last_reply_at`),
  CONSTRAINT `fk_platform_support_tickets_school` FOREIGN KEY (`school_ref`) REFERENCES `school` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_support_tickets`');
    }
}
