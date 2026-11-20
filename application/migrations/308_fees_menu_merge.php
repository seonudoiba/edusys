<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Fees sidebar: one "Collect Fees" (the workspace page, which now also finds students by class and
 * section; the classic Collect Fees page sends there) and one "Discounts & balances" (the classic Fees
 * Discount page sends to its Discounts). The classic entries are hidden; their pages keep the merged
 * entry lit. New schools get the same from School_provisioner (Fees_menu::install).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Fees_menu_merge extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $this->load->library('Fees_menu');
            $this->fees_menu->refresh();
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            $db->where_in('url', array('studentfee', 'admin/feediscount'))->update('sidebar_sub_menus', array('is_active' => 1));
            $db->where('url', 'admin/fees/collections')->update('sidebar_sub_menus', array('lang_key' => 'fees_collections', 'menu' => 'Collections', 'activate_methods' => 'collections,followup,takings'));
        });
    }
}
