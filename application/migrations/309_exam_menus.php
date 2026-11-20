<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Sidebar: the exam area in three menus named for what they do (libraries/Exam_menus.php): Exams
 * (setting up exams, entering scores), Report Cards (designs, printing, results) and CBT Exams.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Exam_menus extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $this->load->library('Exam_menus');
            foreach ($db->select('school_id')->where('lang_key', 'cbse_exam')->get('sidebar_menus')->result_array() as $r) {
                $this->exam_menus->install((int) $r['school_id']);
            }
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            foreach ($db->where('lang_key', 'school_exams')->get('sidebar_menus')->result_array() as $m) {
                $rc = $db->where(array('school_id' => (int) $m['school_id'], 'lang_key' => 'cbse_exam'))->get('sidebar_menus')->row_array();
                if ($rc) {
                    $db->where('sidebar_menu_id', (int) $m['id'])->update('sidebar_sub_menus', array('sidebar_menu_id' => (int) $rc['id']));
                }
                $db->where('id', (int) $m['id'])->delete('sidebar_menus');
            }
        });
    }
}
