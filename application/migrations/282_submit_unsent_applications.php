<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Pressing Submit on the online admission form now submits the application (a form fee is paid
 * after). Applications made before that were left "Not Submitted" until a second step; they are
 * marked submitted, dated the day they were made. Payment status doesn't change.
 */
class Migration_Submit_unsent_applications extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $db->set('submit_date', 'IFNULL(submit_date, DATE(created_at))', false)
                ->set('form_status', 1)
                ->where('form_status', 0)
                ->update('online_admissions');
        });
    }

    public function down()
    {
        // Can't tell these apart from ones submitted the usual way afterwards; nothing is undone.
    }
}
