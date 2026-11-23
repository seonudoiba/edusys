<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Add Staff no longer asks for the date of birth (Edit Staff has it, optional), so staff.dob may be
 * empty; and the "PAN Number" staff field of the original sample data (an Indian tax number) goes.
 * Numbers already typed into it stay in custom_field_values (the deploy also backs up the database).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Staff_form_trim extends CI_Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE `staff` MODIFY `dob` DATE NULL'); // tenancy-lint: ok schema change
        $this->db->without_tenant(function ($db) {
            $db->where('belong_to', 'staff')->where('LOWER(name)', 'pan number')->delete('custom_fields');
        });
    }

    public function down()
    {
    }
}
