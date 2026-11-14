<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * "Discounts & balances" in the fees workspace (admin/fees/adjust): changes to what students owe, each
 * shown as a preview before it is applied. It writes what the classic screens write, so collection,
 * receipts and reports work unchanged:
 *
 *   catch up       students admitted after their class was charged get the class's fees
 *                  (student_fees_master, as Fees Master > Assign)
 *   discounts      a discount given to chosen students (student_fees_discounts, as Fees Discount > Assign);
 *                  it is taken off when the next payment is collected, as before
 *   carry forward  last session's unpaid balance brought into this session
 *                  (studentfeemaster_model->addPreviousBal, as Fees Carry Forward)
 */
class Fee_adjustments
{
    private $CI;
    private $session_id;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model(array('setting_model', 'studentfeemaster_model', 'feediscount_model', 'session_model'));
        $this->CI->load->library('Fees_overview');
        $this->session_id = (int) $this->CI->setting_model->getCurrentSession();
    }

    // ------------------------------------------------------------------ catch up

    /** Charges the chosen students what they miss. $picked: ["student_session_id-fsg_id", ...]. Returns [students, charges]. */
    public function catch_up(array $picked)
    {
        $allowed = array();
        foreach ($this->CI->fees_overview->uncharged() as $st) {
            foreach ($st['missing'] as $m) {
                $allowed[$st['student_session_id'] . '-' . $m['fsg_id']] = array($st['student_session_id'], $m['fsg_id']);
            }
        }
        $students = array();
        $n = 0;
        $db = $this->CI->db;
        $db->trans_start();
        foreach (array_unique($picked) as $key) {
            if (!isset($allowed[$key])) {
                continue; // charged meanwhile, or not in a charged class
            }
            list($ss, $fsg) = $allowed[$key];
            $this->CI->studentfeemaster_model->add(array('student_session_id' => $ss, 'fee_session_group_id' => $fsg, 'is_system' => 0));
            $students[$ss] = true;
            $n++;
        }
        $db->trans_complete();
        if ($db->trans_status() === false) {
            throw new RuntimeException('The fees could not be saved. Nothing was changed.');
        }
        return array(count($students), $n);
    }

    // ------------------------------------------------------------------ discounts

    /** Discounts of the school, with how many students have each (assigned, and already used at a payment). */
    public function discounts()
    {
        $counts = array();
        foreach ($this->CI->db->select("d.fees_discount_id, COUNT(*) AS n, SUM(CASE WHEN d.payment_id IS NOT NULL AND d.payment_id <> '' THEN 1 ELSE 0 END) AS used", false)
            ->from('student_fees_discounts d')->join('student_session ss', 'ss.id = d.student_session_id')
            ->where('ss.session_id', $this->session_id)->group_by('d.fees_discount_id')->get()->result_array() as $r) {
            $counts[(int) $r['fees_discount_id']] = array((int) $r['n'], (int) $r['used']);
        }
        $out = array();
        foreach ($this->CI->db->select('id, name, code, type, amount, percentage')->order_by('name')->get('fees_discounts')->result_array() as $d) {
            $d['students'] = isset($counts[(int) $d['id']]) ? $counts[(int) $d['id']][0] : 0;
            $d['used'] = isset($counts[(int) $d['id']]) ? $counts[(int) $d['id']][1] : 0;
            $out[(int) $d['id']] = $d;
        }
        return $out;
    }

    /** "₦5,000" or "10%". */
    public function discount_label(array $d, $currency_symbol)
    {
        return $d['type'] === 'percentage' ? rtrim(rtrim(number_format((float) $d['percentage'], 2), '0'), '.') . '%' : $currency_symbol . amountFormat($d['amount']);
    }

    /** What the discount would take off a balance (the estimate shown in the preview). */
    public function discount_value(array $d, $balance)
    {
        $balance = max(0.0, (float) $balance);
        $v = $d['type'] === 'percentage' ? $balance * (float) $d['percentage'] / 100 : (float) $d['amount'];
        return round(min($v, $balance), 2);
    }

    /**
     * Students of the chosen class sections, with their balance and whether they have the discount:
     * [student_session_id => [..., balance, state: '' | 'assigned' | 'used']]
     * $sections: ["class_id-section_id", ...]
     */
    public function discount_students($discount_id, array $sections)
    {
        $pairs = array();
        foreach ($sections as $pair) {
            if (preg_match('/^(\d+)-(\d+)$/', (string) $pair, $m)) {
                $pairs[] = array((int) $m[1], (int) $m[2]);
            }
        }
        if (!$pairs) {
            return array();
        }
        $db = $this->CI->db->select('ss.id, c.class, s.section, st.firstname, st.middlename, st.lastname, st.admission_no')
            ->from('student_session ss')->join('students st', 'st.id = ss.student_id')
            ->join('classes c', 'c.id = ss.class_id')->join('sections s', 's.id = ss.section_id')
            ->where('ss.session_id', $this->session_id)->where('st.is_active', 'yes')->group_start();
        foreach ($pairs as $p) {
            $db->or_group_start()->where('ss.class_id', $p[0])->where('ss.section_id', $p[1])->group_end();
        }
        $rows = $db->group_end()->order_by('c.id')->order_by('s.section')->order_by('st.firstname')->get()->result_array();
        if (!$rows) {
            return array();
        }
        $balance = array();
        foreach ($this->CI->fees_overview->items() as $i) {
            $balance[$i['student_session_id']] = (isset($balance[$i['student_session_id']]) ? $balance[$i['student_session_id']] : 0) + $i['balance'];
        }
        $state = array();
        if ($discount_id) {
            foreach ($this->CI->db->select('student_session_id, payment_id')->where('fees_discount_id', (int) $discount_id)
                ->where_in('student_session_id', array_map('intval', array_column($rows, 'id')))->get('student_fees_discounts')->result_array() as $r) {
                $state[(int) $r['student_session_id']] = ($r['payment_id'] !== null && $r['payment_id'] !== '') ? 'used' : 'assigned';
            }
        }
        $out = array();
        foreach ($rows as $r) {
            $r['student_session_id'] = (int) $r['id'];
            $r['balance'] = isset($balance[(int) $r['id']]) ? $balance[(int) $r['id']] : 0.0;
            $r['state'] = isset($state[(int) $r['id']]) ? $state[(int) $r['id']] : '';
            $out[(int) $r['id']] = $r;
        }
        return $out;
    }

    /**
     * Gives the discount to $add and takes it back from $remove (only where it has not been used at a
     * payment yet). $discount: an existing discount row, or ['id' => null, name, type, amount, percentage]
     * to create it first. Returns [discount id, added, removed].
     */
    public function apply_discount(array $discount, array $students, array $add, array $remove)
    {
        $db = $this->CI->db;
        $db->trans_start();
        $id = $discount['id'] ? (int) $discount['id'] : 0;
        if (!$id) {
            $db->insert('fees_discounts', array(
                'session_id' => $this->session_id,
                'name' => $discount['name'],
                'code' => $this->discount_code($discount['name']),
                'type' => $discount['type'],
                'amount' => $discount['type'] === 'fix' ? (float) $discount['amount'] : 0,
                'percentage' => $discount['type'] === 'percentage' ? (float) $discount['percentage'] : null,
                'description' => '',
            ));
            $id = (int) $db->insert_id();
        }
        $added = 0;
        foreach (array_unique(array_map('intval', $add)) as $ss) {
            if (isset($students[$ss]) && $students[$ss]['state'] === '') {
                $this->CI->feediscount_model->allotdiscount(array('student_session_id' => $ss, 'fees_discount_id' => $id));
                $added++;
            }
        }
        $removable = array();
        foreach (array_unique(array_map('intval', $remove)) as $ss) {
            if (isset($students[$ss]) && $students[$ss]['state'] === 'assigned') {
                $removable[] = $ss;
            }
        }
        if ($removable) {
            $db->where('fees_discount_id', $id)->where_in('student_session_id', $removable)
                ->group_start()->where('payment_id IS NULL', null, false)->or_where('payment_id', '')->group_end()
                ->delete('student_fees_discounts');
        }
        $db->trans_complete();
        if ($db->trans_status() === false) {
            throw new RuntimeException('The discount could not be saved. Nothing was changed.');
        }
        return array($id, $added, count($removable));
    }

    private function discount_code($name)
    {
        $base = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-')) ?: 'discount';
        $base = substr($base, 0, 40);
        $code = $base;
        for ($i = 2; $this->CI->db->where('code', $code)->count_all_results('fees_discounts') > 0; $i++) {
            $code = $base . '-' . $i;
        }
        return $code;
    }

    // ------------------------------------------------------------------ carry forward

    /** The session before the current one (object with id, session), or null. */
    public function previous_session()
    {
        $row = $this->CI->session_model->getPreSession($this->session_id);
        return $row ?: null;
    }

    /**
     * Class sections of this session with what their students owed last session:
     * [["class_id-section_id" => [class, section, students, owing, owed, carried]], ...]
     */
    public function carry_summary($previous_session_id)
    {
        $owed = array();
        foreach ($this->CI->fees_overview->items($previous_session_id) as $i) {
            $owed[$i['student_id']] = (isset($owed[$i['student_id']]) ? $owed[$i['student_id']] : 0) + $i['balance'];
        }
        $carried = array();
        foreach ($this->CI->fees_overview->items() as $i) {
            if ($i['is_system']) {
                $carried[$i['student_session_id']] = true;
            }
        }
        $out = array();
        foreach ($this->CI->db->select('ss.id, ss.student_id, ss.class_id, ss.section_id, c.class, s.section')
            ->from('student_session ss')->join('students st', 'st.id = ss.student_id')
            ->join('classes c', 'c.id = ss.class_id')->join('sections s', 's.id = ss.section_id')
            ->where('ss.session_id', $this->session_id)->where('st.is_active', 'yes')
            ->order_by('c.id')->order_by('s.section')->get()->result_array() as $r) {
            $k = $r['class_id'] . '-' . $r['section_id'];
            if (!isset($out[$k])) {
                $out[$k] = array('class_id' => (int) $r['class_id'], 'section_id' => (int) $r['section_id'], 'class' => $r['class'], 'section' => $r['section'],
                    'students' => 0, 'owing' => 0, 'owed' => 0.0, 'carried' => 0);
            }
            $out[$k]['students']++;
            $o = isset($owed[(int) $r['student_id']]) ? $owed[(int) $r['student_id']] : 0;
            if ($o > 0) {
                $out[$k]['owing']++;
                $out[$k]['owed'] += $o;
            }
            if (isset($carried[(int) $r['id']])) {
                $out[$k]['carried']++;
            }
        }
        return $out;
    }

    /** Students of the chosen sections with last session's balance: [student_session_id => [..., class, section, previous, carried]] */
    public function carry_students($previous_session_id, array $sections)
    {
        $out = array();
        $summary = $this->carry_summary($previous_session_id);
        foreach ($sections as $pair) {
            if (!isset($summary[(string) $pair])) {
                continue;
            }
            $sec = $summary[(string) $pair];
            foreach ($this->CI->fees_overview->carry_forward($previous_session_id, $sec['class_id'], $sec['section_id']) as $id => $st) {
                $st['class'] = $sec['class'];
                $st['section'] = $sec['section'];
                $out[$id] = $st;
            }
        }
        return $out;
    }

    /**
     * Brings the balances in. $amounts: [student_session_id => amount] for students of $students;
     * a student already brought forward is updated. Returns the number of students written.
     */
    public function carry(array $students, array $amounts, $due_date)
    {
        $rows = array();
        foreach ($amounts as $ss => $amount) {
            $ss = (int) $ss;
            if (!isset($students[$ss]) || !is_numeric($amount) || $amount < 0) {
                continue;
            }
            if ((float) $amount == 0 && $students[$ss]['carried'] === null) {
                continue; // nothing owed, nothing to bring
            }
            $rows[] = array('student_session_id' => $ss, 'amount' => round((float) $amount, 2), 'is_system' => 1, 'fee_session_group_id' => 0);
        }
        if (!$rows) {
            return 0;
        }
        if (!$this->CI->studentfeemaster_model->addPreviousBal($rows, $due_date)) {
            throw new RuntimeException('The balances could not be saved. Nothing was changed.');
        }
        return count($rows);
    }
}
