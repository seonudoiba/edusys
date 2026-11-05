<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `platform_coupons`: discount codes schools enter when they sign up.
 *   discount_type  percent | fixed     value  the percentage or the amount
 *   plan_ids       JSON plan ids it applies to; NULL = every plan
 *   max_uses       0 = unlimited; used_count counts paid (or free) activations
 *
 * Platform table: one shared copy, no school_id.
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_platform_coupons extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `platform_coupons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(40) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `discount_type` varchar(10) NOT NULL COMMENT 'percent or fixed',
  `value` decimal(12,2) NOT NULL,
  `plan_ids` text NULL COMMENT 'JSON plan ids; NULL = all plans',
  `max_uses` int NOT NULL DEFAULT 0,
  `used_count` int NOT NULL DEFAULT 0,
  `starts_on` date DEFAULT NULL,
  `ends_on` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_platform_coupons_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_coupons`');
    }
}
