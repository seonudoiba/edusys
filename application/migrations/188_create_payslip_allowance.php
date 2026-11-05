<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `payslip_allowance`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: staff, staff_payslip, school.
 */
class Migration_Create_payslip_allowance extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `payslip_allowance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `payslip_id` int NOT NULL,
  `allowance_type` varchar(200) NOT NULL,
  `amount` float NOT NULL,
  `staff_id` int NOT NULL,
  `cal_type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `staff_id` (`staff_id`),
  KEY `payslip_id` (`payslip_id`),
  KEY `idx_payslip_allowance_school_id` (`school_id`),
  CONSTRAINT `payslip_allowance_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payslip_allowance_ibfk_2` FOREIGN KEY (`payslip_id`) REFERENCES `staff_payslip` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_payslip_allowance_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `payslip_allowance`');
    }
}
