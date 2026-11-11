<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class statistics for the CBSE Examination report cards (Nigerian reports show them): per class
 * and arm, per subject, the class average, the highest and the lowest score, and how many
 * students are in the class.
 *
 * A student's subject score is worked out as the report does: for each exam, marks over the
 * maximum of the assessments entered; in percent mode those percentages are weighted by each
 * exam's share of the template, in marks mode (one exam) the marks are added up.
 * Students with no marks in a subject are left out of that subject's figures.
 */
class Cbse_class_stats
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** exam id => weightage (percent) for a template's exams; one exam without weightage counts 100. */
    public function template_weights($cbse_template_id)
    {
        $rows = $this->CI->db->select('cbse_exam_id, weightage')->where('cbse_template_id', (int) $cbse_template_id)
            ->get('cbse_template_term_exams')->result_array();
        $weights = array();
        foreach ($rows as $r) {
            $weights[(int) $r['cbse_exam_id']] = (float) $r['weightage'];
        }
        if (count($weights) === 1 || array_sum($weights) <= 0) {
            $weights = array_fill_keys(array_keys($weights), count($weights) ? 100 / count($weights) : 0);
        }
        return $weights;
    }

    /**
     * "<class_id>-<section_id>" => array('count' => students, 'subjects' => subject id => array(avg, high, low)).
     * $weights: exam id => weightage; $mode: 'percent' (weighted percent out of 100) or 'marks' (sum of marks).
     */
    public function compute(array $weights, $mode = 'percent')
    {
        if (!$weights) {
            return array();
        }
        $rows = $this->CI->db->select('ss.class_id, ss.section_id, es.student_session_id, tt.subject_id, tt.cbse_exam_id, SUM(m.marks) AS got, SUM(t.maximum_marks) AS mx', false)
            ->from('cbse_student_subject_marks m')
            ->join('cbse_exam_students es', 'es.id = m.cbse_exam_student_id')
            ->join('cbse_exam_timetable tt', 'tt.id = m.cbse_exam_timetable_id')
            ->join('cbse_exam_assessment_types t', 't.id = m.cbse_exam_assessment_type_id')
            ->join('student_session ss', 'ss.id = es.student_session_id')
            ->where_in('tt.cbse_exam_id', array_map('intval', array_keys($weights)))
            ->group_by(array('ss.class_id', 'ss.section_id', 'es.student_session_id', 'tt.subject_id', 'tt.cbse_exam_id'))
            ->get()->result_array();
        $score = array();    // class => student => subject => score
        $students = array(); // class => student => true
        foreach ($rows as $r) {
            $class = $r['class_id'] . '-' . $r['section_id'];
            $sid = (int) $r['student_session_id'];
            $subject = (int) $r['subject_id'];
            $students[$class][$sid] = true;
            if ($mode === 'marks') {
                $value = (float) $r['got'];
            } else {
                $pct = (float) $r['mx'] > 0 ? (float) $r['got'] / (float) $r['mx'] * 100 : 0;
                $value = $pct * ($weights[(int) $r['cbse_exam_id']] / 100);
            }
            $score[$class][$sid][$subject] = (isset($score[$class][$sid][$subject]) ? $score[$class][$sid][$subject] : 0) + $value;
        }
        $out = array();
        foreach ($score as $class => $by_student) {
            $per_subject = array();
            foreach ($by_student as $subjects) {
                foreach ($subjects as $subject => $value) {
                    $per_subject[$subject][] = $value;
                }
            }
            $out[$class] = array('count' => count($students[$class]), 'subjects' => array());
            foreach ($per_subject as $subject => $values) {
                $out[$class]['subjects'][$subject] = array(
                    'avg' => round(array_sum($values) / count($values), 2),
                    'high' => round(max($values), 2),
                    'low' => round(min($values), 2),
                );
            }
        }
        return $out;
    }

    /** One subject's figures for a student's class, or null. */
    public static function pick(array $stats, $class_id, $section_id, $subject_id)
    {
        $class = $class_id . '-' . $section_id;
        return isset($stats[$class]['subjects'][(int) $subject_id]) ? $stats[$class]['subjects'][(int) $subject_id] : null;
    }

    public static function class_size(array $stats, $class_id, $section_id)
    {
        $class = $class_id . '-' . $section_id;
        return isset($stats[$class]) ? (int) $stats[$class]['count'] : 0;
    }
}
