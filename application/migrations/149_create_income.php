<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `income`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: income_head, school.
 */
class Migration_Create_income extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `income` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `income_head_id` int DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `invoice_no` varchar(200) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` float(10,2) DEFAULT '0.00',
  `note` text,
  `is_active` varchar(255) DEFAULT 'yes',
  `documents` varchar(255) DEFAULT NULL,
  `is_deleted` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `income_head_id` (`income_head_id`),
  KEY `idx_income_school_id` (`school_id`),
  CONSTRAINT `income_ibfk_1` FOREIGN KEY (`income_head_id`) REFERENCES `income_head` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_income_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `income`');
    }
}
