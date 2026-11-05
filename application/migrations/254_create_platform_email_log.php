<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `platform_email_log`: every email the platform sends (Platform_mailer), with its result,
 * so the platform admin can see what went out and why something didn't.
 *
 * Platform table: one shared copy, no school_id (school_ref = the school it was about, if any).
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_platform_email_log extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `platform_email_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `template_key` varchar(60) NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `school_ref` int DEFAULT NULL COMMENT 'school.id the email is about',
  `status` varchar(20) NOT NULL COMMENT 'sent, failed, disabled',
  `error` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_platform_email_log_created` (`created_at`),
  KEY `idx_platform_email_log_school` (`school_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_email_log`');
    }
}
