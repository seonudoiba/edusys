<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `online_admission_payment`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: online_admissions, school.
 */
class Migration_Create_online_admission_payment extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `online_admission_payment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `online_admission_id` int NOT NULL,
  `paid_amount` float(10,2) NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `payment_type` varchar(100) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `note` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `online_admission_id` (`online_admission_id`),
  KEY `idx_online_admission_payment_school_id` (`school_id`),
  CONSTRAINT `online_admission_payment_ibfk_1` FOREIGN KEY (`online_admission_id`) REFERENCES `online_admissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_online_admission_payment_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `online_admission_payment`');
    }
}
