<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `course_quiz_question`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: online_course_quiz, school.
 */
class Migration_Create_course_quiz_question extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `course_quiz_question` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `course_quiz_id` int DEFAULT NULL,
  `question` text,
  `option_1` varchar(255) DEFAULT NULL,
  `option_2` varchar(255) DEFAULT NULL,
  `option_3` varchar(255) DEFAULT NULL,
  `option_4` varchar(255) DEFAULT NULL,
  `option_5` varchar(255) DEFAULT NULL,
  `correct_answer` varchar(255) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `course_quiz_id` (`course_quiz_id`),
  KEY `idx_course_quiz_question_school_id` (`school_id`),
  CONSTRAINT `course_quiz_question_ibfk_1` FOREIGN KEY (`course_quiz_id`) REFERENCES `online_course_quiz` (`id`),
  CONSTRAINT `fk_course_quiz_question_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `course_quiz_question`');
    }
}
