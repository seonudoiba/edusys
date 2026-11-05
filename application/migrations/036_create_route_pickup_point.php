<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `route_pickup_point`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: transport_route, pickup_point, school.
 */
class Migration_Create_route_pickup_point extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `route_pickup_point` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `transport_route_id` int NOT NULL,
  `pickup_point_id` int NOT NULL,
  `fees` float(10,2) DEFAULT '0.00',
  `destination_distance` float(10,1) DEFAULT '0.0',
  `pickup_time` time DEFAULT NULL,
  `order_number` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `transport_route_id` (`transport_route_id`),
  KEY `pickup_point_id` (`pickup_point_id`),
  KEY `idx_route_pickup_point_school_id` (`school_id`),
  CONSTRAINT `route_pickup_point_ibfk_1` FOREIGN KEY (`transport_route_id`) REFERENCES `transport_route` (`id`) ON DELETE CASCADE,
  CONSTRAINT `route_pickup_point_ibfk_2` FOREIGN KEY (`pickup_point_id`) REFERENCES `pickup_point` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_route_pickup_point_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `route_pickup_point`');
    }
}
