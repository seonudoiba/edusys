<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Billing pages in the platform sidebar (platform schools): Subscription > Invoices, Coupons,
 * Reports. Also lets platform roles be given "edit" on subscriptions (coupons).
 * New installations get the same rows from the default seed.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Billing_menus extends CI_Migration
{
    private $subs = array(
        array('billing_invoices', 'invoices', 'admin/billing/invoices', 3, 'billing', 'invoices'),
        array('billing_coupons', 'coupons', 'admin/billing/coupons', 4, 'billing', 'coupons,coupon'),
        array('billing_reports', 'reports', 'admin/billing/reports', 5, 'billing', 'reports'),
    );

    public function up()
    {
        $subs = $this->subs;
        $this->db->without_tenant(function ($db) use ($subs) {
            foreach ($db->select('id')->where('is_platform', 1)->get('school')->result() as $school) {
                $sid = (int) $school->id;
                $menu = $db->where(array('school_id' => $sid, 'lang_key' => 'subscription'))->get('sidebar_menus')->row();
                if (!$menu) {
                    continue;
                }
                foreach ($subs as $s) {
                    if ($db->where(array('school_id' => $sid, 'url' => $s[2]))->count_all_results('sidebar_sub_menus') > 0) {
                        continue;
                    }
                    $db->insert('sidebar_sub_menus', array('school_id' => $sid, 'sidebar_menu_id' => $menu->id, 'menu' => $s[0], 'key' => $s[0],
                        'lang_key' => $s[1], 'url' => $s[2], 'level' => $s[3], 'access_permissions' => "('subscription_list', 'can_view')",
                        'permission_group_id' => $menu->permission_group_id, 'activate_controller' => $s[4], 'activate_methods' => $s[5],
                        'addon_permission' => '', 'is_active' => 1));
                }
                $db->where(array('school_id' => $sid, 'short_code' => 'subscription_list'))->update('permission_category', array('enable_edit' => 1));
            }
        });
    }

    public function down()
    {
        $urls = array_column($this->subs, 2);
        $this->db->without_tenant(function ($db) use ($urls) {
            $db->where_in('url', $urls)->delete('sidebar_sub_menus');
        });
    }
}
