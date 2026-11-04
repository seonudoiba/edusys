<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `staff_payroll`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_staff_payroll extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `staff_payroll` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `basic_salary` int NOT NULL,
  `pay_scale` varchar(200) NOT NULL,
  `grade` varchar(50) NOT NULL,
  `is_active` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_staff_payroll_school_id` (`school_id`),
  CONSTRAINT `fk_staff_payroll_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `staff_payroll`');
    }
}
