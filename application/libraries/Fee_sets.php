<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Working with fees already charged, in the fees workspace:
 *
 *   repeat   a set of fees ("First Term fees" in every class it was charged to) becomes a Create fees
 *            plan for the next term, with the same items, classes and amounts (admin/fees/repeat)
 *   edit     change the amount or due date of a charge's items: one fee group, so every section of the
 *            class it was created for (admin/fees/edit/F/C/S), with a preview of what changes
 *   remove   take a charge off the students of one class section, when none of them has paid it
 *
 * A "set" is the fee groups that share a name before " · " (how Create fees names them, e.g.
 * "First Term fees · JSS 1"), or a single fee group with any other name.
 */
class Fee_sets
{
    const SEP = ' · ';
    private $CI;
    private $session_id;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('setting_model');
        $this->CI->load->library('Fees_overview');
        $this->session_id = (int) $this->CI->setting_model->getCurrentSession();
    }

    // ------------------------------------------------------------------ repeat

    /**
     * Sets of fees charged in the given sessions, newest first:
     * [["key" => "<session>|<name>", session_id, session, name, term, fsg_ids, classes (names), per_student (min..max), students]]
     */
    public function sets(array $session_ids)
    {
        $out = array();
        foreach ($session_ids as $sid) {
            $sid = (int) $sid;
            $session = $this->CI->db->select('session')->where('id', $sid)->get('sessions')->row();
            $per = array(); // fsg => per-student total
            $students = array();
            foreach ($this->CI->fees_overview->items($sid) as $i) {
                if ($i['is_system']) {
                    continue;
                }
                $name = $this->base_name($i['group']);
                $k = $sid . '|' . $name;
                if (!isset($out[$k])) {
                    $out[$k] = array('key' => $k, 'session_id' => $sid, 'session' => $session ? $session->session : '', 'name' => $name, 'term' => $i['term'],
                        'fsg_ids' => array(), 'classes' => array(), 'per_student' => array(), 'students' => 0);
                }
                $out[$k]['fsg_ids'][$i['fsg_id']] = true;
                $out[$k]['classes'][$i['class_id']] = $i['class'];
                $students[$k][$i['student_session_id']] = true;
                $per[$k][$i['fsg_id']][$i['student_session_id']] = (isset($per[$k][$i['fsg_id']][$i['student_session_id']]) ? $per[$k][$i['fsg_id']][$i['student_session_id']] : 0) + $i['expected'];
            }
            foreach ($out as $k => &$set) {
                if ($set['session_id'] !== $sid) {
                    continue;
                }
                $set['students'] = count($students[$k]);
                $amounts = array();
                foreach ($per[$k] as $by_student) {
                    $amounts[] = max($by_student);
                }
                $set['per_student'] = array(min($amounts), max($amounts));
                $set['fsg_ids'] = array_keys($set['fsg_ids']);
            }
            unset($set);
        }
        return array_values($out);
    }

    /** A Create fees plan repeating a set: same items, classes, sections and amounts; no due dates; next term. */
    public function plan_from_set(array $set)
    {
        $db = $this->CI->db;
        $items = array();
        $amounts = array();
        $classes = array();
        $class_of = array(); // fsg => class_id
        foreach ($db->select('m.fee_session_group_id, ss.class_id, ss.section_id')->distinct()->from('student_fees_master m')
            ->join('student_session ss', 'ss.id = m.student_session_id')
            ->where_in('m.fee_session_group_id', $set['fsg_ids'])->where('ss.session_id', (int) $set['session_id'])->get()->result_array() as $r) {
            $classes[(int) $r['class_id']][(int) $r['section_id']] = (int) $r['section_id'];
            $class_of[(int) $r['fee_session_group_id']] = (int) $r['class_id'];
        }
        foreach ($db->select('t.fee_session_group_id, t.feetype_id, t.amount, ft.type')->from('fee_groups_feetype t')->join('feetype ft', 'ft.id = t.feetype_id')
            ->where_in('t.fee_session_group_id', $set['fsg_ids'])->where('ft.is_system', 0)->order_by('t.id')->get()->result_array() as $t) {
            $key = 'ft' . (int) $t['feetype_id'];
            if (!isset($items[$key])) {
                $items[$key] = array('key' => $key, 'feetype_id' => (int) $t['feetype_id'], 'name' => $t['type'], 'due_date' => null);
            }
            $fsg = (int) $t['fee_session_group_id'];
            if (isset($class_of[$fsg])) {
                $amounts[$class_of[$fsg]][$key] = (float) $t['amount'];
            }
        }
        foreach ($classes as $cid => $sections) {
            $classes[$cid] = array_values($sections);
            foreach ($items as $key => $it) {
                if (!isset($amounts[$cid][$key])) {
                    $amounts[$cid][$key] = 0; // item not charged in this class
                }
            }
        }
        $term = $this->next_term($set['term']);
        return array('name' => $this->next_name($set['name'], $set['term'], $term), 'term' => $term,
            'items' => array_values($items), 'classes' => $classes, 'amounts' => $amounts);
    }

    public function base_name($group)
    {
        $pos = mb_strrpos((string) $group, self::SEP);
        return $pos === false ? (string) $group : mb_substr((string) $group, 0, $pos);
    }

    private function next_term($term)
    {
        $order = array('first' => 'second', 'second' => 'third', 'third' => 'first');
        return isset($order[(string) $term]) ? $order[$term] : '';
    }

    /** "First Term fees" -> "Second Term fees"; otherwise the name with the new term added, or "(copy)". */
    private function next_name($name, $from, $to)
    {
        $words = array('first' => array('First', '1st'), 'second' => array('Second', '2nd'), 'third' => array('Third', '3rd'));
        if ($to !== '') {
            foreach ($words as $term => $forms) {
                foreach ($forms as $i => $w) {
                    if (preg_match('/\b' . $w . '(?=\s*term\b)/i', $name)) {
                        $new = $words[$to][$i];
                        return preg_replace_callback('/\b' . $w . '(?=\s*term\b)/i', function ($m) use ($new) {
                            return ctype_lower($m[0][0]) ? strtolower($new) : $new;
                        }, $name, 1);
                    }
                }
            }
            return $name . ' (' . Fees_overview::TERMS[$to] . ')';
        }
        return $name . ' (copy)';
    }

    // ------------------------------------------------------------------ edit

    /** The items of a charge: [fee_groups_feetype id => [id, feetype_id, item, amount, due_date]] (current session only). */
    public function items($fsg_id)
    {
        $out = array();
        foreach ($this->CI->db->select('t.id, t.feetype_id, t.amount, t.due_date, ft.type')->from('fee_groups_feetype t')
            ->join('feetype ft', 'ft.id = t.feetype_id')->join('fee_session_groups f', 'f.id = t.fee_session_group_id')
            ->where('t.fee_session_group_id', (int) $fsg_id)->where('f.session_id', $this->session_id)->order_by('t.id')->get()->result_array() as $t) {
            $due = ($t['due_date'] && $t['due_date'] !== '0000-00-00') ? $t['due_date'] : null;
            $out[(int) $t['id']] = array('id' => (int) $t['id'], 'item' => $t['type'], 'amount' => (float) $t['amount'], 'due_date' => $due);
        }
        return $out;
    }

    /**
     * What an edit would change. $changes: [fgt id => ['amount' => float, 'due_date' => 'Y-m-d'|null]].
     * Returns [rows => [item, old/new amount, old/new due, students, paid_more (students who paid more than the new amount)],
     *          students, sections (class sections sharing the group), expected_before, expected_after, changed]
     */
    public function edit_preview($fsg_id, array $changes)
    {
        $items = $this->items($fsg_id);
        $students = array();
        $sections = array();
        $paid = array(); // fgt id => [student => paid + discount]
        foreach ($this->CI->fees_overview->items() as $i) {
            if ($i['fsg_id'] !== (int) $fsg_id) {
                continue;
            }
            $students[$i['student_session_id']] = true;
            $sections[$i['class'] . ' ' . $i['section']] = true;
        }
        if ($students) {
            foreach ($this->CI->db->select('d.fee_groups_feetype_id, d.student_fees_master_id, d.amount_detail')->from('student_fees_deposite d')
                ->join('student_fees_master m', 'm.id = d.student_fees_master_id')->where('m.fee_session_group_id', (int) $fsg_id)
                ->where_in('d.fee_groups_feetype_id', array_keys($items) ?: array(0))->get()->result_array() as $d) {
                $sum = 0.0;
                foreach ((array) json_decode((string) $d['amount_detail']) as $x) {
                    $sum += (isset($x->amount) ? (float) $x->amount : 0) + (isset($x->amount_discount) ? (float) $x->amount_discount : 0);
                }
                $paid[(int) $d['fee_groups_feetype_id']][(int) $d['student_fees_master_id']] = $sum;
            }
        }
        $n = count($students);
        $rows = array();
        $before = 0.0;
        $after = 0.0;
        $changed = 0;
        foreach ($items as $id => $it) {
            $new_amount = isset($changes[$id]['amount']) ? (float) $changes[$id]['amount'] : $it['amount'];
            $new_due = array_key_exists('due_date', isset($changes[$id]) ? $changes[$id] : array()) ? $changes[$id]['due_date'] : $it['due_date'];
            $paid_more = 0;
            foreach (isset($paid[$id]) ? $paid[$id] : array() as $sum) {
                if ($sum > $new_amount + 0.004) {
                    $paid_more++;
                }
            }
            $is_changed = abs($new_amount - $it['amount']) > 0.004 || $new_due !== $it['due_date'];
            $changed += $is_changed ? 1 : 0;
            $rows[$id] = array('item' => $it['item'], 'old_amount' => $it['amount'], 'new_amount' => $new_amount, 'old_due' => $it['due_date'], 'new_due' => $new_due,
                'changed' => $is_changed, 'paid_more' => $paid_more);
            $before += $n * $it['amount'];
            $after += $n * $new_amount;
        }
        ksort($sections);
        return array('rows' => $rows, 'students' => $n, 'sections' => array_keys($sections), 'expected_before' => $before, 'expected_after' => $after, 'changed' => $changed);
    }

    /** Saves the changes (amounts and due dates of this charge's items). Returns how many items changed. */
    public function apply_edit($fsg_id, array $changes)
    {
        $preview = $this->edit_preview($fsg_id, $changes);
        $db = $this->CI->db;
        $db->trans_start();
        $n = 0;
        foreach ($preview['rows'] as $id => $r) {
            if ($r['changed']) {
                $db->where(array('id' => (int) $id, 'fee_session_group_id' => (int) $fsg_id))
                    ->update('fee_groups_feetype', array('amount' => $r['new_amount'], 'due_date' => $r['new_due']));
                $n++;
            }
        }
        $db->trans_complete();
        if ($db->trans_status() === false) {
            throw new RuntimeException('The changes could not be saved. Nothing was changed.');
        }
        return $n;
    }

    // ------------------------------------------------------------------ remove

    /** Who would lose this charge in one class section: [masters => [id...], students, expected, paid (students who paid something)] */
    public function removal($fsg_id, $class_id, $section_id)
    {
        $masters = array();
        $expected = 0.0;
        $paying = array();
        foreach ($this->CI->fees_overview->items() as $i) {
            if ($i['fsg_id'] !== (int) $fsg_id || $i['class_id'] !== (int) $class_id || $i['section_id'] !== (int) $section_id) {
                continue;
            }
            $masters[$i['master_id']] = $i['student_session_id'];
            $expected += $i['expected'];
            if ($i['paid'] + $i['discount'] > 0) {
                $paying[$i['student_session_id']] = true;
            }
        }
        if ($masters && !$paying) { // payments recorded on items no longer in the group still count
            $any = $this->CI->db->where_in('student_fees_master_id', array_keys($masters))->count_all_results('student_fees_deposite');
            if ($any) {
                $paying[0] = true;
            }
        }
        return array('masters' => array_keys($masters), 'students' => count(array_unique($masters)), 'expected' => $expected, 'paid' => count($paying));
    }

    /** Takes the charge off the class section's students. Refuses when any of them has paid it. */
    public function remove($fsg_id, $class_id, $section_id)
    {
        $r = $this->removal($fsg_id, $class_id, $section_id);
        if ($r['paid']) {
            throw new RuntimeException('Some students have already paid towards these fees, so they cannot be removed. Reverse the payments first, or change the amounts instead.');
        }
        if (!$r['masters']) {
            return 0;
        }
        $this->CI->db->where('fee_session_group_id', (int) $fsg_id)->where_in('id', $r['masters'])->delete('student_fees_master');
        return $r['students'];
    }
}
