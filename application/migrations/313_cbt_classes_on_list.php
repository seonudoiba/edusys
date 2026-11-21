<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CBT Exams: the class cards are on top of the CBT list (admin/onlineexam), so the separate
 * "CBT Exams by Class" menu entry goes; its class and subject pages light up CBT Exams.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Cbt_classes_on_list extends CI_Migration
{
    public function up()
    {
        $this->load->library('Exam_menus');
        $this->db->without_tenant(function ($db) {
            foreach ($db->select('school_id')->where('lang_key', 'cbse_exam')->get('sidebar_menus')->result_array() as $r) {
                $this->exam_menus->install((int) $r['school_id']);
            }
        });
    }

    public function down()
    {
    }
}
