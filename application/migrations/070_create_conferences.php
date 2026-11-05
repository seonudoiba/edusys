<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `conferences`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: staff, school.
 */
class Migration_Create_conferences extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `conferences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `purpose` varchar(20) NOT NULL DEFAULT 'class',
  `staff_id` int DEFAULT NULL,
  `created_id` int NOT NULL,
  `title` text,
  `date` datetime DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `section_id` int DEFAULT NULL,
  `session_id` int NOT NULL,
  `host_video` int NOT NULL DEFAULT '1',
  `client_video` int NOT NULL DEFAULT '1',
  `description` text,
  `timezone` varchar(100) DEFAULT NULL,
  `return_response` text,
  `api_type` varchar(30) NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conferences_ibfk_1` (`staff_id`),
  KEY `conferences_ibfk_2` (`created_id`),
  KEY `idx_class_id` (`class_id`),
  KEY `idx_conferences_school_id` (`school_id`),
  CONSTRAINT `conferences_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conferences_ibfk_2` FOREIGN KEY (`created_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_conferences_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `conferences`');
    }
}
