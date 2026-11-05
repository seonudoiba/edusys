<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `conference_staff`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: conferences, staff, school.
 */
class Migration_Create_conference_staff extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `conference_staff` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `conference_id` int NOT NULL,
  `staff_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conference_staff_ibfk_1` (`conference_id`),
  KEY `conference_staff_ibfk_2` (`staff_id`),
  KEY `idx_conference_staff_school_id` (`school_id`),
  CONSTRAINT `conference_staff_ibfk_1` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conference_staff_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_conference_staff_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `conference_staff`');
    }
}
