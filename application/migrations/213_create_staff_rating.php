<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `staff_rating`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: staff, school.
 */
class Migration_Create_staff_rating extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `staff_rating` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `staff_id` int NOT NULL,
  `comment` text NOT NULL,
  `rate` int NOT NULL,
  `user_id` int NOT NULL,
  `role` varchar(255) NOT NULL,
  `status` int NOT NULL COMMENT '0 decline, 1 Approve',
  `entrydt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_staff_rating_staff` (`staff_id`),
  KEY `idx_staff_rating_school_id` (`school_id`),
  CONSTRAINT `FK_staff_rating_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_staff_rating_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `staff_rating`');
    }
}
