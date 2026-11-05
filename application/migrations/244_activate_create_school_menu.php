<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Shows "Create School" (admin/schools/create) under Schools in the platform sidebar.
 *
 * The submenu row came from the old data switched off. Only the platform school has it
 * (it is a platform-only row of the default seed).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Activate_create_school_menu extends CI_Migration
{
    public function up()
    {
        $this->set_active(1);
    }

    public function down()
    {
        $this->set_active(0);
    }

    private function set_active($active)
    {
        $this->db->without_tenant(function ($db) use ($active) {
            $db->where('url', 'admin/schools/create')->update('sidebar_sub_menus', array('is_active' => $active));
        });
    }
}
