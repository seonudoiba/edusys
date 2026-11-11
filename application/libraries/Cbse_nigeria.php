<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Nigerian report-card defaults for the CBSE Examination module:
 *
 *   terms               First Term, Second Term, Third Term
 *   assessment schemes  CA1 + CA2 + Exam (20/20/60), CA + Exam (40/60), CA + Exam (30/70)
 *   grade scales        WAEC (A1-F9) and Primary (A-F), saved highest first: the module takes the
 *                       first grade whose minimum the percentage reaches, and also looks grades up
 *                       by whole number between minimum and maximum, so the ranges have no gaps
 *   ratings             Affective domain and Psychomotor domain (observations), each item rated 1-5
 *
 * install() adds whatever is missing (matched by name), so running it again changes nothing.
 * With $replace_samples, the sample rows from the default data are removed first, but only those
 * no exam uses (provisioning a new school).
 */
class Cbse_nigeria
{
    const TERMS = array(array('First Term', 'T1'), array('Second Term', 'T2'), array('Third Term', 'T3'));

    const SCHEMES = array(
        'CA1 + CA2 + Exam (20/20/60)' => array(array('First CA', 'CA1', 20), array('Second CA', 'CA2', 20), array('Examination', 'EXAM', 60)),
        'CA + Exam (40/60)' => array(array('Continuous Assessment', 'CA', 40), array('Examination', 'EXAM', 60)),
        'CA + Exam (30/70)' => array(array('Continuous Assessment', 'CA', 30), array('Examination', 'EXAM', 70)),
    );

    const GRADES = array(
        'WAEC (A1-F9)' => array(
            array('A1', 75, 100, 'Excellent'), array('B2', 70, 74, 'Very good'), array('B3', 65, 69, 'Good'),
            array('C4', 60, 64, 'Credit'), array('C5', 55, 59, 'Credit'), array('C6', 50, 54, 'Credit'),
            array('D7', 45, 49, 'Pass'), array('E8', 40, 44, 'Pass'), array('F9', 0, 39, 'Fail'),
        ),
        'Primary (A-F)' => array(
            array('A', 70, 100, 'Excellent'), array('B', 60, 69, 'Very good'), array('C', 50, 59, 'Good'),
            array('D', 45, 49, 'Fair'), array('E', 40, 44, 'Pass'), array('F', 0, 39, 'Fail'),
        ),
    );

    const RATINGS = array(
        'Affective domain' => array('Punctuality', 'Attendance in class', 'Neatness', 'Politeness', 'Honesty', 'Self-control',
            'Relationship with others', 'Attentiveness', 'Perseverance'),
        'Psychomotor domain' => array('Handwriting', 'Verbal fluency', 'Sports and games', 'Drawing and painting', 'Musical skills',
            'Handling of tools', 'Crafts'),
    );
    const RATING_MAX = 5;
    const RATING_KEY = '5 Excellent, 4 Very good, 3 Good, 2 Fair, 1 Poor';

    const PASS_PERCENTAGE = 40;

    /** Sample rows of the default data (removed for new schools when nothing uses them). */
    const SAMPLE_ASSESSMENTS = array('Periodic Assessment', 'Summative Assessment 1', 'CBET EXAM');
    const SAMPLE_GRADES = array('EXAM Grade 1', 'Exam grade 1', 'CBET GRADING');
    const SAMPLE_TERMS = array('Term 1', 'Term 2', 'Term 3');
    const SAMPLE_PARAMETERS = array('Behaviour', 'Game', 'Painting', 'Art & Culture');

    private $CI;
    private $school_id;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    private function sid()
    {
        return $this->school_id ?: (int) Tenant::id();
    }

    /** Works on this school (School_provisioner runs without a tenant). */
    public function for_school($school_id)
    {
        $this->school_id = (int) $school_id;
        return $this;
    }

    /** What is already set up: counts of the Nigerian defaults found (for the settings page). */
    public function status()
    {
        $db = $this->CI->db;
        $sid = $this->sid();
        $count = function ($table, $names) use ($db, $sid) {
            return $names ? $db->where('school_id', $sid)->where_in('name', $names)->count_all_results($table) : 0;
        };
        $items = array();
        foreach (self::RATINGS as $list) {
            $items = array_merge($items, $list);
        }
        return array(
            'terms' => array($count('cbse_terms', array_column(self::TERMS, 0)), count(self::TERMS)),
            'schemes' => array($count('cbse_exam_assessments', array_keys(self::SCHEMES)), count(self::SCHEMES)),
            'grades' => array($count('cbse_exam_grades', array_keys(self::GRADES)), count(self::GRADES)),
            'ratings' => array($count('cbse_exam_observations', array_keys(self::RATINGS)), count(self::RATINGS)),
            'items' => array($count('cbse_observation_parameters', $items), count($items)),
        );
    }

    /** Adds what is missing; returns counts of what was added and removed. */
    public function install($replace_samples = false)
    {
        $db = $this->CI->db;
        $sid = $this->sid();
        $done = array('terms' => 0, 'schemes' => 0, 'grades' => 0, 'ratings' => 0, 'removed' => 0);
        $db->trans_start();
        if ($replace_samples) {
            $done['removed'] = $this->remove_samples();
        }
        $find = function ($table, $name) use ($db, $sid) {
            $row = $db->select('id')->where(array('school_id' => $sid, 'name' => $name))->get($table)->row();
            return $row ? (int) $row->id : 0;
        };
        foreach (self::TERMS as $t) {
            if (!$find('cbse_terms', $t[0])) {
                $db->insert('cbse_terms', array('school_id' => $sid, 'name' => $t[0], 'term_code' => $t[1], 'description' => ''));
                $done['terms']++;
            }
        }
        foreach (self::SCHEMES as $name => $types) {
            if ($find('cbse_exam_assessments', $name)) {
                continue;
            }
            $db->insert('cbse_exam_assessments', array('school_id' => $sid, 'name' => $name, 'description' => ''));
            $aid = (int) $db->insert_id();
            foreach ($types as $t) {
                $db->insert('cbse_exam_assessment_types', array('school_id' => $sid, 'cbse_exam_assessment_id' => $aid, 'name' => $t[0], 'code' => $t[1],
                    'maximum_marks' => $t[2], 'pass_percentage' => self::PASS_PERCENTAGE, 'description' => ''));
            }
            $done['schemes']++;
        }
        foreach (self::GRADES as $name => $ranges) {
            if ($find('cbse_exam_grades', $name)) {
                continue;
            }
            $db->insert('cbse_exam_grades', array('school_id' => $sid, 'name' => $name, 'description' => ''));
            $gid = (int) $db->insert_id();
            foreach ($ranges as $r) { // highest first
                $db->insert('cbse_exam_grades_range', array('school_id' => $sid, 'cbse_exam_grade_id' => $gid, 'name' => $r[0],
                    'minimum_percentage' => $r[1], 'maximum_percentage' => $r[2], 'description' => $r[3]));
            }
            $done['grades']++;
        }
        foreach (self::RATINGS as $group => $items) {
            if ($find('cbse_exam_observations', $group)) {
                continue;
            }
            $db->insert('cbse_exam_observations', array('school_id' => $sid, 'name' => $group, 'description' => 'Rated 1 to ' . self::RATING_MAX . ': ' . self::RATING_KEY . '.'));
            $oid = (int) $db->insert_id();
            foreach ($items as $item) {
                $pid = $find('cbse_observation_parameters', $item);
                if (!$pid) {
                    $db->insert('cbse_observation_parameters', array('school_id' => $sid, 'name' => $item, 'description' => ''));
                    $pid = (int) $db->insert_id();
                }
                $db->insert('cbse_observation_subparameter', array('school_id' => $sid, 'cbse_exam_observation_id' => $oid,
                    'cbse_observation_parameter_id' => $pid, 'maximum_marks' => self::RATING_MAX, 'description' => ''));
            }
            $done['ratings']++;
        }
        $db->trans_complete();
        if (!$db->trans_status()) {
            throw new RuntimeException('The report-card defaults could not be saved. Nothing was changed.');
        }
        return $done;
    }

    /** The sample terms, assessments, grades and rating items of the default data, when no exam or rating uses them. */
    private function remove_samples()
    {
        $db = $this->CI->db;
        $sid = $this->sid();
        $removed = 0;
        $unused = function ($table, $names, $column) use ($db, $sid) {
            $ids = array_map('intval', array_column($db->select('id')->where('school_id', $sid)->where_in('name', $names)->get($table)->result_array(), 'id'));
            if (!$ids) {
                return array();
            }
            $used = array_map('intval', array_column($db->select($column)->where('school_id', $sid)->where_in($column, $ids)->get('cbse_exams')->result_array(), $column));
            return array_values(array_diff($ids, $used));
        };
        foreach ($unused('cbse_exam_assessments', self::SAMPLE_ASSESSMENTS, 'cbse_exam_assessment_id') as $id) {
            $db->where(array('school_id' => $sid, 'cbse_exam_assessment_id' => $id))->delete('cbse_exam_assessment_types');
            $db->where(array('school_id' => $sid, 'id' => $id))->delete('cbse_exam_assessments');
            $removed++;
        }
        foreach ($unused('cbse_exam_grades', self::SAMPLE_GRADES, 'cbse_exam_grade_id') as $id) {
            $db->where(array('school_id' => $sid, 'cbse_exam_grade_id' => $id))->delete('cbse_exam_grades_range');
            $db->where(array('school_id' => $sid, 'id' => $id))->delete('cbse_exam_grades');
            $removed++;
        }
        foreach ($unused('cbse_terms', self::SAMPLE_TERMS, 'cbse_term_id') as $id) {
            $db->where(array('school_id' => $sid, 'id' => $id))->delete('cbse_terms');
            $removed++;
        }
        $params = array();
        foreach ($db->select('id, name')->where('school_id', $sid)->get('cbse_observation_parameters')->result_array() as $p) {
            if (in_array(trim($p['name']), self::SAMPLE_PARAMETERS, true)) { // the sample names end in tabs
                $params[] = (int) $p['id'];
            }
        }
        foreach ($params as $id) {
            if (!$db->where(array('school_id' => $sid, 'cbse_observation_parameter_id' => $id))->count_all_results('cbse_observation_subparameter')) {
                $db->where(array('school_id' => $sid, 'id' => $id))->delete('cbse_observation_parameters');
                $removed++;
            }
        }
        return $removed;
    }
}
