<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CBT exams by class and subject (CBT Exams pages and Report Cards > Results > subject):
 *  - which CBTs a class has (by the students given the CBT), for a subject, and which report card
 *    exam each sends its scores to (onlineexam.marks_target 'cbse:<timetable id>:<assessment id>');
 *  - figures of each CBT: questions, marks, students, taken, average;
 *  - making a CBT for a class (its students given it), changing its dates and settings, opening and
 *    closing it, drawing questions from the question bank by level, pulling its scores into the report card.
 */
class Cbt_admin
{
    private $CI;
    private $db;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->db = $this->CI->db;
    }

    /** CBTs given to a class this session: [onlineexam_id => students of the class given it]. */
    public function class_cbts($class_id, $session_id)
    {
        $out = array();
        foreach ($this->db->select('os.onlineexam_id AS id, COUNT(*) AS n', false)->from('onlineexam_students os')
            ->join('student_session ss', 'ss.id = os.student_session_id')->join('onlineexam o', 'o.id = os.onlineexam_id')
            ->where(array('ss.class_id' => (int) $class_id, 'o.session_id' => (int) $session_id))->group_by('os.onlineexam_id')->get()->result_array() as $r) {
            $out[(int) $r['id']] = (int) $r['n'];
        }
        return $out;
    }

    /** Number of CBTs per class this session, and the CBTs given to no class. */
    public function per_class($session_id)
    {
        $per = array();
        foreach ($this->db->select('ss.class_id, COUNT(DISTINCT os.onlineexam_id) AS n', false)->from('onlineexam_students os')
            ->join('student_session ss', 'ss.id = os.student_session_id')->join('onlineexam o', 'o.id = os.onlineexam_id')
            ->where('o.session_id', (int) $session_id)->group_by('ss.class_id')->get()->result_array() as $r) {
            $per[(int) $r['class_id']] = (int) $r['n'];
        }
        $none = (int) $this->db->where('session_id', (int) $session_id)
            ->where('NOT EXISTS (SELECT 1 FROM onlineexam_students s WHERE s.onlineexam_id = onlineexam.id)', null, false) // tenancy-lint: ok correlated to onlineexam
            ->count_all_results('onlineexam');
        return array($per, $none);
    }

    /** Report card exams of a class's subject a CBT can send its scores to: ['cbse:tt:type' => [label, taken, term_id]]. */
    public function targets($class_id, $subject_id, $session_id)
    {
        $this->CI->load->library('class_results');
        $cr = $this->CI->class_results;
        $out = array();
        foreach ($cr->terms() as $t) {
            $exam = $cr->exam_for((int) $class_id, (int) $t['id'], (int) $session_id);
            if (!$exam) {
                continue;
            }
            foreach ($cr->subjects($exam, (int) $class_id) as $tt => $s) {
                if ((int) $s['subject_id'] !== (int) $subject_id) {
                    continue;
                }
                foreach ($s['types'] as $tid => $ty) {
                    $out['cbse:' . $tt . ':' . $tid] = array('label' => $t['name'] . ' — ' . $ty['name'] . ' (' . (float) $ty['maximum_marks'] . ' marks)', 'taken' => (bool) $ty['cbt'], 'term_id' => (int) $t['id']);
                }
            }
        }
        return $out;
    }

    /** Rows of CBTs with their figures and report card link. */
    public function rows(array $ids)
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (!$ids) {
            return array();
        }
        $out = array();
        foreach ($this->db->select('o.*, s.name AS subject_name')->from('onlineexam o')->join('subjects s', 's.id = o.subject_id', 'left')
            ->where_in('o.id', $ids)->order_by('o.exam_from', 'desc')->get()->result_array() as $o) {
            $o['questions'] = 0;
            $o['total_marks'] = 0;
            $o['students'] = 0;
            $o['taken'] = 0;
            $o['average'] = null;
            $o['to_mark'] = 0;
            $o['link'] = null;
            $out[(int) $o['id']] = $o;
        }
        foreach ($this->db->select('onlineexam_id AS id, COUNT(*) AS n, SUM(marks) AS m', false)->where_in('onlineexam_id', $ids)->group_by('onlineexam_id')->get('onlineexam_questions')->result_array() as $r) {
            $out[(int) $r['id']]['questions'] = (int) $r['n'];
            $out[(int) $r['id']]['total_marks'] = (float) $r['m'];
        }
        foreach ($this->db->select('onlineexam_id AS id, COUNT(*) AS n, SUM(is_attempted = 1) AS t, AVG(CASE WHEN is_attempted = 1 THEN percentage END) AS a, SUM(is_attempted = 1 AND pending_count > 0) AS w', false)->where_in('onlineexam_id', $ids)->group_by('onlineexam_id')->get('onlineexam_students')->result_array() as $r) {
            $out[(int) $r['id']]['to_mark'] = (int) $r['w'];
            $out[(int) $r['id']]['students'] = (int) $r['n'];
            $out[(int) $r['id']]['taken'] = (int) $r['t'];
            $out[(int) $r['id']]['average'] = $r['a'] === null ? null : round((float) $r['a'], 1);
        }
        foreach ($out as $id => &$o) {
            if ($o['marks_target'] && preg_match('/^cbse:(\d+):(\d+)$/', $o['marks_target'], $m)) {
                $o['link'] = $this->db->select('tt.id AS tt_id, tt.cbse_exam_id, tt.subject_id, e.cbse_term_id, e.class_id, tr.name AS term_name, t.id AS type_id, t.name AS assessment, t.maximum_marks')
                    ->from('cbse_exam_timetable tt')->join('cbse_exams e', 'e.id = tt.cbse_exam_id')->join('cbse_terms tr', 'tr.id = e.cbse_term_id', 'left')
                    ->join('cbse_exam_assessment_types t', 't.id = ' . (int) $m[2], 'inner', false)->where('tt.id', (int) $m[1])->get()->row_array() ?: null; // tenancy-lint: ok int
            }
            $o['state'] = $this->state($o);
        }
        unset($o);
        return $out;
    }

    /** draft (off) | upcoming | open | closed */
    public function state(array $o)
    {
        if (!(int) $o['is_active']) {
            return 'draft';
        }
        $now = time();
        if ($now < strtotime($o['exam_from'])) {
            return 'upcoming';
        }
        return $now > strtotime($o['exam_to']) ? 'closed' : 'open';
    }

    /** The CBTs of a class for a subject: those given to the class with that subject, or sending scores to its report card. */
    public function for_class_subject($class_id, $subject_id, $session_id)
    {
        $given = $this->class_cbts($class_id, $session_id);
        $ids = array();
        if ($given) {
            $ids = array_map('intval', array_column($this->db->select('id')->where_in('id', array_keys($given))->where('subject_id', (int) $subject_id)->get('onlineexam')->result_array(), 'id'));
        }
        // linked to this class's report card for the subject
        foreach ($this->db->select('o.id, o.marks_target')->like('o.marks_target', 'cbse:', 'after')->where('o.session_id', (int) $session_id)->get('onlineexam o')->result_array() as $o) {
            if (preg_match('/^cbse:(\d+):/', $o['marks_target'], $m)) {
                $tt = $this->db->select('tt.subject_id, e.class_id, e.id AS exam_id')->from('cbse_exam_timetable tt')->join('cbse_exams e', 'e.id = tt.cbse_exam_id')->where('tt.id', (int) $m[1])->get()->row_array();
                if ($tt && (int) $tt['subject_id'] === (int) $subject_id && ((int) $tt['class_id'] === (int) $class_id || isset($given[(int) $o['id']]))) {
                    $ids[] = (int) $o['id'];
                }
            }
        }
        $rows = $this->rows(array_unique($ids));
        foreach ($rows as $id => &$r) {
            $r['class_students'] = isset($given[$id]) ? $given[$id] : 0;
        }
        unset($r);
        return $rows;
    }

    /**
     * Makes a CBT for students. $f: title, subject_id, from, to (Y-m-d H:i), minutes, attempts, pass,
     * target ('cbse:tt:type' or ''). Returns its id.
     */
    public function create(array $f, array $student_session_ids)
    {
        $from = strtotime((string) $f['from']) ?: strtotime('today 08:00');
        $to = strtotime((string) $f['to']) ?: strtotime('+7 days 17:00', $from);
        if ($to <= $from) {
            throw new RuntimeException('The closing time must be after the opening time.');
        }
        $minutes = max(1, min(600, (int) $f['minutes']));
        $this->db->insert('onlineexam', array('session_id' => (int) $f['session_id'], 'exam' => mb_substr(trim((string) $f['title']), 0, 250) ?: 'CBT',
            'subject_id' => (int) $f['subject_id'] ?: null, 'attempt' => max(1, min(10, (int) $f['attempts'])), 'exam_from' => date('Y-m-d H:i:s', $from),
            'exam_to' => date('Y-m-d H:i:s', $to), 'duration' => sprintf('%02d:%02d:00', intdiv($minutes, 60), $minutes % 60), 'passing_percentage' => max(0, min(100, (float) $f['pass'])),
            'description' => (string) (isset($f['description']) ? $f['description'] : ''), 'publish_result' => 1, 'is_active' => 0, 'is_marks_display' => 1, 'is_neg_marking' => 0,
            'is_random_question' => 1, 'is_random_option' => 1, 'is_quiz' => 0, 'answer_word_count' => -1, 'max_tab_switches' => 3, 'block_copy' => 1));
        $id = (int) $this->db->insert_id();
        foreach (array_unique(array_map('intval', $student_session_ids)) as $ss) {
            $this->db->insert('onlineexam_students', array('onlineexam_id' => $id, 'student_session_id' => $ss));
        }
        if (!empty($f['target'])) {
            $this->link($id, $f['target']);
        }
        return $id;
    }

    /** Its scores go to this report card exam (one CBT per exam). */
    public function link($id, $target)
    {
        if (!preg_match('/^cbse:\d+:\d+$/', (string) $target)) {
            return;
        }
        $this->db->where('marks_target', $target)->where('id !=', (int) $id)->update('onlineexam', array('marks_target' => null, 'marks_auto' => 0));
        $this->db->where('id', (int) $id)->update('onlineexam', array('marks_target' => $target, 'marks_auto' => 1, 'marks_scale' => 1, 'marks_replace' => 1));
    }

    public function update($id, array $f)
    {
        $set = array();
        if (isset($f['title']) && trim($f['title']) !== '') {
            $set['exam'] = mb_substr(trim($f['title']), 0, 250);
        }
        if (!empty($f['from']) && !empty($f['to'])) {
            $from = strtotime($f['from']);
            $to = strtotime($f['to']);
            if (!$from || !$to || $to <= $from) {
                throw new RuntimeException('The closing time must be after the opening time.');
            }
            $set['exam_from'] = date('Y-m-d H:i:s', $from);
            $set['exam_to'] = date('Y-m-d H:i:s', $to);
        }
        if (isset($f['minutes']) && (int) $f['minutes'] > 0) {
            $m = min(600, (int) $f['minutes']);
            $set['duration'] = sprintf('%02d:%02d:00', intdiv($m, 60), $m % 60);
        }
        if (isset($f['attempts']) && (int) $f['attempts'] > 0) {
            $set['attempt'] = min(10, (int) $f['attempts']);
        }
        if ($set) {
            $this->db->where('id', (int) $id)->update('onlineexam', $set);
        }
    }

    /** Opens (students can take it in its window) or closes it. An exam without questions stays closed. */
    public function set_active($id, $on)
    {
        if ($on && !$this->db->where('onlineexam_id', (int) $id)->count_all_results('onlineexam_questions')) {
            throw new RuntimeException('Add questions first: a CBT without questions cannot be opened.');
        }
        $this->db->where('id', (int) $id)->update('onlineexam', array('is_active' => $on ? 1 : 0));
    }

    /** Adds random questions of each level from the subject's question bank (not already in it). Returns array(added, shortages). */
    public function draw($id, array $levels, $marks, $class_id = 0)
    {
        $exam = $this->db->where('id', (int) $id)->get('onlineexam')->row_array();
        if (!$exam || !(int) $exam['subject_id']) {
            throw new RuntimeException('The CBT needs a subject.');
        }
        if ($marks <= 0) {
            throw new RuntimeException('Marks for each question must be more than 0.');
        }
        $in = array_map('intval', array_column($this->db->select('question_id')->where('onlineexam_id', (int) $id)->get('onlineexam_questions')->result_array(), 'question_id'));
        $added = 0;
        $short = array();
        foreach ($levels as $level => $n) {
            $n = max(0, min(200, (int) $n));
            if (!$n || !in_array($level, array('low', 'medium', 'high'), true)) {
                continue;
            }
            // the class's own questions first when it has enough of that level
            $own = $class_id ? $this->db->where(array('subject_id' => (int) $exam['subject_id'], 'level' => $level, 'class_id' => (int) $class_id))->count_all_results('questions') : 0;
            $this->db->select('id')->from('questions')->where(array('subject_id' => (int) $exam['subject_id'], 'level' => $level));
            if ($class_id && $own >= $n) {
                $this->db->where('class_id', (int) $class_id);
            }
            if ($in) {
                $this->db->where_not_in('id', $in);
            }
            $ids = array_map('intval', array_column($this->db->order_by('RAND()', '', false)->limit($n)->get()->result_array(), 'id'));
            foreach ($ids as $q) {
                $this->db->insert('onlineexam_questions', array('onlineexam_id' => (int) $id, 'question_id' => $q, 'marks' => (float) $marks, 'neg_marks' => 0, 'session_id' => $exam['session_id'], 'is_active' => 1));
                $in[] = $q;
                $added++;
            }
            if (count($ids) < $n) {
                $short[] = array('low' => 'Easy', 'medium' => 'Medium', 'high' => 'Hard')[$level] . ': only ' . count($ids) . ' of ' . $n;
            }
        }
        return array($added, $short);
    }

    /** Sends every finished attempt's score to the linked report card exam (scaled). Returns the counts. */
    public function pull_scores($id)
    {
        $o = $this->db->where('id', (int) $id)->get('onlineexam')->row_array();
        if (!$o || !$o['marks_target']) {
            throw new RuntimeException('This CBT is not linked to a report card exam.');
        }
        $this->CI->load->library(array('Cbt_attempt', 'Cbt_scoring'));
        $this->CI->cbt_attempt->close_expired((int) $id);
        foreach ($this->db->select('id')->where(array('onlineexam_id' => (int) $id, 'is_attempted' => 1))->where('scored_at IS NULL', null, false)->get('onlineexam_students')->result_array() as $r) {
            $this->CI->cbt_scoring->score((int) $r['id']);
        }
        return $this->CI->cbt_scoring->send_to_cbse((int) $id, $o['marks_target'], true, true);
    }
}
