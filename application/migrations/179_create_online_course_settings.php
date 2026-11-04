<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `online_course_settings`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_online_course_settings extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `online_course_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `guest_prefix` varchar(50) NOT NULL,
  `guest_id_start_from` int NOT NULL,
  `guest_login` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_online_course_settings_school_id` (`school_id`),
  CONSTRAINT `fk_online_course_settings_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `online_course_settings`');
    }
}
