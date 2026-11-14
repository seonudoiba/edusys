<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The End-of-day takings page (admin/fees/takings) keeps the Collections menu entry highlighted:
 * refreshes the fees workspace entries' activate_methods from libraries/Fees_menu.php.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Fee_takings_menu extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function () {
            $this->load->library('Fees_menu');
            $this->fees_menu->refresh();
        });
    }

    public function down()
    {
    }
}
