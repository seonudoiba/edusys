<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `gmeet_staff`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: gmeet, staff, school.
 */
class Migration_Create_gmeet_staff extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `gmeet_staff` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `gmeet_id` int NOT NULL,
  `staff_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `gmeet_id` (`gmeet_id`),
  KEY `staff_id` (`staff_id`),
  KEY `idx_gmeet_staff_school_id` (`school_id`),
  CONSTRAINT `gmeet_staff_ibfk_1` FOREIGN KEY (`gmeet_id`) REFERENCES `gmeet` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gmeet_staff_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gmeet_staff_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `gmeet_staff`');
    }
}
