<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Promotion from Report Cards results (migration 306: cbse_promotion_rules, cbse_promotion_decisions).
 *
 * For a class / section in a session, each student's annual figures over the session's Report Cards
 * exams: average (mean of the exams' percentages), each core subject's average, attendance (days
 * present / school days of those exams). Against the rules (per class, else the school default):
 *   promote    every rule met;
 *   borderline a rule missed by no more than the margin (for the principal and class teacher to decide);
 *   repeat     otherwise.
 * Decisions (promote / repeat / probation) are recorded with a reason. Moving students to the new
 * class is still done in Student Information > Promote Students.
 */
class Cbse_promotion
{
    const DEFAULT_RULE = array('min_average' => 40, 'core_subject_ids' => '', 'core_pass_mark' => 40, 'min_attendance' => 0, 'borderline_margin' => 5);

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** The rule for a class (its own, else the school default, else the built-in one). */
    public function rule($class_id)
    {
        $db = $this->CI->db;
        $row = $db->where('class_id', (int) $class_id)->get('cbse_promotion_rules')->row_array();
        if (!$row) {
            $row = $db->where('class_id IS NULL', null, false)->get('cbse_promotion_rules')->row_array();
        }
        $rule = $row ? array_intersect_key($row, self::DEFAULT_RULE) + self::DEFAULT_RULE : self::DEFAULT_RULE;
        $rule['own'] = $row && (int) $row['class_id'] === (int) $class_id;
        $rule['core'] = array_filter(array_map('intval', explode(',', (string) $rule['core_subject_ids'])));
        return $rule;
    }

    public function save_rule($class_id, array $in)
    {
        $db = $this->CI->db;
        $row = array(
            'min_average' => max(0, min(100, (float) $in['min_average'])),
            'core_subject_ids' => implode(',', array_filter(array_map('intval', (array) $in['core_subject_ids']))),
            'core_pass_mark' => max(0, min(100, (float) $in['core_pass_mark'])),
            'min_attendance' => max(0, min(100, (float) $in['min_attendance'])),
            'borderline_margin' => max(0, min(50, (float) $in['borderline_margin'])),
        );
        $class_id = $class_id ? (int) $class_id : null;
        $db->where($class_id === null ? 'class_id IS NULL' : 'class_id = ' . (int) $class_id, null, false); // tenancy-lint: ok int
        $existing = $db->get('cbse_promotion_rules')->row_array();
        if ($existing) {
            $db->where('id', (int) $existing['id'])->update('cbse_promotion_rules', $row);
        } else {
            $db->insert('cbse_promotion_rules', $row + array('class_id' => $class_id));
        }
    }

    /**
     * Annual figures and suggestion for each student of a class section in a session.
     * Returns array(students, exams, rule).
     */
    public function evaluate($class_section_id, $session_id)
    {
        $db = $this->CI->db;
        $cs = $db->select('class_id, section_id')->where('id', (int) $class_section_id)->get('class_sections')->row_array();
        if (!$cs) {
            return array(array(), array(), self::DEFAULT_RULE);
        }
        $rule = $this->rule($cs['class_id']);
        $exams = $db->select('e.id, e.name, e.total_working_days')->from('cbse_exams e')
            ->join('cbse_exam_class_sections ecs', 'ecs.cbse_exam_id = e.id')
            ->where(array('ecs.class_section_id' => (int) $class_section_id, 'e.session_id' => (int) $session_id))
            ->group_by('e.id')->order_by('e.id')->get()->result_array();
        $students = array();
        foreach ($db->select('ss.id AS student_session_id, st.id AS student_id, st.admission_no, st.firstname, st.middlename, st.lastname')
            ->from('student_session ss')->join('students st', 'st.id = ss.student_id')
            ->where(array('ss.class_id' => (int) $cs['class_id'], 'ss.section_id' => (int) $cs['section_id'], 'ss.session_id' => (int) $session_id, 'st.is_active' => 'yes'))
            ->order_by('st.firstname')->get()->result_array() as $r) {
            $r['name'] = trim(preg_replace('/\s+/', ' ', $r['firstname'] . ' ' . $r['middlename'] . ' ' . $r['lastname']));
            $r['exam_pct'] = array();
            $r['subjects'] = array();
            $r['present'] = 0;
            $r['school_days'] = 0;
            $students[(int) $r['student_session_id']] = $r;
        }
        $exam_ids = array_map('intval', array_column($exams, 'id'));
        if ($students && $exam_ids) {
            $rows = $db->select('es.student_session_id, es.cbse_exam_id, es.total_present_days, tt.subject_id, SUM(m.marks) AS got, SUM(t.maximum_marks) AS max', false)
                ->from('cbse_exam_students es')
                ->join('cbse_student_subject_marks m', 'm.cbse_exam_student_id = es.id')
                ->join('cbse_exam_timetable tt', 'tt.id = m.cbse_exam_timetable_id')
                ->join('cbse_exam_assessment_types t', 't.id = m.cbse_exam_assessment_type_id')
                ->where_in('es.cbse_exam_id', $exam_ids)->where_in('es.student_session_id', array_keys($students))
                ->group_by(array('es.id', 'tt.subject_id'))->get()->result_array();
            $per_exam = array();
            foreach ($rows as $r) {
                $ss = (int) $r['student_session_id'];
                $per_exam[$ss][(int) $r['cbse_exam_id']][0] = (isset($per_exam[$ss][(int) $r['cbse_exam_id']][0]) ? $per_exam[$ss][(int) $r['cbse_exam_id']][0] : 0) + (float) $r['got'];
                $per_exam[$ss][(int) $r['cbse_exam_id']][1] = (isset($per_exam[$ss][(int) $r['cbse_exam_id']][1]) ? $per_exam[$ss][(int) $r['cbse_exam_id']][1] : 0) + (float) $r['max'];
                if ((float) $r['max'] > 0) {
                    $students[$ss]['subjects'][(int) $r['subject_id']][] = (float) $r['got'] * 100 / (float) $r['max'];
                }
            }
            foreach ($per_exam as $ss => $list) {
                foreach ($list as $eid => $v) {
                    if ($v[1] > 0) {
                        $students[$ss]['exam_pct'][$eid] = round($v[0] * 100 / $v[1], 2);
                    }
                }
            }
            $working = array();
            foreach ($exams as $e) {
                $working[(int) $e['id']] = (int) $e['total_working_days'];
            }
            foreach ($db->select('student_session_id, cbse_exam_id, total_present_days')->where_in('cbse_exam_id', $exam_ids)->where_in('student_session_id', array_keys($students))->get('cbse_exam_students')->result_array() as $r) {
                if (!empty($working[(int) $r['cbse_exam_id']])) {
                    $students[(int) $r['student_session_id']]['present'] += (int) $r['total_present_days'];
                    $students[(int) $r['student_session_id']]['school_days'] += $working[(int) $r['cbse_exam_id']];
                }
            }
        }
        $names = array();
        foreach ($rule['core'] ? $db->select('id, name')->where_in('id', $rule['core'])->get('subjects')->result_array() : array() as $s) {
            $names[(int) $s['id']] = $s['name'];
        }
        $decisions = array();
        foreach ($students ? $db->where_in('student_session_id', array_keys($students))->where('cbse_template_id', 0)->get('cbse_promotion_decisions')->result_array() : array() as $d) {
            $decisions[(int) $d['student_session_id']] = $d;
        }
        $m = (float) $rule['borderline_margin'];
        foreach ($students as $ss => &$st) {
            $st['average'] = $st['exam_pct'] ? round(array_sum($st['exam_pct']) / count($st['exam_pct']), 2) : null;
            $st['attendance'] = $st['school_days'] ? round($st['present'] * 100 / $st['school_days'], 1) : null;
            $st['core'] = array();
            $miss = array();
            $near = true;
            if ($st['average'] === null) {
                $st['suggestion'] = 'none';
                $st['why'] = 'No scores yet';
            } else {
                if ($st['average'] < $rule['min_average']) {
                    $miss[] = 'average ' . $st['average'] . '% (needs ' . (float) $rule['min_average'] . '%)';
                    $near = $near && $st['average'] >= $rule['min_average'] - $m;
                }
                foreach ($rule['core'] as $sid) {
                    $avg = isset($st['subjects'][$sid]) ? round(array_sum($st['subjects'][$sid]) / count($st['subjects'][$sid]), 1) : null;
                    $st['core'][$sid] = $avg;
                    $label = isset($names[$sid]) ? $names[$sid] : 'Subject';
                    if ($avg === null) {
                        $miss[] = $label . ' not taken';
                        $near = false;
                    } elseif ($avg < $rule['core_pass_mark']) {
                        $miss[] = $label . ' ' . $avg . '%';
                        $near = $near && $avg >= $rule['core_pass_mark'] - $m;
                    }
                }
                if ((float) $rule['min_attendance'] > 0 && $st['attendance'] !== null && $st['attendance'] < $rule['min_attendance']) {
                    $miss[] = 'attendance ' . $st['attendance'] . '%';
                    $near = $near && $st['attendance'] >= $rule['min_attendance'] - $m;
                }
                $st['suggestion'] = !$miss ? 'promote' : ($near ? 'borderline' : 'repeat');
                $st['why'] = $miss ? 'Below: ' . implode(', ', $miss) : 'Meets every rule';
            }
            $st['decision'] = isset($decisions[$ss]) ? $decisions[$ss] : null;
        }
        unset($st);
        $rule['core_names'] = $names;
        return array($students, $exams, $rule);
    }

    /** Records decisions: [student_session_id => [decision, reason]]. */
    public function decide(array $list, $staff_id, array $averages)
    {
        $db = $this->CI->db;
        $now = date('Y-m-d H:i:s');
        $n = 0;
        foreach ($list as $ss => $d) {
            $ss = (int) $ss;
            $decision = isset($d['decision']) ? (string) $d['decision'] : '';
            if (!in_array($decision, array('promote', 'repeat', 'probation'), true)) {
                continue;
            }
            $row = array('decision' => $decision, 'reason' => mb_substr(trim((string) (isset($d['reason']) ? $d['reason'] : '')), 0, 500),
                'average' => isset($averages[$ss]) ? $averages[$ss] : null, 'decided_by' => $staff_id, 'decided_at' => $now);
            $existing = $db->where(array('cbse_template_id' => 0, 'student_session_id' => $ss))->get('cbse_promotion_decisions')->row_array();
            if ($existing) {
                if ($existing['decision'] === $row['decision'] && (string) $existing['reason'] === $row['reason']) {
                    continue;
                }
                $db->where('id', (int) $existing['id'])->update('cbse_promotion_decisions', $row);
            } else {
                $db->insert('cbse_promotion_decisions', $row + array('cbse_template_id' => 0, 'student_session_id' => $ss));
            }
            $n++;
        }
        return $n;
    }
}
