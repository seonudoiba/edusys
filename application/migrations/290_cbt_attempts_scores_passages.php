<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CBT (Online Examination) rework: libraries/Cbt_attempt.php and libraries/Cbt_scoring.php.
 *
 *   onlineexam_attempts      started_at, ends_at (the server's deadline), submitted_at, seed (the
 *                            question and option order, so a resumed attempt looks the same)
 *   onlineexam_students      the stored score: score, max_score, neg_score, correct/wrong/unanswered
 *                            counts, pending (written answers not marked yet), percentage, passed
 *   onlineexam               is_random_option (shuffle A-E per student), marks_target (the report-card
 *                            mark the scores were last sent to: exam_group_class_batch_exam_subjects.id)
 *   onlineexam_student_results  duplicate answers (a resubmitted form added a second copy) are
 *                            removed, keeping the newest; then one answer per question per student
 *   question_passages        a reading passage shared by several questions (comprehension);
 *                            questions.passage_id
 *
 * School-owned table (school_id): question_passages. References: school, subjects.
 */
class Migration_Cbt_attempts_scores_passages extends CI_Migration
{
    public function up()
    {
        $add = function ($table, $column, $definition) {
            if (!$this->db->field_exists($column, $table)) {
                $this->db->query("ALTER TABLE `$table` ADD COLUMN `$column` $definition"); // tenancy-lint: ok schema change
            }
        };
        $add('onlineexam_attempts', 'started_at', 'DATETIME NULL');
        $add('onlineexam_attempts', 'ends_at', 'DATETIME NULL');
        $add('onlineexam_attempts', 'submitted_at', 'DATETIME NULL');
        $add('onlineexam_attempts', 'seed', 'INT NOT NULL DEFAULT 0');

        $add('onlineexam_students', 'score', 'DECIMAL(10,2) NULL');
        $add('onlineexam_students', 'max_score', 'DECIMAL(10,2) NULL');
        $add('onlineexam_students', 'neg_score', 'DECIMAL(10,2) NULL');
        $add('onlineexam_students', 'correct_count', 'INT NULL');
        $add('onlineexam_students', 'wrong_count', 'INT NULL');
        $add('onlineexam_students', 'unanswered_count', 'INT NULL');
        $add('onlineexam_students', 'pending_count', 'INT NULL COMMENT \'written answers not marked yet\'');
        $add('onlineexam_students', 'percentage', 'DECIMAL(6,2) NULL');
        $add('onlineexam_students', 'passed', 'TINYINT(1) NULL');
        $add('onlineexam_students', 'scored_at', 'DATETIME NULL');

        $add('onlineexam', 'is_random_option', 'TINYINT(1) NOT NULL DEFAULT 0');
        $add('onlineexam', 'marks_target', 'INT NULL COMMENT \'exam_group_class_batch_exam_subjects.id the scores were last sent to\'');

        // A written answer's mark is 0 until a teacher marks it: marked_at tells "not marked" from "0".
        $add('onlineexam_student_results', 'marked_at', 'DATETIME NULL');
        $this->db->query("UPDATE `onlineexam_student_results` r JOIN `onlineexam_questions` q ON q.id = r.onlineexam_question_id JOIN `questions` qs ON qs.id = q.question_id" // tenancy-lint: ok whole table, all schools
            . " SET r.marked_at = COALESCE(r.updated_at, r.created_at) WHERE qs.question_type = 'descriptive' AND (r.marks <> 0 OR (r.remark IS NOT NULL AND r.remark <> '')) AND r.marked_at IS NULL");

        // One answer per question per student: keep the newest of any duplicates first.
        $this->db->query('DELETE r1 FROM `onlineexam_student_results` r1 JOIN `onlineexam_student_results` r2' // tenancy-lint: ok whole table, all schools
            . ' ON r1.onlineexam_student_id = r2.onlineexam_student_id AND r1.onlineexam_question_id = r2.onlineexam_question_id AND r1.id < r2.id');
        $index = $this->db->query("SHOW INDEX FROM `onlineexam_student_results` WHERE Key_name = 'uq_onlineexam_result_question'")->row(); // tenancy-lint: ok schema
        if (!$index) {
            $this->db->query('ALTER TABLE `onlineexam_student_results` ADD UNIQUE KEY `uq_onlineexam_result_question` (`onlineexam_student_id`, `onlineexam_question_id`)'); // tenancy-lint: ok schema change
        }

        if (!$this->db->table_exists('question_passages')) {
            $this->db->query(<<<'SQL'
CREATE TABLE `question_passages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `subject_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `body` mediumtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_question_passages_school` (`school_id`, `subject_id`),
  CONSTRAINT `fk_question_passages_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );
        }
        $add('questions', 'passage_id', 'INT NULL');
    }

    public function down()
    {
        $drop = function ($table, $column) {
            if ($this->db->field_exists($column, $table)) {
                $this->db->query("ALTER TABLE `$table` DROP COLUMN `$column`"); // tenancy-lint: ok schema change
            }
        };
        $drop('questions', 'passage_id');
        $this->db->query('DROP TABLE IF EXISTS `question_passages`');
        if ($this->db->query("SHOW INDEX FROM `onlineexam_student_results` WHERE Key_name = 'uq_onlineexam_result_question'")->row()) { // tenancy-lint: ok schema
            $this->db->query('ALTER TABLE `onlineexam_student_results` DROP INDEX `uq_onlineexam_result_question`'); // tenancy-lint: ok schema change
        }
        $drop('onlineexam_student_results', 'marked_at');
        foreach (array('is_random_option', 'marks_target') as $c) {
            $drop('onlineexam', $c);
        }
        foreach (array('score', 'max_score', 'neg_score', 'correct_count', 'wrong_count', 'unanswered_count', 'pending_count', 'percentage', 'passed', 'scored_at') as $c) {
            $drop('onlineexam_students', $c);
        }
        foreach (array('started_at', 'ends_at', 'submitted_at', 'seed') as $c) {
            $drop('onlineexam_attempts', $c);
        }
    }
}
