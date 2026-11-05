<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `video_tutorial`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: staff, school.
 */
class Migration_Create_video_tutorial extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `video_tutorial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `vid_title` text,
  `description` text NOT NULL,
  `thumb_path` varchar(500) DEFAULT NULL,
  `dir_path` varchar(500) DEFAULT NULL,
  `img_name` varchar(300) NOT NULL,
  `thumb_name` varchar(300) NOT NULL,
  `video_link` varchar(100) NOT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_title` (`title`),
  KEY `idx_video_tutorial_school_id` (`school_id`),
  CONSTRAINT `video_tutorial_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_video_tutorial_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `video_tutorial`');
    }
}
