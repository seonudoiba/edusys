<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Front website pages can now be switched off (Front CMS > Pages: a hidden page gives the 404
 * page and leaves the menus), using front_cms_pages.is_active ('yes' = on). The column was never
 * used before and held 'no' everywhere, so this switches every existing page on: what schools
 * see today doesn't change. New schools start with only their home page on (default seed).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Front_pages_enabled_flag extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $db->where('is_active <>', 'yes')->update('front_cms_pages', array('is_active' => 'yes'));
        });
    }

    public function down()
    {
        // The flag had no effect before; nothing to restore.
    }
}
