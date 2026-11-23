<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Income List and Expense List (admin/income/incomesearch, admin/expense/expensesearch): every
 * record of the school, newest first, for a period, an income/expense head or words in the name,
 * invoice number or description, with the total and the total of each head.
 *
 * Income also lists, automatically and read-only, the money the school receives through its own
 * payments: fee payments (student_fees_deposite: class and transport fees, at the desk or online),
 * admission form fees (online_admission_payment) and online course payments (online_course_payment).
 * They are read from those records each time, so a reverted payment drops out and nothing is
 * copied twice (the Transaction report adds fees to income by itself).
 */
class Money_list
{
    const KINDS = array(
        'income' => array('table' => 'income', 'head_table' => 'income_head', 'head_id' => 'income_head_id', 'head_name' => 'income_category', 'docs' => 'school_income'),
        'expense' => array('table' => 'expenses', 'head_table' => 'expense_head', 'head_id' => 'exp_head_id', 'head_name' => 'exp_category', 'docs' => 'school_expense'),
    );

    /** Where income comes from: typed in here, or recorded automatically by the payments themselves. */
    const SOURCES = array('manual' => 'Recorded here', 'fees' => 'School fees', 'admission' => 'Admission forms', 'course' => 'Online courses');

    const PERIODS = array('all' => 'All time', 'today' => 'Today', 'this_week' => 'This week', 'this_month' => 'This month', 'last_month' => 'Last month', 'this_year' => 'This year', 'custom' => 'Between dates');

    /** @return array [from, to] (Y-m-d or null) for a period. */
    public static function range($period, $from = '', $to = '')
    {
        $today = date('Y-m-d');
        switch ($period) {
            case 'today':
                return array($today, $today);
            case 'this_week':
                return array(date('Y-m-d', strtotime('monday this week')), $today);
            case 'this_month':
                return array(date('Y-m-01'), $today);
            case 'last_month':
                return array(date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month')));
            case 'this_year':
                return array(date('Y-01-01'), $today);
            case 'custom':
                $ok = function ($d) {
                    return preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $d) && strtotime($d) ? $d : null;
                };
                return array($ok($from), $ok($to));
        }
        return array(null, null);
    }

    /** The heads (categories) of a kind: id => name. */
    public function heads($kind)
    {
        $k = self::KINDS[$kind];
        $out = array();
        foreach (get_instance()->db->select('id, ' . $k['head_name'] . ' AS name')->order_by($k['head_name'])->get($k['head_table'])->result_array() as $h) {
            $out[(int) $h['id']] = $h['name'];
        }
        return $out;
    }

    /**
     * @param array $f period, from, to, head_id, q
     * @return array rows (id, date, name, invoice_no, amount, documents, note, head_id, head)
     */
    public function rows($kind, array $f)
    {
        $source = isset($f['source']) ? (string) $f['source'] : '';
        $rows = ($kind !== 'income' || $source === '' || $source === 'manual') ? $this->recorded($kind, $f) : array();
        if ($kind === 'income' && empty($f['head_id'])) {
            list($from, $to) = self::range(isset($f['period']) ? $f['period'] : 'all', isset($f['from']) ? $f['from'] : '', isset($f['to']) ? $f['to'] : '');
            $auto = array();
            if ($source === '' || $source === 'fees') {
                $auto = array_merge($auto, $this->fee_payments($from, $to));
            }
            if ($source === '' || $source === 'admission') {
                $auto = array_merge($auto, $this->admission_payments($from, $to));
            }
            if ($source === '' || $source === 'course') {
                $auto = array_merge($auto, $this->course_payments($from, $to));
            }
            $q = isset($f['q']) ? mb_strtolower(trim((string) $f['q'])) : '';
            foreach ($auto as $r) {
                if ($q === '' || mb_strpos(mb_strtolower($r['name'] . ' ' . $r['invoice_no'] . ' ' . $r['note']), $q) !== false) {
                    $rows[] = $r;
                }
            }
            usort($rows, function ($a, $b) {
                return strcmp((string) $b['date'], (string) $a['date']);
            });
        }
        return $rows;
    }

    /** Income or expense typed in on Record Income / Record Expense. */
    private function recorded($kind, array $f)
    {
        $k = self::KINDS[$kind];
        $db = get_instance()->db;
        list($from, $to) = self::range(isset($f['period']) ? $f['period'] : 'all', isset($f['from']) ? $f['from'] : '', isset($f['to']) ? $f['to'] : '');
        $db->select('m.id, m.date, m.name, m.invoice_no, m.amount, m.documents, m.note, m.' . $k['head_id'] . ' AS head_id, h.' . $k['head_name'] . ' AS head', false)
            ->from($k['table'] . ' m')
            ->join($k['head_table'] . ' h', 'h.id = m.' . $k['head_id'], 'left');
        if ($from) {
            $db->where('m.date >=', $from);
        }
        if ($to) {
            $db->where('m.date <=', $to);
        }
        if (!empty($f['head_id'])) {
            $db->where('m.' . $k['head_id'], (int) $f['head_id']);
        }
        $q = isset($f['q']) ? trim((string) $f['q']) : '';
        if ($q !== '') {
            $db->group_start()->like('m.name', $q)->or_like('m.invoice_no', $q)->or_like('m.note', $q)->group_end();
        }
        $rows = $db->order_by('m.date', 'DESC')->order_by('m.id', 'DESC')->get()->result_array();
        foreach ($rows as &$r) {
            $r['source'] = 'manual';
            $r['link'] = '';
        }
        unset($r);
        return $rows;
    }

    /** Fee payments (class and transport fees; at the desk or online), one row per receipt. */
    private function fee_payments($from, $to)
    {
        $CI = get_instance();
        $CI->load->library('Fee_takings');
        $deposits = $CI->db->select("d.id, d.amount_detail, d.student_transport_fee_id, ft.type AS fee_type, g.name AS fee_group,
                ss.id AS ss_id, CONCAT_WS(' ', st.firstname, st.middlename, st.lastname) AS student, c.class, s.section", false)
            ->from('student_fees_deposite d')
            ->join('student_fees_master m', 'm.id = d.student_fees_master_id', 'left')
            ->join('fee_groups_feetype gft', 'gft.id = d.fee_groups_feetype_id', 'left')
            ->join('feetype ft', 'ft.id = gft.feetype_id', 'left')
            ->join('fee_groups g', 'g.id = gft.fee_groups_id', 'left')
            ->join('student_transport_fees stf', 'stf.id = d.student_transport_fee_id', 'left')
            ->join('student_session ss', 'ss.id = COALESCE(m.student_session_id, stf.student_session_id)', 'left')
            ->join('students st', 'st.id = ss.student_id', 'left')
            ->join('classes c', 'c.id = ss.class_id', 'left')
            ->join('sections s', 's.id = ss.section_id', 'left')
            ->get()->result_array();
        $out = array();
        foreach ($deposits as $d) {
            foreach ((array) json_decode((string) $d['amount_detail'], true) as $inv => $p) {
                if (!is_array($p) || empty($p['date']) || ($from && $p['date'] < $from) || ($to && $p['date'] > $to)) {
                    continue;
                }
                $paid = (float) (isset($p['amount']) ? $p['amount'] : 0) + (float) (isset($p['amount_fine']) ? $p['amount_fine'] : 0);
                if ($paid <= 0) {
                    continue;
                }
                $no = (int) (isset($p['inv_no']) ? $p['inv_no'] : $inv);
                $fee = $d['student_transport_fee_id'] ? 'Transport fees' : trim($d['fee_type'] . ($d['fee_group'] ? ' (' . $d['fee_group'] . ')' : ''));
                $out[] = array(
                    'id' => 'F' . (int) $d['id'] . '-' . $no,
                    'date' => $p['date'],
                    'name' => trim(preg_replace('/\s+/', ' ', (string) $d['student'])) . ($d['class'] ? ' - ' . $d['class'] . ' ' . $d['section'] : ''),
                    'invoice_no' => (int) $d['id'] . '/' . $no,
                    'amount' => $paid,
                    'documents' => '',
                    'note' => $fee . ' · ' . Fee_takings::mode_label(isset($p['payment_mode']) ? $p['payment_mode'] : ''),
                    'head_id' => 0,
                    'head' => self::SOURCES['fees'],
                    'source' => 'fees',
                    'link' => $d['ss_id'] ? 'studentfee/addfee/' . (int) $d['ss_id'] : '',
                );
            }
        }
        return $out;
    }

    /** Admission form fees (paid online, or by a bank transfer the school approved). */
    private function admission_payments($from, $to)
    {
        $db = get_instance()->db;
        $db->select("p.id, p.paid_amount, p.payment_mode, p.transaction_id, p.date, a.id AS admission_id, a.reference_no, CONCAT_WS(' ', a.firstname, a.middlename, a.lastname) AS applicant", false)
            ->from('online_admission_payment p')->join('online_admissions a', 'a.id = p.online_admission_id', 'left');
        $this->dates($db, 'DATE(p.date)', $from, $to);
        $out = array();
        foreach ($db->get()->result_array() as $p) {
            $out[] = array(
                'id' => 'A' . (int) $p['id'], 'date' => substr((string) $p['date'], 0, 10),
                'name' => trim(preg_replace('/\s+/', ' ', (string) $p['applicant'])) . ' (application ' . $p['reference_no'] . ')', 'invoice_no' => (string) $p['transaction_id'],
                'amount' => (float) $p['paid_amount'], 'documents' => '', 'note' => 'Admission form fee · ' . ucwords(str_replace('_', ' ', (string) $p['payment_mode'])),
                'head_id' => 0, 'head' => self::SOURCES['admission'], 'source' => 'admission',
                'link' => $p['admission_id'] ? 'admin/onlinestudent/edit/' . (int) $p['admission_id'] : '',
            );
        }
        return $out;
    }

    /** Online course payments (students and guests). */
    private function course_payments($from, $to)
    {
        $db = get_instance()->db;
        $db->select("p.id, p.paid_amount, p.payment_mode, p.transaction_id, p.date, p.course_name, p.guest_id,
                CONCAT_WS(' ', st.firstname, st.middlename, st.lastname) AS student", false)
            ->from('online_course_payment p')->join('students st', 'st.id = p.student_id', 'left');
        $this->dates($db, 'DATE(p.date)', $from, $to);
        $out = array();
        foreach ($db->get()->result_array() as $p) {
            $who = trim((string) $p['student']) !== '' ? trim(preg_replace('/\s+/', ' ', $p['student'])) : ((int) $p['guest_id'] ? 'Guest #' . (int) $p['guest_id'] : 'Guest');
            $out[] = array(
                'id' => 'C' . (int) $p['id'], 'date' => substr((string) $p['date'], 0, 10),
                'name' => $who . ' - ' . $p['course_name'], 'invoice_no' => (string) $p['transaction_id'],
                'amount' => (float) $p['paid_amount'], 'documents' => '', 'note' => 'Online course · ' . ucwords(str_replace('_', ' ', (string) $p['payment_mode'])),
                'head_id' => 0, 'head' => self::SOURCES['course'], 'source' => 'course', 'link' => 'onlinecourse/coursereport/coursesellreport',
            );
        }
        return $out;
    }

    private function dates($db, $col, $from, $to)
    {
        if ($from) {
            $db->where($col . ' >=', $from);
        }
        if ($to) {
            $db->where($col . ' <=', $to);
        }
    }

    /** Total and, per head, [name, count, amount] (largest first). */
    public static function totals(array $rows)
    {
        $total = 0.0;
        $by = array();
        foreach ($rows as $r) {
            $total += (float) $r['amount'];
            $key = (string) $r['head'] !== '' ? $r['head'] : 'No head';
            if (!isset($by[$key])) {
                $by[$key] = array($key, 0, 0.0);
            }
            $by[$key][1]++;
            $by[$key][2] += (float) $r['amount'];
        }
        usort($by, function ($a, $b) {
            return $b[2] <=> $a[2];
        });
        return array($total, $by);
    }
}
