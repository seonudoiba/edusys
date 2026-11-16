<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Platform sidebar: the Plan menu is shown again (299 hid it), and its "Plan" item opens the pricing
 * bands (admin/billing/bands) instead of the old plans page. Plan Type List stays.
 * Runs libraries/Platform_admin_setup.php again, which also covers platforms set up after 299.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Platform_plan_menu extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $this->load->library('Platform_admin_setup');
            foreach ($db->select('id')->where('is_platform', 1)->get('school')->result_array() as $school) {
                $this->platform_admin_setup->install((int) $school['id']);
            }
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            foreach ($db->select('id')->where('is_platform', 1)->get('school')->result_array() as $school) {
                $db->where(array('school_id' => (int) $school['id'], 'url' => 'admin/billing/bands', 'lang_key' => 'plan'))
                    ->update('sidebar_sub_menus', array('url' => 'admin/plan', 'access_permissions' => "('plan_list', 'can_view')", 'activate_controller' => 'plan', 'activate_methods' => 'index,create,edit,save'));
            }
        });
    }
}
