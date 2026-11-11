<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Nigerian defaults for the schools already on the platform (libraries/Nigeria_defaults.php, which new
 * schools get when they are set up). Only what a school has not changed itself is touched:
 *
 *   - ₦ before amounts (schools in naira still showing it after);
 *   - caste, RTE, IFSC, Samagra ID, height, weight and measurement date off on the student form,
 *     unless a student already has a value in the field;
 *   - "State of origin" and "LGA" student fields;
 *   - WAEC (A1-F9) and Primary (A-F) grades where the sample grades were never changed; the sample
 *     marks divisions removed and their menu hidden;
 *   - report-card permissions for Admin and Teacher, where the school gave those roles none;
 *     narrower defaults for deleting students, behaviour settings and the Zoom/Google Meet settings,
 *     where the role still has the original default;
 *   - the sample website of an Indian school, where its pages and posts are still word for word;
 *   - transport fees per term (sch_settings.transport_billing, new column) where no transport fees
 *     are set up yet.
 *
 * Every school: the "CBSE Examination" module is called "Report Cards" in Modules and the panel;
 * Hausa, Yoruba and Igbo are added to the languages (partly translated, the rest in English).
 * The session start month is not changed for existing schools (their sessions are already set up).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Nigerian_defaults extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('transport_billing', 'sch_settings')) {
            $this->db->query("ALTER TABLE `sch_settings` ADD COLUMN `transport_billing` VARCHAR(10) NOT NULL DEFAULT 'month' COMMENT 'term or month: how transport fees are charged'"); // tenancy-lint: ok schema change
        }
        $this->db->without_tenant(function ($db) {
            $this->load->library('Nigeria_defaults');
            foreach ($db->select('id, school_name')->where('is_platform', 0)->get('school')->result_array() as $school) {
                $log = $this->nigeria_defaults->apply((int) $school['id'], false);
                echo 'School ' . (int) $school['id'] . ' (' . $school['school_name'] . '): ' . ($log ? implode('; ', $log) : 'nothing to change') . PHP_EOL;
            }
            foreach (array('permission_group', 'permission_student') as $table) {
                if ($db->table_exists($table)) {
                    $db->where('name', 'CBSE Examination')->update($table, array('name' => 'Report Cards'));
                }
            }
            // Hausa, Yoruba and Igbo (application/language/<name>/): schools can choose them in Languages.
            // Only where the language list is already loaded; a fresh install gets them from the platform seed.
            $has_languages = $db->count_all_results('languages') > 0;
            foreach (array('Hausa' => 'ha', 'Yoruba' => 'yo', 'Igbo' => 'ig') as $name => $code) {
                if ($has_languages && $db->where('language', $name)->count_all_results('languages') === 0) {
                    $db->insert('languages', array('language' => $name, 'short_code' => $code, 'country_code' => 'ng',
                        'is_rtl' => 0, 'is_deleted' => 'no', 'is_active' => 'no'));
                }
            }
        });
    }

    public function down()
    {
        if ($this->db->field_exists('transport_billing', 'sch_settings')) {
            $this->db->query('ALTER TABLE `sch_settings` DROP COLUMN `transport_billing`'); // tenancy-lint: ok schema change
        }
        // Settings and content changes are not reversed (the pre-deploy dump has the previous state).
        $this->db->without_tenant(function ($db) {
            foreach (array('permission_group', 'permission_student') as $table) {
                if ($db->table_exists($table)) {
                    $db->where('name', 'Report Cards')->update($table, array('name' => 'CBSE Examination'));
                }
            }
        });
    }
}
