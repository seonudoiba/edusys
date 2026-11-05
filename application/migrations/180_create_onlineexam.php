<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `onlineexam`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: sessions, school.
 */
class Migration_Create_onlineexam extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `onlineexam` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `session_id` int DEFAULT NULL,
  `exam` text,
  `attempt` int NOT NULL,
  `exam_from` datetime DEFAULT NULL,
  `exam_to` datetime DEFAULT NULL,
  `is_quiz` int NOT NULL DEFAULT '0',
  `auto_publish_date` datetime DEFAULT NULL,
  `time_from` time DEFAULT NULL,
  `time_to` time DEFAULT NULL,
  `duration` time NOT NULL,
  `passing_percentage` float NOT NULL DEFAULT '0',
  `description` text,
  `publish_result` int NOT NULL DEFAULT '0',
  `answer_word_count` int NOT NULL DEFAULT '-1',
  `is_active` varchar(1) DEFAULT '0',
  `is_marks_display` int NOT NULL DEFAULT '0',
  `is_neg_marking` int NOT NULL DEFAULT '0',
  `is_random_question` int NOT NULL DEFAULT '0',
  `is_rank_generated` int NOT NULL DEFAULT '0',
  `publish_exam_notification` int NOT NULL,
  `publish_result_notification` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `idx_onlineexam_school_id` (`school_id`),
  CONSTRAINT `onlineexam_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_onlineexam_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `onlineexam`');
    }
}
