<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Exams, CBT and report cards (Nigerian school workflow):
 *  - cbse_exam_timetable.marks_status: draft → submitted (teacher sends a subject's scores for
 *    review) → approved (locked; an exam manager unlocks it with a reason). Who and when.
 *  - cbse_marks_audit: every score change (who, when, before and after, from entry / import / CBT)
 *    and every status change of a subject's scores (submit, approve, unlock, return).
 *  - cbse_comment_bank: teacher and principal comments by average score, for "Fill comments".
 *  - result_pins: scratch-card PINs for the public result checker (admission no + PIN).
 *  - onlineexam.max_tab_switches (0 = off) and per attempt: tab_switches, extra_minutes (+ reason, by).
 *  - cbse_promotion_rules / cbse_promotion_decisions: promotion rules and the recorded decisions.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Exam_workflow extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('marks_status', 'cbse_exam_timetable')) {
            $this->db->query("ALTER TABLE `cbse_exam_timetable` ADD `marks_status` varchar(12) NOT NULL DEFAULT 'draft',
                ADD `marks_submitted_by` int DEFAULT NULL, ADD `marks_submitted_at` datetime DEFAULT NULL,
                ADD `marks_approved_by` int DEFAULT NULL, ADD `marks_approved_at` datetime DEFAULT NULL"); // tenancy-lint: ok schema change
        }
        if (!$this->db->table_exists('cbse_marks_audit')) {
            $this->db->query("CREATE TABLE `cbse_marks_audit` (
                `id` int NOT NULL AUTO_INCREMENT,
                `school_id` int NOT NULL,
                `cbse_exam_id` int DEFAULT NULL,
                `cbse_exam_timetable_id` int DEFAULT NULL,
                `cbse_exam_student_id` int DEFAULT NULL,
                `cbse_exam_assessment_type_id` int DEFAULT NULL,
                `action` varchar(12) NOT NULL DEFAULT 'marks',
                `source` varchar(12) NOT NULL DEFAULT 'entry',
                `old_marks` decimal(10,2) DEFAULT NULL,
                `new_marks` decimal(10,2) DEFAULT NULL,
                `old_absent` tinyint(1) DEFAULT NULL,
                `new_absent` tinyint(1) DEFAULT NULL,
                `note` varchar(255) DEFAULT NULL,
                `staff_id` int DEFAULT NULL,
                `created_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                KEY `school_tt` (`school_id`, `cbse_exam_timetable_id`),
                KEY `school_exam` (`school_id`, `cbse_exam_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"); // tenancy-lint: ok schema change
        }
        if (!$this->db->table_exists('cbse_comment_bank')) {
            $this->db->query("CREATE TABLE `cbse_comment_bank` (
                `id` int NOT NULL AUTO_INCREMENT,
                `school_id` int NOT NULL,
                `type` varchar(10) NOT NULL DEFAULT 'teacher',
                `min_percentage` decimal(5,2) NOT NULL DEFAULT 0,
                `max_percentage` decimal(5,2) NOT NULL DEFAULT 100,
                `comment` varchar(500) NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `school_id` (`school_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"); // tenancy-lint: ok schema change
        }
        if (!$this->db->table_exists('result_pins')) {
            $this->db->query("CREATE TABLE `result_pins` (
                `id` int NOT NULL AUTO_INCREMENT,
                `school_id` int NOT NULL,
                `batch` varchar(40) DEFAULT NULL,
                `serial_no` varchar(20) NOT NULL,
                `pin` varchar(20) NOT NULL,
                `session_id` int DEFAULT NULL,
                `cbse_template_id` int DEFAULT NULL,
                `max_uses` int NOT NULL DEFAULT 5,
                `used_count` int NOT NULL DEFAULT 0,
                `student_session_id` int DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_by` int DEFAULT NULL,
                `created_at` datetime NOT NULL,
                `first_used_at` datetime DEFAULT NULL,
                `last_used_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `school_pin` (`school_id`, `pin`),
                KEY `school_batch` (`school_id`, `batch`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"); // tenancy-lint: ok schema change
        }
        if (!$this->db->field_exists('max_tab_switches', 'onlineexam')) {
            $this->db->query("ALTER TABLE `onlineexam` ADD `max_tab_switches` int NOT NULL DEFAULT 0, ADD `block_copy` tinyint(1) NOT NULL DEFAULT 1"); // tenancy-lint: ok schema change
        }
        if (!$this->db->field_exists('tab_switches', 'onlineexam_attempts')) {
            $this->db->query("ALTER TABLE `onlineexam_attempts` ADD `tab_switches` int NOT NULL DEFAULT 0, ADD `extra_minutes` int NOT NULL DEFAULT 0,
                ADD `extra_reason` varchar(255) DEFAULT NULL, ADD `extra_by` int DEFAULT NULL, ADD `forced_submit` varchar(20) DEFAULT NULL"); // tenancy-lint: ok schema change
        }
        if (!$this->db->table_exists('cbse_promotion_rules')) {
            $this->db->query("CREATE TABLE `cbse_promotion_rules` (
                `id` int NOT NULL AUTO_INCREMENT,
                `school_id` int NOT NULL,
                `class_id` int DEFAULT NULL,
                `min_average` decimal(5,2) NOT NULL DEFAULT 40,
                `core_subject_ids` varchar(255) DEFAULT NULL,
                `core_pass_mark` decimal(5,2) NOT NULL DEFAULT 40,
                `min_attendance` decimal(5,2) NOT NULL DEFAULT 0,
                `borderline_margin` decimal(5,2) NOT NULL DEFAULT 5,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `school_class` (`school_id`, `class_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"); // tenancy-lint: ok schema change
        }
        if (!$this->db->table_exists('cbse_promotion_decisions')) {
            $this->db->query("CREATE TABLE `cbse_promotion_decisions` (
                `id` int NOT NULL AUTO_INCREMENT,
                `school_id` int NOT NULL,
                `cbse_template_id` int NOT NULL,
                `student_session_id` int NOT NULL,
                `decision` varchar(12) NOT NULL,
                `reason` varchar(500) DEFAULT NULL,
                `average` decimal(6,2) DEFAULT NULL,
                `decided_by` int DEFAULT NULL,
                `decided_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `school_tpl_student` (`school_id`, `cbse_template_id`, `student_session_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"); // tenancy-lint: ok schema change
        }
    }

    public function down()
    {
        foreach (array('cbse_marks_audit', 'cbse_comment_bank', 'result_pins', 'cbse_promotion_rules', 'cbse_promotion_decisions') as $t) {
            if ($this->db->table_exists($t)) {
                $this->db->query('DROP TABLE `' . $t . '`'); // tenancy-lint: ok schema change
            }
        }
        if ($this->db->field_exists('marks_status', 'cbse_exam_timetable')) {
            $this->db->query('ALTER TABLE `cbse_exam_timetable` DROP `marks_status`, DROP `marks_submitted_by`, DROP `marks_submitted_at`, DROP `marks_approved_by`, DROP `marks_approved_at`'); // tenancy-lint: ok schema change
        }
        if ($this->db->field_exists('max_tab_switches', 'onlineexam')) {
            $this->db->query('ALTER TABLE `onlineexam` DROP `max_tab_switches`, DROP `block_copy`'); // tenancy-lint: ok schema change
        }
        if ($this->db->field_exists('tab_switches', 'onlineexam_attempts')) {
            $this->db->query('ALTER TABLE `onlineexam_attempts` DROP `tab_switches`, DROP `extra_minutes`, DROP `extra_reason`, DROP `extra_by`, DROP `forced_submit`'); // tenancy-lint: ok schema change
        }
    }
}
