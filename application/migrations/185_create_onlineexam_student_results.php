<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `onlineexam_student_results`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: onlineexam_students, onlineexam_questions, school.
 */
class Migration_Create_onlineexam_student_results extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `onlineexam_student_results` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `onlineexam_student_id` int NOT NULL,
  `onlineexam_question_id` int NOT NULL,
  `select_option` longtext,
  `marks` float(10,2) NOT NULL DEFAULT '0.00',
  `remark` text,
  `attachment_name` text,
  `attachment_upload_name` varchar(250) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `onlineexam_student_id` (`onlineexam_student_id`),
  KEY `onlineexam_question_id` (`onlineexam_question_id`),
  KEY `idx_onlineexam_student_results_school_id` (`school_id`),
  CONSTRAINT `onlineexam_student_results_ibfk_1` FOREIGN KEY (`onlineexam_student_id`) REFERENCES `onlineexam_students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `onlineexam_student_results_ibfk_2` FOREIGN KEY (`onlineexam_question_id`) REFERENCES `onlineexam_questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_onlineexam_student_results_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `onlineexam_student_results`');
    }
}
