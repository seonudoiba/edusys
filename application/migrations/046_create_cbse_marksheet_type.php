<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_marksheet_type`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_cbse_marksheet_type extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_marksheet_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_code` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_short_code` (`short_code`),
  KEY `idx_cbse_marksheet_type_school_id` (`school_id`),
  CONSTRAINT `fk_cbse_marksheet_type_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_marksheet_type`');
    }
}
