<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Report card comments by score (migration 306, cbse_comment_bank): the class teacher's and the
 * principal's comment for an average, from the school's own comment bank, or these defaults while
 * the bank is empty. Also each student's weakest subject in an exam ("Needs to improve in ...").
 */
class Cbse_comments
{
    const TYPES = array('teacher' => "Class teacher's comment", 'principal' => "Principal's comment");

    /** type => [[min %, max %, comment]] */
    const DEFAULTS = array(
        'teacher' => array(
            array(75, 100, 'An excellent result. Keep up the hard work.'),
            array(65, 74.99, 'A very good performance. Keep it up.'),
            array(55, 64.99, 'A good result. With more effort you can do even better.'),
            array(45, 54.99, 'A fair performance. More reading and practice are needed.'),
            array(40, 44.99, 'A weak pass. Needs to work much harder next term.'),
            array(0, 39.99, 'A poor result. Needs serious improvement and close support.'),
        ),
        'principal' => array(
            array(75, 100, 'Outstanding. Well done.'),
            array(60, 74.99, 'Very good. Keep it up.'),
            array(50, 59.99, 'Good. Aim higher next term.'),
            array(40, 49.99, 'Fair. Can do better with more effort.'),
            array(0, 39.99, 'Below expectation. Parents should please see the class teacher.'),
        ),
    );

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** The school's comments: type => [[min, max, comment, id]]; the defaults where it has none. */
    public function bank()
    {
        $out = array('teacher' => array(), 'principal' => array());
        foreach ($this->rows() as $r) {
            if (isset($out[$r['type']])) {
                $out[$r['type']][] = array((float) $r['min_percentage'], (float) $r['max_percentage'], $r['comment'], (int) $r['id']);
            }
        }
        foreach ($out as $type => $list) {
            if (!$list) {
                foreach (self::DEFAULTS[$type] as $d) {
                    $out[$type][] = array($d[0], $d[1], $d[2], 0);
                }
            }
        }
        return $out;
    }

    public function rows()
    {
        return $this->CI->db->order_by('type')->order_by('min_percentage', 'desc')->get('cbse_comment_bank')->result_array();
    }

    public function add($type, $min, $max, $comment)
    {
        if (!isset(self::TYPES[$type]) || trim($comment) === '' || !is_numeric($min) || !is_numeric($max) || $min < 0 || $max > 100 || $min > $max) {
            return false;
        }
        $this->CI->db->insert('cbse_comment_bank', array('type' => $type, 'min_percentage' => $min, 'max_percentage' => $max, 'comment' => mb_substr(trim($comment), 0, 500)));
        return true;
    }

    public function delete($id)
    {
        $this->CI->db->where('id', (int) $id)->delete('cbse_comment_bank');
    }

    /** Starts the bank from the defaults, so the school can edit them. */
    public function seed_defaults()
    {
        if ($this->CI->db->count_all_results('cbse_comment_bank') > 0) {
            return;
        }
        foreach (self::DEFAULTS as $type => $list) {
            foreach ($list as $d) {
                $this->add($type, $d[0], $d[1], $d[2]);
            }
        }
    }

    /**
     * Each student's subjects in an exam: [exam_student_id] => [[subject, percentage]], weakest first.
     * Absent counts as 0; a subject with no score at all is left out.
     */
    public function subject_scores($exam_id)
    {
        $rows = $this->CI->db->select('m.cbse_exam_student_id AS es, sub.name AS subject, SUM(m.marks) AS got, SUM(t.maximum_marks) AS max', false)
            ->from('cbse_student_subject_marks m')
            ->join('cbse_exam_timetable tt', 'tt.id = m.cbse_exam_timetable_id')
            ->join('subjects sub', 'sub.id = tt.subject_id')
            ->join('cbse_exam_assessment_types t', 't.id = m.cbse_exam_assessment_type_id')
            ->where('tt.cbse_exam_id', (int) $exam_id)
            ->group_by(array('m.cbse_exam_student_id', 'tt.subject_id'))
            ->get()->result_array();
        $out = array();
        foreach ($rows as $r) {
            if ((float) $r['max'] > 0) {
                $out[(int) $r['es']][] = array($r['subject'], round((float) $r['got'] * 100 / (float) $r['max'], 1));
            }
        }
        foreach ($out as &$list) {
            usort($list, function ($a, $b) { return $a[1] <=> $b[1]; });
        }
        return $out;
    }
}
