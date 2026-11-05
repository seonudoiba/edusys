<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `user_google_authenticate_codes`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: users, staff, school.
 */
class Migration_Create_user_google_authenticate_codes extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `user_google_authenticate_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `guest_id` int DEFAULT NULL,
  `staff_id` int DEFAULT NULL,
  `secret_code` varchar(100) DEFAULT NULL,
  `is_active` int DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `staff_id` (`staff_id`),
  KEY `idx_user_google_authenticate_codes_school_id` (`school_id`),
  CONSTRAINT `user_google_authenticate_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_google_authenticate_codes_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_google_authenticate_codes_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `user_google_authenticate_codes`');
    }
}
