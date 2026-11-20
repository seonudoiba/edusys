<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The fees workspace in a school's sidebar: "Overview", "Collections", "Create fees" and "Discounts &
 * balances" at the top of Fees Collection (the classic entries stay below them). Migrations 293 and 294
 * for existing schools, School_provisioner for new ones. Running it again changes nothing.
 */
class Fees_menu
{
    const ENTRIES = array(
        // url => [label, permission, activate_methods (also pages by address: "studentfee/addfee"), lang_key]
        'admin/fees' => array('Overview', "('collect_fees', 'can_view')", 'index,charge,edit,remove,remind'),
        'admin/fees/collections' => array('Collect Fees', "('collect_fees', 'can_view')", 'collections,followup,takings,studentfee/index,studentfee/addfee,studentfee/getProcessingfees', 'collect_fees'),
        'admin/fees/create' => array('Create fees', "('fees_master', 'can_add')", 'create,repeat'),
        'admin/fees/adjust' => array('Discounts & balances', "('fees_discount', 'can_view')", 'adjust,discounts,carryforward,catchup,admin/feediscount', 'fees_discounts_balances'),
    );

    /** Classic entries the workspace took over (Collect Fees, Fees Discount): hidden from the sidebar. */
    const RETIRED = array('studentfee', 'admin/feediscount');

    /** Brings existing entries up to date (pages added since they were installed) and hides retired ones. */
    public function refresh()
    {
        $db = get_instance()->db;
        foreach (self::ENTRIES as $url => $e) {
            $set = array('activate_methods' => $e[2]);
            if (isset($e[3])) {
                $set += array('lang_key' => $e[3], 'menu' => $e[0]);
            }
            $db->where('url', $url)->where('activate_controller', 'fees')->update('sidebar_sub_menus', $set);
        }
        $db->where_in('url', self::RETIRED)->update('sidebar_sub_menus', array('is_active' => 0));
    }

    /** Call inside without_tenant (or with the school as tenant). */
    public function install($school_id)
    {
        $CI = &get_instance();
        $db = $CI->db;
        $menu = $db->select('id, permission_group_id')->where(array('school_id' => (int) $school_id, 'lang_key' => 'fees_collection'))->get('sidebar_menus')->row();
        if (!$menu) {
            return 0;
        }
        $added = 0;
        $missing = array();
        foreach (self::ENTRIES as $url => $e) {
            if ($db->where(array('school_id' => (int) $school_id, 'url' => $url))->count_all_results('sidebar_sub_menus') === 0) {
                $missing[$url] = $e;
            }
        }
        if (!$missing) {
            return 0;
        }
        // Make room at the top: the classic entries move down, in the same order.
        $db->set('level', 'level + ' . count($missing), false)
            ->where(array('school_id' => (int) $school_id, 'sidebar_menu_id' => (int) $menu->id))
            ->where_not_in('url', array_keys(self::ENTRIES))->where('level >=', count(self::ENTRIES) - count($missing) + 1)->update('sidebar_sub_menus');
        $level = 1;
        foreach (self::ENTRIES as $url => $e) {
            if (isset($missing[$url])) {
                $key = isset($e[3]) ? $e[3] : 'fees_' . preg_replace('/[^a-z]+/', '_', strtolower($e[0]));
                $db->insert('sidebar_sub_menus', array(
                    'school_id' => (int) $school_id,
                    'sidebar_menu_id' => (int) $menu->id,
                    'menu' => $e[0],
                    'key' => $key,
                    'lang_key' => $key,
                    'url' => $url,
                    'level' => $level,
                    'access_permissions' => $e[1],
                    'permission_group_id' => $menu->permission_group_id,
                    'activate_controller' => 'fees',
                    'activate_methods' => $e[2],
                    'addon_permission' => null,
                    'is_active' => 1,
                ));
                $added++;
            }
            $level++;
        }
        $db->where('school_id', (int) $school_id)->where_in('url', self::RETIRED)->update('sidebar_sub_menus', array('is_active' => 0));
        return $added;
    }
}
