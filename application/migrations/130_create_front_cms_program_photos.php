<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `front_cms_program_photos`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: front_cms_programs, school.
 */
class Migration_Create_front_cms_program_photos extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `front_cms_program_photos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `program_id` int DEFAULT NULL,
  `media_gallery_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `program_id` (`program_id`),
  KEY `idx_front_cms_program_photos_school_id` (`school_id`),
  CONSTRAINT `front_cms_program_photos_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `front_cms_programs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_front_cms_program_photos_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `front_cms_program_photos`');
    }
}
