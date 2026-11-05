<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `pickup_point`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_pickup_point extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `pickup_point` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `latitude` varchar(100) DEFAULT NULL,
  `longitude` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pickup_point_school_id` (`school_id`),
  CONSTRAINT `fk_pickup_point_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `pickup_point`');
    }
}
