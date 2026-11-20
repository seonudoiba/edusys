<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Report Cards figures for whole classes and single students:
 *  - sheet(): the broadsheet of an exam (students × subjects): subject totals, overall total,
 *    percentage, grade and position (equal scores share a position, the next is skipped), and each
 *    subject's average, highest and lowest;
 *  - transcript(): every Report Cards exam a student sat, across sessions;
 *  - export_rows(): every score of every exam (for archiving / migration).
 */
class Cbse_broadsheet
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** The exam's grade ranges, best first: [[name, min, max, description]]. */
    public function grades($exam_id)
    {
        $out = array();
        foreach ($this->CI->db->select('r.name, r.minimum_percentage, r.maximum_percentage, r.description')
            ->from('cbse_exams e')->join('cbse_exam_grades_range r', 'r.cbse_exam_grade_id = e.cbse_exam_grade_id')
            ->where('e.id', (int) $exam_id)->order_by('r.minimum_percentage', 'desc')->get()->result_array() as $r) {
            $out[] = array($r['name'], (float) $r['minimum_percentage'], (float) $r['maximum_percentage'], $r['description']);
        }
        return $out;
    }

    public static function grade_for(array $grades, $pct)
    {
        if ($pct === null) {
            return '';
        }
        foreach ($grades as $g) {
            if ($pct >= $g[1] - 0.0001) {
                return $g[0];
            }
        }
        return $grades ? $grades[count($grades) - 1][0] : '';
    }

    /** Positions for [key => score] (higher is better): [key => position], equal scores share one. */
    public static function positions(array $scores)
    {
        arsort($scores);
        $out = array();
        $pos = 0;
        $prev = null;
        $rank = 0;
        foreach ($scores as $k => $v) {
            $pos++;
            if ($prev === null || abs($v - $prev) > 0.0001) {
                $rank = $pos;
                $prev = $v;
            }
            $out[$k] = $rank;
        }
        return $out;
    }

    /** Class sections of an exam: [id => "JSS 1 (A)"]. */
    public function class_sections($exam_id)
    {
        $out = array();
        foreach ($this->CI->db->select('cs.id, c.class, s.section')->from('cbse_exam_class_sections ecs')
            ->join('class_sections cs', 'cs.id = ecs.class_section_id')->join('classes c', 'c.id = cs.class_id')->join('sections s', 's.id = cs.section_id')
            ->where('ecs.cbse_exam_id', (int) $exam_id)->order_by('c.class')->order_by('s.section')->get()->result_array() as $r) {
            $out[(int) $r['id']] = $r['class'] . ' (' . $r['section'] . ')';
        }
        return $out;
    }

    /**
     * The broadsheet. $class_section_id 0 = every class / section of the exam (positions across them).
     * Returns array(subjects, students, stats, grades).
     */
    public function sheet($exam_id, $class_section_id = 0)
    {
        $db = $this->CI->db;
        $subjects = array();
        foreach ($db->select('tt.id, sub.name, sub.code, SUM(t.maximum_marks) AS max', false)->from('cbse_exam_timetable tt')
            ->join('subjects sub', 'sub.id = tt.subject_id')
            ->join('cbse_exam_timetable_assessment_types l', 'l.cbse_exam_timetable_id = tt.id')
            ->join('cbse_exam_assessment_types t', 't.id = l.cbse_exam_assessment_type_id')
            ->where('tt.cbse_exam_id', (int) $exam_id)->group_by('tt.id')->order_by('sub.name')->get()->result_array() as $r) {
            $subjects[(int) $r['id']] = array('name' => $r['name'], 'code' => $r['code'], 'max' => (float) $r['max']);
        }
        $db->select("es.id AS es, ss.id AS student_session_id, st.id AS student_id, st.admission_no, st.firstname, st.middlename, st.lastname, c.class, s.section, cs.id AS class_section_id", false)
            ->from('cbse_exam_students es')
            ->join('student_session ss', 'ss.id = es.student_session_id')
            ->join('students st', 'st.id = ss.student_id')
            ->join('classes c', 'c.id = ss.class_id')->join('sections s', 's.id = ss.section_id')
            ->join('class_sections cs', 'cs.class_id = ss.class_id AND cs.section_id = ss.section_id')
            ->where('es.cbse_exam_id', (int) $exam_id)->where('st.is_active', 'yes');
        if ($class_section_id) {
            $db->where('cs.id', (int) $class_section_id);
        }
        $students = array();
        foreach ($db->order_by('c.class')->order_by('s.section')->order_by('st.firstname')->get()->result_array() as $r) {
            $r['name'] = trim(preg_replace('/\s+/', ' ', $r['firstname'] . ' ' . $r['middlename'] . ' ' . $r['lastname']));
            $r['marks'] = array();
            $r['absent'] = array();
            $students[(int) $r['es']] = $r;
        }
        if ($students && $subjects) {
            foreach (array_chunk(array_keys($students), 500) as $chunk) {
                foreach ($db->select('m.cbse_exam_student_id AS es, m.cbse_exam_timetable_id AS tt, SUM(m.marks) AS got, MIN(m.is_absent) AS all_absent', false)
                    ->from('cbse_student_subject_marks m')->where_in('m.cbse_exam_student_id', $chunk)
                    ->where_in('m.cbse_exam_timetable_id', array_keys($subjects))
                    ->group_by(array('m.cbse_exam_student_id', 'm.cbse_exam_timetable_id'))->get()->result_array() as $m) {
                    $students[(int) $m['es']]['marks'][(int) $m['tt']] = round((float) $m['got'], 2);
                    if ((int) $m['all_absent'] === 1) {
                        $students[(int) $m['es']]['absent'][(int) $m['tt']] = true;
                    }
                }
            }
        }
        $grades = $this->grades($exam_id);
        $totals = array();
        foreach ($students as $es => &$st) {
            $got = 0;
            $max = 0;
            foreach ($subjects as $tt => $sub) {
                if (isset($st['marks'][$tt])) {
                    $got += $st['marks'][$tt];
                    $max += $sub['max'];
                }
            }
            $st['total'] = round($got, 2);
            $st['max'] = $max;
            $st['subjects_taken'] = count($st['marks']);
            $st['percentage'] = $max > 0 ? round($got * 100 / $max, 2) : null;
            $st['average'] = $st['subjects_taken'] ? round($got / $st['subjects_taken'], 2) : null;
            $st['grade'] = self::grade_for($grades, $st['percentage']);
            if ($st['percentage'] !== null) {
                $totals[$es] = $st['percentage'];
            }
        }
        unset($st);
        foreach (self::positions($totals) as $es => $pos) {
            $students[$es]['position'] = $pos;
        }
        $stats = array();
        foreach ($subjects as $tt => $sub) {
            $vals = array();
            $by = array();
            foreach ($students as $es => $st) {
                if (isset($st['marks'][$tt])) {
                    $vals[] = $st['marks'][$tt];
                    $by[$es] = $st['marks'][$tt];
                }
            }
            $stats[$tt] = $vals ? array('avg' => round(array_sum($vals) / count($vals), 2), 'high' => max($vals), 'low' => min($vals), 'count' => count($vals), 'pos' => self::positions($by)) : null;
        }
        return array($subjects, $students, $stats, $grades);
    }

    /** Every Report Cards exam of a student (all sessions), newest session first. */
    public function transcript($student_id)
    {
        $db = $this->CI->db;
        $exams = $db->select('es.id AS es, ss.id AS student_session_id, e.id AS exam_id, e.name AS exam_name, t.name AS term_name, se.session, c.class, s.section, e.session_id, e.is_publish')
            ->from('cbse_exam_students es')
            ->join('student_session ss', 'ss.id = es.student_session_id')
            ->join('cbse_exams e', 'e.id = es.cbse_exam_id')
            ->join('cbse_terms t', 't.id = e.cbse_term_id', 'left')
            ->join('sessions se', 'se.id = e.session_id', 'left')
            ->join('classes c', 'c.id = ss.class_id')->join('sections s', 's.id = ss.section_id')
            ->where('ss.student_id', (int) $student_id)
            ->order_by('se.session', 'desc')->order_by('e.id', 'asc')->get()->result_array();
        foreach ($exams as &$x) {
            $grades = $this->grades($x['exam_id']);
            $x['subjects'] = array();
            $got = 0;
            $max = 0;
            foreach ($db->select('sub.name, SUM(m.marks) AS got, SUM(t.maximum_marks) AS max', false)
                ->from('cbse_student_subject_marks m')
                ->join('cbse_exam_timetable tt', 'tt.id = m.cbse_exam_timetable_id')
                ->join('subjects sub', 'sub.id = tt.subject_id')
                ->join('cbse_exam_assessment_types t', 't.id = m.cbse_exam_assessment_type_id')
                ->where('m.cbse_exam_student_id', (int) $x['es'])
                ->group_by('tt.id')->order_by('sub.name')->get()->result_array() as $r) {
                $pct = (float) $r['max'] > 0 ? round((float) $r['got'] * 100 / (float) $r['max'], 1) : null;
                $x['subjects'][] = array('name' => $r['name'], 'got' => round((float) $r['got'], 2), 'max' => (float) $r['max'], 'pct' => $pct, 'grade' => self::grade_for($grades, $pct));
                $got += (float) $r['got'];
                $max += (float) $r['max'];
            }
            $x['percentage'] = $max > 0 ? round($got * 100 / $max, 2) : null;
            $x['grade'] = self::grade_for($grades, $x['percentage']);
            $x['rank'] = $db->select('rank')->where(array('cbse_exam_id' => (int) $x['exam_id'], 'student_session_id' => (int) $x['student_session_id']))->get('cbse_student_exam_ranks')->row('rank');
        }
        unset($x);
        // sessions: average of that session's exams
        $sessions = array();
        foreach ($exams as $x) {
            if ($x['percentage'] !== null) {
                $sessions[$x['session']][] = $x['percentage'];
            }
        }
        foreach ($sessions as $k => $v) {
            $sessions[$k] = round(array_sum($v) / count($v), 2);
        }
        return array($exams, $sessions);
    }

    /** Every score of every exam: header + rows, for a CSV export. $session_id 0 = all sessions. */
    public function export_rows($session_id = 0)
    {
        $db = $this->CI->db;
        $db->select('se.session, t.name AS term, e.name AS exam, c.class, s.section, st.admission_no, st.firstname, st.lastname, sub.name AS subject, at.name AS assessment, at.maximum_marks, m.marks, m.is_absent, m.note')
            ->from('cbse_student_subject_marks m')
            ->join('cbse_exam_timetable tt', 'tt.id = m.cbse_exam_timetable_id')
            ->join('cbse_exams e', 'e.id = tt.cbse_exam_id')
            ->join('cbse_terms t', 't.id = e.cbse_term_id', 'left')
            ->join('sessions se', 'se.id = e.session_id', 'left')
            ->join('subjects sub', 'sub.id = tt.subject_id')
            ->join('cbse_exam_assessment_types at', 'at.id = m.cbse_exam_assessment_type_id')
            ->join('cbse_exam_students es', 'es.id = m.cbse_exam_student_id')
            ->join('student_session ss', 'ss.id = es.student_session_id')
            ->join('students st', 'st.id = ss.student_id')
            ->join('classes c', 'c.id = ss.class_id')->join('sections s', 's.id = ss.section_id');
        if ($session_id) {
            $db->where('e.session_id', (int) $session_id);
        }
        $rows = $db->order_by('se.session')->order_by('e.id')->order_by('c.class')->order_by('s.section')->order_by('st.admission_no')->order_by('sub.name')->order_by('at.id')->get()->result_array();
        $out = array(array('Session', 'Term', 'Exam', 'Class', 'Section', 'Admission no', 'First name', 'Last name', 'Subject', 'Assessment', 'Maximum', 'Score', 'Absent', 'Note'));
        foreach ($rows as $r) {
            $out[] = array($r['session'], $r['term'], $r['exam'], $r['class'], $r['section'], $r['admission_no'], $r['firstname'], $r['lastname'], $r['subject'], $r['assessment'],
                (float) $r['maximum_marks'], (int) $r['is_absent'] ? '' : (float) $r['marks'], (int) $r['is_absent'] ? 'ABS' : '', $r['note']);
        }
        return $out;
    }
}
