<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `reference`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_reference extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `reference` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `reference` varchar(100) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reference_school_id` (`school_id`),
  CONSTRAINT `fk_reference_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `reference`');
    }
}
