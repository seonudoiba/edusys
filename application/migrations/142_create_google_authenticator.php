<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `google_authenticator`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_google_authenticator extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `google_authenticator` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `use_authenticator` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_google_authenticator_school_id` (`school_id`),
  CONSTRAINT `fk_google_authenticator_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `google_authenticator`');
    }
}
