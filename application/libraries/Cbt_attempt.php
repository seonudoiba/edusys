<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * A student sitting an online exam (CBT), with the server in charge of time and answers.
 *
 *   - start() opens an attempt only between the exam's open and close times, and only while the
 *     student has attempts left. The deadline (ends_at) is the duration from the start or the
 *     close time, whichever comes first. An attempt that is still running is resumed, not
 *     counted again: a reload, a power cut or a lost connection carries on with the time left.
 *   - save() stores one answer as soon as it is chosen (one row per question per student),
 *     until the deadline plus GRACE seconds for answers already on their way.
 *   - submit() closes the attempt and stores the score (Cbt_scoring). An attempt whose
 *     deadline passed without a submit is closed the next time anyone looks (close_expired()).
 *   - the question order (when the exam shuffles questions) and the option order (when it
 *     shuffles options) come from the attempt's seed, so a resumed attempt looks the same.
 *   - every call checks the attempt belongs to the student signed in.
 *
 * A new attempt (when the exam allows more than one) clears the previous answers: the latest
 * attempt is the one that counts.
 */
class Cbt_attempt
{
    const GRACE = 30; // seconds after the deadline when answers already sent still count
    const OPTION_KEYS = array('opt_a', 'opt_b', 'opt_c', 'opt_d', 'opt_e');

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->helper('custom');
        $this->CI->load->library('Cbt_scoring');
    }

    // ------------------------------------------------------------------ lookups

    /** The exam and this student's place in it, or null when the student isn't assigned. */
    public function enrolment($exam_id, $student_session_id)
    {
        $db = $this->CI->db;
        $exam = $db->where(array('id' => (int) $exam_id, 'is_active' => 1))->get('onlineexam')->row_array();
        if (!$exam) {
            return null;
        }
        $os = $db->where(array('onlineexam_id' => (int) $exam['id'], 'student_session_id' => (int) $student_session_id))
            ->get('onlineexam_students')->row_array();
        return $os ? array($exam, $os) : null;
    }

    /** The attempt still running, or null. Closes it first if its deadline has passed. */
    public function running(array $exam, array $os)
    {
        $a = $this->CI->db->where(array('onlineexam_student_id' => (int) $os['id'], 'submitted_at' => null))
            ->order_by('id', 'desc')->limit(1)->get('onlineexam_attempts')->row_array();
        if (!$a) {
            return null;
        }
        if (empty($a['ends_at'])) {
            // Started by the old exam screen: it can't be resumed. Close it.
            $this->close($a, $os);
            return null;
        }
        if (time() > strtotime($a['ends_at']) + self::GRACE) {
            $this->close($a, $os);
            return null;
        }
        return $a;
    }

    public function attempts_used(array $os)
    {
        return (int) $this->CI->db->where('onlineexam_student_id', (int) $os['id'])->count_all_results('onlineexam_attempts');
    }

    /**
     * What the student can do now: array(state, message). States: running, ready, not_open,
     * closed, used_up, no_questions.
     */
    public function state(array $exam, array $os)
    {
        if ($this->running($exam, $os)) {
            return array('running', 'Your exam is in progress. Continue where you stopped.');
        }
        $now = time();
        if ($now < strtotime($exam['exam_from'])) {
            return array('not_open', 'This exam opens on ' . date('j M Y, g:i a', strtotime($exam['exam_from'])) . '.');
        }
        if ($now >= strtotime($exam['exam_to'])) {
            return array('closed', 'This exam closed on ' . date('j M Y, g:i a', strtotime($exam['exam_to'])) . '.');
        }
        if ($this->attempts_used($os) >= (int) $exam['attempt']) {
            return array('used_up', 'You have used all your attempts for this exam.');
        }
        if (!$this->CI->db->where('onlineexam_id', (int) $exam['id'])->count_all_results('onlineexam_questions')) {
            return array('no_questions', 'This exam has no questions yet. Please tell your teacher.');
        }
        return array('ready', '');
    }

    // ------------------------------------------------------------------ the attempt

    /** Starts a new attempt or resumes the running one. Throws RuntimeException with a message for the student. */
    public function start(array $exam, array $os)
    {
        $running = $this->running($exam, $os);
        if ($running) {
            return $running;
        }
        list($state, $message) = $this->state($exam, $os);
        if ($state !== 'ready') {
            throw new RuntimeException($message);
        }
        $db = $this->CI->db;
        $now = time();
        $ends = min($now + max(60, (int) getSecondsFromHMS($exam['duration'])), strtotime($exam['exam_to']));
        $this->CI->load->library('Cbt_integrity');
        $seed = random_int(1, 2000000000);
        $own = $this->CI->cbt_integrity->pick($exam, $seed);
        $db->trans_start();
        // A new attempt: the latest one counts, so earlier answers, photos and score go.
        $this->delete_files($os);
        $this->CI->cbt_integrity->delete_snapshots((int) $os['id']);
        $db->where('onlineexam_student_id', (int) $os['id'])->delete('onlineexam_student_results');
        $db->where('id', (int) $os['id'])->update('onlineexam_students', array('is_attempted' => 0, 'score' => null, 'max_score' => null,
            'neg_score' => null, 'correct_count' => null, 'wrong_count' => null, 'unanswered_count' => null, 'pending_count' => null,
            'percentage' => null, 'passed' => null, 'scored_at' => null, 'question_ids' => $own ? json_encode($own) : null));
        $db->insert('onlineexam_attempts', array(
            'onlineexam_student_id' => (int) $os['id'],
            'started_at' => date('Y-m-d H:i:s', $now),
            'ends_at' => date('Y-m-d H:i:s', $ends),
            'seed' => $seed,
        ) + $this->CI->cbt_integrity->stamp());
        $id = (int) $db->insert_id();
        $db->trans_complete();
        if (!$db->trans_status()) {
            throw new RuntimeException('The exam could not be started. Please try again.');
        }
        return $db->where('id', $id)->get('onlineexam_attempts')->row_array();
    }

    public function seconds_left(array $attempt)
    {
        return max(0, strtotime($attempt['ends_at']) - time());
    }

    /**
     * The questions as the student sees them: no correct answers, options in the attempt's
     * order, reading passages, and what the student has saved so far.
     */
    public function paper(array $exam, array $os, array $attempt)
    {
        $db = $this->CI->db;
        $rows = $db->select('oq.id, oq.marks, oq.neg_marks, q.question, q.question_type, q.opt_a, q.opt_b, q.opt_c, q.opt_d, q.opt_e, q.descriptive_word_limit, q.passage_id, s.name AS subject')
            ->from('onlineexam_questions oq')
            ->join('questions q', 'q.id = oq.question_id')
            ->join('subjects s', 's.id = q.subject_id', 'left')
            ->where('oq.onlineexam_id', (int) $exam['id'])
            ->order_by('oq.id', 'asc')->get()->result_array();
        // the student's own set of questions, when each student gets a different set
        $own = $this->own_ids($os);
        if ($own) {
            $rows = array_values(array_filter($rows, function ($r) use ($own) { return in_array((int) $r['id'], $own, true); }));
        }
        $seed = (int) $attempt['seed'];
        if (!empty($exam['is_random_question'])) {
            $rows = self::shuffled($rows, $seed);
            // Questions sharing a passage stay together, in the place of the first one.
            $rows = self::group_passages($rows);
        }
        $passage_ids = array_filter(array_unique(array_column($rows, 'passage_id')));
        $passages = array();
        if ($passage_ids) {
            foreach ($db->select('id, title, body')->where_in('id', array_map('intval', $passage_ids))->get('question_passages')->result_array() as $p) {
                $passages[(int) $p['id']] = array('title' => $p['title'], 'body' => $p['body']);
            }
        }
        $saved = array();
        foreach ($db->select('onlineexam_question_id, select_option, attachment_name')->where('onlineexam_student_id', (int) $os['id'])
            ->get('onlineexam_student_results')->result_array() as $r) {
            $saved[(int) $r['onlineexam_question_id']] = $r;
        }
        $questions = array();
        foreach ($rows as $r) {
            $id = (int) $r['id'];
            $options = array();
            if ($r['question_type'] === 'true_false') {
                $options = array(array('true', $this->CI->lang->line('true') ?: 'True'), array('false', $this->CI->lang->line('false') ?: 'False'));
            } elseif (in_array($r['question_type'], array('singlechoice', 'multichoice'), true)) {
                foreach (self::OPTION_KEYS as $k) {
                    if (trim(strip_tags((string) $r[$k], '<img><math>')) !== '') {
                        $options[] = array($k, $r[$k]);
                    }
                }
                if (!empty($exam['is_random_option'])) {
                    $options = self::shuffled($options, $seed + $id);
                }
            }
            $answer = null;
            if (isset($saved[$id])) {
                $answer = $r['question_type'] === 'multichoice' ? (json_decode((string) $saved[$id]['select_option'], true) ?: array()) : $saved[$id]['select_option'];
            }
            $limit = self::word_limit($exam, $r);
            $questions[] = array(
                'id' => $id,
                'type' => $r['question_type'],
                'html' => $r['question'],
                'subject' => (string) $r['subject'],
                'marks' => (float) $r['marks'],
                'neg_marks' => !empty($exam['is_neg_marking']) ? (float) $r['neg_marks'] : 0,
                'options' => $options,
                'word_limit' => $r['question_type'] === 'descriptive' ? $limit : 0,
                'passage' => $r['passage_id'] ? (int) $r['passage_id'] : null,
                'answer' => $answer,
                'file' => isset($saved[$id]) && $saved[$id]['attachment_name'] !== '' ? $saved[$id]['attachment_name'] : null,
            );
        }
        return array('questions' => $questions, 'passages' => $passages);
    }

    /** Saves one answer. $value: option key, 'true'/'false', array of option keys, or text. */
    public function save(array $exam, array $os, array $attempt, $question_id, $value)
    {
        $this->require_open($attempt);
        $q = $this->exam_question($exam, $question_id, $os);
        $answer = $this->clean_answer($exam, $q, $value);
        $this->upsert($os, (int) $question_id, array('select_option' => $answer));
    }

    /** A file for a written answer, checked against the school's allowed file types and size. */
    public function upload(array $exam, array $os, array $attempt, $question_id, array $file)
    {
        $this->require_open($attempt);
        $q = $this->exam_question($exam, $question_id, $os);
        if ($q['question_type'] !== 'descriptive') {
            throw new RuntimeException('Files can only be added to written answers.');
        }
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('The file did not arrive. Please try again.');
        }
        $this->CI->load->model('filetype_model');
        $types = $this->CI->filetype_model->get();
        $allowed_ext = array_filter(array_map('trim', array_map('strtolower', explode(',', (string) $types->file_extension))));
        $allowed_mime = array_filter(array_map('trim', array_map('strtolower', explode(',', (string) $types->file_mime))));
        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $mime = strtolower((string) (function_exists('mime_content_type') ? mime_content_type($file['tmp_name']) : $file['type']));
        if ($ext === '' || !in_array($ext, $allowed_ext, true) || ($allowed_mime && !in_array($mime, $allowed_mime, true))) {
            throw new RuntimeException('This type of file is not allowed. Allowed: ' . implode(', ', $allowed_ext) . '.');
        }
        if ((int) $types->file_size > 0 && (int) $file['size'] > (int) $types->file_size) {
            throw new RuntimeException('The file is too big. The largest allowed is ' . round($types->file_size / 1048576, 1) . ' MB.');
        }
        $dir = upload_root() . 'onlinexam_images/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $stored = bin2hex(random_bytes(16)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $dir . $stored)) {
            throw new RuntimeException('The file could not be saved. Please try again.');
        }
        $old = $this->CI->db->select('attachment_upload_name')->where(array('onlineexam_student_id' => (int) $os['id'], 'onlineexam_question_id' => (int) $question_id))
            ->get('onlineexam_student_results')->row();
        $name = mb_substr(preg_replace('/[^\w.\- ]+/u', '_', basename((string) $file['name'])), 0, 200);
        $this->upsert($os, (int) $question_id, array('attachment_name' => $name, 'attachment_upload_name' => $stored));
        if ($old && $old->attachment_upload_name !== '' && preg_match('/^[\w.\-]+$/', $old->attachment_upload_name)) {
            @unlink($dir . $old->attachment_upload_name);
        }
        return $name;
    }

    /** Closes the attempt and stores the score. */
    public function submit(array $exam, array $os, array $attempt)
    {
        $this->close($attempt, $os);
    }

    /** Closes every attempt in the exam whose deadline has passed (staff pages call this). */
    public function close_expired($exam_id)
    {
        $db = $this->CI->db;
        $rows = $db->select('a.*, os.id AS os_id, os.onlineexam_id, os.student_session_id')
            ->from('onlineexam_attempts a')->join('onlineexam_students os', 'os.id = a.onlineexam_student_id')
            ->where('os.onlineexam_id', (int) $exam_id)->where('a.submitted_at IS NULL', null, false)
            ->group_start()->where('a.ends_at IS NULL', null, false)->or_where('a.ends_at <', date('Y-m-d H:i:s', time() - self::GRACE))->group_end()
            ->get()->result_array();
        foreach ($rows as $a) {
            $this->close($a, array('id' => $a['os_id']));
        }
        return count($rows);
    }

    // ------------------------------------------------------------------ helpers

    private function close(array $attempt, array $os)
    {
        $db = $this->CI->db;
        $db->where(array('id' => (int) $attempt['id'], 'submitted_at' => null))->update('onlineexam_attempts', array('submitted_at' => date('Y-m-d H:i:s')));
        $db->where('id', (int) $os['id'])->update('onlineexam_students', array('is_attempted' => 1));
        $this->CI->cbt_scoring->score((int) $os['id']);
    }

    private function require_open(array $attempt)
    {
        if (!empty($attempt['submitted_at'])) {
            throw new RuntimeException('This exam has already been submitted.');
        }
        if (time() > strtotime($attempt['ends_at']) + self::GRACE) {
            throw new RuntimeException('Time is up. Your saved answers have been submitted.');
        }
    }

    /** The word limit of a written answer: the question's own, else the exam's Answer Word Limit; 0 = none. */
    public static function word_limit(array $exam, array $q)
    {
        $limit = (int) $q['descriptive_word_limit'] > 0 ? (int) $q['descriptive_word_limit'] : (int) $exam['answer_word_count'];
        return $limit > 0 ? $limit : 0;
    }

    /** $text cut after its $limit-th word (words: runs of non-space characters); $limit 0 = whole. */
    public static function limit_words($text, $limit)
    {
        if ($limit < 1 || !preg_match_all('/\S+/u', $text, $m, PREG_OFFSET_CAPTURE) || count($m[0]) <= $limit) {
            return $text;
        }
        $last = $m[0][$limit - 1];
        return substr($text, 0, $last[1] + strlen($last[0]));
    }

    /** The student's own question ids (stored on their onlineexam_students row), or null for all. */
    private function own_ids(array $os)
    {
        $this->CI->load->library('Cbt_integrity');
        $row = $this->CI->db->select('question_ids')->where('id', (int) $os['id'])->get('onlineexam_students')->row_array();
        return Cbt_integrity::own_ids($row ?: array());
    }

    private function exam_question(array $exam, $question_id, array $os = null)
    {
        if ($os) {
            $own = $this->own_ids($os);
            if ($own && !in_array((int) $question_id, $own, true)) {
                throw new RuntimeException('That question is not part of your exam.');
            }
        }
        $q = $this->CI->db->select('oq.id, q.question_type, q.opt_a, q.opt_b, q.opt_c, q.opt_d, q.opt_e, q.descriptive_word_limit')
            ->from('onlineexam_questions oq')->join('questions q', 'q.id = oq.question_id')
            ->where(array('oq.id' => (int) $question_id, 'oq.onlineexam_id' => (int) $exam['id']))->get()->row_array();
        if (!$q) {
            throw new RuntimeException('That question is not part of this exam.');
        }
        return $q;
    }

    /** The stored form of an answer, or a RuntimeException when it isn't a valid answer. */
    private function clean_answer(array $exam, array $q, $value)
    {
        $type = $q['question_type'];
        if ($value === null || $value === '' || $value === array()) {
            return null; // cleared
        }
        $valid_keys = array();
        foreach (self::OPTION_KEYS as $k) {
            if (trim(strip_tags((string) $q[$k], '<img><math>')) !== '') {
                $valid_keys[] = $k;
            }
        }
        if ($type === 'true_false') {
            if (!in_array($value, array('true', 'false'), true)) {
                throw new RuntimeException('Choose True or False.');
            }
            return $value;
        }
        if ($type === 'singlechoice') {
            if (!is_string($value) || !in_array($value, $valid_keys, true)) {
                throw new RuntimeException('Choose one of the options.');
            }
            return $value;
        }
        if ($type === 'multichoice') {
            $value = array_values(array_unique((array) $value));
            foreach ($value as $v) {
                if (!is_string($v) || !in_array($v, $valid_keys, true)) {
                    throw new RuntimeException('Choose from the options given.');
                }
            }
            sort($value);
            return $value ? json_encode($value) : null;
        }
        // Written answer: plain text, cut to the word limit (the exam screen stops typing at the
        // limit; words are counted as there: anything between spaces, numbers included).
        $text = trim(mb_substr(strip_tags((string) $value), 0, 20000));
        $text = self::limit_words($text, self::word_limit($exam, $q));
        return $text === '' ? null : $text;
    }

    private function upsert(array $os, $question_id, array $values)
    {
        $db = $this->CI->db;
        $where = array('onlineexam_student_id' => (int) $os['id'], 'onlineexam_question_id' => (int) $question_id);
        $row = $db->select('id')->where($where)->get('onlineexam_student_results')->row();
        if ($row) {
            $db->where('id', (int) $row->id)->update('onlineexam_student_results', $values);
        } else {
            $db->insert('onlineexam_student_results', $where + $values + array('attachment_name' => '', 'attachment_upload_name' => ''));
        }
    }

    private function delete_files(array $os)
    {
        $dir = upload_root() . 'onlinexam_images/';
        foreach ($this->CI->db->select('attachment_upload_name')->where('onlineexam_student_id', (int) $os['id'])
            ->where('attachment_upload_name <>', '')->get('onlineexam_student_results')->result_array() as $r) {
            if (preg_match('/^[\w.\-]+$/', $r['attachment_upload_name'])) {
                @unlink($dir . $r['attachment_upload_name']);
            }
        }
    }

    /** $items in an order fixed by $seed (Fisher-Yates with its own generator: the same seed, the same order). */
    private static function shuffled(array $items, $seed)
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

    /** After shuffling, questions of one passage are put back together where the first one landed. */
    private static function group_passages(array $rows)
    {
        $out = array();
        $placed = array();
        foreach ($rows as $r) {
            $p = $r['passage_id'] ? (int) $r['passage_id'] : 0;
            if (!$p) {
                $out[] = $r;
                continue;
            }
            if (isset($placed[$p])) {
                continue;
            }
            $placed[$p] = true;
            foreach ($rows as $other) {
                if ((int) $other['passage_id'] === $p) {
                    $out[] = $other;
                }
            }
        }
        return $out;
    }
}
