<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `gateway_ins`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: online_admissions, school.
 */
class Migration_Create_gateway_ins extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `gateway_ins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `online_admission_id` int DEFAULT NULL,
  `gateway_name` varchar(50) NOT NULL,
  `module_type` varchar(255) NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `parameter_details` mediumtext NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `online_admission_id` (`online_admission_id`),
  KEY `idx_gateway_ins_school_id` (`school_id`),
  CONSTRAINT `gateway_ins_ibfk_1` FOREIGN KEY (`online_admission_id`) REFERENCES `online_admissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gateway_ins_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `gateway_ins`');
    }
}
