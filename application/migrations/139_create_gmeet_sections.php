<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `gmeet_sections`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: class_sections, gmeet, school.
 */
class Migration_Create_gmeet_sections extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `gmeet_sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `gmeet_id` int NOT NULL,
  `cls_section_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cls_section_id` (`cls_section_id`),
  KEY `gmeet_id` (`gmeet_id`),
  KEY `idx_gmeet_sections_school_id` (`school_id`),
  CONSTRAINT `gmeet_sections_ibfk_1` FOREIGN KEY (`cls_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gmeet_sections_ibfk_2` FOREIGN KEY (`gmeet_id`) REFERENCES `gmeet` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gmeet_sections_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `gmeet_sections`');
    }
}
