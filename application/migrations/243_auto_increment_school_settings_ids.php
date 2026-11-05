<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Makes `id` AUTO_INCREMENT on `sch_settings` and `QR_code_settings`.
 *
 * In the legacy schema both were single-row tables with a fixed id. Every school now has its own
 * row, inserted without an id by School_provisioner, so the id must be generated; otherwise every
 * new school's row gets id 0 and the second school collides.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Auto_increment_school_settings_ids extends CI_Migration
{
    private $tables = array('sch_settings', 'QR_code_settings');

    public function up()
    {
        foreach ($this->tables as $table) {
            // Rows inserted with id 0 before this migration get a real id.
            $max = (int) $this->db->query("SELECT IFNULL(MAX(id), 0) AS m FROM `$table`")->row()->m;
            foreach ($this->db->query("SELECT school_id FROM `$table` WHERE id = 0")->result() as $row) {
                $max++;
                $this->db->query("UPDATE `$table` SET id = ? WHERE id = 0 AND school_id = ?", array($max, $row->school_id));
            }
            $this->db->query("ALTER TABLE `$table` MODIFY `id` int NOT NULL AUTO_INCREMENT");
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            $this->db->query("ALTER TABLE `$table` MODIFY `id` int NOT NULL");
        }
    }
}
