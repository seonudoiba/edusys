<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `conference_sections`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: conferences, class_sections, school.
 */
class Migration_Create_conference_sections extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `conference_sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `conference_id` int DEFAULT NULL,
  `cls_section_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conference_sections_ibfk_1` (`conference_id`),
  KEY `conference_sections_ibfk_2` (`cls_section_id`),
  KEY `idx_conference_sections_school_id` (`school_id`),
  CONSTRAINT `conference_sections_ibfk_1` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conference_sections_ibfk_2` FOREIGN KEY (`cls_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_conference_sections_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `conference_sections`');
    }
}
