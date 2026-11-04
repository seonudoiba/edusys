<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_exam_timetable`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: cbse_exams, subjects, school.
 */
class Migration_Create_cbse_exam_timetable extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_exam_timetable` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `cbse_exam_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  `date` date NOT NULL,
  `time_from` time NOT NULL,
  `time_to` time NOT NULL,
  `duration` int NOT NULL,
  `room_no` varchar(255) NOT NULL,
  `is_written` int NOT NULL DEFAULT '1',
  `written_maximum_marks` float NOT NULL,
  `is_practical` int NOT NULL,
  `practical_maximum_mark` float DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cbse_exam_id` (`cbse_exam_id`),
  KEY `subject_id` (`subject_id`),
  KEY `idx_cbse_exam_timetable_school_id` (`school_id`),
  CONSTRAINT `cbse_exam_timetable_ibfk_1` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_exam_timetable_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cbse_exam_timetable_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_exam_timetable`');
    }
}
