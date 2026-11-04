<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `template_admitcards`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: school.
 */
class Migration_Create_template_admitcards extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `template_admitcards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `template` varchar(250) DEFAULT NULL,
  `heading` text,
  `title` text,
  `left_logo` varchar(200) DEFAULT NULL,
  `right_logo` varchar(200) DEFAULT NULL,
  `exam_name` varchar(200) DEFAULT NULL,
  `school_name` varchar(200) DEFAULT NULL,
  `exam_center` varchar(200) DEFAULT NULL,
  `sign` varchar(200) DEFAULT NULL,
  `background_img` varchar(200) DEFAULT NULL,
  `is_name` int NOT NULL DEFAULT '1',
  `is_father_name` int NOT NULL DEFAULT '1',
  `is_mother_name` int NOT NULL DEFAULT '1',
  `is_dob` int NOT NULL DEFAULT '1',
  `is_admission_no` int NOT NULL DEFAULT '1',
  `is_roll_no` int NOT NULL DEFAULT '1',
  `is_address` int NOT NULL DEFAULT '1',
  `is_gender` int NOT NULL DEFAULT '1',
  `is_photo` int NOT NULL,
  `is_class` int NOT NULL DEFAULT '0',
  `is_section` int NOT NULL DEFAULT '0',
  `content_footer` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_template_admitcards_school_id` (`school_id`),
  CONSTRAINT `fk_template_admitcards_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `template_admitcards`');
    }
}
