<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Report cards by class (libraries/Class_results.php): a class's exam for a term (cbse_exams.class_id),
 * its default score breakdown (default_type_ids) and next-term date; a subject with its own
 * breakdown (cbse_exam_timetable.custom_breakdown). Sidebar: Report Cards > Classes & Results and
 * Comment Bank; older exam screens hidden (libraries/Exam_menus.php).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Class_results extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('class_id', 'cbse_exams')) {
            $this->db->query("ALTER TABLE `cbse_exams` ADD `class_id` int DEFAULT NULL, ADD `default_type_ids` varchar(255) DEFAULT NULL, ADD `next_term_begins` date DEFAULT NULL, ADD KEY `class_term` (`school_id`, `class_id`, `cbse_term_id`)"); // tenancy-lint: ok schema change
        }
        if (!$this->db->field_exists('custom_breakdown', 'cbse_exam_timetable')) {
            $this->db->query("ALTER TABLE `cbse_exam_timetable` ADD `custom_breakdown` tinyint(1) NOT NULL DEFAULT 0"); // tenancy-lint: ok schema change
        }
        $this->db->without_tenant(function ($db) {
            $this->load->library('Exam_menus');
            foreach ($db->select('school_id')->where('lang_key', 'cbse_exam')->get('sidebar_menus')->result_array() as $r) {
                $this->exam_menus->install((int) $r['school_id']);
            }
        });
    }

    public function down()
    {
        if ($this->db->field_exists('class_id', 'cbse_exams')) {
            $this->db->query('ALTER TABLE `cbse_exams` DROP KEY `class_term`, DROP `class_id`, DROP `default_type_ids`, DROP `next_term_begins`'); // tenancy-lint: ok schema change
        }
        if ($this->db->field_exists('custom_breakdown', 'cbse_exam_timetable')) {
            $this->db->query('ALTER TABLE `cbse_exam_timetable` DROP `custom_breakdown`'); // tenancy-lint: ok schema change
        }
        $this->load->library('Exam_menus');
        $this->db->without_tenant(function ($db) {
            $db->where_in('url', array('cbseexam/results', 'cbseexam/exam/comments'))->delete('sidebar_sub_menus');
            $db->where_in('url', Exam_menus::HIDDEN)->update('sidebar_sub_menus', array('is_active' => 1));
        });
    }
}
