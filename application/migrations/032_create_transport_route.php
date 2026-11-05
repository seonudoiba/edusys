<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `transport_route`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_transport_route extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `transport_route` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `route_title` varchar(100) DEFAULT NULL,
  `no_of_vehicle` int DEFAULT NULL,
  `note` text,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_transport_route_school_id` (`school_id`),
  CONSTRAINT `fk_transport_route_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `transport_route`');
    }
}
