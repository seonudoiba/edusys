<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `guest`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_guest extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `guest` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `guest_name` varchar(200) NOT NULL,
  `guest_unique_id` varchar(200) NOT NULL,
  `lang_id` int NOT NULL,
  `currency_id` int NOT NULL,
  `email` varchar(200) NOT NULL,
  `mobileno` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `dob` varchar(200) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `note` text NOT NULL,
  `address` varchar(200) NOT NULL,
  `guest_image` varchar(200) NOT NULL,
  `verification_code` varchar(200) NOT NULL,
  `created_at` date NOT NULL,
  `is_active` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_lang_id` (`lang_id`),
  KEY `idx_currency_id` (`currency_id`),
  KEY `idx_email` (`email`),
  KEY `idx_guest_school_id` (`school_id`),
  CONSTRAINT `fk_guest_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `guest`');
    }
}
