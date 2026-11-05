<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `follow_up`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: enquiry, staff, school.
 */
class Migration_Create_follow_up extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `follow_up` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `enquiry_id` int NOT NULL,
  `date` date NOT NULL,
  `next_date` date NOT NULL,
  `response` text NOT NULL,
  `note` text NOT NULL,
  `followup_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `enquiry_id` (`enquiry_id`),
  KEY `followup_by` (`followup_by`),
  KEY `idx_follow_up_school_id` (`school_id`),
  CONSTRAINT `follow_up_ibfk_1` FOREIGN KEY (`enquiry_id`) REFERENCES `enquiry` (`id`) ON DELETE CASCADE,
  CONSTRAINT `follow_up_ibfk_2` FOREIGN KEY (`followup_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_follow_up_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `follow_up`');
    }
}
