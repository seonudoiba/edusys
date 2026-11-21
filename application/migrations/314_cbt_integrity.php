<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CBT integrity settings (libraries/Cbt_attempt.php, Cbt_integrity.php):
 *
 *   onlineexam              lock_device (an attempt stays on the device that started it),
 *                           questions_per_student (0 = all; else each student's own random set,
 *                           in proportion to the easy / medium / hard questions of the exam),
 *                           require_fullscreen, exam_pin (read out by the invigilator),
 *                           allowed_ips (the school's network: addresses, prefixes or CIDR),
 *                           webcam (photos during the exam), webcam_minutes (how often)
 *   onlineexam_attempts     device_key, ip, user_agent (where the attempt was taken)
 *   onlineexam_students     question_ids (the latest attempt's own set: onlineexam_questions.id JSON)
 *   onlineexam_snapshots    webcam photos of an attempt (school-owned)
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Cbt_integrity extends CI_Migration
{
    public function up()
    {
        $add = function ($table, $column, $definition) {
            if (!$this->db->field_exists($column, $table)) {
                $this->db->query("ALTER TABLE `$table` ADD COLUMN `$column` $definition"); // tenancy-lint: ok schema change
            }
        };
        $add('onlineexam', 'lock_device', 'TINYINT(1) NOT NULL DEFAULT 0');
        $add('onlineexam', 'questions_per_student', 'INT NOT NULL DEFAULT 0');
        $add('onlineexam', 'require_fullscreen', 'TINYINT(1) NOT NULL DEFAULT 0');
        $add('onlineexam', 'exam_pin', 'VARCHAR(12) NULL');
        $add('onlineexam', 'allowed_ips', 'VARCHAR(500) NULL');
        $add('onlineexam', 'webcam', 'TINYINT(1) NOT NULL DEFAULT 0');
        $add('onlineexam', 'webcam_minutes', 'INT NOT NULL DEFAULT 3');

        $add('onlineexam_attempts', 'device_key', 'CHAR(64) NULL');
        $add('onlineexam_attempts', 'ip', 'VARCHAR(45) NULL');
        $add('onlineexam_attempts', 'user_agent', 'VARCHAR(255) NULL');

        $add('onlineexam_students', 'question_ids', 'TEXT NULL');

        if (!$this->db->table_exists('onlineexam_snapshots')) {
            $this->db->query(<<<'SQL'
CREATE TABLE `onlineexam_snapshots` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `onlineexam_student_id` int NOT NULL,
  `attempt_id` int NOT NULL,
  `file` varchar(80) NOT NULL,
  `reason` varchar(20) NOT NULL DEFAULT 'interval',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_onlineexam_snapshots_student` (`school_id`, `onlineexam_student_id`),
  CONSTRAINT `fk_onlineexam_snapshots_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );
        }
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `onlineexam_snapshots`');
        $drop = function ($table, $column) {
            if ($this->db->field_exists($column, $table)) {
                $this->db->query("ALTER TABLE `$table` DROP COLUMN `$column`"); // tenancy-lint: ok schema change
            }
        };
        $drop('onlineexam_students', 'question_ids');
        foreach (array('device_key', 'ip', 'user_agent') as $c) {
            $drop('onlineexam_attempts', $c);
        }
        foreach (array('lock_device', 'questions_per_student', 'require_fullscreen', 'exam_pin', 'allowed_ips', 'webcam', 'webcam_minutes') as $c) {
            $drop('onlineexam', $c);
        }
    }
}
