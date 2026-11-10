<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The score of one student in one online exam, worked out in one place and stored on
 * onlineexam_students (score, max_score, neg_score, counts, percentage, passed, scored_at).
 *
 * Rules:
 *   - single choice and true/false: right = the question's marks; wrong = minus its negative marks
 *     when the exam uses negative marking; not answered = 0 (never negative).
 *   - multiple choice: right only when exactly the correct options are chosen.
 *   - written (descriptive): the teacher's mark once marked (marked_at); until then it is "pending"
 *     and the student hasn't passed or failed yet (passed = NULL).
 *   - the total never goes below 0.
 */
class Cbt_scoring
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** Works out and stores the score; returns the stored values. */
    public function score($onlineexam_student_id)
    {
        $db = $this->CI->db;
        $os = $db->where('id', (int) $onlineexam_student_id)->get('onlineexam_students')->row_array();
        if (!$os) {
            return null;
        }
        $exam = $db->where('id', (int) $os['onlineexam_id'])->get('onlineexam')->row_array();
        $rows = $db->select('oq.id, oq.marks, oq.neg_marks, q.question_type, q.correct, r.select_option, r.attachment_upload_name, r.marks AS given, r.marked_at')
            ->from('onlineexam_questions oq')
            ->join('questions q', 'q.id = oq.question_id')
            ->join('onlineexam_student_results r', 'r.onlineexam_question_id = oq.id AND r.onlineexam_student_id = ' . (int) $os['id'], 'left')
            ->where('oq.onlineexam_id', (int) $os['onlineexam_id'])
            ->get()->result_array();

        $neg_on = !empty($exam['is_neg_marking']);
        $s = array('score' => 0.0, 'max_score' => 0.0, 'neg_score' => 0.0, 'correct_count' => 0, 'wrong_count' => 0, 'unanswered_count' => 0, 'pending_count' => 0);
        foreach ($rows as $r) {
            $marks = (float) $r['marks'];
            $s['max_score'] += $marks;
            $verdict = self::verdict($r);
            if ($verdict === 'unanswered') {
                $s['unanswered_count']++;
            } elseif ($verdict === 'pending') {
                $s['pending_count']++;
            } elseif ($verdict === 'marked') {
                $s['score'] += min($marks, max(0, (float) $r['given']));
            } elseif ($verdict === 'correct') {
                $s['correct_count']++;
                $s['score'] += $marks;
            } else {
                $s['wrong_count']++;
                if ($neg_on) {
                    $s['neg_score'] += (float) $r['neg_marks'];
                }
            }
        }
        $s['score'] = max(0, $s['score'] - $s['neg_score']);
        $s['percentage'] = $s['max_score'] > 0 ? round($s['score'] / $s['max_score'] * 100, 2) : 0;
        $s['passed'] = $s['pending_count'] > 0 ? null : (int) ($s['percentage'] >= (float) $exam['passing_percentage']);
        $s['scored_at'] = date('Y-m-d H:i:s');
        $db->where('id', (int) $os['id'])->update('onlineexam_students', $s);
        return $s;
    }

    /** Scores every student in the exam who has submitted (after marking, or to fill in old scores). */
    public function score_exam($onlineexam_id)
    {
        $ids = array_column($this->CI->db->select('id')->where(array('onlineexam_id' => (int) $onlineexam_id, 'is_attempted' => 1))
            ->get('onlineexam_students')->result_array(), 'id');
        foreach ($ids as $id) {
            $this->score($id);
        }
        return count($ids);
    }

    // ------------------------------------------------------------------ report card (Examinations)

    /** The subjects of this session's exams (Examinations > Exam Group), where CBT scores can go. */
    public function report_card_targets($session_id)
    {
        return $this->CI->db->select('s.id, s.max_marks, sub.name AS subject, e.exam, e.id AS exam_id, g.name AS exam_group')
            ->from('exam_group_class_batch_exam_subjects s')
            ->join('exam_group_class_batch_exams e', 'e.id = s.exam_group_class_batch_exams_id')
            ->join('exam_groups g', 'g.id = e.exam_group_id')
            ->join('subjects sub', 'sub.id = s.subject_id')
            ->where('e.session_id', (int) $session_id)
            ->order_by('g.name')->order_by('e.exam')->order_by('sub.name')
            ->get()->result_array();
    }

    /**
     * Puts each student's CBT score into one exam subject's marks (exam_group_exam_results), as
     * the teacher would type them in Examinations > Exam Group > Exam > Assign / View > Marks.
     *
     * $scale: true = the percentage of that subject's maximum marks; false = the score as it is
     * (never above the maximum). $replace: overwrite marks already entered.
     * Returns counts: sent, kept (marks already there), pending (written answers not marked),
     * not_in_exam (student not in that exam), not_sat.
     */
    public function send_to_report_card($onlineexam_id, $target_id, $scale = true, $replace = false)
    {
        $db = $this->CI->db;
        $target = $db->select('s.id, s.max_marks, s.exam_group_class_batch_exams_id')->where('s.id', (int) $target_id)
            ->get('exam_group_class_batch_exam_subjects s')->row_array();
        if (!$target) {
            throw new RuntimeException('Choose the exam subject the scores should go to.');
        }
        $max = (float) $target['max_marks'];
        $exam_students = array();
        foreach ($db->select('id, student_session_id')->where('exam_group_class_batch_exam_id', (int) $target['exam_group_class_batch_exams_id'])
            ->get('exam_group_class_batch_exam_students')->result_array() as $r) {
            $exam_students[(int) $r['student_session_id']] = (int) $r['id'];
        }
        $existing = array();
        foreach ($db->select('exam_group_class_batch_exam_student_id, get_marks')->where('exam_group_class_batch_exam_subject_id', (int) $target['id'])
            ->get('exam_group_exam_results')->result_array() as $r) {
            $existing[(int) $r['exam_group_class_batch_exam_student_id']] = $r['get_marks'];
        }
        $count = array('sent' => 0, 'kept' => 0, 'pending' => 0, 'not_in_exam' => 0, 'not_sat' => 0);
        $rows = array();
        foreach ($db->where('onlineexam_id', (int) $onlineexam_id)->get('onlineexam_students')->result_array() as $os) {
            if (empty($os['is_attempted']) || $os['scored_at'] === null) {
                $count['not_sat']++;
                continue;
            }
            if ((int) $os['pending_count'] > 0) {
                $count['pending']++;
                continue;
            }
            if (!isset($exam_students[(int) $os['student_session_id']])) {
                $count['not_in_exam']++;
                continue;
            }
            $egs = $exam_students[(int) $os['student_session_id']];
            if (!$replace && isset($existing[$egs]) && $existing[$egs] !== null && $existing[$egs] !== '') {
                $count['kept']++;
                continue;
            }
            $marks = $scale ? round((float) $os['percentage'] / 100 * $max, 2) : min($max, (float) $os['score']);
            $rows[] = array('exam_group_class_batch_exam_subject_id' => (int) $target['id'], 'exam_group_class_batch_exam_student_id' => $egs,
                'attendence' => 'present', 'get_marks' => $marks);
            $count['sent']++;
        }
        if ($rows) {
            $this->CI->load->model('examgroupstudent_model');
            $this->CI->examgroupstudent_model->add_result($rows);
        }
        $db->where('id', (int) $onlineexam_id)->update('onlineexam', array('marks_target' => (int) $target['id']));
        return $count;
    }

    /**
     * One answer against its question: correct, wrong, unanswered, pending (written, not marked)
     * or marked (written, marked). $r: question_type, correct, select_option, attachment_upload_name, marked_at.
     */
    public static function verdict(array $r)
    {
        $answer = isset($r['select_option']) ? $r['select_option'] : null;
        if ($r['question_type'] === 'descriptive') {
            $has = ($answer !== null && trim(strip_tags((string) $answer)) !== '') || !empty($r['attachment_upload_name']);
            if (!$has) {
                return 'unanswered';
            }
            return empty($r['marked_at']) ? 'pending' : 'marked';
        }
        if ($answer === null || $answer === '' || $answer === '[]') {
            return 'unanswered';
        }
        if ($r['question_type'] === 'multichoice') {
            $want = json_decode((string) $r['correct'], true);
            $got = json_decode((string) $answer, true);
            if (!is_array($want) || !is_array($got)) {
                return 'wrong';
            }
            sort($want);
            sort($got);
            return array_values($want) === array_values($got) ? 'correct' : 'wrong';
        }
        return (string) $answer === (string) $r['correct'] ? 'correct' : 'wrong';
    }
}
