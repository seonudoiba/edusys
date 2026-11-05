<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `exam_group_exam_connections`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: exam_groups, exam_group_class_batch_exams, school.
 */
class Migration_Create_exam_group_exam_connections extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `exam_group_exam_connections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `exam_group_id` int DEFAULT NULL,
  `exam_group_class_batch_exams_id` int DEFAULT NULL,
  `exam_weightage` float(10,2) DEFAULT '0.00',
  `is_active` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_group_id` (`exam_group_id`),
  KEY `exam_group_class_batch_exams_id` (`exam_group_class_batch_exams_id`),
  KEY `idx_exam_group_exam_connections_school_id` (`school_id`),
  CONSTRAINT `exam_group_exam_connections_ibfk_1` FOREIGN KEY (`exam_group_id`) REFERENCES `exam_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_group_exam_connections_ibfk_2` FOREIGN KEY (`exam_group_class_batch_exams_id`) REFERENCES `exam_group_class_batch_exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_exam_group_exam_connections_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `exam_group_exam_connections`');
    }
}
