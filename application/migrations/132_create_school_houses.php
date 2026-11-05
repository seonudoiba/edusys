<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `school_houses`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_school_houses extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `school_houses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `house_name` varchar(200) NOT NULL,
  `description` varchar(400) NOT NULL,
  `is_active` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_school_houses_school_id` (`school_id`),
  CONSTRAINT `fk_school_houses_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `school_houses`');
    }
}
