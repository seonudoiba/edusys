<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `school`.
 *
 * Platform table: one shared copy, no school_id.
 * References: none.
 */
class Migration_Create_school extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `school` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `school_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subdomain` varchar(63) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL COMMENT 'Lowercase DNS label, e.g. greenfield for greenfield.edusys.ng',
  `is_platform` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = school served on the central host (platform owner)',
  `school_type` int DEFAULT NULL,
  `school_category` int DEFAULT NULL,
  `school_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `principal_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `owner_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `cron_flag` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_school_subdomain` (`subdomain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `school`');
    }
}
