<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `notification_setting`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_notification_setting extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `notification_setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `is_mail` varchar(10) DEFAULT '0',
  `is_sms` varchar(10) DEFAULT '0',
  `is_notification` int NOT NULL DEFAULT '0',
  `display_notification` int NOT NULL DEFAULT '0',
  `display_sms` int NOT NULL DEFAULT '1',
  `is_student_recipient` int DEFAULT NULL,
  `is_guardian_recipient` int DEFAULT NULL,
  `is_staff_recipient` int DEFAULT NULL,
  `display_student_recipient` int DEFAULT NULL,
  `display_guardian_recipient` int DEFAULT NULL,
  `display_staff_recipient` int DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `template_id` varchar(100) NOT NULL,
  `template` longtext NOT NULL,
  `variables` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notification_setting_school_id` (`school_id`),
  CONSTRAINT `fk_notification_setting_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `notification_setting`');
    }
}
