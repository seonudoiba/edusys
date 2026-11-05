<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `platform_invoices`: one numbered invoice (receipt) per paid subscription
 * (Platform_invoices). The owner downloads it with a private link (token).
 *
 * Platform table: one shared copy, no school_id (school_ref = the school billed).
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_platform_invoices extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `platform_invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(30) NOT NULL,
  `subscription_id` int NOT NULL,
  `school_ref` int DEFAULT NULL,
  `bill_to_name` varchar(255) NOT NULL,
  `bill_to_email` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0,
  `discount` decimal(12,2) NOT NULL DEFAULT 0,
  `total` decimal(12,2) NOT NULL DEFAULT 0,
  `method` varchar(40) NOT NULL DEFAULT '',
  `reference` varchar(255) DEFAULT NULL,
  `token` char(40) NOT NULL,
  `issued_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_platform_invoices_no` (`invoice_no`),
  UNIQUE KEY `uq_platform_invoices_subscription` (`subscription_id`),
  UNIQUE KEY `uq_platform_invoices_token` (`token`),
  KEY `idx_platform_invoices_school` (`school_ref`),
  CONSTRAINT `fk_platform_invoices_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `subscription` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_invoices`');
    }
}
