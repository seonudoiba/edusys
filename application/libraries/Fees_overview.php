<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Fee collection figures for the Fees overview (admin/fees), from the session's assigned fees:
 *
 *   a charge   = a fee group assigned to students of a class/section (e.g. "First Term fees" in JSS 1 A)
 *   expected   = the fee items' amounts for every student it is assigned to (a carried-forward balance:
 *                the student's own amount)
 *   collected  = payments recorded against those items; discount = discounts given at payment
 *   outstanding = expected - collected - discount; overdue = outstanding on items past their due date
 *
 * Active students of the current session only. Every query goes through the school-scoped database.
 */
class Fees_overview
{
    private $CI;
    private $session_id;
    private $rows = array();

    /** Terms a charge can belong to (fee_session_groups.term). */
    const TERMS = array('first' => 'First term', 'second' => 'Second term', 'third' => 'Third term');

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('setting_model');
        $this->session_id = (int) $this->CI->setting_model->getCurrentSession();
    }

    /**
     * One row per student and fee item:
     * [master_id, student_session_id, student_id, class_id, class, section_id, section, fsg_id, group, is_system,
     *  term, item, due_date, expected, paid, discount, balance, overdue]
     * $session_id: another session than the current one (carry forward reads the last session).
     */
    public function items($session_id = null, $only_student_session = 0)
    {
        $session_id = $session_id ? (int) $session_id : $this->session_id;
        $cache = $session_id . ($only_student_session ? '-' . (int) $only_student_session : '');
        if (isset($this->rows[$cache])) {
            return $this->rows[$cache];
        }
        if ($only_student_session) {
            $this->CI->db->where('m.student_session_id', (int) $only_student_session);
        }
        $db = $this->CI->db;
        $masters = $db->select('m.id, m.student_session_id, m.fee_session_group_id, m.is_system, m.amount, ss.student_id, ss.class_id, ss.section_id, c.class, s.section, g.name AS group_name, fsg.term')
            ->from('student_fees_master m')
            ->join('student_session ss', 'ss.id = m.student_session_id')
            ->join('students st', 'st.id = ss.student_id')
            ->join('fee_session_groups fsg', 'fsg.id = m.fee_session_group_id')
            ->join('fee_groups g', 'g.id = fsg.fee_groups_id')
            ->join('classes c', 'c.id = ss.class_id')
            ->join('sections s', 's.id = ss.section_id')
            ->where('ss.session_id', $session_id)
            ->where('st.is_active', 'yes')
            ->get()->result_array();
        $rows = array();
        if (!$masters) {
            return $this->rows[$cache] = $rows;
        }
        $fsg_ids = array_values(array_unique(array_map('intval', array_column($masters, 'fee_session_group_id'))));
        $items_by_group = array();
        foreach ($db->select('t.id, t.fee_session_group_id, t.amount, t.due_date, ft.type')
            ->from('fee_groups_feetype t')->join('feetype ft', 'ft.id = t.feetype_id')
            ->where_in('t.fee_session_group_id', $fsg_ids)->get()->result_array() as $t) {
            $items_by_group[(int) $t['fee_session_group_id']][] = $t;
        }
        $paid = array();
        foreach (array_chunk(array_map('intval', array_column($masters, 'id')), 2000) as $chunk) {
            foreach ($db->select('student_fees_master_id, fee_groups_feetype_id, amount_detail')->where_in('student_fees_master_id', $chunk)
                ->where('fee_groups_feetype_id IS NOT NULL', null, false)->get('student_fees_deposite')->result_array() as $d) {
                $p = 0.0;
                $disc = 0.0;
                foreach ((array) json_decode((string) $d['amount_detail']) as $x) {
                    $p += isset($x->amount) ? (float) $x->amount : 0;
                    $disc += isset($x->amount_discount) ? (float) $x->amount_discount : 0;
                }
                $key = $d['student_fees_master_id'] . '-' . $d['fee_groups_feetype_id'];
                $paid[$key] = array(isset($paid[$key]) ? $paid[$key][0] + $p : $p, isset($paid[$key]) ? $paid[$key][1] + $disc : $disc);
            }
        }
        $today = date('Y-m-d');
        foreach ($masters as $m) {
            foreach (isset($items_by_group[(int) $m['fee_session_group_id']]) ? $items_by_group[(int) $m['fee_session_group_id']] : array() as $t) {
                $expected = (int) $m['is_system'] === 1 ? (float) $m['amount'] : (float) $t['amount'];
                list($p, $disc) = isset($paid[$m['id'] . '-' . $t['id']]) ? $paid[$m['id'] . '-' . $t['id']] : array(0.0, 0.0);
                $balance = max(0.0, round($expected - $p - $disc, 2));
                $due = ($t['due_date'] && $t['due_date'] !== '0000-00-00') ? $t['due_date'] : null;
                $rows[] = array(
                    'master_id' => (int) $m['id'], 'student_session_id' => (int) $m['student_session_id'], 'student_id' => (int) $m['student_id'],
                    'class_id' => (int) $m['class_id'], 'class' => $m['class'], 'section_id' => (int) $m['section_id'], 'section' => $m['section'],
                    'fsg_id' => (int) $m['fee_session_group_id'], 'group' => $m['group_name'], 'is_system' => (int) $m['is_system'],
                    'term' => isset(self::TERMS[(string) $m['term']]) ? $m['term'] : null, 'item' => $t['type'], 'due_date' => $due, 'expected' => $expected, 'paid' => $p, 'discount' => $disc,
                    'balance' => $balance, 'overdue' => ($due && $due < $today) ? $balance : 0.0,
                );
            }
        }
        return $this->rows[$cache] = $rows;
    }

    /** Totals over the given items. */
    public function totals(array $items)
    {
        $t = array('expected' => 0.0, 'paid' => 0.0, 'discount' => 0.0, 'balance' => 0.0, 'overdue' => 0.0, 'students' => 0, 'owing' => 0);
        $students = array();
        $owing = array();
        foreach ($items as $i) {
            foreach (array('expected', 'paid', 'discount', 'balance', 'overdue') as $k) {
                $t[$k] += $i[$k];
            }
            $students[$i['student_session_id']] = true;
            if ($i['balance'] > 0) {
                $owing[$i['student_session_id']] = true;
            }
        }
        $t['students'] = count($students);
        $t['owing'] = count($owing);
        $t['progress'] = $t['expected'] > 0 ? min(100, round(100 * ($t['paid'] + $t['discount']) / $t['expected'])) : 0;
        return $t;
    }

    /**
     * One row per charge (fee group in a class/section), filtered:
     * $filters: class_id, fsg_id, term ('', 'first', 'second', 'third', 'none'), status ('', 'owing', 'overdue', 'paid').
     */
    public function charges(array $filters = array())
    {
        $groups = array();
        foreach ($this->items() as $i) {
            if (!empty($filters['class_id']) && $i['class_id'] !== (int) $filters['class_id']) {
                continue;
            }
            if (!empty($filters['fsg_id']) && $i['fsg_id'] !== (int) $filters['fsg_id']) {
                continue;
            }
            if (!empty($filters['term']) && ($filters['term'] === 'none' ? $i['term'] !== null : $i['term'] !== $filters['term'])) {
                continue;
            }
            $k = $i['fsg_id'] . '-' . $i['class_id'] . '-' . $i['section_id'];
            if (!isset($groups[$k])) {
                $groups[$k] = array('fsg_id' => $i['fsg_id'], 'group' => $i['group'], 'is_system' => $i['is_system'], 'term' => $i['term'], 'class_id' => $i['class_id'],
                    'class' => $i['class'], 'section_id' => $i['section_id'], 'section' => $i['section'], 'items' => array(), 'due' => null);
            }
            $groups[$k]['items'][] = $i;
            if ($i['due_date'] && (!$groups[$k]['due'] || $i['due_date'] < $groups[$k]['due'])) {
                $groups[$k]['due'] = $i['due_date'];
            }
        }
        $out = array();
        foreach ($groups as $g) {
            $g['totals'] = $this->totals($g['items']);
            unset($g['items']);
            $status = isset($filters['status']) ? $filters['status'] : '';
            if (($status === 'owing' && $g['totals']['balance'] <= 0) || ($status === 'overdue' && $g['totals']['overdue'] <= 0)
                || ($status === 'paid' && $g['totals']['balance'] > 0)) {
                continue;
            }
            $out[] = $g;
        }
        usort($out, function ($a, $b) {
            return array($a['is_system'], $a['group'], $a['class'], $a['section']) <=> array($b['is_system'], $b['group'], $b['class'], $b['section']);
        });
        return $out;
    }

    /** Students of one charge with their balances, most owed first. */
    public function students($fsg_id, $class_id, $section_id)
    {
        $by = array();
        foreach ($this->items() as $i) {
            if ($i['fsg_id'] !== (int) $fsg_id || $i['class_id'] !== (int) $class_id || $i['section_id'] !== (int) $section_id) {
                continue;
            }
            $by[$i['student_session_id']][] = $i;
        }
        if (!$by) {
            return array();
        }
        $people = array();
        foreach ($this->CI->db->select('ss.id, st.firstname, st.middlename, st.lastname, st.admission_no, st.guardian_name, st.guardian_phone')
            ->from('student_session ss')->join('students st', 'st.id = ss.student_id')
            ->where_in('ss.id', array_keys($by))->get()->result_array() as $p) {
            $people[(int) $p['id']] = $p;
        }
        $out = array();
        foreach ($by as $ss => $items) {
            $p = isset($people[$ss]) ? $people[$ss] : array();
            $out[] = array('student_session_id' => $ss, 'person' => $p, 'items' => $items, 'totals' => $this->totals($items));
        }
        usort($out, function ($a, $b) { return $b['totals']['balance'] <=> $a['totals']['balance']; });
        return $out;
    }

    /** Fee groups of the session with assigned students (for the filter). */
    public function groups()
    {
        $out = array();
        foreach ($this->items() as $i) {
            $out[$i['fsg_id']] = $i['group'];
        }
        asort($out);
        return $out;
    }

    /** Classes with assigned fees (for the filter). */
    public function classes()
    {
        $out = array();
        foreach ($this->items() as $i) {
            $out[$i['class_id']] = $i['class'];
        }
        asort($out);
        return $out;
    }

    /** Terms used by this session's charges (for the filter): [key => label], plus 'none' when some have none. */
    public function terms()
    {
        $out = array();
        foreach ($this->items() as $i) {
            if (!$i['is_system']) {
                $out[$i['term'] === null ? 'none' : $i['term']] = true;
            }
        }
        $labels = array();
        foreach (self::TERMS + array('none' => 'No term set') as $k => $label) {
            if (isset($out[$k])) {
                $labels[$k] = $label;
            }
        }
        return $labels;
    }

    /**
     * Active students of a class section that has a charge, who are not charged it (usually admitted
     * after the fees were set). One row per student with what they miss:
     * [student_session_id, class_id, class, section_id, section, firstname, ..., missing => [[fsg_id, group, amount], ...], amount]
     * Optionally limited to one charge (fsg_id in class_id/section_id).
     */
    public function uncharged($fsg_id = 0, $class_id = 0, $section_id = 0)
    {
        $charges = array(); // "class-section" => [fsg_id => group]
        $charged = array(); // "fsg-student_session" => true
        foreach ($this->items() as $i) {
            $charged[$i['fsg_id'] . '-' . $i['student_session_id']] = true;
            if ($i['is_system'] || ($fsg_id && $i['fsg_id'] !== (int) $fsg_id)
                || ($class_id && ($i['class_id'] !== (int) $class_id || $i['section_id'] !== (int) $section_id))) {
                continue;
            }
            $charges[$i['class_id'] . '-' . $i['section_id']][$i['fsg_id']] = $i['group'];
        }
        if (!$charges) {
            return array();
        }
        $fsg_ids = array();
        foreach ($charges as $list) {
            $fsg_ids = array_merge($fsg_ids, array_keys($list));
        }
        $amounts = array();
        foreach ($this->CI->db->select('fee_session_group_id, SUM(amount) AS total', false)->where_in('fee_session_group_id', array_values(array_unique($fsg_ids)))
            ->group_by('fee_session_group_id')->get('fee_groups_feetype')->result_array() as $r) {
            $amounts[(int) $r['fee_session_group_id']] = (float) $r['total'];
        }
        $db = $this->CI->db->select('ss.id, ss.class_id, ss.section_id, c.class, s.section, st.firstname, st.middlename, st.lastname, st.admission_no, st.admission_date')
            ->from('student_session ss')->join('students st', 'st.id = ss.student_id')
            ->join('classes c', 'c.id = ss.class_id')->join('sections s', 's.id = ss.section_id')
            ->where('ss.session_id', $this->session_id)->where('st.is_active', 'yes');
        if ($class_id) {
            $db->where(array('ss.class_id' => (int) $class_id, 'ss.section_id' => (int) $section_id));
        }
        $out = array();
        foreach ($db->order_by('c.id')->order_by('s.section')->order_by('st.firstname')->get()->result_array() as $st) {
            $key = $st['class_id'] . '-' . $st['section_id'];
            if (!isset($charges[$key])) {
                continue;
            }
            $missing = array();
            $total = 0.0;
            foreach ($charges[$key] as $fsg => $group) {
                if (!isset($charged[$fsg . '-' . $st['id']])) {
                    $a = isset($amounts[$fsg]) ? $amounts[$fsg] : 0.0;
                    $missing[] = array('fsg_id' => (int) $fsg, 'group' => $group, 'amount' => $a);
                    $total += $a;
                }
            }
            if ($missing) {
                $st['student_session_id'] = (int) $st['id'];
                $st['missing'] = $missing;
                $st['amount'] = $total;
                $out[] = $st;
            }
        }
        return $out;
    }

    /**
     * Last session's unpaid balance of each student now in the given class section of this session:
     * [student_session_id => [student_session_id, firstname, ..., admission_no, previous (owed last session), carried (already brought forward, or null)]]
     */
    public function carry_forward($previous_session_id, $class_id, $section_id)
    {
        $db = $this->CI->db;
        $students = $db->select('ss.id, ss.student_id, st.firstname, st.middlename, st.lastname, st.admission_no')
            ->from('student_session ss')->join('students st', 'st.id = ss.student_id')
            ->where(array('ss.session_id' => $this->session_id, 'ss.class_id' => (int) $class_id, 'ss.section_id' => (int) $section_id, 'st.is_active' => 'yes'))
            ->order_by('st.firstname')->get()->result_array();
        if (!$students) {
            return array();
        }
        $owed = array();
        foreach ($this->items($previous_session_id) as $i) {
            $owed[$i['student_id']] = (isset($owed[$i['student_id']]) ? $owed[$i['student_id']] : 0) + $i['balance'];
        }
        $carried = array();
        foreach ($this->items() as $i) {
            if ($i['is_system']) {
                $carried[$i['student_session_id']] = (isset($carried[$i['student_session_id']]) ? $carried[$i['student_session_id']] : 0) + $i['expected'];
            }
        }
        $out = array();
        foreach ($students as $st) {
            $st['student_session_id'] = (int) $st['id'];
            $st['previous'] = isset($owed[(int) $st['student_id']]) ? round($owed[(int) $st['student_id']], 2) : 0.0;
            $st['carried'] = isset($carried[(int) $st['id']]) ? $carried[(int) $st['id']] : null;
            $out[(int) $st['id']] = $st;
        }
        return $out;
    }
}
