<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * A student's fee bill: every fee of the session with what was paid, the discount and what is still
 * owed, transport fees included, and how to pay (the school's bank details, online payment).
 * Shown by views/fee_bill.php, for staff (studentfee/bill/<student_session_id>) and for parents and
 * students (user/user/feebill).
 */
class Fee_bill
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model(array('studentfeemaster_model', 'student_model', 'module_model', 'setting_model', 'paymentsetting_model'));
    }

    /** @return array|null the bill, or null when the student session doesn't exist */
    public function build($student_session_id)
    {
        $student = $this->CI->student_model->getByStudentSession((int) $student_session_id);
        if (!$student) {
            return null;
        }
        $rows = array();
        $today = strtotime(date('Y-m-d'));
        foreach ((array) $this->CI->studentfeemaster_model->getStudentFees((int) $student_session_id) as $group) {
            foreach ((array) $group->fees as $f) {
                list($paid, $discount) = $this->deposits($f->amount_detail);
                $due = ($f->due_date && $f->due_date !== '0000-00-00') ? $f->due_date : null;
                $rows[] = array(
                    'group' => (string) $f->name,
                    'type' => (string) $f->type,
                    'due_date' => $due,
                    'overdue' => $due && strtotime($due) < $today,
                    'amount' => (float) $f->amount,
                    'paid' => $paid,
                    'discount' => $discount,
                );
            }
        }
        $transport = $this->CI->module_model->getPermissionByModulename('transport');
        if (!empty($transport['is_active']) && !empty($student['route_pickup_point_id'])) {
            foreach ((array) $this->CI->studentfeemaster_model->getStudentTransportFees((int) $student_session_id, (int) $student['route_pickup_point_id']) as $t) {
                list($paid, $discount) = $this->deposits($t->amount_detail);
                $due = ($t->due_date && $t->due_date !== '0000-00-00') ? $t->due_date : null;
                $label = $this->CI->lang->line(strtolower((string) $t->month));
                $rows[] = array(
                    'group' => $this->CI->lang->line('transport_fees') ?: 'Transport fees',
                    'type' => $label ?: (string) $t->month,
                    'due_date' => $due,
                    'overdue' => $due && strtotime($due) < $today,
                    'amount' => (float) $t->fees,
                    'paid' => $paid,
                    'discount' => $discount,
                );
            }
        }
        $totals = array('amount' => 0.0, 'paid' => 0.0, 'discount' => 0.0, 'balance' => 0.0);
        foreach ($rows as &$r) {
            $r['balance'] = max(0, $r['amount'] - $r['paid'] - $r['discount']);
            foreach ($totals as $k => $v) {
                $totals[$k] += $r[$k];
            }
        }
        unset($r);
        $school = $this->CI->setting_model->get();
        $this->CI->load->library('School_bank_transfer');
        $bank = $this->CI->school_bank_transfer->details();
        $online = $this->CI->paymentsetting_model->getActiveMethod();
        return array(
            'student' => $student,
            'school' => $school ? $school[0] : array(),
            'session' => $this->CI->setting_model->getCurrentSessionName(),
            'rows' => $rows,
            'totals' => $totals,
            'bank' => $this->CI->school_bank_transfer->complete() ? $bank : null,
            'online' => !empty($online),
            'portal' => site_url('site/login'),
        );
    }

    /** [paid, discount] from a fee's payment records (JSON). */
    private function deposits($json)
    {
        $paid = 0.0;
        $discount = 0.0;
        foreach ((array) json_decode((string) $json) as $d) {
            $paid += isset($d->amount) ? (float) $d->amount : 0;
            $discount += isset($d->amount_discount) ? (float) $d->amount_discount : 0;
        }
        return array($paid, $discount);
    }
}
