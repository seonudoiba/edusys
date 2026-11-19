<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * A Report Cards exam's marks as one spreadsheet: a row per student, a column per subject and
 * assessment (e.g. "Mathematics - First CA (20) [12:5]", the [timetable:assessment type] at the
 * end is how the import finds the column). Export it, fill or correct it in Excel, import it back.
 *
 * In a mark cell: a number from 0 to the maximum, ABS for absent, or empty (left as it is).
 * The same rows the marks screen writes (cbse_student_subject_marks); the student's note is kept.
 */
class Cbse_marks_sheet
{
    const ABSENT = 'ABS';

    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** The exam's subject assessments in order: key "tt:type", link id, label, max. */
    public function columns($exam_id)
    {
        $rows = $this->CI->db->select('tt.id AS tt_id, t.id AS type_id, link.id AS link_id, t.name AS type_name, t.maximum_marks, sub.name AS subject')
            ->from('cbse_exam_timetable tt')
            ->join('cbse_exam_timetable_assessment_types link', 'link.cbse_exam_timetable_id = tt.id')
            ->join('cbse_exam_assessment_types t', 't.id = link.cbse_exam_assessment_type_id')
            ->join('subjects sub', 'sub.id = tt.subject_id')
            ->where('tt.cbse_exam_id', (int) $exam_id)
            ->order_by('sub.name')->order_by('t.id')->get()->result_array();
        $out = array();
        foreach ($rows as $r) {
            $key = (int) $r['tt_id'] . ':' . (int) $r['type_id'];
            $out[$key] = array('tt_id' => (int) $r['tt_id'], 'type_id' => (int) $r['type_id'], 'link_id' => (int) $r['link_id'], 'max' => (float) $r['maximum_marks'],
                'label' => $r['subject'] . ' - ' . $r['type_name'] . ' (' . (float) $r['maximum_marks'] . ') [' . $key . ']');
        }
        return $out;
    }

    /** The exam's students (Assign / View Student), by class, section and admission no. */
    public function students($exam_id)
    {
        return $this->CI->db->select('es.id AS exam_student_id, s.admission_no, s.firstname, s.middlename, s.lastname, c.class, sec.section')
            ->from('cbse_exam_students es')
            ->join('student_session ss', 'ss.id = es.student_session_id')
            ->join('students s', 's.id = ss.student_id')
            ->join('classes c', 'c.id = ss.class_id')
            ->join('sections sec', 'sec.id = ss.section_id')
            ->where('es.cbse_exam_id', (int) $exam_id)->where('s.is_active', 'yes')
            ->order_by('c.id')->order_by('sec.section')->order_by('s.admission_no')->get()->result_array();
    }

    /** The marks already there: [exam_student_id]["tt:type"] => row. */
    private function marks(array $exam_student_ids)
    {
        $out = array();
        if (!$exam_student_ids) {
            return $out;
        }
        foreach (array_chunk($exam_student_ids, 500) as $chunk) {
            foreach ($this->CI->db->select('id, cbse_exam_student_id, cbse_exam_timetable_id, cbse_exam_assessment_type_id, marks, is_absent')
                ->where_in('cbse_exam_student_id', $chunk)->get('cbse_student_subject_marks')->result_array() as $m) {
                $out[(int) $m['cbse_exam_student_id']][(int) $m['cbse_exam_timetable_id'] . ':' . (int) $m['cbse_exam_assessment_type_id']] = $m;
            }
        }
        return $out;
    }

    /** The sheet: array(headings, rows). */
    public function export($exam_id)
    {
        $cols = $this->columns($exam_id);
        $students = $this->students($exam_id);
        $marks = $this->marks(array_map('intval', array_column($students, 'exam_student_id')));
        $sch = $this->CI->setting_model->getSetting();
        $headings = array_merge(array('Admission no', 'Student', 'Class'), array_column($cols, 'label'));
        $rows = array();
        foreach ($students as $st) {
            $row = array($st['admission_no'], $this->CI->customlib->getFullName($st['firstname'], $st['middlename'], $st['lastname'], $sch->middlename, $sch->lastname),
                $st['class'] . ' (' . $st['section'] . ')');
            foreach ($cols as $key => $c) {
                $m = isset($marks[(int) $st['exam_student_id']][$key]) ? $marks[(int) $st['exam_student_id']][$key] : null;
                $row[] = !$m ? '' : ((int) $m['is_absent'] === 1 ? self::ABSENT : ($m['marks'] === null || $m['marks'] === '' ? '' : (string) (float) $m['marks']));
            }
            $rows[] = $row;
        }
        return array($headings, $rows);
    }

    /**
     * Saves the marks of an uploaded sheet (rows as read, heading row first). Returns
     * array('saved' => n (marks changed), 'students' => n, 'problems' => [text, ...], 'columns' => n).
     */
    public function import($exam_id, array $rows)
    {
        $result = array('saved' => 0, 'students' => 0, 'columns' => 0, 'problems' => array());
        if (!$rows) {
            $result['problems'][] = 'The file is empty.';
            return $result;
        }
        $cols = $this->columns($exam_id);
        $headings = array_map(function ($h) { return trim((string) $h); }, array_shift($rows));
        $adm_col = null;
        $map = array(); // column index => key
        foreach ($headings as $i => $h) {
            if ($adm_col === null && preg_match('/^admission\s*no/i', $h)) {
                $adm_col = $i;
            } elseif (preg_match('/\[(\d+:\d+)\]\s*$/', $h, $m)) {
                if (isset($cols[$m[1]])) {
                    $map[$i] = $m[1];
                } else {
                    $result['problems'][] = 'Column "' . $h . '" is not part of this exam (the file may be from another exam). It was skipped.';
                }
            }
        }
        if ($adm_col === null || !$map) {
            $result['problems'][] = 'This is not a marks sheet of this exam: download it with Export marks, fill it in and import that file.';
            return $result;
        }
        $result['columns'] = count($map);
        $students = array();
        foreach ($this->students($exam_id) as $st) {
            $students[strtolower(trim($st['admission_no']))] = (int) $st['exam_student_id'];
        }
        $existing = $this->marks(array_values($students));
        $db = $this->CI->db;
        $db->trans_start();
        foreach ($rows as $n => $cells) {
            $line = $n + 2; // the heading is row 1
            if (trim(implode('', array_map('strval', $cells))) === '') {
                continue;
            }
            $adm = strtolower(trim((string) (isset($cells[$adm_col]) ? $cells[$adm_col] : '')));
            if (!isset($students[$adm])) {
                $result['problems'][] = 'Row ' . $line . ': admission no "' . trim((string) $cells[$adm_col]) . '" is not in this exam.';
                continue;
            }
            $es = $students[$adm];
            $touched = false;
            foreach ($map as $i => $key) {
                $v = trim((string) (isset($cells[$i]) ? $cells[$i] : ''));
                if ($v === '') {
                    continue;
                }
                $c = $cols[$key];
                if (in_array(strtoupper($v), array('ABS', 'A', 'ABSENT'), true)) {
                    $values = array('marks' => 0, 'is_absent' => 1);
                } elseif (is_numeric($v) && (float) $v >= 0 && (float) $v <= $c['max']) {
                    $values = array('marks' => round((float) $v, 2), 'is_absent' => 0);
                } else {
                    $result['problems'][] = 'Row ' . $line . ' (' . trim((string) $cells[$adm_col]) . '), ' . preg_replace('/\s*\[\d+:\d+\]$/', '', $c['label']) . ': "' . $v . '" is not a mark from 0 to ' . $c['max'] . ' or ABS. Not saved.';
                    continue;
                }
                if (isset($existing[$es][$key])) {
                    $was = $existing[$es][$key];
                    if (isset($was['marks']) && (int) $was['is_absent'] === $values['is_absent'] && $was['marks'] !== null && $was['marks'] !== ''
                        && abs((float) $was['marks'] - $values['marks']) < 0.001) {
                        continue; // the same mark as already there (e.g. the exported value left as it was)
                    }
                    $db->where('id', (int) $was['id'])->update('cbse_student_subject_marks', $values);
                } else {
                    $db->insert('cbse_student_subject_marks', $values + array('cbse_exam_timetable_assessment_type_id' => $c['link_id'], 'cbse_exam_timetable_id' => $c['tt_id'],
                        'cbse_exam_student_id' => $es, 'cbse_exam_assessment_type_id' => $c['type_id'], 'note' => ''));
                    $existing[$es][$key] = array('id' => $db->insert_id());
                }
                $result['saved']++;
                $touched = true;
            }
            if ($touched) {
                $result['students']++;
            }
        }
        $db->trans_complete();
        return $result;
    }

    /**
     * Each student's total for the exam: [student_session_id] => array(score, max, percentage).
     * max is every assessment of every subject of the exam (as the rank counts them).
     */
    public function totals($exam_id)
    {
        $max = array_sum(array_column($this->columns($exam_id), 'max'));
        $out = array();
        foreach ($this->CI->db->select('es.student_session_id, SUM(m.marks) AS score', false)
            ->from('cbse_exam_students es')
            ->join('cbse_student_subject_marks m', 'm.cbse_exam_student_id = es.id')
            ->join('cbse_exam_timetable tt', 'tt.id = m.cbse_exam_timetable_id')
            ->where('es.cbse_exam_id', (int) $exam_id)->where('tt.cbse_exam_id', (int) $exam_id)
            ->group_by('es.student_session_id')->get()->result_array() as $r) {
            $score = round((float) $r['score'], 2);
            $out[(int) $r['student_session_id']] = array('score' => $score, 'max' => $max, 'percentage' => $max > 0 ? round($score / $max * 100, 2) : null);
        }
        return $out;
    }
}
