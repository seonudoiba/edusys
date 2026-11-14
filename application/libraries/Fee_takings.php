<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * End-of-day takings: every fee payment recorded on one day, by the staff member who received it,
 * split by payment mode, so the bursar can reconcile the cash and transfers each collector hands in.
 *
 * Payments live in student_fees_deposite.amount_detail, a JSON object of sub-receipts keyed by inv_no:
 * {"1": {"amount", "amount_discount", "amount_fine", "date", "payment_mode", "received_by", "collected_by", ...}}.
 * A receipt number is "<deposit id>/<inv_no>", as Find a receipt (studentfee/searchpayment) expects.
 * Payments of any session are included: a payment taken today for last session's balance is still today's money.
 */
class Fee_takings
{
    const MODES = array('Cash' => 'Cash', 'bank_transfer' => 'Bank transfer', 'card' => 'Card (POS)', 'Cheque' => 'Cheque', 'DD' => 'Bank draft', 'upi' => 'UPI');

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public static function mode_label($mode)
    {
        $mode = (string) $mode;
        return isset(self::MODES[$mode]) ? self::MODES[$mode] : ($mode === '' ? 'Not recorded' : ucwords(str_replace('_', ' ', $mode)));
    }

    /**
     * The day's payments and their totals.
     * Returns array('rows' => [...], 'collectors' => [key => summary], 'modes' => [label => amount], 'total', 'count').
     */
    public function day($date, $collector = '')
    {
        $db = $this->CI->db;
        $deposits = $db->select('d.id, d.amount_detail, d.student_transport_fee_id, ft.type AS fee_type, g.name AS fee_group,
                COALESCE(m.student_session_id, stf.student_session_id) AS student_session_id', false)
            ->from('student_fees_deposite d')
            ->join('student_fees_master m', 'm.id = d.student_fees_master_id', 'left')
            ->join('fee_groups_feetype gft', 'gft.id = d.fee_groups_feetype_id', 'left')
            ->join('feetype ft', 'ft.id = gft.feetype_id', 'left')
            ->join('fee_groups g', 'g.id = gft.fee_groups_id', 'left')
            ->join('student_transport_fees stf', 'stf.id = d.student_transport_fee_id', 'left')
            ->like('d.amount_detail', '"date":"' . $date . '"')
            ->get()->result_array();

        $rows = array();
        $staff_ids = array();
        $ss_ids = array();
        foreach ($deposits as $d) {
            foreach ((array) json_decode((string) $d['amount_detail'], true) as $inv => $p) {
                if (!is_array($p) || !isset($p['date']) || $p['date'] !== $date) {
                    continue;
                }
                $received_by = isset($p['received_by']) ? (int) $p['received_by'] : 0;
                $rows[] = array(
                    'receipt' => (int) $d['id'] . '/' . (int) (isset($p['inv_no']) ? $p['inv_no'] : $inv),
                    'student_session_id' => (int) $d['student_session_id'],
                    'fee' => $d['student_transport_fee_id'] ? 'Transport' : trim($d['fee_type'] . ($d['fee_group'] ? ' · ' . $d['fee_group'] : '')),
                    'mode' => self::mode_label(isset($p['payment_mode']) ? $p['payment_mode'] : ''),
                    'amount' => (float) (isset($p['amount']) ? $p['amount'] : 0),
                    'fine' => (float) (isset($p['amount_fine']) ? $p['amount_fine'] : 0),
                    'discount' => (float) (isset($p['amount_discount']) ? $p['amount_discount'] : 0),
                    'note' => isset($p['description']) ? (string) $p['description'] : '',
                    'received_by' => $received_by,
                    'collected_by' => isset($p['collected_by']) ? (string) $p['collected_by'] : '',
                );
                if ($received_by) {
                    $staff_ids[$received_by] = true;
                }
                $ss_ids[(int) $d['student_session_id']] = true;
            }
        }

        $staff = array();
        if ($staff_ids) {
            foreach ($db->select('id, name, surname, employee_id')->where_in('id', array_keys($staff_ids))->get('staff')->result_array() as $s) {
                $staff[(int) $s['id']] = trim($s['name'] . ' ' . $s['surname']) . ($s['employee_id'] !== '' ? ' (' . $s['employee_id'] . ')' : '');
            }
        }
        $students = array();
        unset($ss_ids[0]);
        if ($ss_ids) {
            foreach ($db->select('ss.id, st.firstname, st.middlename, st.lastname, st.admission_no, c.class, s.section')
                ->from('student_session ss')->join('students st', 'st.id = ss.student_id')
                ->join('classes c', 'c.id = ss.class_id', 'left')->join('sections s', 's.id = ss.section_id', 'left')
                ->where_in('ss.id', array_keys($ss_ids))->get()->result_array() as $s) {
                $students[(int) $s['id']] = $s;
            }
        }

        $out = array('rows' => array(), 'collectors' => array(), 'modes' => array(), 'total' => 0.0, 'count' => 0);
        foreach ($rows as $r) {
            if ($r['received_by']) {
                $key = 'staff-' . $r['received_by'];
                $name = isset($staff[$r['received_by']]) ? $staff[$r['received_by']] : ($r['collected_by'] !== '' ? $r['collected_by'] : 'Former staff (#' . $r['received_by'] . ')');
            } else {
                $key = 'online';
                $name = 'Paid online';
            }
            $r['collector_key'] = $key;
            $r['collector'] = $name;
            $s = isset($students[$r['student_session_id']]) ? $students[$r['student_session_id']] : null;
            $r['student'] = $s ? $s : array('firstname' => 'Unknown student', 'middlename' => '', 'lastname' => '', 'admission_no' => '', 'class' => '', 'section' => '');
            $r['paid'] = $r['amount'] + $r['fine'];

            if (!isset($out['collectors'][$key])) {
                $out['collectors'][$key] = array('name' => $name, 'total' => 0.0, 'count' => 0, 'modes' => array());
            }
            $c = &$out['collectors'][$key];
            $c['total'] += $r['paid'];
            $c['count']++;
            $c['modes'][$r['mode']] = (isset($c['modes'][$r['mode']]) ? $c['modes'][$r['mode']] : 0) + $r['paid'];
            unset($c);

            if ($collector !== '' && $collector !== $key) {
                continue;
            }
            $out['rows'][] = $r;
            $out['modes'][$r['mode']] = (isset($out['modes'][$r['mode']]) ? $out['modes'][$r['mode']] : 0) + $r['paid'];
            $out['total'] += $r['paid'];
            $out['count']++;
        }
        uasort($out['collectors'], function ($a, $b) { return $b['total'] <=> $a['total']; });
        arsort($out['modes']);
        usort($out['rows'], function ($a, $b) { return array($a['collector'], array_map('intval', explode('/', $a['receipt']))) <=> array($b['collector'], array_map('intval', explode('/', $b['receipt']))); });
        return $out;
    }
}
