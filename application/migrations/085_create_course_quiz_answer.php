<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `course_quiz_answer`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: students, online_course_quiz, course_quiz_question, school.
 */
class Migration_Create_course_quiz_answer extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `course_quiz_answer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `guest_id` int DEFAULT NULL,
  `course_quiz_id` int DEFAULT NULL,
  `course_quiz_question_id` int DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `quiz_id` (`course_quiz_id`),
  KEY `question_id` (`course_quiz_question_id`),
  KEY `idx_course_quiz_answer_school_id` (`school_id`),
  CONSTRAINT `course_quiz_answer_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_quiz_answer_ibfk_2` FOREIGN KEY (`course_quiz_id`) REFERENCES `online_course_quiz` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_quiz_answer_ibfk_3` FOREIGN KEY (`course_quiz_question_id`) REFERENCES `course_quiz_question` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_course_quiz_answer_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `course_quiz_answer`');
    }
}
