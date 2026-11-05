<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `platform_school_domains`: a school's own domains (e.g. portal.greenfield.com), served
 * as the school besides its subdomain. A domain only works once it is verified (a DNS TXT record
 * "_edusys.<domain>" holding the token, or a CNAME to the school's subdomain), so nobody can
 * point a host at another school.
 *
 * Platform table: one shared copy, no school_id (school_ref = the school it serves).
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_platform_school_domains extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `platform_school_domains` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_ref` int NOT NULL,
  `domain` varchar(253) NOT NULL,
  `verify_token` char(32) NOT NULL,
  `verified_at` datetime DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'used in links and emails instead of the subdomain',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_platform_school_domains_domain` (`domain`),
  KEY `idx_platform_school_domains_school` (`school_ref`),
  CONSTRAINT `fk_platform_school_domains_school` FOREIGN KEY (`school_ref`) REFERENCES `school` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_school_domains`');
    }
}
