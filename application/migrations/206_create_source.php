<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `source`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_source extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `source` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `source` varchar(100) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_source_school_id` (`school_id`),
  CONSTRAINT `fk_source_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `source`');
    }
}
