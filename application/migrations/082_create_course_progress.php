<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `course_progress`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: online_courses, students, online_course_section, school.
 */
class Migration_Create_course_progress extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `course_progress` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `guest_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `course_section_id` int DEFAULT NULL,
  `lesson_quiz_id` int DEFAULT NULL,
  `lesson_quiz_type` int DEFAULT NULL COMMENT '1 lesson, 2 quiz',
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `student_id` (`student_id`),
  KEY `course_section_id` (`course_section_id`),
  KEY `idx_course_progress_school_id` (`school_id`),
  CONSTRAINT `course_progress_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `online_courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_progress_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_progress_ibfk_3` FOREIGN KEY (`course_section_id`) REFERENCES `online_course_section` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_course_progress_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `course_progress`');
    }
}
