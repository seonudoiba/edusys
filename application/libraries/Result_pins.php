<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Result checker PINs (scratch cards) for the school website's exam result page (migration 306,
 * result_pins). Once a school has generated PINs, the page asks for admission no + PIN:
 *  - a PIN works for one student: the first student it is used for (then only them);
 *  - it works max_uses times; it belongs to a session (results of that session's exams only);
 *  - a disabled PIN does not work.
 */
class Result_pins
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** Whether the result page asks for a PIN (the school has active PINs). */
    public function required()
    {
        return $this->CI->db->where('is_active', 1)->count_all_results('result_pins') > 0;
    }

    /** Makes $count PINs in a new batch. Returns the batch code. */
    public function generate($count, $max_uses, $session_id, $staff_id = null)
    {
        $db = $this->CI->db;
        $count = max(1, min(2000, (int) $count));
        $max_uses = max(1, min(100, (int) $max_uses));
        $batch = 'B' . date('ymd-His');
        $last = (int) $db->select_max('id')->get('result_pins')->row('id');
        $serial = (int) $db->select('COUNT(*) AS n', false)->get('result_pins')->row('n');
        $now = date('Y-m-d H:i:s');
        $existing = array();
        $db->trans_start();
        for ($i = 0; $i < $count; $i++) {
            do {
                $pin = '';
                for ($k = 0; $k < 12; $k++) {
                    $pin .= random_int(0, 9);
                }
                $pin = ltrim($pin, '0') === '' ? '1' . substr($pin, 1) : $pin;
            } while (isset($existing[$pin]) || $db->where('pin', $pin)->count_all_results('result_pins') > 0);
            $existing[$pin] = true;
            $serial++;
            $db->insert('result_pins', array('batch' => $batch, 'serial_no' => sprintf('RC%06d', $serial), 'pin' => $pin, 'session_id' => (int) $session_id ?: null,
                'max_uses' => $max_uses, 'used_count' => 0, 'is_active' => 1, 'created_by' => $staff_id, 'created_at' => $now));
        }
        $db->trans_complete();
        return $batch;
    }

    public function batches()
    {
        return $this->CI->db->select("p.batch, MIN(p.created_at) AS created_at, COUNT(*) AS total, SUM(p.used_count > 0) AS used, SUM(p.is_active = 0) AS disabled, MAX(p.max_uses) AS max_uses, se.session", false)
            ->from('result_pins p')->join('sessions se', 'se.id = p.session_id', 'left')
            ->group_by('p.batch')->order_by('created_at', 'desc')->get()->result_array();
    }

    public function pins($batch)
    {
        return $this->CI->db->select("p.*, st.admission_no, CONCAT_WS(' ', st.firstname, st.lastname) AS student_name", false)
            ->from('result_pins p')
            ->join('student_session ss', 'ss.id = p.student_session_id', 'left')
            ->join('students st', 'st.id = ss.student_id', 'left')
            ->where('p.batch', $batch)->order_by('p.id')->get()->result_array();
    }

    public function set_active($ids, $active)
    {
        $ids = array_filter(array_map('intval', (array) $ids));
        if ($ids) {
            $this->CI->db->where_in('id', $ids)->update('result_pins', array('is_active' => $active ? 1 : 0));
        }
    }

    public function set_batch_active($batch, $active)
    {
        $this->CI->db->where('batch', (string) $batch)->update('result_pins', array('is_active' => $active ? 1 : 0));
    }

    /**
     * Checks a PIN for a student's result in an exam and counts the use.
     * Returns '' when it may be shown, else the reason.
     */
    public function use_pin($pin, $student_session_id, $exam_session_id)
    {
        $db = $this->CI->db;
        $pin = preg_replace('/\D/', '', (string) $pin);
        $row = $pin === '' ? null : $db->where('pin', $pin)->get('result_pins')->row_array();
        if (!$row || !(int) $row['is_active']) {
            return 'That PIN is not valid. Check the number on your card.';
        }
        if ($row['session_id'] && (int) $row['session_id'] !== (int) $exam_session_id) {
            return 'That PIN is for another session\'s results.';
        }
        if ($row['student_session_id'] && (int) $row['student_session_id'] !== (int) $student_session_id) {
            return 'That PIN has already been used for another student.';
        }
        if ((int) $row['used_count'] >= (int) $row['max_uses']) {
            return 'That PIN has been used ' . (int) $row['max_uses'] . ' times, the most allowed.';
        }
        $now = date('Y-m-d H:i:s');
        $set = array('used_count' => (int) $row['used_count'] + 1, 'last_used_at' => $now, 'student_session_id' => (int) $student_session_id);
        if (!$row['first_used_at']) {
            $set['first_used_at'] = $now;
        }
        $db->where('id', (int) $row['id'])->update('result_pins', $set);
        return '';
    }
}
