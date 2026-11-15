<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Caste is not used by the schools on the platform: the field is switched off for every school
 * (student form, profile, reports, the online admission form) and off by default for new ones.
 * Its switch is gone from System Fields and from the admission form settings. Values already saved
 * in students.cast stay in the table, but are no longer shown anywhere.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Remove_caste extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function () {
            $this->db->query('UPDATE sch_settings SET cast = 0'); // tenancy-lint: ok (every school)
            $this->db->query('ALTER TABLE sch_settings ALTER COLUMN cast SET DEFAULT 0'); // tenancy-lint: ok
            $this->db->query("UPDATE online_admission_fields SET status = 0 WHERE name = 'cast'"); // tenancy-lint: ok (every school)
        });
    }

    public function down()
    {
        $this->db->without_tenant(function () {
            $this->db->query('ALTER TABLE sch_settings ALTER COLUMN cast SET DEFAULT 1'); // tenancy-lint: ok
        });
    }
}
