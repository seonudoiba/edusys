<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Highlights "Schools" and "Plans" in the platform sidebar on their new pages
 * (a school's page and its edit form; the plan form). New installations get the same values
 * from the default seed.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Platform_menu_highlight extends CI_Migration
{
    private $methods = array(
        'admin/schools' => array('index', 'index,view,edit,save'),
        'admin/plan' => array('index', 'index,create,edit,save'),
    );

    public function up()
    {
        $this->apply(1);
    }

    public function down()
    {
        $this->apply(0);
    }

    private function apply($to)
    {
        $methods = $this->methods;
        $this->db->without_tenant(function ($db) use ($methods, $to) {
            foreach ($methods as $url => $pair) {
                $db->where(array('url' => $url, 'activate_methods' => $pair[1 - $to]))
                    ->update('sidebar_sub_menus', array('activate_methods' => $pair[$to]));
            }
        });
    }
}
