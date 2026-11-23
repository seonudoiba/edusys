<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * "Pay all dues" in the student and parent portal (user/user/getfees, user/gateway/payment/payall):
 * every fee of a student's session still owing (class fees and transport), with its balance and
 * any late fine, oldest due date first; and how a part payment is spread over them (the oldest
 * dues are cleared first, each fee's fine before its amount).
 *
 * The balances are worked out as user/gateway/payment/grouppay does for "Pay Selected".
 */
class Portal_dues
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model(array('studentfeemaster_model', 'module_model'));
    }

    /**
     * @return array fee records (the shape grouppay sends to the gateways), owing only, oldest first:
     *   fee_category, student_transport_fee_id, fee_groups_feetype_id, student_fees_master_id,
     *   fee_group_name, fee_type_code, is_system, amount_balance, fine_balance, due_date
     */
    public function owing($student_session_id, $route_pickup_point_id = null)
    {
        $rows = array();
        foreach ((array) $this->CI->studentfeemaster_model->getStudentFees($student_session_id) as $group) {
            foreach ($group->fees as $f) {
                $rows[] = $this->fee(array('fee_category' => 'fees', 'student_transport_fee_id' => 0,
                    'fee_groups_feetype_id' => $f->fee_groups_feetype_id, 'student_fees_master_id' => $f->id));
            }
        }
        $module = $this->CI->module_model->getPermissionByModulename('transport');
        if (!empty($module['is_active'])) {
            foreach ((array) $this->CI->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id) as $t) {
                $rows[] = $this->fee(array('fee_category' => 'transport', 'student_transport_fee_id' => $t->id,
                    'fee_groups_feetype_id' => 0, 'student_fees_master_id' => 0));
            }
        }
        $rows = array_values(array_filter($rows, function ($r) {
            return $r && ($r['amount_balance'] + $r['fine_balance']) > 0.004;
        }));
        usort($rows, function ($a, $b) {
            $da = $a['due_date'] ?: '9999-12-31';
            $db = $b['due_date'] ?: '9999-12-31';
            return $da === $db ? 0 : ($da < $db ? -1 : 1);
        });
        return $rows;
    }

    /** Total owed: [amount, fine]. */
    public static function total(array $rows)
    {
        $amount = $fine = 0.0;
        foreach ($rows as $r) {
            $amount += $r['amount_balance'];
            $fine += $r['fine_balance'];
        }
        return array(round($amount, 2), round($fine, 2));
    }

    /**
     * Spreads $pay over the dues, oldest first (each fee's fine, then its amount). Fees it does not
     * reach are left out. @return array the fee records with amount_balance / fine_balance set to
     * what this payment covers
     */
    public static function allocate(array $rows, $pay)
    {
        $left = round((float) $pay, 2);
        $out = array();
        foreach ($rows as $r) {
            if ($left <= 0.004) {
                break;
            }
            $fine = min($left, $r['fine_balance']);
            $left = round($left - $fine, 2);
            $amount = min($left, $r['amount_balance']);
            $left = round($left - $amount, 2);
            if ($fine + $amount > 0.004) {
                $r['fine_balance'] = round($fine, 2);
                $r['amount_balance'] = round($amount, 2);
                $out[] = $r;
            }
        }
        return $out;
    }

    /** One fee's balance and fine (null when the fee is not found). */
    private function fee(array $rec)
    {
        $paid = $discount = $fine_paid = 0.0;
        $fine_due = 0.0;
        $today = strtotime(date('Y-m-d'));
        if ($rec['fee_category'] === 'transport') {
            $r = $this->CI->studentfeemaster_model->studentTRansportDeposit($rec['student_transport_fee_id']);
            if (!$r) {
                return null;
            }
            $rec['fee_group_name'] = $this->CI->lang->line('transport_fees');
            $rec['fee_type_code'] = $r->month;
            $rec['is_system'] = 0;
            list($paid, $discount, $fine_paid) = $this->deposits($r->amount_detail);
            $balance = $r->fees - ($paid + $discount);
            if ($this->overdue($r->due_date, $today) && $balance > 0) {
                $fine_due = is_null($r->fine_percentage) ? (float) $r->fine_amount : (float) percentageAmount($r->fees, $r->fine_percentage);
            }
        } else {
            $r = $this->CI->studentfeemaster_model->studentDeposit($rec);
            if (!$r) {
                return null;
            }
            $rec['fee_group_name'] = $r->fee_group_name;
            $rec['fee_type_code'] = $r->fee_type_code;
            $rec['is_system'] = $r->is_system;
            list($paid, $discount, $fine_paid) = $this->deposits($r->amount_detail);
            $balance = ($r->is_system ? $r->student_fees_master_amount : $r->amount) - ($paid + $discount);
            if ($this->overdue($r->due_date, $today) && $balance > 0) {
                $fine_due = (float) $r->fine_amount;
            }
        }
        $rec['amount_balance'] = round(max(0, (float) $balance), 2);
        $rec['fine_balance'] = round(max(0, $fine_due - $fine_paid), 2);
        $rec['due_date'] = ($r->due_date && $r->due_date !== '0000-00-00') ? $r->due_date : null;
        return $rec;
    }

    private function deposits($json)
    {
        $paid = $discount = $fine = 0.0;
        $d = json_decode((string) $json);
        if (is_object($d)) {
            foreach ($d as $x) {
                $paid += (float) $x->amount;
                $discount += (float) $x->amount_discount;
                $fine += (float) $x->amount_fine;
            }
        }
        return array($paid, $discount, $fine);
    }

    private function overdue($due, $today)
    {
        return $due && $due !== '0000-00-00' && strtotime($due) < $today;
    }
}
