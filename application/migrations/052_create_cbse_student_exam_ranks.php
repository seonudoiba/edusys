<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_student_exam_ranks`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: cbse_exams, student_session, school.
 */
class Migration_Create_cbse_student_exam_ranks extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_student_exam_ranks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `cbse_exam_id` int NOT NULL,
  `student_session_id` int DEFAULT NULL,
  `rank` int DEFAULT NULL,
  `rank_percentage` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cbse_exam_id` (`cbse_exam_id`),
  KEY `student_session_id` (`student_session_id`),
  KEY `idx_rank` (`rank`),
  KEY `idx_rank_percentage` (`rank_percentage`),
  KEY `idx_cbse_student_exam_ranks_school_id` (`school_id`),
  CONSTRAINT `cbse_student_exam_ranks_ibfk_1` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_student_exam_ranks_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cbse_student_exam_ranks_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_student_exam_ranks`');
    }
}
