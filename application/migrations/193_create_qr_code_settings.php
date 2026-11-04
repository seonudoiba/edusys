<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `QR_code_settings`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_qr_code_settings extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `QR_code_settings` (
  `id` int NOT NULL,
  `school_id` int NOT NULL,
  `camera_type` varchar(15) DEFAULT NULL,
  `auto_attendance` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_QR_code_settings_school_id` (`school_id`),
  CONSTRAINT `fk_QR_code_settings_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `QR_code_settings`');
    }
}
