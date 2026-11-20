<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CBT Exams > CBT Exams by Class (admin/cbtexams): class cards → subjects → each subject's CBTs by
 * term. The full list stays as "All CBTs". Added by libraries/Exam_menus.php.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Cbt_by_class extends CI_Migration
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
        $this->db->without_tenant(function ($db) {
            $db->where('url', 'admin/cbtexams')->delete('sidebar_sub_menus');
        });
    }
}
