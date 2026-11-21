<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CBT integrity: where and how an attempt may be taken (settings on onlineexam, migration 314).
 *
 *   - lock_device: an attempt stays on the device (browser) that started it. The device is known
 *     by a random key in a cookie set on the exam screen. Another device gets a message and can't
 *     see or answer the questions; the invigilator can move the attempt (release()).
 *   - allowed_ips: the exam can only be taken from the school's network (IP addresses, prefixes
 *     like 102.89. or CIDR like 102.89.32.0/20). Checked on every request.
 *   - exam_pin: a code the invigilator reads out; asked once, when an attempt starts.
 *   - questions_per_student: each student's own random set of the exam's questions, in the same
 *     proportion of easy / medium / hard as the whole exam; questions of one reading passage go
 *     together. Kept on onlineexam_students.question_ids (the latest attempt counts).
 *   - webcam: photos at the start, every webcam_minutes, and when the student leaves the page;
 *     stored outside the public folder, shown to staff only (snapshot_file()).
 */
class Cbt_integrity
{
    const COOKIE = 'edusys_cbt_device';
    const SNAP_REASONS = array('start', 'interval', 'left_page', 'fullscreen');

    /** Cloudflare's IPv4 ranges: behind its proxy the student's address is in CF-Connecting-IP. */
    const CLOUDFLARE = array('173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22', '141.101.64.0/18', '108.162.192.0/18',
        '190.93.240.0/20', '188.114.96.0/20', '197.234.240.0/22', '198.41.128.0/17', '162.158.0.0/15', '104.16.0.0/13', '104.24.0.0/14',
        '172.64.0.0/13', '131.0.72.0/22');

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    // ------------------------------------------------------------------ the device

    /** This browser's device key; set (two years, HTTP only) when it has none. */
    public function device_key()
    {
        $key = isset($_COOKIE[self::COOKIE]) ? (string) $_COOKIE[self::COOKIE] : '';
        if (!preg_match('/^[a-f0-9]{64}$/', $key)) {
            $key = bin2hex(random_bytes(32));
            if (!headers_sent()) {
                setcookie(self::COOKIE, $key, array('expires' => time() + 63072000, 'path' => '/', 'secure' => (bool) $this->CI->config->item('cookie_secure'),
                    'httponly' => true, 'samesite' => 'Lax'));
            }
            $_COOKIE[self::COOKIE] = $key;
        }
        return $key;
    }

    /** The student's IP address (the visitor's own when the site is behind Cloudflare). */
    public function client_ip()
    {
        $remote = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '';
        $cf = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? trim((string) $_SERVER['HTTP_CF_CONNECTING_IP']) : '';
        if ($cf !== '' && filter_var($cf, FILTER_VALIDATE_IP)) {
            foreach (self::CLOUDFLARE as $range) {
                if (self::in_range($remote, $range)) {
                    return $cf;
                }
            }
        }
        return $remote;
    }

    /** The allowed-network list as entered, cleaned: one entry per line or comma. */
    public static function clean_ips($text)
    {
        $out = array();
        foreach (preg_split('/[\s,;]+/', (string) $text) as $e) {
            $e = trim($e);
            if ($e === '') {
                continue;
            }
            if (filter_var($e, FILTER_VALIDATE_IP) || preg_match('#^\d{1,3}(\.\d{1,3}){0,3}\.?\*?$#', $e) || preg_match('#^[\d.]+/\d{1,2}$#', $e) || preg_match('#^[0-9a-f:]+/\d{1,3}$#i', $e)) {
                $out[] = $e;
            }
        }
        return implode(', ', array_unique($out));
    }

    public static function ip_allowed($ip, $list)
    {
        $list = trim((string) $list);
        if ($list === '') {
            return true;
        }
        foreach (preg_split('/[\s,;]+/', $list) as $e) {
            $e = trim($e);
            if ($e === '') {
                continue;
            }
            if (strpos($e, '/') !== false) {
                if (self::in_range($ip, $e)) {
                    return true;
                }
            } elseif (substr($e, -1) === '*' || substr($e, -1) === '.') {
                if (strpos($ip, rtrim($e, '*')) === 0) {
                    return true;
                }
            } elseif ($ip === $e) {
                return true;
            }
        }
        return false;
    }

    private static function in_range($ip, $cidr)
    {
        list($net, $bits) = array_pad(explode('/', $cidr, 2), 2, null);
        $ip_bin = @inet_pton($ip);
        $net_bin = @inet_pton($net);
        if ($ip_bin === false || $net_bin === false || strlen($ip_bin) !== strlen($net_bin)) {
            return false;
        }
        $bits = $bits === null ? strlen($ip_bin) * 8 : (int) $bits;
        $bytes = intdiv($bits, 8);
        if ($bytes && substr($ip_bin, 0, $bytes) !== substr($net_bin, 0, $bytes)) {
            return false;
        }
        $rest = $bits % 8;
        if ($rest) {
            $mask = (0xff << (8 - $rest)) & 0xff;
            return (ord($ip_bin[$bytes]) & $mask) === (ord($net_bin[$bytes]) & $mask);
        }
        return true;
    }

    /**
     * Every request of a running attempt (and its start): the school network, and the device the
     * attempt belongs to. An attempt the invigilator released goes to the device asking now.
     * Throws RuntimeException with the message for the student.
     */
    public function check(array $exam, array $attempt = null)
    {
        if (!self::ip_allowed($this->client_ip(), isset($exam['allowed_ips']) ? $exam['allowed_ips'] : '')) {
            throw new RuntimeException("This exam can only be taken on the school's network. Ask your teacher.");
        }
        if (!$attempt || empty($exam['lock_device'])) {
            return;
        }
        $key = $this->device_key();
        if (empty($attempt['device_key'])) {
            $this->CI->db->where('id', (int) $attempt['id'])->update('onlineexam_attempts', array('device_key' => $key, 'ip' => $this->client_ip(),
                'user_agent' => mb_substr((string) $this->CI->input->user_agent(), 0, 255)));
            return;
        }
        if (!hash_equals((string) $attempt['device_key'], $key)) {
            throw new RuntimeException('This exam is already open on another device. If that device stopped working, ask your teacher to move your exam to this one.');
        }
    }

    /** What a new attempt records about where it was started. */
    public function stamp()
    {
        return array('device_key' => $this->device_key(), 'ip' => $this->client_ip(), 'user_agent' => mb_substr((string) $this->CI->input->user_agent(), 0, 255));
    }

    /** The PIN typed by the student matches the exam's (none set: always). */
    public static function pin_ok(array $exam, $typed)
    {
        $pin = trim((string) (isset($exam['exam_pin']) ? $exam['exam_pin'] : ''));
        return $pin === '' || strcasecmp($pin, trim((string) $typed)) === 0;
    }

    /** The invigilator moves a student's running attempt to whichever device opens it next. */
    public function release($os_id)
    {
        $a = $this->CI->db->where('onlineexam_student_id', (int) $os_id)->order_by('id', 'desc')->limit(1)->get('onlineexam_attempts')->row_array();
        if (!$a) {
            return false;
        }
        $this->CI->db->where('id', (int) $a['id'])->update('onlineexam_attempts', array('device_key' => null));
        return true;
    }

    // ------------------------------------------------------------------ each student's own questions

    /**
     * $n of the exam's questions for one attempt, picked with $seed: easy / medium / hard in the same
     * proportion as the exam (largest remainder), a reading passage's questions always together.
     * Returns onlineexam_questions ids, or null when every student answers every question.
     */
    public function pick(array $exam, $seed)
    {
        $n = (int) (isset($exam['questions_per_student']) ? $exam['questions_per_student'] : 0);
        if ($n < 1) {
            return null;
        }
        $rows = $this->CI->db->select('oq.id, q.level, q.passage_id')->from('onlineexam_questions oq')->join('questions q', 'q.id = oq.question_id')
            ->where('oq.onlineexam_id', (int) $exam['id'])->order_by('oq.id')->get()->result_array();
        if ($n >= count($rows)) {
            return null;
        }
        // units: a single question, or all the questions of a passage
        $units = array();
        foreach ($rows as $r) {
            $key = $r['passage_id'] ? 'p' . (int) $r['passage_id'] : 'q' . (int) $r['id'];
            if (!isset($units[$key])) {
                $units[$key] = array('ids' => array(), 'level' => in_array($r['level'], array('low', 'medium', 'high'), true) ? $r['level'] : 'medium');
            }
            $units[$key]['ids'][] = (int) $r['id'];
        }
        $by_level = array();
        foreach ($units as $u) {
            $by_level[$u['level']][] = $u;
        }
        // how many of each level: in proportion to the exam
        $total = count($rows);
        $want = array();
        $rem = array();
        foreach ($by_level as $level => $list) {
            $size = array_sum(array_map(function ($u) { return count($u['ids']); }, $list));
            $exact = $n * $size / $total;
            $want[$level] = (int) floor($exact);
            $rem[$level] = $exact - $want[$level];
        }
        arsort($rem);
        foreach (array_keys($rem) as $level) {
            if (array_sum($want) >= $n) {
                break;
            }
            $want[$level]++;
        }
        $picked = array();
        $left = array();
        $i = 0;
        foreach ($by_level as $level => $list) {
            $list = self::shuffle($list, (int) $seed + (++$i) * 7919);
            $got = 0;
            foreach ($list as $u) {
                if ($got + count($u['ids']) <= $want[$level]) {
                    $picked = array_merge($picked, $u['ids']);
                    $got += count($u['ids']);
                } else {
                    $left[] = $u;
                }
            }
        }
        // a level short (passages too long to fit): fill from what is left
        foreach (self::shuffle($left, (int) $seed + 104729) as $u) {
            if (count($picked) + count($u['ids']) <= $n) {
                $picked = array_merge($picked, $u['ids']);
            }
        }
        sort($picked);
        return $picked;
    }

    /** The question ids of the student's own set, or null (all of the exam's questions). */
    public static function own_ids(array $os)
    {
        if (empty($os['question_ids'])) {
            return null;
        }
        $ids = json_decode((string) $os['question_ids'], true);
        return is_array($ids) && $ids ? array_map('intval', $ids) : null;
    }

    private static function shuffle(array $items, $seed)
    {
        $items = array_values($items);
        $state = ((int) $seed) & 0x7fffffff ?: 1;
        for ($i = count($items) - 1; $i > 0; $i--) {
            $state = (int) (($state * 1103515245 + 12345) & 0x7fffffff);
            $j = $state % ($i + 1);
            $t = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $t;
        }
        return $items;
    }

    // ------------------------------------------------------------------ webcam photos

    private function snap_dir()
    {
        // under application/ (not served by the web server): staff see them through snapshot_file()
        $dir = APPPATH . 'cbt_snapshots/' . (int) $this->CI->db->school_id() . '/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0750, true);
        }
        return $dir;
    }

    /** Stores one JPEG photo (data URL or raw bytes, at most 400 KB) of a running attempt. */
    public function snapshot(array $os, array $attempt, $data, $reason)
    {
        if (strpos((string) $data, 'data:image/jpeg;base64,') === 0) {
            $data = base64_decode(substr($data, 23), true);
        }
        if (!is_string($data) || strlen($data) < 500 || strlen($data) > 409600 || substr($data, 0, 3) !== "\xFF\xD8\xFF") {
            throw new RuntimeException('That photo could not be saved.');
        }
        $db = $this->CI->db;
        // at most one photo every 20 seconds per attempt (a page left many times stays small)
        $last = $db->select('created_at')->where('attempt_id', (int) $attempt['id'])->order_by('id', 'desc')->limit(1)->get('onlineexam_snapshots')->row('created_at');
        if ($last && time() - strtotime($last) < 20) {
            return false;
        }
        $file = (int) $attempt['id'] . '_' . bin2hex(random_bytes(8)) . '.jpg';
        if (file_put_contents($this->snap_dir() . $file, $data) === false) {
            throw new RuntimeException('That photo could not be saved.');
        }
        $db->insert('onlineexam_snapshots', array('onlineexam_student_id' => (int) $os['id'], 'attempt_id' => (int) $attempt['id'], 'file' => $file,
            'reason' => in_array($reason, self::SNAP_REASONS, true) ? $reason : 'interval', 'created_at' => date('Y-m-d H:i:s')));
        return true;
    }

    public function snapshots($os_id)
    {
        return $this->CI->db->where('onlineexam_student_id', (int) $os_id)->order_by('id')->get('onlineexam_snapshots')->result_array();
    }

    /** The file path of one photo of this school, or null. */
    public function snapshot_file($id)
    {
        $row = $this->CI->db->where('id', (int) $id)->get('onlineexam_snapshots')->row_array();
        if (!$row || !preg_match('/^\d+_[a-f0-9]{16}\.jpg$/', $row['file'])) {
            return null;
        }
        $path = $this->snap_dir() . $row['file'];
        return is_file($path) ? $path : null;
    }

    /** A new attempt: the previous attempt's photos go. */
    public function delete_snapshots($os_id)
    {
        $db = $this->CI->db;
        foreach ($db->select('file')->where('onlineexam_student_id', (int) $os_id)->get('onlineexam_snapshots')->result_array() as $r) {
            if (preg_match('/^\d+_[a-f0-9]{16}\.jpg$/', $r['file'])) {
                @unlink($this->snap_dir() . $r['file']);
            }
        }
        $db->where('onlineexam_student_id', (int) $os_id)->delete('onlineexam_snapshots');
    }
}
