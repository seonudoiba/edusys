<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Everything about one online exam's results, for the Results page and its Excel download:
 *   - summary: assigned, submitted, not taken, pending marking, passed, failed, average/highest/lowest %
 *   - students: each one's score, percentage, pass/fail and the questions they got wrong or left out
 *   - questions: for each question, how many got it right, wrong or left it, and the wrong answer chosen most
 * Scores are the stored ones (Cbt_scoring); answers are judged with Cbt_scoring::verdict().
 */
class Cbt_report
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('Cbt_scoring');
    }

    public function build($exam_id)
    {
        $db = $this->CI->db;
        $exam_id = (int) $exam_id;
        $exam = $db->where('id', $exam_id)->get('onlineexam')->row_array();
        if (!$exam) {
            return null;
        }

        // questions, numbered in the order they were added to the exam
        $questions = array();
        $n = 0;
        $rows = $db->select('oq.id AS oq_id, oq.marks, q.question, q.question_type, q.correct, q.opt_a, q.opt_b, q.opt_c, q.opt_d, q.opt_e')
            ->from('onlineexam_questions oq')->join('questions q', 'q.id = oq.question_id')
            ->where('oq.onlineexam_id', $exam_id)->order_by('oq.id')->get()->result_array();
        foreach ($rows as $q) {
            $q['no'] = ++$n;
            $q['text'] = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $q['question']), ENT_QUOTES, 'UTF-8')));
            $q['answer_text'] = $this->answer_text($q, $q['correct']);
            $q['stats'] = array('correct' => 0, 'wrong' => 0, 'unanswered' => 0, 'pending' => 0, 'marked' => 0, 'wrong_choices' => array());
            $questions[(int) $q['oq_id']] = $q;
        }

        // students assigned to the exam
        $students = $db->select('os.*, s.id AS student_id, s.firstname, s.middlename, s.lastname, s.admission_no, c.class, se.section')
            ->from('onlineexam_students os')
            ->join('student_session ss', 'ss.id = os.student_session_id')
            ->join('students s', 's.id = ss.student_id')
            ->join('classes c', 'c.id = ss.class_id', 'left')
            ->join('sections se', 'se.id = ss.section_id', 'left')
            ->where('os.onlineexam_id', $exam_id)
            ->order_by('c.id')->order_by('se.section')->order_by('s.firstname')->order_by('s.lastname')
            ->get()->result_array();

        $answers = array();
        $ids = array_map('intval', array_column($students, 'id'));
        if ($ids) {
            foreach ($db->where_in('onlineexam_student_id', $ids)->get('onlineexam_student_results')->result_array() as $a) {
                $answers[(int) $a['onlineexam_student_id']][(int) $a['onlineexam_question_id']] = $a;
            }
        }

        $summary = array('assigned' => count($students), 'submitted' => 0, 'not_taken' => 0, 'pending' => 0, 'passed' => 0, 'failed' => 0,
            'average' => null, 'highest' => null, 'lowest' => null);
        $percentages = array();
        foreach ($students as &$st) {
            $st['name'] = trim($st['firstname'] . ' ' . $st['middlename'] . ' ' . $st['lastname']);
            $st['name'] = preg_replace('/\s+/', ' ', $st['name']);
            $st['missed'] = array();
            $st['status'] = empty($st['is_attempted']) ? 'not_taken' : ((int) $st['pending_count'] > 0 ? 'pending' : 'done');
            if ($st['status'] === 'not_taken') {
                $summary['not_taken']++;
                continue;
            }
            $summary['submitted']++;
            if ($st['status'] === 'pending') {
                $summary['pending']++;
            } elseif ($st['passed'] !== null) {
                $summary[(int) $st['passed'] ? 'passed' : 'failed']++;
            }
            if ($st['percentage'] !== null && $st['status'] === 'done') {
                $percentages[] = (float) $st['percentage'];
            }
            $own = !empty($st['question_ids']) ? json_decode((string) $st['question_ids'], true) : null;
            $own = is_array($own) && $own ? array_map('intval', $own) : null;
            foreach ($questions as $oq_id => &$q) {
                if ($own && !in_array((int) $oq_id, $own, true)) {
                    continue; // not in this student's set
                }
                $a = isset($answers[(int) $st['id']][$oq_id]) ? $answers[(int) $st['id']][$oq_id] : array();
                $verdict = Cbt_scoring::verdict(array(
                    'question_type' => $q['question_type'], 'correct' => $q['correct'],
                    'select_option' => isset($a['select_option']) ? $a['select_option'] : null,
                    'attachment_upload_name' => isset($a['attachment_upload_name']) ? $a['attachment_upload_name'] : null,
                    'marked_at' => isset($a['marked_at']) ? $a['marked_at'] : null,
                ));
                $q['stats'][$verdict]++;
                if ($verdict === 'wrong' || $verdict === 'unanswered') {
                    $given = $verdict === 'wrong' ? $this->answer_text($q, $a['select_option']) : '';
                    $st['missed'][] = array('no' => $q['no'], 'verdict' => $verdict, 'given' => $given);
                    if ($verdict === 'wrong' && $given !== '') {
                        $q['stats']['wrong_choices'][$given] = (isset($q['stats']['wrong_choices'][$given]) ? $q['stats']['wrong_choices'][$given] : 0) + 1;
                    }
                }
            }
            unset($q);
        }
        unset($st);
        if ($percentages) {
            $summary['average'] = round(array_sum($percentages) / count($percentages), 1);
            $summary['highest'] = max($percentages);
            $summary['lowest'] = min($percentages);
        }
        foreach ($questions as &$q) {
            $answered = $q['stats']['correct'] + $q['stats']['wrong'] + $q['stats']['unanswered'];
            $q['stats']['correct_pct'] = $answered ? round($q['stats']['correct'] / $answered * 100) : null;
            arsort($q['stats']['wrong_choices']);
            $q['stats']['top_wrong'] = $q['stats']['wrong_choices'] ? key($q['stats']['wrong_choices']) : '';
            $q['stats']['top_wrong_count'] = $q['stats']['wrong_choices'] ? reset($q['stats']['wrong_choices']) : 0;
        }
        unset($q);
        return array('exam' => $exam, 'summary' => $summary, 'students' => $students, 'questions' => array_values($questions));
    }

    /** An answer as the student saw it: "B. Abuja", "A. 2, C. 7" (multiple), "True", or the written text. */
    public function answer_text(array $q, $value)
    {
        $value = (string) $value;
        if ($value === '' || $value === '[]') {
            return '';
        }
        if ($q['question_type'] === 'descriptive') {
            return trim(mb_substr(strip_tags($value), 0, 80));
        }
        if ($q['question_type'] === 'true_false') {
            return ucfirst($value);
        }
        $keys = $q['question_type'] === 'multichoice' ? (array) json_decode($value, true) : array($value);
        $out = array();
        foreach ($keys as $k) {
            $letter = strtoupper(substr((string) $k, -1)); // opt_b -> B
            $text = isset($q[$k]) ? trim(html_entity_decode(strip_tags((string) $q[$k]), ENT_QUOTES, 'UTF-8')) : '';
            $out[] = $letter . ($text !== '' ? '. ' . mb_substr($text, 0, 40) : '');
        }
        return implode(', ', $out);
    }
}
