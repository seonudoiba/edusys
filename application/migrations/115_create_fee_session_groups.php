<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `fee_session_groups`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: fee_groups, sessions, school.
 */
class Migration_Create_fee_session_groups extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `fee_session_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `fee_groups_id` int DEFAULT NULL,
  `session_id` int DEFAULT NULL,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fee_groups_id` (`fee_groups_id`),
  KEY `session_id` (`session_id`),
  KEY `idx_fee_session_groups_school_id` (`school_id`),
  CONSTRAINT `fee_session_groups_ibfk_1` FOREIGN KEY (`fee_groups_id`) REFERENCES `fee_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fee_session_groups_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fee_session_groups_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `fee_session_groups`');
    }
}
