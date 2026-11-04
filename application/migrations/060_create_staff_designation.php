<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `staff_designation`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_staff_designation extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `staff_designation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `designation` varchar(200) NOT NULL,
  `is_active` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_staff_designation_school_id` (`school_id`),
  CONSTRAINT `fk_staff_designation_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `staff_designation`');
    }
}
