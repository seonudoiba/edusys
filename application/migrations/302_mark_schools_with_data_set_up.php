<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Schools that have data (sch_settings) but were not set up through the platform (imported or
 * moved in) are marked set up (cron_flag = 1), so the platform shows them as running schools:
 * Disable/Enable, Purge, expiry reminders. Purged schools stay as they are.
 */
class Migration_Mark_schools_with_data_set_up extends CI_Migration
{
    public function up()
    {
        $this->db->query("UPDATE school s SET s.cron_flag = 1
            WHERE s.is_platform = 0 AND COALESCE(s.cron_flag, 0) <> 1
              AND COALESCE(s.status_reason, '') <> 'purged'
              AND EXISTS (SELECT 1 FROM sch_settings ss WHERE ss.school_id = s.id)"); // tenancy-lint: ok all schools
    }

    public function down()
    {
    }
}
