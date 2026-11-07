<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admission numbers are always generated (libraries/Student_numbers.php). Every school is switched
 * on, keeping its prefix; schools that never set the format get 4 digits starting at 1. Existing
 * admission numbers don't change.
 */
class Migration_Auto_admission_numbers extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $db->update('sch_settings', array('adm_auto_insert' => 1, 'adm_update_status' => 1));
            $db->where('(adm_no_digit IS NULL OR adm_no_digit = 0)', null, false)->update('sch_settings', array('adm_no_digit' => 4));
            $db->where("(adm_start_from IS NULL OR adm_start_from = '' OR adm_start_from = '0')", null, false)->update('sch_settings', array('adm_start_from' => '1'));
        });
    }

    public function down()
    {
        // Numbers already given stay; schools can't be told apart afterwards, so nothing is undone.
    }
}
