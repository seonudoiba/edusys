<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `platform_enquiries`: messages sent through the platform website's contact and
 * complaint forms (the platform's inbox: Support > Enquiries).
 *   kind    contact | complaint      status  new, replied, closed
 *
 * Platform table: one shared copy, no school_id.
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_platform_enquiries extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `platform_enquiries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kind` varchar(15) NOT NULL DEFAULT 'contact',
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(50) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'new',
  `reply` text,
  `replied_by` varchar(255) DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_platform_enquiries_status` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_enquiries`');
    }
}
