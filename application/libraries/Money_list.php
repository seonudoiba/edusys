<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Income List and Expense List (admin/income/incomesearch, admin/expense/expensesearch): every
 * record of the school, newest first, for a period, an income/expense head or words in the name,
 * invoice number or description, with the total and the total of each head.
 */
class Money_list
{
    const KINDS = array(
        'income' => array('table' => 'income', 'head_table' => 'income_head', 'head_id' => 'income_head_id', 'head_name' => 'income_category', 'docs' => 'school_income'),
        'expense' => array('table' => 'expenses', 'head_table' => 'expense_head', 'head_id' => 'exp_head_id', 'head_name' => 'exp_category', 'docs' => 'school_expense'),
    );

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
        return $db->order_by('m.date', 'DESC')->order_by('m.id', 'DESC')->get()->result_array();
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
