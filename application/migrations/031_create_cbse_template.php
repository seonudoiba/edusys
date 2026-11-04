<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_template`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: cbse_exams, sessions, school.
 */
class Migration_Create_cbse_template extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_template` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `orientation` varchar(1) NOT NULL DEFAULT 'P',
  `description` varchar(255) NOT NULL,
  `gradeexam_id` int DEFAULT NULL,
  `remarkexam_id` int DEFAULT NULL,
  `is_weightage` varchar(10) NOT NULL,
  `marksheet_type` varchar(50) NOT NULL,
  `created_by` int NOT NULL,
  `header_image` varbinary(500) DEFAULT NULL,
  `title` text,
  `left_logo` varchar(200) DEFAULT NULL,
  `right_logo` varchar(200) DEFAULT NULL,
  `exam_name` varchar(200) DEFAULT NULL,
  `school_name` varchar(200) DEFAULT NULL,
  `exam_center` varchar(200) DEFAULT NULL,
  `session_id` int NOT NULL,
  `left_sign` varchar(200) DEFAULT NULL,
  `middle_sign` varchar(200) DEFAULT NULL,
  `right_sign` varchar(200) DEFAULT NULL,
  `background_img` varchar(200) DEFAULT NULL,
  `content` text,
  `content_footer` text,
  `date` date DEFAULT NULL,
  `is_name` int DEFAULT '1',
  `is_father_name` int DEFAULT '1',
  `is_mother_name` int DEFAULT '1',
  `exam_session` int DEFAULT '1',
  `is_admission_no` int DEFAULT '1',
  `is_division` int NOT NULL DEFAULT '1',
  `is_roll_no` int DEFAULT '1',
  `is_photo` int DEFAULT '1',
  `is_class` int NOT NULL DEFAULT '0',
  `is_section` int NOT NULL DEFAULT '0',
  `is_dob` int DEFAULT '1',
  `is_remark` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cbse_template_ibfk_3` (`session_id`),
  KEY `cbse_template_ibfk_1` (`gradeexam_id`),
  KEY `cbse_template_ibfk_2` (`remarkexam_id`),
  KEY `idx_name` (`name`),
  KEY `idx_marksheet_type` (`marksheet_type`),
  KEY `idx_exam_name` (`exam_name`),
  KEY `idx_school_name` (`school_name`),
  KEY `idx_cbse_template_school_id` (`school_id`),
  CONSTRAINT `cbse_template_ibfk_1` FOREIGN KEY (`gradeexam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cbse_template_ibfk_2` FOREIGN KEY (`remarkexam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cbse_template_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cbse_template_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_template`');
    }
}
