<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `gmeet`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: staff, sessions, school.
 */
class Migration_Create_gmeet extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `gmeet` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `purpose` varchar(20) NOT NULL DEFAULT 'class',
  `staff_id` int DEFAULT NULL,
  `created_id` int NOT NULL,
  `title` text,
  `date` datetime DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'manual',
  `api_data` text,
  `duration` int DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `url` text NOT NULL,
  `session_id` int NOT NULL,
  `description` text,
  `timezone` varchar(100) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `staff_id` (`staff_id`),
  KEY `created_id` (`created_id`),
  KEY `session_id` (`session_id`),
  KEY `idx_gmeet_school_id` (`school_id`),
  CONSTRAINT `gmeet_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gmeet_ibfk_2` FOREIGN KEY (`created_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gmeet_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gmeet_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `gmeet`');
    }
}
