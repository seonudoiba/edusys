<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * "Assign Subject Teachers" (admin/teacher/subject_teachers) in every school's Academics menu, after
 * "Assign Class Teacher" (libraries/Subject_teachers.php; new schools get it from School_provisioner).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Subject_teachers_menu extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function () {
            $this->load->library('Subject_teachers');
            foreach ($this->db->select('id')->get('school')->result_array() as $school) {
                $this->subject_teachers->install_menu((int) $school['id']);
            }
        });
    }

    public function down()
    {
        $this->db->without_tenant(function () {
            $this->db->where('url', 'admin/teacher/subject_teachers')->delete('sidebar_sub_menus');
        });
    }
}
