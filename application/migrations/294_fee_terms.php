<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Fees by term: fee_session_groups.term ('first', 'second', 'third' or NULL), set by Create fees and on a
 * charge's page, used by the Overview's term filter. Existing charges get a term when their fee group's
 * name says so ("First Term fees", "2nd term levy", ...). Also adds "Discounts & balances" to the fees
 * workspace menu (libraries/Fees_menu.php) where migration 293 ran before it existed.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Fee_terms extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            if (!$db->field_exists('term', 'fee_session_groups')) {
                $db->query("ALTER TABLE `fee_session_groups` ADD `term` VARCHAR(10) NULL DEFAULT NULL AFTER `session_id`"); // tenancy-lint: ok schema change
            }
            $patterns = array('first' => '(first|1st)', 'second' => '(second|2nd)', 'third' => '(third|3rd)');
            foreach ($patterns as $term => $re) {
                $db->query('UPDATE `fee_session_groups` f JOIN `fee_groups` g ON g.id = f.fee_groups_id' // tenancy-lint: ok whole table, all schools
                    . ' SET f.term = ? WHERE f.term IS NULL AND g.is_system = 0 AND LOWER(g.name) REGEXP ?', array($term, $re . '[[:space:]]*term'));
            }
            $this->load->library('Fees_menu');
            foreach ($db->select('id')->where('is_platform', 0)->get('school')->result_array() as $school) {
                $this->fees_menu->install((int) $school['id']);
            }
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            if ($db->field_exists('term', 'fee_session_groups')) {
                $db->query('ALTER TABLE `fee_session_groups` DROP `term`'); // tenancy-lint: ok schema change
            }
        });
    }
}
