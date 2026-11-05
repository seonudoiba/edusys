<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `hostel_rooms`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: hostel, room_types, school.
 */
class Migration_Create_hostel_rooms extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `hostel_rooms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `hostel_id` int DEFAULT NULL,
  `room_type_id` int DEFAULT NULL,
  `room_no` varchar(200) DEFAULT NULL,
  `no_of_bed` int DEFAULT NULL,
  `cost_per_bed` float(10,2) DEFAULT '0.00',
  `title` varchar(200) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `hostel_id` (`hostel_id`),
  KEY `room_type_id` (`room_type_id`),
  KEY `idx_hostel_rooms_school_id` (`school_id`),
  CONSTRAINT `hostel_rooms_ibfk_1` FOREIGN KEY (`hostel_id`) REFERENCES `hostel` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hostel_rooms_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_hostel_rooms_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `hostel_rooms`');
    }
}
