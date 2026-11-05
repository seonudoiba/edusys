<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_template_class_sections`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: cbse_template, class_sections, school.
 */
class Migration_Create_cbse_template_class_sections extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_template_class_sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `cbse_template_id` int NOT NULL,
  `class_section_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cbse_template_id` (`cbse_template_id`),
  KEY `class_section_id` (`class_section_id`),
  KEY `idx_cbse_template_class_sections_school_id` (`school_id`),
  CONSTRAINT `cbse_template_class_sections_ibfk_1` FOREIGN KEY (`cbse_template_id`) REFERENCES `cbse_template` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_template_class_sections_ibfk_2` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cbse_template_class_sections_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_template_class_sections`');
    }
}
