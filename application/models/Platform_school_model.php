<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Platform view of schools (platform admin only): one row per school with its latest
 * subscription, computed state and expiry, plus the details shown on a school's page.
 *
 * Expiry follows the owner's Plan page (user/Plan.php): the latest paid subscription's
 * created_at + days.
 *
 * State of a school (state()):
 *   awaiting_payment  registered, not paid yet
 *   waiting_approval  paid (or added by the platform), not set up yet
 *   active            set up and enabled
 *   disabled          set up and disabled
 */
class Platform_school_model extends CI_Model
{
    const EXPIRING_DAYS = 30;

    /** @return array[] every school (not the platform school), newest first, with computed fields */
    public function all()
    {
        $schools = $this->db->select('school.*')
            ->from('school')
            ->where('school.is_platform', 0)
            ->order_by('school.id', 'desc')
            ->get()->result_array();
        if (!$schools) {
            return array();
        }
        $ids = array_column($schools, 'id');
        $latest = $this->latest_subscriptions($ids);
        $paid = $this->latest_subscriptions($ids, true);
        foreach ($schools as &$s) {
            $this->decorate($s, isset($latest[$s['id']]) ? $latest[$s['id']] : null, isset($paid[$s['id']]) ? $paid[$s['id']] : null);
        }
        return $schools;
    }

    /** One school (any, including the platform school) with computed fields, or null. */
    public function get($id)
    {
        $s = $this->db->where('id', (int) $id)->get('school')->row_array();
        if (!$s) {
            return null;
        }
        $latest = $this->latest_subscriptions(array($s['id']));
        $paid = $this->latest_subscriptions(array($s['id']), true);
        $this->decorate($s, isset($latest[$s['id']]) ? $latest[$s['id']] : null, isset($paid[$s['id']]) ? $paid[$s['id']] : null);
        return $s;
    }

    /** Count of schools per state, plus expiring and expired (for tabs and the dashboard). */
    public function counts(array $schools)
    {
        $c = array('all' => count($schools), 'awaiting_payment' => 0, 'waiting_approval' => 0, 'active' => 0, 'disabled' => 0, 'expiring' => 0, 'expired' => 0, 'onboarding' => 0);
        foreach ($schools as $s) {
            $c[$s['state']]++;
            if ($s['expiry'] === 'expiring') {
                $c['expiring']++;
            } elseif ($s['expiry'] === 'expired') {
                $c['expired']++;
            } elseif ($s['expiry'] === 'onboarding' || $s['expiry'] === 'unpaid') {
                $c['onboarding']++;
            }
        }
        return $c;
    }

    /** All subscriptions of a school, newest first, with plan titles. */
    public function subscriptions($school_id)
    {
        return $this->db->select('subscription.*, plan.title AS plan_title')
            ->from('subscription')
            ->join('plan', 'plan.plan_id = subscription.plan_id', 'left')
            ->where('subscription.school_id', (int) $school_id)
            ->order_by('subscription.id', 'desc')
            ->get()->result_array();
    }

    /** Bank transfers of a school's subscriptions, newest first. */
    public function bank_transfers($school_id)
    {
        return $this->db->select('t.id, t.status, t.amount, t.payment_date, t.reference, t.submitted_at, t.reviewed_at, s.total')
            ->from('subscription_bank_transfers t')
            ->join('subscription s', 's.id = t.subscription_id')
            ->where('s.school_id', (int) $school_id)
            ->order_by('t.id', 'desc')
            ->get()->result_array();
    }

    /** Usage figures of a set-up school (read across the tenant filter, platform only). */
    public function usage($school_id)
    {
        $school_id = (int) $school_id;
        return $this->db->without_tenant(function ($db) use ($school_id) {
            $count = function ($table, array $where = array()) use ($db, $school_id) {
                return (int) $db->where(array_merge(array('school_id' => $school_id), $where))->count_all_results($table);
            };
            $last = $db->select_max('login_datetime', 'last')->where('school_id', $school_id)->get('userlog')->row();
            $since = date('Y-m-d H:i:s', strtotime('-30 days'));
            return array(
                'students' => $count('students', array('is_active' => 'yes')),
                'staff' => $count('staff', array('is_active' => 1)),
                'classes' => $count('classes'),
                'sections' => $count('sections'),
                'logins_30_days' => (int) $db->where('school_id', $school_id)->where('login_datetime >=', $since)->count_all_results('userlog'),
                'last_login' => $last ? $last->last : null,
            );
        });
    }

    /** The school's super admins (role 7). */
    public function super_admins($school_id)
    {
        $school_id = (int) $school_id;
        return $this->db->without_tenant(function ($db) use ($school_id) {
            return $db->select('staff.id, staff.name, staff.surname, staff.email, staff.is_active, staff.verification_code')
                ->from('staff')
                ->join('staff_roles', 'staff_roles.staff_id = staff.id AND staff_roles.school_id = staff.school_id')
                ->where('staff.school_id', $school_id)
                ->where('staff_roles.role_id', 7)
                ->order_by('staff.id')
                ->get()->result_array();
        });
    }

    /** The school's modules (permission groups), without the platform's own. */
    public function modules($school_id)
    {
        $school_id = (int) $school_id;
        return $this->db->without_tenant(function ($db) use ($school_id) {
            return $db->select('id, name, short_code, is_active, system')
                ->where('school_id', $school_id)
                ->where_not_in('short_code', array('school_information', 'plan_list', 'subscription', 'platform_admin'))
                ->order_by('name')
                ->get('permission_group')->result_array();
        });
    }

    /** Latest logins of a school. */
    public function recent_logins($school_id, $limit = 8)
    {
        $school_id = (int) $school_id;
        return $this->db->without_tenant(function ($db) use ($school_id, $limit) {
            return $db->select('user, role, ipaddress, login_datetime')
                ->where('school_id', $school_id)
                ->order_by('login_datetime', 'desc')
                ->limit((int) $limit)
                ->get('userlog')->result_array();
        });
    }

    /** Paid revenue per month for the last $months months (oldest first): ['Y-m' => total]. */
    public function monthly_revenue($months = 6)
    {
        $out = array();
        for ($i = $months - 1; $i >= 0; $i--) {
            $out[date('Y-m', strtotime("first day of -$i month"))] = 0.0;
        }
        $from = array_key_first($out) . '-01 00:00:00';
        $rows = $this->db->select("DATE_FORMAT(created_at, '%Y-%m') AS ym, SUM(total) AS total", false)
            ->where('payment_status', 1)
            ->where('created_at >=', $from)
            ->group_by('ym')
            ->get('subscription')->result_array();
        foreach ($rows as $r) {
            if (isset($out[$r['ym']])) {
                $out[$r['ym']] = (float) $r['total'];
            }
        }
        return $out;
    }

    public function total_revenue()
    {
        $row = $this->db->select_sum('total')->where('payment_status', 1)->get('subscription')->row();
        return $row ? (float) $row->total : 0.0;
    }

    /** Latest subscription per school ([school_id => row]), optionally only paid ones. */
    private function latest_subscriptions(array $ids, $paid_only = false)
    {
        if (!$ids) {
            return array();
        }
        $this->db->select('subscription.*, plan.title AS plan_title')
            ->from('subscription')
            ->join('plan', 'plan.plan_id = subscription.plan_id', 'left')
            ->where_in('subscription.school_id', array_map('intval', $ids))
            ->order_by('subscription.id', 'desc');
        if ($paid_only) {
            $this->db->where('subscription.payment_status', 1);
        }
        $out = array();
        foreach ($this->db->get()->result_array() as $row) {
            if (!isset($out[$row['school_id']])) {
                $out[$row['school_id']] = $row;
            }
        }
        return $out;
    }

    private function decorate(array &$s, $latest, $paid)
    {
        $s['set_up'] = (int) $s['cron_flag'] === 1;
        // A school with data that wasn't set up through the platform (imported, moved in) is running too.
        if (!$s['set_up'] && empty($s['is_platform']) && (string) ($s['status_reason'] ?? '') !== 'purged') {
            $id = (int) $s['id'];
            $s['set_up'] = $this->db->without_tenant(function ($db) use ($id) {
                return $db->where('school_id', $id)->count_all_results('sch_settings') > 0;
            });
        }
        $s['enabled'] = (int) $s['status'] === 1;
        $s['plan_title'] = $latest ? $latest['plan_title'] : null;
        $s['latest_subscription'] = $latest;
        $s['paid_subscription'] = $paid;
        $s['expires_at'] = null;
        $s['days_left'] = null;
        $s['expiry'] = 'none';
        if (!$paid && !empty($s['pay_before_portal']) && empty($s['is_platform'])) {
            $s['expiry'] = 'unpaid'; // added by platform staff, nothing paid: staff only (Plan_limits)
        } elseif ($paid && $paid['kind'] === 'setup') {
            $s['expiry'] = 'onboarding'; // setup fee paid, subscription not yet: no end date
        } elseif ($paid) {
            $expires = strtotime($paid['created_at']) + ((int) $paid['days']) * 86400;
            $s['expires_at'] = date('Y-m-d', $expires);
            $s['days_left'] = (int) floor(($expires - time()) / 86400);
            $s['expiry'] = $s['days_left'] < 0 ? 'expired' : ($s['days_left'] <= self::EXPIRING_DAYS ? 'expiring' : 'ok');
        }
        if (!empty($s['is_platform'])) {
            $s['state'] = 'active';
        } elseif (isset($s['status_reason']) && $s['status_reason'] === 'purged') {
            $s['state'] = 'disabled'; // data deleted (Schools > a school > Data > Purge); kept for the accounts
        } elseif ($s['set_up']) {
            $s['state'] = $s['enabled'] ? 'active' : 'disabled';
        } else {
            $s['state'] = ((int) $s['payment_status'] === 1 || !$latest) ? 'waiting_approval' : 'awaiting_payment';
        }
    }
}
