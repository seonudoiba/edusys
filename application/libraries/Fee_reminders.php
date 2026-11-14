<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * "Remind all who owe" on a class's fees page (admin/fees/remind/F/C/S): one SMS to each guardian with
 * their child's own balance, through the school's SMS gateway (Smsgateway, usually Termii). Every SMS is
 * logged in fee_sms_reminders; a guardian already reminded about the same fees today is skipped.
 *
 * EDUSYS_SMS_SINK (tests only): a file path; messages are appended there as JSON lines instead of sent.
 */
class Fee_reminders
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('smsconfig_model');
        $this->CI->load->library('Fees_overview');
    }

    /** The active SMS gateway's name ('termii', ...), 'test' when messages go to EDUSYS_SMS_SINK, or null. */
    public function gateway()
    {
        if ((string) getenv('EDUSYS_SMS_SINK') !== '') {
            return 'test';
        }
        $row = $this->CI->smsconfig_model->getActiveSMS();
        return $row ? (string) $row->type : null;
    }

    /**
     * One row per student of the charge who owes:
     * [student_session_id, name, guardian, phone, balance, message, pages, skip: '' | 'no phone' | 'reminded today']
     */
    public function preview($fsg_id, $class_id, $section_id, $title, $sch_setting)
    {
        $students = $this->CI->fees_overview->students($fsg_id, $class_id, $section_id);
        $today = $this->reminded_today($fsg_id);
        $school = $this->CI->customlib->getSchoolName();
        $out = array();
        foreach ($students as $s) {
            $t = $s['totals'];
            if ($t['balance'] <= 0) {
                continue;
            }
            $p = $s['person'];
            $name = $this->CI->customlib->getFullName(isset($p['firstname']) ? $p['firstname'] : '', isset($p['middlename']) ? $p['middlename'] : '',
                isset($p['lastname']) ? $p['lastname'] : '', $sch_setting->middlename, $sch_setting->lastname);
            $due = null;
            foreach ($s['items'] as $i) {
                if ($i['balance'] > 0 && $i['due_date'] && (!$due || $i['due_date'] < $due)) {
                    $due = $i['due_date'];
                }
            }
            $phone = trim(isset($p['guardian_phone']) ? (string) $p['guardian_phone'] : '');
            $msg = $this->message(isset($p['guardian_name']) ? $p['guardian_name'] : '', $name, $s['items'][0]['class'] . ' ' . $s['items'][0]['section'],
                $title, $t['balance'], $due, $school);
            $out[] = array('student_session_id' => (int) $s['student_session_id'], 'name' => $name, 'guardian' => isset($p['guardian_name']) ? $p['guardian_name'] : '',
                'phone' => $phone, 'balance' => $t['balance'], 'overdue' => $t['overdue'], 'message' => $msg, 'pages' => $this->pages($msg),
                'skip' => $phone === '' ? 'no phone' : (isset($today[(int) $s['student_session_id']]) ? 'reminded today' : ''));
        }
        return $out;
    }

    /** The reminder text. Plain characters only, so each SMS page holds 160 characters (₦ is written N). */
    public function message($guardian, $student, $class, $title, $balance, $due, $school)
    {
        $amount = $this->money($balance);
        $when = $due ? ($due < date('Y-m-d') ? ', overdue since ' : ', due ') . date('j M Y', strtotime($due)) : '';
        // Short on purpose: most reminders fit one SMS page (160 characters), which halves the cost.
        $text = 'Dear ' . (trim((string) $guardian) !== '' ? trim($guardian) : 'Parent') . ', ' . $student . ' (' . $class . ') owes '
            . $amount . ' for ' . $title . $when . '. Please pay. Thank you. ' . $school;
        return $this->plain($text);
    }

    /** SMS pages a message takes (160 characters, or 153 each when split). */
    public function pages($text)
    {
        $n = strlen($text);
        return $n <= 160 ? 1 : (int) ceil($n / 153);
    }

    /**
     * Sends to the chosen students (student_session_ids among the preview's rows that are not skipped).
     * Returns [sent, failed].
     */
    public function send($fsg_id, array $rows, array $picked, $staff_id)
    {
        $picked = array_flip(array_map('intval', $picked));
        $gateway = $this->gateway();
        if (!$gateway) {
            throw new RuntimeException('No SMS gateway is set up. Set one up in System Settings > SMS Setting.');
        }
        if (function_exists('set_time_limit')) {
            @set_time_limit(300);
        }
        if ($gateway !== 'test') {
            $this->CI->load->library('smsgateway');
        }
        $sent = 0;
        $failed = 0;
        foreach ($rows as $r) {
            if ($r['skip'] !== '' || !isset($picked[$r['student_session_id']])) {
                continue;
            }
            $ok = $gateway === 'test' ? $this->sink($r['phone'], $r['message']) : $this->CI->smsgateway->sendSMS($r['phone'], $r['message'], '', '') !== false;
            $this->CI->db->insert('fee_sms_reminders', array(
                'student_session_id' => $r['student_session_id'], 'fee_session_group_id' => (int) $fsg_id, 'phone' => mb_substr($r['phone'], 0, 30),
                'message' => $r['message'], 'balance' => $r['balance'], 'status' => $ok ? 'sent' : 'failed', 'sent_by' => $staff_id ? (int) $staff_id : null,
            ));
            $ok ? $sent++ : $failed++;
        }
        return array($sent, $failed);
    }

    /** The last reminders sent for a charge's class section: [date, sent, failed] of the latest day, or null. */
    public function last($fsg_id, array $student_session_ids)
    {
        if (!$student_session_ids || !$this->CI->db->table_exists('fee_sms_reminders')) {
            return null;
        }
        $row = $this->CI->db->select("DATE(created_at) AS day, SUM(status = 'sent') AS sent, SUM(status = 'failed') AS failed", false)
            ->where('fee_session_group_id', (int) $fsg_id)->where_in('student_session_id', $student_session_ids)
            ->group_by('DATE(created_at)')->order_by('day', 'desc')->limit(1)->get('fee_sms_reminders')->row();
        return $row ? array($row->day, (int) $row->sent, (int) $row->failed) : null;
    }

    private function reminded_today($fsg_id)
    {
        $out = array();
        foreach ($this->CI->db->select('student_session_id')->where(array('fee_session_group_id' => (int) $fsg_id, 'status' => 'sent'))
            ->where('created_at >=', date('Y-m-d 00:00:00'))->get('fee_sms_reminders')->result_array() as $r) {
            $out[(int) $r['student_session_id']] = true;
        }
        return $out;
    }

    private function money($v)
    {
        $symbol = (string) $this->CI->customlib->getSchoolCurrencyFormat();
        $symbol = str_replace('₦', 'N', $symbol);
        return $this->plain($symbol) . number_format((float) $v, 2);
    }

    /** Characters outside plain GSM text (curly quotes, accents, ...) replaced or dropped. */
    private function plain($text)
    {
        $text = strtr($text, array('‘' => "'", '’' => "'", '“' => '"', '”' => '"', '–' => '-', '—' => '-', '·' => '-', '…' => '...'));
        if (function_exists('iconv')) {
            $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            if ($t !== false) {
                $text = $t;
            }
        }
        return preg_replace('/[^\x20-\x7E]/', '', $text);
    }

    private function sink($phone, $message)
    {
        return file_put_contents(getenv('EDUSYS_SMS_SINK'), json_encode(array('to' => $phone, 'message' => $message)) . "\n", FILE_APPEND) !== false;
    }
}
