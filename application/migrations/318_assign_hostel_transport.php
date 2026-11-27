<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Transport > Assign Students and Hostel > Assign Students (admin/Studentplaces) for every school
 * (School_tidy; new schools get them from School_provisioner).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Assign_hostel_transport extends CI_Migration
{
    public function up()
    {
        $this->load->library('School_tidy');
        $this->db->without_tenant(function ($db) {
            foreach ($db->select('id')->get('school')->result_array() as $s) {
                $this->school_tidy->install((int) $s['id']);
            }
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            $db->where_in('url', array('admin/studentplaces/transport', 'admin/studentplaces/hostel'))->delete('sidebar_sub_menus');
        });
    }
}
