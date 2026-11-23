<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Every school (libraries/School_tidy.php): a shorter Add Staff form, no Visitor Book or Apply Leave
 * in the student and parent portal, and Behaviour > Report Behaviour. The fees menu's "Collect
 * Fees" is now "Record Payments" (libraries/Fees_menu.php).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_School_tidy extends CI_Migration
{
    public function up()
    {
        $this->load->library('School_tidy');
        $this->load->library('Fees_menu');
        $this->db->without_tenant(function ($db) {
            foreach ($db->select('id')->get('school')->result_array() as $s) {
                $this->school_tidy->install((int) $s['id']);
            }
            $this->fees_menu->refresh();
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            $db->where('url', 'behaviour/log')->delete('sidebar_sub_menus');
        });
    }
}
