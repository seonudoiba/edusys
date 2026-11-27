<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Hostel and room on Add/Edit Student, for every school (sch_settings.hostel_id; new schools get it
 * from School_tidy). System Settings > System Fields can still switch them off.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Student_hostel_fields extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $db->update('sch_settings', array('hostel_id' => 1));
        });
    }

    public function down()
    {
    }
}
