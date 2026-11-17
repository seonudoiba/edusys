<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The platform admin's permissions, menus and Platform Manager role (finer permissions: approve schools,
 * enable/disable, sign in as a school, modules, domains, notes, data, a school's billing, bank transfers,
 * pricing, invoices, coupons, reports, emails, support, enquiries). Roles that had the broad permission
 * get the matching finer ones. Plan > Plan opens the pricing bands.
 *
 * Run by migrations 299/300 for an existing platform and by School_provisioner when the platform school
 * is set up. Running it again changes nothing. Call inside without_tenant.
 */
class Platform_admin_setup
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** short_code => [group short_code, name, view, add, edit, delete] */
    const CATEGORIES = array(
        'school_approve' => array('school_information', 'Approve new schools', 1, 0, 1, 0),
        'school_status' => array('school_information', 'Enable and disable schools', 1, 0, 1, 0),
        'school_impersonate' => array('school_information', 'Sign in to a school as its super admin', 1, 0, 0, 0),
        'school_modules' => array('school_information', 'Switch a school\'s modules on and off', 1, 0, 1, 0),
        'school_domains' => array('school_information', 'School web addresses and domains', 1, 1, 1, 1),
        'school_notes' => array('school_information', 'Notes on schools', 1, 1, 0, 1),
        'school_data' => array('school_information', 'Export (view) and purge (delete) a school\'s data', 1, 0, 0, 1),
        'school_billing' => array('school_information', 'A school\'s subscription: extend it and give discounts', 1, 1, 1, 1),
        'subscription_transfers' => array('subscription', 'Bank transfers: view (and approve with edit)', 1, 0, 1, 0),
        'pricing_bands' => array('subscription', 'Pricing bands', 1, 0, 1, 0),
        'platform_invoices' => array('subscription', 'Invoices', 1, 0, 1, 0),
        'platform_coupons' => array('subscription', 'Coupons', 1, 1, 1, 1),
        'billing_reports' => array('subscription', 'Billing reports', 1, 0, 0, 0),
        'email_templates' => array('platform_admin', 'Email templates and email log', 1, 0, 1, 0),
        'support_desk' => array('platform_admin', 'Support desk (edit = reply)', 1, 0, 1, 0),
        'platform_enquiries' => array('platform_admin', 'Website enquiries and quotes', 1, 0, 1, 1),
    );

    /** Roles that had [old code, flag] get [new code => flags view,add,edit,delete]. */
    const CARRY = array(
        array('schools', 'can_edit', array('school_approve' => array(1, 0, 1, 0), 'school_status' => array(1, 0, 1, 0), 'school_impersonate' => array(1, 0, 0, 0),
            'school_modules' => array(1, 0, 1, 0), 'school_domains' => array(1, 1, 1, 1), 'school_notes' => array(1, 1, 0, 1), 'school_data' => array(1, 0, 0, 0),
            'school_billing' => array(1, 1, 1, 1))),
        array('schools', 'can_delete', array('school_data' => array(1, 0, 0, 1))),
        array('subscription_list', 'can_view', array('subscription_transfers' => array(1, 0, 1, 0), 'pricing_bands' => array(1, 0, 0, 0),
            'platform_invoices' => array(1, 0, 1, 0), 'platform_coupons' => array(1, 0, 0, 0), 'billing_reports' => array(1, 0, 0, 0))),
        array('subscription_list', 'can_edit', array('pricing_bands' => array(1, 0, 1, 0), 'platform_coupons' => array(1, 1, 1, 1))),
        array('platform_settings', 'can_view', array('email_templates' => array(1, 0, 0, 0), 'support_desk' => array(1, 0, 0, 0), 'platform_enquiries' => array(1, 0, 0, 0))),
        array('platform_settings', 'can_edit', array('email_templates' => array(1, 0, 1, 0), 'support_desk' => array(1, 0, 1, 0), 'platform_enquiries' => array(1, 0, 1, 0))),
        array('platform_settings', 'can_delete', array('platform_enquiries' => array(1, 0, 0, 1))),
    );

    /** sidebar_sub_menus url => access_permissions */
    const SUBMENUS = array(
        'admin/schools?tab=waiting_approval' => "('schools', 'can_view') || ('school_approve', 'can_view')",
        'admin/subscription_bank_transfers' => "('subscription_transfers', 'can_view')",
        'admin/billing/bands' => "('pricing_bands', 'can_view')",
        'admin/billing/invoices' => "('platform_invoices', 'can_view')",
        'admin/billing/coupons' => "('platform_coupons', 'can_view')",
        'admin/billing/reports' => "('billing_reports', 'can_view')",
        'admin/platform/emails' => "('email_templates', 'can_view')",
        'admin/platform/email_log' => "('email_templates', 'can_view')",
        'admin/support_desk' => "('support_desk', 'can_view')",
        'admin/enquiries' => "('platform_enquiries', 'can_view')",
    );

    /** sidebar_menus lang_key => access_permissions */
    const MENUS = array(
        'subscription' => "('subscription', 'can_view') || ('subscription_list', 'can_view') || ('subscription_transfers', 'can_view') || ('platform_invoices', 'can_view') || ('platform_coupons', 'can_view') || ('billing_reports', 'can_view')",
        'platform_communication' => "('platform_settings', 'can_view') || ('email_templates', 'can_view')",
        'platform_support' => "('platform_settings', 'can_view') || ('support_desk', 'can_view') || ('platform_enquiries', 'can_view')",
    );

    const MANAGER = 'Platform Manager';

    public function install($sid)
    {
        $db = $this->CI->db;
        // Plan > Plan opens the pricing bands (per-student pricing); Plan Type List stays.
        $db->where(array('school_id' => $sid, 'lang_key' => 'plan'))->update('sidebar_menus', array('sidebar_display' => 1,
            'access_permissions' => "('pricing_bands', 'can_view') || ('plan_type', 'can_view') || ('plan_list', 'can_view')"));
        $db->where(array('school_id' => $sid, 'url' => 'admin/plan'))->update('sidebar_sub_menus', array('url' => 'admin/billing/bands',
            'access_permissions' => "('pricing_bands', 'can_view')", 'activate_controller' => 'billing', 'activate_methods' => 'bands'));
        // Pricing Bands is no longer repeated under Subscription List (Plan > Plan opens it).
        $db->where(array('school_id' => $sid, 'lang_key' => 'pricing_bands', 'url' => 'admin/billing/bands'))->delete('sidebar_sub_menus');

        $groups = array();
        foreach ($db->where('school_id', $sid)->where_in('short_code', array('school_information', 'subscription', 'platform_admin'))->get('permission_group')->result_array() as $g) {
            $groups[$g['short_code']] = (int) $g['id'];
        }
        $cat = array();
        foreach (self::CATEGORIES as $code => $c) {
            if (!isset($groups[$c[0]])) {
                continue;
            }
            $row = $db->where(array('school_id' => $sid, 'short_code' => $code))->get('permission_category')->row_array();
            if (!$row) {
                $db->insert('permission_category', array('school_id' => $sid, 'perm_group_id' => $groups[$c[0]], 'name' => $c[1], 'short_code' => $code,
                    'enable_view' => $c[2], 'enable_add' => $c[3], 'enable_edit' => $c[4], 'enable_delete' => $c[5]));
                $cat[$code] = (int) $db->insert_id();
            } else {
                $cat[$code] = (int) $row['id'];
            }
        }
        $db->where(array('school_id' => $sid, 'short_code' => 'platform_settings'))->update('permission_category', array('name' => 'Platform settings, health and backups'));
        $db->where(array('school_id' => $sid, 'short_code' => 'subscription_list'))->update('permission_category', array('name' => 'Subscriptions list'));

        // Carry over what roles could do.
        $old = array();
        foreach ($db->select('id, short_code')->where('school_id', $sid)->where_in('short_code', array('schools', 'subscription_list', 'platform_settings'))->get('permission_category')->result_array() as $r) {
            $old[$r['short_code']] = (int) $r['id'];
        }
        $grants = array(); // role => code => flags
        foreach (self::CARRY as $rule) {
            list($code, $flag, $gives) = $rule;
            if (!isset($old[$code])) {
                continue;
            }
            foreach ($db->select('role_id')->where(array('school_id' => $sid, 'perm_cat_id' => $old[$code], $flag => 1))->get('roles_permissions')->result_array() as $rp) {
                foreach ($gives as $new => $flags) {
                    $have = isset($grants[$rp['role_id']][$new]) ? $grants[$rp['role_id']][$new] : array(0, 0, 0, 0);
                    foreach ($flags as $i => $f) {
                        $have[$i] = max($have[$i], $f);
                    }
                    $grants[$rp['role_id']][$new] = $have;
                }
            }
        }
        foreach ($grants as $role_id => $codes) {
            foreach ($codes as $code => $f) {
                if (isset($cat[$code])) {
                    $this->grant($sid, (int) $role_id, $cat[$code], $f);
                }
            }
        }

        foreach (self::SUBMENUS as $url => $perm) {
            $db->where(array('school_id' => $sid, 'url' => $url))->update('sidebar_sub_menus', array('access_permissions' => $perm));
        }
        foreach (self::MENUS as $key => $perm) {
            $db->where(array('school_id' => $sid, 'lang_key' => $key))->update('sidebar_menus', array('access_permissions' => $perm));
        }

        // Platform Manager: everything on the platform except roles and permissions, and purging data.
        $role = $db->where(array('school_id' => $sid, 'name' => self::MANAGER))->get('roles')->row_array();
        if (!$role) {
            $db->insert('roles', array('school_id' => $sid, 'name' => self::MANAGER, 'slug' => 'platform-manager', 'is_active' => 0, 'is_system' => 1, 'is_superadmin' => 0));
            $role_id = (int) $db->insert_id();
            $groups_on = array_column($db->select('id')->where(array('school_id' => $sid, 'is_active' => 1))->get('permission_group')->result_array(), 'id');
            if ($groups_on) {
                foreach ($db->where('school_id', $sid)->where_in('perm_group_id', $groups_on)->get('permission_category')->result_array() as $c) {
                    if ($c['short_code'] === 'superadmin') {
                        continue;
                    }
                    $this->grant($sid, $role_id, (int) $c['id'], array((int) $c['enable_view'], (int) $c['enable_add'], (int) $c['enable_edit'],
                        $c['short_code'] === 'school_data' ? 0 : (int) $c['enable_delete']));
                }
            }
        }
    }

    /** Permission groups that are all about running the platform. */
    const PLATFORM_GROUPS = array('school_information', 'plan_list', 'subscription', 'platform_admin');

    /** Permissions the platform uses that no sidebar item names (Platform team pages). */
    const PLATFORM_EXTRA = array('disable_staff', 'can_see_other_users_profile');

    /**
     * The permission short codes that matter on the platform: the platform groups, what the platform's
     * shown sidebar menus and items ask for, and PLATFORM_EXTRA. Roles and permissions on the platform
     * lists only these (the school modules stay in the database, unlisted and unchanged).
     */
    public function platform_permission_codes($sid)
    {
        $db = $this->CI->db;
        $codes = array_fill_keys(self::PLATFORM_EXTRA, true);
        $rules = array();
        // The items' rules (a menu's own rule lists everything it ever held, so it's used only for a menu without items).
        foreach ($db->select('id, access_permissions')->where(array('school_id' => $sid, 'sidebar_display' => 1))->get('sidebar_menus')->result_array() as $m) {
            $items = $db->select('access_permissions')->where(array('school_id' => $sid, 'sidebar_menu_id' => (int) $m['id'], 'is_active' => 1))->get('sidebar_sub_menus')->result_array();
            if (!$items) {
                $rules[] = $m['access_permissions'];
            }
            foreach ($items as $s) {
                $rules[] = $s['access_permissions'];
            }
        }
        foreach ($rules as $rule) {
            if (preg_match_all("/\\(\\s*'([a-z0-9_]+)'\\s*,/", (string) $rule, $m)) {
                foreach ($m[1] as $code) {
                    $codes[$code] = true;
                }
            }
        }
        $groups = array_column($db->select('id')->where('school_id', $sid)->where_in('short_code', self::PLATFORM_GROUPS)->get('permission_group')->result_array(), 'id');
        if ($groups) {
            foreach ($db->select('short_code')->where('school_id', $sid)->where_in('perm_group_id', $groups)->get('permission_category')->result_array() as $c) {
                $codes[$c['short_code']] = true;
            }
        }
        return $codes;
    }

    private function grant($sid, $role_id, $cat_id, array $f)
    {
        if (!array_sum($f)) {
            return;
        }
        $row = $this->CI->db->where(array('school_id' => $sid, 'role_id' => $role_id, 'perm_cat_id' => $cat_id))->get('roles_permissions')->row_array();
        $vals = array('can_view' => $f[0], 'can_add' => $f[1], 'can_edit' => $f[2], 'can_delete' => $f[3]);
        if ($row) {
            foreach ($vals as $k => $v) {
                $vals[$k] = max($v, (int) $row[$k]);
            }
            $this->CI->db->where('id', (int) $row['id'])->update('roles_permissions', $vals);
        } else {
            $this->CI->db->insert('roles_permissions', $vals + array('school_id' => $sid, 'role_id' => $role_id, 'perm_cat_id' => $cat_id));
        }
    }

}
