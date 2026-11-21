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
        // each student their own set of questions: only those count (and make the maximum)
        if (!empty($os['question_ids'])) {
            $own = json_decode((string) $os['question_ids'], true);
            if (is_array($own) && $own) {
                $own = array_map('intval', $own);
                $rows = array_values(array_filter($rows, function ($r) use ($own) { return in_array((int) $r['id'], $own, true); }));
            }
        }

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
        if (!empty($exam['marks_auto']) && !empty($os['is_attempted']) && $s['pending_count'] === 0) {
            $this->auto_send($exam, (int) $os['id']);
        }
        return $s;
    }

    /**
     * The exam's automatic sending (marks_auto): its scores go to marks_target as set on Send
     * scores to report card. $onlineexam_student_id: one student (their score just became final),
     * or null for everyone. Never stops the caller (a student submitting): problems are logged.
     */
    public function auto_send(array $exam, $onlineexam_student_id = null)
    {
        $target = (string) $exam['marks_target'];
        if (empty($exam['marks_auto']) || $target === '') {
            return null;
        }
        $scale = !isset($exam['marks_scale']) || (int) $exam['marks_scale'] === 1;
        $replace = !empty($exam['marks_replace']);
        try {
            return strpos($target, 'cbse:') === 0
                ? $this->send_to_cbse((int) $exam['id'], $target, $scale, $replace, $onlineexam_student_id)
                : $this->send_to_report_card((int) $exam['id'], (int) $target, $scale, $replace, $onlineexam_student_id);
        } catch (Exception $e) {
            log_message('error', 'CBT auto-send to report card, exam ' . (int) $exam['id'] . ': ' . $e->getMessage());
            return null;
        }
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
        return $this->CI->db->select('s.id, s.max_marks, s.subject_id, sub.name AS subject, e.exam, e.id AS exam_id, g.name AS exam_group')
            ->from('exam_group_class_batch_exam_subjects s')
            ->join('exam_group_class_batch_exams e', 'e.id = s.exam_group_class_batch_exams_id')
            ->join('exam_groups g', 'g.id = e.exam_group_id')
            ->join('subjects sub', 'sub.id = s.subject_id')
            ->where('e.session_id', (int) $session_id)
            ->order_by('g.name')->order_by('e.exam')->order_by('sub.name')
            ->get()->result_array();
    }

    /**
     * The CBSE Examination report-card assessments of this session: one per exam, subject and
     * assessment type (e.g. First Term Examination > Mathematics > First CA, out of 20).
     * key "cbse:<timetable id>:<assessment type id>".
     */
    public function cbse_targets($session_id)
    {
        $rows = $this->CI->db->select('tt.id AS timetable_id, tt.subject_id, t.id AS type_id, t.name AS type_name, t.code, t.maximum_marks, sub.name AS subject, e.name AS exam, term.name AS term')
            ->from('cbse_exam_timetable tt')
            ->join('cbse_exams e', 'e.id = tt.cbse_exam_id')
            ->join('cbse_exam_timetable_assessment_types link', 'link.cbse_exam_timetable_id = tt.id')
            ->join('cbse_exam_assessment_types t', 't.id = link.cbse_exam_assessment_type_id')
            ->join('subjects sub', 'sub.id = tt.subject_id')
            ->join('cbse_terms term', 'term.id = e.cbse_term_id', 'left')
            ->where('e.session_id', (int) $session_id)
            ->order_by('term.id')->order_by('e.name')->order_by('sub.name')->order_by('t.id')
            ->get()->result_array();
        $out = array();
        foreach ($rows as $r) {
            $out[] = array('key' => 'cbse:' . $r['timetable_id'] . ':' . $r['type_id'], 'subject_id' => (int) $r['subject_id'], 'group' => trim(($r['term'] ? $r['term'] . ' · ' : '') . $r['exam']),
                'label' => $r['subject'] . ' · ' . $r['type_name'] . ' (out of ' . (float) $r['maximum_marks'] . ')', 'max_marks' => (float) $r['maximum_marks']);
        }
        return $out;
    }

    /**
     * Puts the CBT scores into a CBSE Examination assessment (cbse_student_subject_marks), as the
     * teacher would type them under CBSE Examination > Exam > Marks. Same counts as send_to_report_card().
     */
    public function send_to_cbse($onlineexam_id, $key, $scale = true, $replace = false, $only = null)
    {
        $db = $this->CI->db;
        if (!preg_match('/^cbse:(\d+):(\d+)$/', (string) $key, $m)) {
            throw new RuntimeException('Choose the assessment the scores should go to.');
        }
        $timetable_id = (int) $m[1];
        $type_id = (int) $m[2];
        $link = $db->select('link.id, tt.cbse_exam_id, t.maximum_marks')
            ->from('cbse_exam_timetable_assessment_types link')
            ->join('cbse_exam_timetable tt', 'tt.id = link.cbse_exam_timetable_id')
            ->join('cbse_exam_assessment_types t', 't.id = link.cbse_exam_assessment_type_id')
            ->where(array('link.cbse_exam_timetable_id' => $timetable_id, 'link.cbse_exam_assessment_type_id' => $type_id))
            ->get()->row_array();
        if (!$link) {
            throw new RuntimeException('That assessment is no longer part of the exam. Reload the page.');
        }
        if ($db->select('marks_status')->where('id', $timetable_id)->get('cbse_exam_timetable')->row('marks_status') === 'approved') {
            throw new RuntimeException('The scores of that Report Cards subject are approved and locked. An exam manager can unlock them first.');
        }
        $this->CI->load->library('cbse_marks_guard');
        $max = (float) $link['maximum_marks'];
        $exam_students = array();
        foreach ($db->select('id, student_session_id')->where('cbse_exam_id', (int) $link['cbse_exam_id'])->get('cbse_exam_students')->result_array() as $r) {
            $exam_students[(int) $r['student_session_id']] = (int) $r['id'];
        }
        $existing = array();
        foreach ($db->select('id, cbse_exam_student_id, marks')->where(array('cbse_exam_timetable_id' => $timetable_id, 'cbse_exam_assessment_type_id' => $type_id))
            ->get('cbse_student_subject_marks')->result_array() as $r) {
            $existing[(int) $r['cbse_exam_student_id']] = $r;
        }
        $count = array('sent' => 0, 'kept' => 0, 'pending' => 0, 'not_in_exam' => 0, 'not_sat' => 0, 'added' => 0, 'kind' => 'cbse');
        // A student whose class and section are part of that Report Cards exam but who was never
        // added to it (Assign / View Student) is added now, as that screen would.
        $exam_class_sections = array_map('intval', array_column($db->select('class_section_id')->where('cbse_exam_id', (int) $link['cbse_exam_id'])
            ->get('cbse_exam_class_sections')->result_array(), 'class_section_id'));
        $db->trans_start();
        foreach ($this->sat_rows($onlineexam_id, $only) as $os) {
            if (empty($os['is_attempted']) || $os['scored_at'] === null) {
                $count['not_sat']++;
                continue;
            }
            if ((int) $os['pending_count'] > 0) {
                $count['pending']++;
                continue;
            }
            if (!isset($exam_students[(int) $os['student_session_id']])) {
                $cs = $exam_class_sections ? $db->select('cs.id')->from('student_session ss')
                    ->join('class_sections cs', 'cs.class_id = ss.class_id AND cs.section_id = ss.section_id')
                    ->where('ss.id', (int) $os['student_session_id'])->get()->row() : null;
                if (!$cs || !in_array((int) $cs->id, $exam_class_sections, true)) {
                    $count['not_in_exam']++;
                    continue;
                }
                $db->insert('cbse_exam_students', array('cbse_exam_id' => (int) $link['cbse_exam_id'], 'student_session_id' => (int) $os['student_session_id'], 'delete_student_id' => 0));
                $exam_students[(int) $os['student_session_id']] = (int) $db->insert_id();
                $count['added']++;
            }
            $es = $exam_students[(int) $os['student_session_id']];
            $marks = $scale ? round((float) $os['percentage'] / 100 * $max, 2) : min($max, (float) $os['score']);
            if (isset($existing[$es])) {
                if (!$replace && $existing[$es]['marks'] !== null && $existing[$es]['marks'] !== '') {
                    $count['kept']++;
                    continue;
                }
                $db->where('id', (int) $existing[$es]['id'])->update('cbse_student_subject_marks', array('marks' => $marks, 'is_absent' => 0));
                $this->CI->cbse_marks_guard->log_one((int) $link['cbse_exam_id'], $timetable_id, $es, $type_id, $existing[$es], array('marks' => $marks, 'is_absent' => 0), 'cbt');
            } else {
                $db->insert('cbse_student_subject_marks', array('cbse_exam_timetable_assessment_type_id' => (int) $link['id'], 'cbse_exam_timetable_id' => $timetable_id,
                    'cbse_exam_student_id' => $es, 'cbse_exam_assessment_type_id' => $type_id, 'marks' => $marks, 'is_absent' => 0, 'note' => ''));
                $this->CI->cbse_marks_guard->log_one((int) $link['cbse_exam_id'], $timetable_id, $es, $type_id, null, array('marks' => $marks, 'is_absent' => 0), 'cbt');
            }
            $count['sent']++;
        }
        $db->where('id', (int) $onlineexam_id)->update('onlineexam', array('marks_target' => $key));
        $db->trans_complete();
        return $count;
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
    public function send_to_report_card($onlineexam_id, $target_id, $scale = true, $replace = false, $only = null)
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
        $count = array('sent' => 0, 'kept' => 0, 'pending' => 0, 'not_in_exam' => 0, 'not_sat' => 0, 'added' => 0, 'kind' => 'examgroup');
        $rows = array();
        foreach ($this->sat_rows($onlineexam_id, $only) as $os) {
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
        $db->where('id', (int) $onlineexam_id)->update('onlineexam', array('marks_target' => (string) (int) $target['id']));
        return $count;
    }

    /** The exam's students, or just one ($only: onlineexam_students.id). */
    private function sat_rows($onlineexam_id, $only)
    {
        $db = $this->CI->db->where('onlineexam_id', (int) $onlineexam_id);
        if ($only !== null) {
            $db->where('id', (int) $only);
        }
        return $db->get('onlineexam_students')->result_array();
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
