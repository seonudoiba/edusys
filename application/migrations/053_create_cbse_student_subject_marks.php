<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_student_subject_marks`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: cbse_exam_timetable_assessment_types, cbse_exam_timetable, cbse_exam_students, cbse_exam_assessment_types, school.
 */
class Migration_Create_cbse_student_subject_marks extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_student_subject_marks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `cbse_exam_timetable_assessment_type_id` int NOT NULL,
  `cbse_exam_timetable_id` int DEFAULT NULL,
  `cbse_exam_student_id` int DEFAULT NULL,
  `cbse_exam_assessment_type_id` int DEFAULT NULL,
  `is_absent` int NOT NULL DEFAULT '0',
  `marks` float(10,2) DEFAULT '0.00',
  `note` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cbse_exam_timetable_id` (`cbse_exam_timetable_id`),
  KEY `cbse_exam_student_id` (`cbse_exam_student_id`),
  KEY `cbse_exam_assessment_type_id` (`cbse_exam_assessment_type_id`),
  KEY `cbse_exam_timetable_assessment_type_ibfk_4` (`cbse_exam_timetable_assessment_type_id`),
  KEY `idx_cbse_student_subject_marks_school_id` (`school_id`),
  CONSTRAINT `cbse_exam_timetable_assessment_type_ibfk_4` FOREIGN KEY (`cbse_exam_timetable_assessment_type_id`) REFERENCES `cbse_exam_timetable_assessment_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_student_subject_marks_ibfk_1` FOREIGN KEY (`cbse_exam_timetable_id`) REFERENCES `cbse_exam_timetable` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_student_subject_marks_ibfk_2` FOREIGN KEY (`cbse_exam_student_id`) REFERENCES `cbse_exam_students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_student_subject_marks_ibfk_3` FOREIGN KEY (`cbse_exam_assessment_type_id`) REFERENCES `cbse_exam_assessment_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cbse_student_subject_marks_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_student_subject_marks`');
    }
}
