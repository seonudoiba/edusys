<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `student_behaviour`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_student_behaviour extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `student_behaviour` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `point` int NOT NULL,
  `description` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`),
  KEY `idx_description` (`description`(200)),
  KEY `idx_student_behaviour_school_id` (`school_id`),
  CONSTRAINT `fk_student_behaviour_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `student_behaviour`');
    }
}
