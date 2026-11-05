<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Adds "Bank Transfer Payments" (admin/subscription_bank_transfers) under Subscription in the
 * sidebar of platform schools already set up. New installations get it from the default seed
 * (application/seeds/default/051_sidebar_sub_menus.php, platform-only row).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_bank_transfers_menu extends CI_Migration
{
    const URL = 'admin/subscription_bank_transfers';

    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $menus = $db->select('sidebar_menus.id, sidebar_menus.school_id, sidebar_menus.permission_group_id')
                ->from('sidebar_menus')
                ->join('school', 'school.id = sidebar_menus.school_id')
                ->where('school.is_platform', 1)
                ->where('sidebar_menus.lang_key', 'subscription')
                ->get()->result();
            foreach ($menus as $menu) {
                $exists = $db->where(array('school_id' => $menu->school_id, 'url' => self::URL))
                    ->count_all_results('sidebar_sub_menus') > 0;
                if ($exists) {
                    continue;
                }
                $db->insert('sidebar_sub_menus', array(
                    'school_id' => $menu->school_id,
                    'sidebar_menu_id' => $menu->id,
                    'menu' => 'subscription_bank_transfers',
                    'key' => 'subscription_bank_transfers',
                    'lang_key' => 'bank_transfer_payments',
                    'url' => self::URL,
                    'level' => null,
                    'access_permissions' => "('subscription_list', 'can_view')",
                    'permission_group_id' => $menu->permission_group_id,
                    'activate_controller' => 'subscription_bank_transfers',
                    'activate_methods' => 'index,view',
                    'addon_permission' => null,
                    'is_active' => 1,
                ));
            }
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            $db->where('url', self::URL)->delete('sidebar_sub_menus');
        });
    }
}
