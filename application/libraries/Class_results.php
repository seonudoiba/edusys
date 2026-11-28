<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Report cards by class (Report Cards > Classes & Results), on the Report Cards tables:
 *
 *   class + term  →  one cbse_exams row ("JSS 1 — First Term", cbse_exams.class_id), its sections
 *                    (cbse_exam_class_sections) and students (cbse_exam_students)
 *   subject       →  cbse_exam_timetable row (the subjects the class offers: its subject groups, plus any added)
 *   assessments   →  cbse_exam_assessment_types of the exam's structure, linked to each subject
 *                    (cbse_exam_timetable_assessment_types): the class default (cbse_exams.default_type_ids,
 *                    e.g. First CA 10 + Mid-term Test 20 + Exam 70) unless the subject has its own
 *                    (cbse_exam_timetable.custom_breakdown)
 *   scores        →  cbse_student_subject_marks (typed in, imported, or sent by a CBT: onlineexam.marks_target)
 *
 * results() computes each student's subject totals, grades, positions (within the class arm, equal
 * scores share a position), class average / highest / lowest, the overall average and position, and
 * for the last term of the session the cumulative of every term. An exam made by the older Exams
 * screen for several classes is used as it is (shared).
 */
class Class_results
{
    const DEFAULT_BREAKDOWN = array(array('First CA', 'CA1', 10), array('Mid-term Test', 'MT', 20), array('Exam', 'EXAM', 70));

    private $CI;
    private $db;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->db = $this->CI->db;
    }

    // ------------------------------------------------------------------ classes and terms

    public function terms()
    {
        return $this->db->order_by('id')->get('cbse_terms')->result_array();
    }

    /** The last term of the session (its report card carries the cumulative). */
    public function is_last_term($term_id)
    {
        $terms = $this->terms();
        return $terms && (int) end($terms)['id'] === (int) $term_id;
    }

    /** Classes with their sections and number of students this session. */
    public function classes($session_id)
    {
        $out = array();
        foreach ($this->db->select('c.id AS class_id, c.class, s.id AS section_id, s.section, cs.id AS class_section_id')->from('class_sections cs')
            ->join('classes c', 'c.id = cs.class_id')->join('sections s', 's.id = cs.section_id')
            ->order_by('c.id')->order_by('s.section')->get()->result_array() as $r) {
            $cid = (int) $r['class_id'];
            if (!isset($out[$cid])) {
                $out[$cid] = array('id' => $cid, 'name' => $r['class'], 'sections' => array(), 'students' => 0);
            }
            $out[$cid]['sections'][(int) $r['section_id']] = $r['section'];
        }
        foreach ($this->db->select('ss.class_id, COUNT(*) AS n', false)->from('student_session ss')->join('students st', 'st.id = ss.student_id')
            ->where(array('ss.session_id' => (int) $session_id, 'st.is_active' => 'yes'))->group_by('ss.class_id')->get()->result_array() as $r) {
            if (isset($out[(int) $r['class_id']])) {
                $out[(int) $r['class_id']]['students'] = (int) $r['n'];
            }
        }
        return $out;
    }

    /**
     * Subjects a class offers this session: its subject groups (of one section, or every section) and
     * any subject already in one of its terms. [subject_id => [id, name, code]], by name.
     */
    public function class_subjects($class_id, $session_id, $section_id = 0)
    {
        $cs = $this->class_section_ids($class_id);
        if ($section_id) {
            $cs = array_map('intval', array_column($this->db->select('id')->where(array('class_id' => (int) $class_id, 'section_id' => (int) $section_id))->get('class_sections')->result_array(), 'id'));
        }
        $ids = array();
        if ($cs) {
            $ids = array_map('intval', array_column($this->db->select('DISTINCT sgs.subject_id', false)->from('subject_group_class_sections sgc')
                ->join('subject_group_subjects sgs', 'sgs.subject_group_id = sgc.subject_group_id')
                ->where('sgc.session_id', (int) $session_id)->where_in('sgc.class_section_id', $cs)->get()->result_array(), 'subject_id'));
        }
        foreach ($this->terms() as $t) {
            $exam = $this->exam_for($class_id, (int) $t['id'], $session_id);
            if ($exam) {
                $ids = array_merge($ids, array_map('intval', array_column($this->db->select('subject_id')->where('cbse_exam_id', (int) $exam['id'])->get('cbse_exam_timetable')->result_array(), 'subject_id')));
            }
        }
        $ids = array_values(array_unique($ids));
        $out = array();
        foreach ($ids ? $this->db->select('id, name, code')->where_in('id', $ids)->order_by('name')->get('subjects')->result_array() : array() as $s) {
            $out[(int) $s['id']] = $s;
        }
        return $out;
    }

    public function class_section_ids($class_id)
    {
        return array_map('intval', array_column($this->db->select('id')->where('class_id', (int) $class_id)->get('class_sections')->result_array(), 'id'));
    }

    /** The session's subject groups: id => [name, subject ids]. */
    public function subject_groups($session_id)
    {
        $out = array();
        foreach ($this->db->select('id, name')->where('session_id', (int) $session_id)->order_by('name')->get('subject_groups')->result_array() as $g) {
            $out[(int) $g['id']] = array('name' => $g['name'], 'subjects' => array());
        }
        if ($out) {
            foreach ($this->db->select('subject_group_id, subject_id')->where_in('subject_group_id', array_keys($out))->get('subject_group_subjects')->result_array() as $s) {
                $out[(int) $s['subject_group_id']]['subjects'][] = (int) $s['subject_id'];
            }
        }
        return $out;
    }

    /**
     * Gives class sections a subject group for the session (Results shows the group's subjects).
     * Sections already in the group are left as they are. Returns how many were added.
     */
    public function give_subject_group($group_id, array $class_section_ids, $session_id)
    {
        $added = 0;
        foreach (array_unique(array_map('intval', $class_section_ids)) as $cs) {
            $has = $this->db->where(array('subject_group_id' => (int) $group_id, 'class_section_id' => $cs, 'session_id' => (int) $session_id))->count_all_results('subject_group_class_sections');
            if (!$has) {
                $this->db->insert('subject_group_class_sections', array('subject_group_id' => (int) $group_id, 'class_section_id' => $cs, 'session_id' => (int) $session_id, 'description' => '', 'is_active' => 1));
                $added++;
            }
        }
        return $added;
    }

    // ------------------------------------------------------------------ the class's exam for a term

    /** The exam of a class for a term (its own, else an older one shared with other classes); null if none. */
    public function exam_for($class_id, $term_id, $session_id)
    {
        $own = $this->db->where(array('class_id' => (int) $class_id, 'cbse_term_id' => (int) $term_id, 'session_id' => (int) $session_id))
            ->order_by('id', 'desc')->get('cbse_exams')->row_array();
        if ($own) {
            $own['shared'] = false;
            return $own;
        }
        $cs = $this->class_section_ids($class_id);
        if (!$cs) {
            return null;
        }
        $shared = $this->db->select('e.*')->from('cbse_exams e')->join('cbse_exam_class_sections x', 'x.cbse_exam_id = e.id')
            ->where(array('e.cbse_term_id' => (int) $term_id, 'e.session_id' => (int) $session_id))->where_in('x.class_section_id', $cs)
            ->group_by('e.id')->order_by('e.id', 'desc')->get()->row_array();
        if ($shared) {
            $shared['shared'] = (int) $this->db->where('cbse_exam_id', (int) $shared['id'])->where_not_in('class_section_id', $cs)->count_all_results('cbse_exam_class_sections') > 0;
        }
        return $shared ?: null;
    }

    /** Sets up a class's term: the exam, its breakdown (the class's last one, else the standard), subjects and students. */
    public function create($class_id, $term_id, $session_id)
    {
        $existing = $this->exam_for($class_id, $term_id, $session_id);
        if ($existing) {
            return $existing;
        }
        $class = $this->db->where('id', (int) $class_id)->get('classes')->row_array();
        $term = $this->db->where('id', (int) $term_id)->get('cbse_terms')->row_array();
        if (!$class || !$term) {
            throw new RuntimeException('Choose a class and a term.');
        }
        $name = $class['class'] . ' — ' . $term['name'];
        // the breakdown: what this class used last, else the standard one
        $rows = array();
        $prev = $this->db->where('class_id', (int) $class_id)->where('default_type_ids IS NOT NULL', null, false)->order_by('id', 'desc')->get('cbse_exams')->row_array();
        if ($prev) {
            foreach ($this->types_by_ids(explode(',', $prev['default_type_ids'])) as $t) {
                $rows[] = array($t['name'], $t['code'], (float) $t['maximum_marks']);
            }
        }
        if (!$rows) {
            $rows = self::DEFAULT_BREAKDOWN;
        }
        $grade = $this->db->select('cbse_exam_grade_id')->where('cbse_exam_grade_id IS NOT NULL', null, false)->order_by('id', 'desc')->get('cbse_exams')->row('cbse_exam_grade_id')
            ?: $this->db->select('id')->order_by('id')->get('cbse_exam_grades')->row('id');
        $staff = (int) $this->CI->customlib->getStaffID() ?: null;
        $this->db->trans_start();
        $this->db->insert('cbse_exam_assessments', array('name' => $name, 'description' => 'Score breakdown of ' . $name));
        $structure = (int) $this->db->insert_id();
        $type_ids = array();
        foreach ($rows as $r) {
            $this->db->insert('cbse_exam_assessment_types', array('cbse_exam_assessment_id' => $structure, 'name' => $r[0], 'code' => $r[1], 'maximum_marks' => $r[2],
                'pass_percentage' => 40, 'description' => '', 'created_by' => $staff));
            $type_ids[] = (int) $this->db->insert_id();
        }
        $this->db->insert('cbse_exams', array('name' => $name, 'exam_code' => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $class['class'])) . '-' . ($term['term_code'] ?: $term['id']),
            'session_id' => (int) $session_id, 'cbse_term_id' => (int) $term_id, 'cbse_exam_assessment_id' => $structure, 'cbse_exam_grade_id' => $grade ?: null,
            'description' => '', 'is_publish' => 0, 'is_active' => 1, 'use_exam_roll_no' => 0, 'total_working_days' => 0, 'created_by' => $staff,
            'class_id' => (int) $class_id, 'default_type_ids' => implode(',', $type_ids)));
        $exam_id = (int) $this->db->insert_id();
        foreach ($this->class_section_ids($class_id) as $cs) {
            $this->db->insert('cbse_exam_class_sections', array('cbse_exam_id' => $exam_id, 'class_section_id' => $cs));
        }
        $this->db->trans_complete();
        $exam = $this->db->where('id', $exam_id)->get('cbse_exams')->row_array();
        $exam['shared'] = false;
        $this->sync($exam, $class_id, $session_id);
        return $exam;
    }

    /** New students of the class join the exam; subjects of the class's subject groups are added. */
    public function sync(array $exam, $class_id, $session_id)
    {
        $eid = (int) $exam['id'];
        $cs = $this->class_section_ids($class_id);
        if (!$cs) {
            return;
        }
        // subjects the class offers (its subject groups this session)
        $have = array_map('intval', array_column($this->db->select('subject_id')->where('cbse_exam_id', $eid)->get('cbse_exam_timetable')->result_array(), 'subject_id'));
        $offered = array_map('intval', array_column($this->db->select('DISTINCT sgs.subject_id', false)->from('subject_group_class_sections sgc')
            ->join('subject_group_subjects sgs', 'sgs.subject_group_id = sgc.subject_group_id')
            ->where('sgc.session_id', (int) $session_id)->where_in('sgc.class_section_id', $cs)->get()->result_array(), 'subject_id'));
        foreach (array_diff($offered, $have) as $sid) {
            $this->add_subject($exam, $sid);
        }
        // students of the class
        $in = array_map('intval', array_column($this->db->select('student_session_id')->where('cbse_exam_id', $eid)->get('cbse_exam_students')->result_array(), 'student_session_id'));
        $sections = array_map('intval', array_column($this->db->select('section_id')->where_in('id', $cs)->get('class_sections')->result_array(), 'section_id'));
        $this->db->select('ss.id')->from('student_session ss')->join('students st', 'st.id = ss.student_id')
            ->where(array('ss.session_id' => (int) $session_id, 'ss.class_id' => (int) $class_id, 'st.is_active' => 'yes'))->where_in('ss.section_id', $sections ?: array(0));
        if ($in) {
            $this->db->where_not_in('ss.id', $in);
        }
        foreach ($this->db->get()->result_array() as $r) {
            $this->db->insert('cbse_exam_students', array('cbse_exam_id' => $eid, 'student_session_id' => (int) $r['id'], 'delete_student_id' => 0));
        }
    }

    public function add_subject(array $exam, $subject_id)
    {
        $eid = (int) $exam['id'];
        if ($this->db->where(array('cbse_exam_id' => $eid, 'subject_id' => (int) $subject_id))->count_all_results('cbse_exam_timetable')) {
            return;
        }
        $this->db->insert('cbse_exam_timetable', array('cbse_exam_id' => $eid, 'subject_id' => (int) $subject_id, 'date' => date('Y-m-d'), 'time_from' => '08:00:00',
            'time_to' => '09:00:00', 'duration' => 60, 'room_no' => '', 'created_by' => (int) $this->CI->customlib->getStaffID() ?: null));
        $tt = (int) $this->db->insert_id();
        foreach ($this->default_type_ids($exam) as $type) {
            $this->db->insert('cbse_exam_timetable_assessment_types', array('cbse_exam_timetable_id' => $tt, 'cbse_exam_assessment_type_id' => $type));
        }
    }

    // ------------------------------------------------------------------ breakdown

    private function types_by_ids(array $ids)
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (!$ids) {
            return array();
        }
        $rows = array();
        foreach ($this->db->where_in('id', $ids)->get('cbse_exam_assessment_types')->result_array() as $t) {
            $rows[(int) $t['id']] = $t;
        }
        $out = array();
        foreach ($ids as $id) {
            if (isset($rows[$id])) {
                $out[] = $rows[$id];
            }
        }
        return $out;
    }

    /** The class default: the stored one, else what most subjects use. */
    public function default_type_ids(array $exam)
    {
        if (!empty($exam['default_type_ids'])) {
            return array_map('intval', explode(',', $exam['default_type_ids']));
        }
        $count = array();
        foreach ($this->db->select('l.cbse_exam_assessment_type_id AS t')->from('cbse_exam_timetable_assessment_types l')->join('cbse_exam_timetable tt', 'tt.id = l.cbse_exam_timetable_id')
            ->where('tt.cbse_exam_id', (int) $exam['id'])->get()->result_array() as $r) {
            $count[(int) $r['t']] = (isset($count[(int) $r['t']]) ? $count[(int) $r['t']] : 0) + 1;
        }
        if (!$count) {
            return array_map('intval', array_column($this->db->select('id')->where('cbse_exam_assessment_id', (int) $exam['cbse_exam_assessment_id'])->order_by('id')->get('cbse_exam_assessment_types')->result_array(), 'id'));
        }
        $max = max($count);
        $ids = array_keys(array_filter($count, function ($n) use ($max) { return $n === $max; }));
        sort($ids);
        return $ids;
    }

    public function default_breakdown(array $exam)
    {
        return $this->types_by_ids($this->default_type_ids($exam));
    }

    /** rows: [[name, max], ...] → type ids of the exam's structure (made when new). */
    private function resolve_types(array $exam, array $rows)
    {
        $ids = array();
        $names = array();
        foreach ($rows as $r) {
            $name = trim((string) $r[0]);
            $max = round((float) $r[1], 2);
            if ($name === '' || $max <= 0) {
                continue;
            }
            if (isset($names[mb_strtolower($name)])) {
                throw new RuntimeException('"' . $name . '" is listed twice.');
            }
            $names[mb_strtolower($name)] = true;
            $found = $this->db->where(array('cbse_exam_assessment_id' => (int) $exam['cbse_exam_assessment_id'], 'name' => $name, 'maximum_marks' => $max))->get('cbse_exam_assessment_types')->row_array();
            if (!$found) {
                $this->db->insert('cbse_exam_assessment_types', array('cbse_exam_assessment_id' => (int) $exam['cbse_exam_assessment_id'], 'name' => $name,
                    'code' => strtoupper(mb_substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 6)), 'maximum_marks' => $max, 'pass_percentage' => 40, 'description' => '',
                    'created_by' => (int) $this->CI->customlib->getStaffID() ?: null));
                $ids[] = (int) $this->db->insert_id();
            } else {
                $ids[] = (int) $found['id'];
            }
        }
        if (!$ids) {
            throw new RuntimeException('Add at least one assessment with a name and its marks.');
        }
        return $ids;
    }

    /** Links a subject to exactly these types; a type with scores cannot be taken off. */
    private function link_types($tt_id, array $type_ids)
    {
        $now = array_map('intval', array_column($this->db->select('cbse_exam_assessment_type_id AS t')->where('cbse_exam_timetable_id', (int) $tt_id)->get('cbse_exam_timetable_assessment_types')->result_array(), 't'));
        $gone = array_diff($now, $type_ids);
        if ($gone) {
            $used = $this->db->select('t.name')->from('cbse_student_subject_marks m')->join('cbse_exam_assessment_types t', 't.id = m.cbse_exam_assessment_type_id')
                ->where('m.cbse_exam_timetable_id', (int) $tt_id)->where_in('m.cbse_exam_assessment_type_id', $gone)->group_by('t.id')->get()->result_array();
            if ($used) {
                $subject = $this->db->select('s.name')->from('cbse_exam_timetable tt')->join('subjects s', 's.id = tt.subject_id')->where('tt.id', (int) $tt_id)->get()->row('name');
                throw new RuntimeException($subject . ': scores were entered for ' . implode(', ', array_column($used, 'name')) . ', so it stays. Clear those scores first.');
            }
            $this->db->where('cbse_exam_timetable_id', (int) $tt_id)->where_in('cbse_exam_assessment_type_id', $gone)->delete('cbse_exam_timetable_assessment_types');
        }
        foreach (array_diff($type_ids, $now) as $t) {
            $this->db->insert('cbse_exam_timetable_assessment_types', array('cbse_exam_timetable_id' => (int) $tt_id, 'cbse_exam_assessment_type_id' => $t));
        }
    }

    /** The class default: every subject without its own breakdown follows it. */
    public function set_default(array $exam, array $rows)
    {
        $this->db->trans_start();
        $ids = $this->resolve_types($exam, $rows);
        foreach ($this->db->select('id')->where(array('cbse_exam_id' => (int) $exam['id'], 'custom_breakdown' => 0))->get('cbse_exam_timetable')->result_array() as $tt) {
            $this->link_types((int) $tt['id'], $ids);
        }
        $this->db->where('id', (int) $exam['id'])->update('cbse_exams', array('default_type_ids' => implode(',', $ids)));
        $this->db->trans_complete();
    }

    /** One subject's own breakdown; $rows null = back to the class default. */
    public function set_subject(array $exam, $tt_id, $rows)
    {
        $this->db->trans_start();
        $ids = $rows === null ? $this->default_type_ids($exam) : $this->resolve_types($exam, $rows);
        $this->link_types((int) $tt_id, $ids);
        $this->db->where('id', (int) $tt_id)->update('cbse_exam_timetable', array('custom_breakdown' => $rows === null ? 0 : 1));
        $this->db->trans_complete();
    }

    // ------------------------------------------------------------------ subjects and students

    /** The exam's subjects with their assessments (and CBTs), and how many of the class's students have every score. */
    public function subjects(array $exam, $class_id)
    {
        $eid = (int) $exam['id'];
        $students = $this->exam_students($exam, $class_id);
        $es_ids = array_keys($students);
        $out = array();
        foreach ($this->db->select('tt.id, tt.subject_id, tt.marks_status, tt.custom_breakdown, s.name, s.code')->from('cbse_exam_timetable tt')->join('subjects s', 's.id = tt.subject_id')
            ->where('tt.cbse_exam_id', $eid)->order_by('s.name')->get()->result_array() as $r) {
            $r['types'] = array();
            $out[(int) $r['id']] = $r;
        }
        if (!$out) {
            return $out;
        }
        foreach ($this->db->select('l.cbse_exam_timetable_id AS tt, t.id, t.name, t.code, t.maximum_marks')->from('cbse_exam_timetable_assessment_types l')
            ->join('cbse_exam_assessment_types t', 't.id = l.cbse_exam_assessment_type_id')->where_in('l.cbse_exam_timetable_id', array_keys($out))->order_by('t.id')->get()->result_array() as $t) {
            $t['cbt'] = null;
            $out[(int) $t['tt']]['types'][(int) $t['id']] = $t;
        }
        foreach ($this->db->select('id, exam, is_active, marks_target, marks_auto')->like('marks_target', 'cbse:', 'after')->get('onlineexam')->result_array() as $o) {
            if (preg_match('/^cbse:(\d+):(\d+)$/', $o['marks_target'], $m) && isset($out[(int) $m[1]]['types'][(int) $m[2]])) {
                $out[(int) $m[1]]['types'][(int) $m[2]]['cbt'] = $o;
            }
        }
        // assessments in the order of the class breakdown (then any others)
        $order = array_flip($this->default_type_ids($exam));
        foreach ($out as $tt => &$s) {
            uksort($s['types'], function ($a, $b) use ($order) {
                $pa = isset($order[$a]) ? $order[$a] : 1000 + $a;
                $pb = isset($order[$b]) ? $order[$b] : 1000 + $b;
                return $pa <=> $pb;
            });
        }
        unset($s);
        foreach ($out as $tt => &$s) {
            $s['done'] = 0;
            $s['total'] = count($es_ids);
            $s['max'] = array_sum(array_column($s['types'], 'maximum_marks'));
        }
        unset($s);
        if ($es_ids) {
            $need = array();
            foreach ($out as $tt => $s) {
                $need[$tt] = count($s['types']);
            }
            foreach ($this->db->select('cbse_exam_timetable_id AS tt, cbse_exam_student_id AS es, COUNT(*) AS n', false)->where_in('cbse_exam_timetable_id', array_keys($out))
                ->where_in('cbse_exam_student_id', $es_ids)->where('(marks IS NOT NULL OR is_absent = 1)', null, false)
                ->group_by(array('cbse_exam_timetable_id', 'cbse_exam_student_id'))->get('cbse_student_subject_marks')->result_array() as $r) {
                if ($need[(int) $r['tt']] && (int) $r['n'] >= $need[(int) $r['tt']]) {
                    $out[(int) $r['tt']]['done']++;
                }
            }
        }
        return $out;
    }

    /** The class's students in the exam: [exam_student_id => row], by section and name. */
    public function exam_students(array $exam, $class_id, $section_id = 0)
    {
        $this->db->select("es.id AS es, es.student_session_id, es.remark, es.principal_remark, es.total_present_days, st.id AS student_id, st.admission_no, st.firstname, st.middlename, st.lastname,
                st.gender, st.dob, st.image, st.guardian_name, st.guardian_email, st.email, st.roll_no, st.current_address, ss.section_id, s.section, c.class", false)
            ->from('cbse_exam_students es')->join('student_session ss', 'ss.id = es.student_session_id')->join('students st', 'st.id = ss.student_id')
            ->join('classes c', 'c.id = ss.class_id')->join('sections s', 's.id = ss.section_id')
            ->where(array('es.cbse_exam_id' => (int) $exam['id'], 'ss.class_id' => (int) $class_id, 'st.is_active' => 'yes'));
        if ($section_id) {
            $this->db->where('ss.section_id', (int) $section_id);
        }
        $out = array();
        foreach ($this->db->order_by('s.section')->order_by('st.firstname')->order_by('st.lastname')->get()->result_array() as $r) {
            $r['name'] = trim(preg_replace('/\s+/', ' ', $r['firstname'] . ' ' . $r['middlename'] . ' ' . $r['lastname']));
            $out[(int) $r['es']] = $r;
        }
        return $out;
    }

    // ------------------------------------------------------------------ results

    public function grades(array $exam)
    {
        $out = array();
        foreach ($exam['cbse_exam_grade_id'] ? $this->db->where('cbse_exam_grade_id', (int) $exam['cbse_exam_grade_id'])->order_by('minimum_percentage', 'desc')->get('cbse_exam_grades_range')->result_array() : array() as $g) {
            $out[] = array('name' => $g['name'], 'min' => (float) $g['minimum_percentage'], 'max' => (float) $g['maximum_percentage'], 'remark' => $g['description']);
        }
        return $out;
    }

    public static function grade_of(array $grades, $pct)
    {
        if ($pct === null) {
            return array('', '');
        }
        foreach ($grades as $g) {
            if ($pct >= $g['min'] - 0.0001) {
                return array($g['name'], $g['remark']);
            }
        }
        return $grades ? array($grades[count($grades) - 1]['name'], $grades[count($grades) - 1]['remark']) : array('', '');
    }

    /** [key => position], higher score first, equal scores share a position (1, 1, 3). */
    public static function positions(array $scores)
    {
        arsort($scores);
        $out = array();
        $pos = 0;
        $rank = 0;
        $prev = null;
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

    public static function ordinal($n)
    {
        if (!$n) {
            return '';
        }
        $v = $n % 100;
        if ($v >= 11 && $v <= 13) {
            return $n . 'th';
        }
        $end = array(1 => 'st', 2 => 'nd', 3 => 'rd');
        return $n . (isset($end[$n % 10]) ? $end[$n % 10] : 'th');
    }

    /**
     * Everything a report card shows, for the class (all its arms) or one student.
     * Returns array('subjects' => ..., 'students' => [es => ...], 'grades' => ..., 'cumulative' => bool, 'terms' => ...).
     */
    public function results(array $exam, $class_id, $only_es = 0)
    {
        $subjects = $this->subjects($exam, $class_id);
        $students = $this->exam_students($exam, $class_id);
        $grades = $this->grades($exam);
        foreach ($students as &$st) {
            $st['subjects'] = array();
        }
        unset($st);
        if ($students && $subjects) {
            foreach (array_chunk(array_keys($students), 500) as $chunk) {
                foreach ($this->db->select('cbse_exam_timetable_id AS tt, cbse_exam_student_id AS es, cbse_exam_assessment_type_id AS t, marks, is_absent')
                    ->where_in('cbse_exam_student_id', $chunk)->where_in('cbse_exam_timetable_id', array_keys($subjects))->get('cbse_student_subject_marks')->result_array() as $m) {
                    if (isset($subjects[(int) $m['tt']]['types'][(int) $m['t']])) {
                        $students[(int) $m['es']]['subjects'][(int) $m['tt']]['marks'][(int) $m['t']] = (int) $m['is_absent'] ? 'ABS' : ($m['marks'] === null || $m['marks'] === '' ? null : round((float) $m['marks'], 2));
                    }
                }
            }
        }
        // subject totals, then positions and class figures within each arm (section)
        $by_section = array();
        foreach ($students as $es => &$st) {
            $sum_pct = 0;
            $n = 0;
            $got_all = 0;
            $max_all = 0;
            foreach ($subjects as $tt => $sub) {
                $marks = isset($st['subjects'][$tt]['marks']) ? $st['subjects'][$tt]['marks'] : array();
                $has = false;
                $total = 0;
                foreach ($sub['types'] as $tid => $t) {
                    if (isset($marks[$tid]) && $marks[$tid] !== null) {
                        $has = true;
                        $total += $marks[$tid] === 'ABS' ? 0 : $marks[$tid];
                    }
                }
                if (!$has) {
                    unset($st['subjects'][$tt]);
                    continue;
                }
                $pct = $sub['max'] > 0 ? round($total * 100 / $sub['max'], 2) : 0;
                list($g, $remark) = self::grade_of($grades, $pct);
                $st['subjects'][$tt] = array('marks' => $marks, 'total' => round($total, 2), 'max' => $sub['max'], 'pct' => $pct, 'grade' => $g, 'remark' => $remark);
                $sum_pct += $pct;
                $n++;
                $got_all += $total;
                $max_all += $sub['max'];
                $by_section[(int) $st['section_id']]['subject'][$tt][$es] = $pct;
            }
            $st['count'] = $n;
            $st['total'] = round($got_all, 2);
            $st['max'] = $max_all;
            $st['average'] = $n ? round($sum_pct / $n, 2) : null;
            list($st['grade'], $st['grade_remark']) = self::grade_of($grades, $st['average']);
            if ($n) {
                $by_section[(int) $st['section_id']]['overall'][$es] = $st['average'];
            }
        }
        unset($st);
        $stats = array();
        foreach ($by_section as $sec => $d) {
            foreach (isset($d['overall']) ? self::positions($d['overall']) : array() as $es => $pos) {
                $students[$es]['position'] = $pos;
            }
            $stats[$sec]['size'] = isset($d['overall']) ? count($d['overall']) : 0;
            $stats[$sec]['class_average'] = !empty($d['overall']) ? round(array_sum($d['overall']) / count($d['overall']), 2) : null;
            foreach (isset($d['subject']) ? $d['subject'] : array() as $tt => $list) {
                foreach (self::positions($list) as $es => $pos) {
                    $students[$es]['subjects'][$tt]['position'] = $pos;
                }
                $stats[$sec]['subject'][$tt] = array('avg' => round(array_sum($list) / count($list), 2), 'high' => max($list), 'low' => min($list), 'n' => count($list));
            }
        }
        // the last term: every term's score per subject and the cumulative
        $cumulative = $this->is_last_term($exam['cbse_term_id']);
        $terms = array();
        if ($cumulative) {
            foreach ($this->terms() as $t) {
                if ((int) $t['id'] === (int) $exam['cbse_term_id']) {
                    $terms[(int) $t['id']] = $t['name'];
                    continue;
                }
                $other = $this->exam_for($class_id, (int) $t['id'], (int) $exam['session_id']);
                if (!$other) {
                    continue;
                }
                $terms[(int) $t['id']] = $t['name'];
                $prev = $this->term_scores($other, array_column($students, 'student_session_id'));
                foreach ($students as $es => &$st) {
                    foreach ($prev[(int) $st['student_session_id']] ?? array() as $subject_id => $pct) {
                        $st['terms'][$subject_id][(int) $t['id']] = $pct;
                    }
                }
                unset($st);
            }
            foreach ($students as $es => &$st) {
                $sums = array();
                foreach ($st['subjects'] as $tt => &$sub) {
                    $sid = (int) $subjects[$tt]['subject_id'];
                    $list = isset($st['terms'][$sid]) ? $st['terms'][$sid] : array();
                    $list[(int) $exam['cbse_term_id']] = $sub['pct'];
                    $sub['terms'] = $list;
                    $sub['cumulative'] = round(array_sum($list) / count($list), 2);
                    $sums[] = $sub['cumulative'];
                }
                unset($sub);
                $st['cumulative_average'] = $sums ? round(array_sum($sums) / count($sums), 2) : null;
            }
            unset($st);
            foreach ($by_section as $sec => $d) {
                $c = array();
                foreach (isset($d['overall']) ? array_keys($d['overall']) : array() as $es) {
                    if ($students[$es]['cumulative_average'] !== null) {
                        $c[$es] = $students[$es]['cumulative_average'];
                    }
                }
                foreach (self::positions($c) as $es => $pos) {
                    $students[$es]['cumulative_position'] = $pos;
                }
            }
        }
        if ($only_es) {
            $students = isset($students[$only_es]) ? array($only_es => $students[$only_es]) : array();
        }
        // attendance of every term (days school opened, days present) and each earlier term's average
        $all_terms = array();
        $ss_ids = array_map('intval', array_column($students, 'student_session_id'));
        foreach ($this->terms() as $t) {
            $tid = (int) $t['id'];
            $all_terms[$tid] = $t['name'];
            $other = $tid === (int) $exam['cbse_term_id'] ? $exam : $this->exam_for($class_id, $tid, (int) $exam['session_id']);
            if (!$other || !$ss_ids) {
                continue;
            }
            $present = array();
            foreach ($this->db->select('student_session_id, total_present_days')->where('cbse_exam_id', (int) $other['id'])->where_in('student_session_id', $ss_ids)->get('cbse_exam_students')->result_array() as $a) {
                $present[(int) $a['student_session_id']] = $a['total_present_days'];
            }
            foreach ($students as $es => &$st) {
                $st['attendance'][$tid] = array((int) $other['total_working_days'], isset($present[(int) $st['student_session_id']]) ? $present[(int) $st['student_session_id']] : null);
                if ($cumulative && $tid !== (int) $exam['cbse_term_id']) {
                    $list = array();
                    foreach ($st['subjects'] as $sub) {
                        if (isset($sub['terms'][$tid])) {
                            $list[] = $sub['terms'][$tid];
                        }
                    }
                    $st['term_average'][$tid] = $list ? round(array_sum($list) / count($list), 2) : null;
                }
            }
            unset($st);
        }
        return array('subjects' => $subjects, 'students' => $students, 'grades' => $grades, 'stats' => $stats, 'cumulative' => $cumulative, 'terms' => $terms, 'all_terms' => $all_terms);
    }

    /** Another term's subject percentages: [student_session_id][subject_id] => %. */
    private function term_scores(array $exam, array $student_session_ids)
    {
        if (!$student_session_ids) {
            return array();
        }
        $out = array();
        foreach ($this->db->select('es.student_session_id AS ss, tt.subject_id, SUM(CASE WHEN m.is_absent = 1 THEN 0 ELSE m.marks END) AS got, SUM(t.maximum_marks) AS max', false)
            ->from('cbse_student_subject_marks m')->join('cbse_exam_students es', 'es.id = m.cbse_exam_student_id')
            ->join('cbse_exam_timetable tt', 'tt.id = m.cbse_exam_timetable_id')->join('cbse_exam_assessment_types t', 't.id = m.cbse_exam_assessment_type_id')
            ->where('tt.cbse_exam_id', (int) $exam['id'])->where_in('es.student_session_id', array_map('intval', $student_session_ids))
            ->group_by(array('es.student_session_id', 'tt.subject_id'))->get()->result_array() as $r) {
            if ((float) $r['max'] > 0) {
                $out[(int) $r['ss']][(int) $r['subject_id']] = round((float) $r['got'] * 100 / (float) $r['max'], 2);
            }
        }
        return $out;
    }

    /** Saves typed scores for a subject: [es][type] => value ('' clears, 'ABS'). Checks maximums; logs changes. */
    public function save_scores(array $exam, $tt_id, array $values, array $allowed_es)
    {
        $this->CI->load->library('cbse_marks_guard');
        $guard = $this->CI->cbse_marks_guard;
        $tt = $guard->subject($tt_id);
        $why = $guard->lock_reason($tt);
        if ($why !== '') {
            throw new RuntimeException($why);
        }
        $links = array();
        foreach ($this->db->select('l.id, l.cbse_exam_assessment_type_id AS t, t.name, t.maximum_marks')->from('cbse_exam_timetable_assessment_types l')
            ->join('cbse_exam_assessment_types t', 't.id = l.cbse_exam_assessment_type_id')->where('l.cbse_exam_timetable_id', (int) $tt_id)->get()->result_array() as $l) {
            $links[(int) $l['t']] = $l;
        }
        // an exam taken as a CBT gets its scores from the CBT only
        foreach ($this->db->select('marks_target')->like('marks_target', 'cbse:' . (int) $tt_id . ':', 'after')->get('onlineexam')->result_array() as $o) {
            if (preg_match('/^cbse:\d+:(\d+)$/', $o['marks_target'], $mm)) {
                unset($links[(int) $mm[1]]);
            }
        }
        $existing = array();
        foreach ($this->db->where('cbse_exam_timetable_id', (int) $tt_id)->get('cbse_student_subject_marks')->result_array() as $m) {
            $existing[(int) $m['cbse_exam_student_id']][(int) $m['cbse_exam_assessment_type_id']] = $m;
        }
        $bad = array();
        $writes = array();
        foreach ($values as $es => $types) {
            $es = (int) $es;
            if (!in_array($es, $allowed_es, true)) {
                continue;
            }
            foreach ((array) $types as $t => $v) {
                $t = (int) $t;
                if (!isset($links[$t])) {
                    continue;
                }
                $v = strtoupper(trim((string) $v));
                if ($v === '') {
                    $new = null;
                } elseif (in_array($v, array('ABS', 'A'), true)) {
                    $new = array('marks' => 0, 'is_absent' => 1);
                } elseif (is_numeric($v) && (float) $v >= 0 && (float) $v <= (float) $links[$t]['maximum_marks'] + 0.0001) {
                    $new = array('marks' => round((float) $v, 2), 'is_absent' => 0);
                } else {
                    $bad[] = array($es, $links[$t]['name'] . ' (out of ' . (float) $links[$t]['maximum_marks'] . '): "' . $v . '"');
                    continue;
                }
                $writes[] = array($es, $t, $new);
            }
        }
        if ($bad) {
            return array(0, $bad);
        }
        $changed = 0;
        $this->db->trans_start();
        foreach ($writes as $w) {
            list($es, $t, $new) = $w;
            $was = isset($existing[$es][$t]) ? $existing[$es][$t] : null;
            if ($new === null) {
                if ($was) {
                    $this->db->where('id', (int) $was['id'])->delete('cbse_student_subject_marks');
                    $guard->log(array('cbse_exam_id' => (int) $exam['id'], 'cbse_exam_timetable_id' => (int) $tt_id, 'cbse_exam_student_id' => $es, 'cbse_exam_assessment_type_id' => $t,
                        'old_marks' => $was['marks'], 'new_marks' => null, 'old_absent' => (int) $was['is_absent'], 'new_absent' => null, 'note' => 'cleared'));
                    $changed++;
                }
                continue;
            }
            if ($was) {
                if ((int) $was['is_absent'] === $new['is_absent'] && $was['marks'] !== null && abs((float) $was['marks'] - $new['marks']) < 0.001) {
                    continue;
                }
                $this->db->where('id', (int) $was['id'])->update('cbse_student_subject_marks', $new);
            } else {
                $this->db->insert('cbse_student_subject_marks', $new + array('cbse_exam_timetable_assessment_type_id' => (int) $links[$t]['id'], 'cbse_exam_timetable_id' => (int) $tt_id,
                    'cbse_exam_student_id' => $es, 'cbse_exam_assessment_type_id' => $t, 'note' => ''));
            }
            $guard->log_one((int) $exam['id'], (int) $tt_id, $es, $t, $was, $new, 'entry');
            $changed++;
        }
        $this->db->trans_complete();
        return array($changed, array());
    }
}
