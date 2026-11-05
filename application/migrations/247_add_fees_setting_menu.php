<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Adds "Fees Setting" (schsettings/fees) under System Setting in every school's sidebar, next
 * to General Setting. The page holds the fee options and the offline bank payment instruction
 * (the bank details schools see when they pay a subscription by bank transfer).
 *
 * General Setting stops highlighting on that page (its activate_methods lose "fees").
 * New schools get the same rows from the default seed.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_fees_setting_menu extends CI_Migration
{
    const URL = 'schsettings/fees';
    const GENERAL_WITH_FEES = 'index,logo,miscellaneous,backendtheme,mobileapp,studentguardianpanel,fees,idautogeneration,attendancetype,maintenance';
    const GENERAL_WITHOUT_FEES = 'index,logo,miscellaneous,backendtheme,mobileapp,studentguardianpanel,idautogeneration,attendancetype,maintenance';

    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $general_rows = $db->where('url', 'schsettings')->get('sidebar_sub_menus')->result();
            foreach ($general_rows as $general) {
                $exists = $db->where(array('school_id' => $general->school_id, 'url' => self::URL))
                    ->count_all_results('sidebar_sub_menus') > 0;
                if (!$exists) {
                    $db->insert('sidebar_sub_menus', array(
                        'school_id' => $general->school_id,
                        'sidebar_menu_id' => $general->sidebar_menu_id,
                        'menu' => 'fees_setting',
                        'key' => null,
                        'lang_key' => 'fees_setting',
                        'url' => self::URL,
                        'level' => $general->level,
                        'access_permissions' => $general->access_permissions,
                        'permission_group_id' => $general->permission_group_id,
                        'activate_controller' => 'schsettings',
                        'activate_methods' => 'fees',
                        'addon_permission' => '',
                        'is_active' => 1,
                    ));
                }
            }
            $db->where(array('url' => 'schsettings', 'activate_methods' => self::GENERAL_WITH_FEES))
                ->update('sidebar_sub_menus', array('activate_methods' => self::GENERAL_WITHOUT_FEES));
        });
    }

    public function down()
    {
        $this->db->without_tenant(function ($db) {
            $db->where('url', self::URL)->delete('sidebar_sub_menus');
            $db->where(array('url' => 'schsettings', 'activate_methods' => self::GENERAL_WITHOUT_FEES))
                ->update('sidebar_sub_menus', array('activate_methods' => self::GENERAL_WITH_FEES));
        });
    }
}
