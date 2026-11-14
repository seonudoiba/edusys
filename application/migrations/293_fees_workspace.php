<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The fees workspace (controllers/admin/Fees.php): "Overview", "Collections", "Create fees" and "Discounts &
 * balances" at the top of every school's Fees Collection menu (libraries/Fees_menu.php). The classic
 * entries stay, below. No table changes here: the workspace reads and writes the existing fee tables
 * (migration 294 adds the term).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Fees_workspace extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $this->load->library('Fees_menu');
            foreach ($db->select('id')->where('is_platform', 0)->get('school')->result_array() as $school) {
                $this->fees_menu->install((int) $school['id']);
            }
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            $this->load->library('Fees_menu');
            $menus = array(); // "school-menu" => [school_id, sidebar_menu_id, entries removed]
            foreach ($db->select('id, school_id, sidebar_menu_id')->where_in('url', array_keys(Fees_menu::ENTRIES))->get('sidebar_sub_menus')->result_array() as $row) {
                $db->where('id', (int) $row['id'])->delete('sidebar_sub_menus');
                $k = $row['school_id'] . '-' . $row['sidebar_menu_id'];
                $menus[$k] = array((int) $row['school_id'], (int) $row['sidebar_menu_id'], isset($menus[$k]) ? $menus[$k][2] + 1 : 1);
            }
            foreach ($menus as $m) { // the classic entries move back up
                $db->set('level', 'level - ' . $m[2], false)
                    ->where(array('school_id' => $m[0], 'sidebar_menu_id' => $m[1]))
                    ->where('level >', $m[2])->update('sidebar_sub_menus');
            }
        });
    }
}
