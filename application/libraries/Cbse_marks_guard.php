<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Report Cards scores: review and lock workflow, and the change log (migration 306).
 *
 * A subject of an exam (cbse_exam_timetable row) has marks_status:
 *  - draft:     teachers enter and change scores;
 *  - submitted: the teacher sent them for review; only an exam manager can change them, approve
 *               them or return them to the teacher;
 *  - approved:  locked for everyone; an exam manager unlocks them (back to draft) with a reason.
 * When the exam's results are published, only an exam manager can change scores.
 * Exam manager = Report Cards > Exam with edit permission (Super Admin always).
 *
 * Every score change (entry, import, CBT) and every status change goes to cbse_marks_audit.
 */
class Cbse_marks_guard
{
    const LABELS = array('draft' => 'Draft', 'submitted' => 'Submitted for review', 'approved' => 'Approved & locked');

    private $CI;
    private $manager = null;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function can_approve()
    {
        if ($this->manager === null) {
            $this->manager = (bool) $this->CI->rbac->hasPrivilege('cbse_exam', 'can_edit');
        }
        return $this->manager;
    }

    /** The subject row with its exam: tt.*, exam_name, is_publish. */
    public function subject($timetable_id)
    {
        return $this->CI->db->select('tt.*, e.name AS exam_name, e.is_publish, s.name AS subject_name')
            ->from('cbse_exam_timetable tt')
            ->join('cbse_exams e', 'e.id = tt.cbse_exam_id')
            ->join('subjects s', 's.id = tt.subject_id', 'left')
            ->where('tt.id', (int) $timetable_id)->get()->row_array();
    }

    /** Why the current user may not change this subject's scores ('' = they may). */
    public function lock_reason($tt)
    {
        if (!$tt) {
            return 'This subject is no longer part of the exam. Reload the page.';
        }
        $status = isset($tt['marks_status']) ? $tt['marks_status'] : 'draft';
        if ($status === 'approved') {
            return 'These scores are approved and locked. An exam manager can unlock them to make a change.';
        }
        if ($this->can_approve()) {
            return '';
        }
        if ($status === 'submitted') {
            return 'These scores were submitted for review. They can be changed again if an exam manager returns them to you.';
        }
        if (!empty($tt['is_publish'])) {
            return 'The results of this exam are published. Ask an exam manager to make a change.';
        }
        return '';
    }

    /**
     * Changes the status of subjects. $action: submit | approve | unlock | return.
     * Returns array(done, skipped messages).
     */
    public function set_status(array $timetable_ids, $action, $note = '')
    {
        $db = $this->CI->db;
        $staff = (int) $this->CI->customlib->getStaffID();
        $now = date('Y-m-d H:i:s');
        $done = 0;
        $skipped = array();
        foreach (array_unique(array_map('intval', $timetable_ids)) as $id) {
            $tt = $this->subject($id);
            if (!$tt) {
                continue;
            }
            $name = $tt['subject_name'] ?: 'Subject';
            $status = $tt['marks_status'] ?: 'draft';
            $set = null;
            if ($action === 'submit') {
                if ($status !== 'draft') {
                    $skipped[] = $name . ' is already ' . strtolower(self::LABELS[$status]) . '.';
                    continue;
                }
                $set = array('marks_status' => 'submitted', 'marks_submitted_by' => $staff, 'marks_submitted_at' => $now);
            } elseif (!$this->can_approve()) {
                $skipped[] = 'Only an exam manager can approve, unlock or return scores.';
                break;
            } elseif ($action === 'approve') {
                if ($status === 'approved') {
                    continue;
                }
                $set = array('marks_status' => 'approved', 'marks_approved_by' => $staff, 'marks_approved_at' => $now);
                if ($status === 'draft') {
                    $set += array('marks_submitted_by' => $staff, 'marks_submitted_at' => $now);
                }
            } elseif ($action === 'unlock' || $action === 'return') {
                if ($status === 'draft') {
                    continue;
                }
                if (trim($note) === '') {
                    $skipped[] = 'Give a reason for ' . ($action === 'unlock' ? 'unlocking' : 'returning') . ' the scores.';
                    break;
                }
                $set = array('marks_status' => 'draft', 'marks_approved_by' => null, 'marks_approved_at' => null);
            } else {
                break;
            }
            $db->where('id', $id)->update('cbse_exam_timetable', $set);
            $this->log(array('cbse_exam_id' => $tt['cbse_exam_id'], 'cbse_exam_timetable_id' => $id, 'action' => $action, 'note' => mb_substr(trim($note), 0, 250)));
            $done++;
        }
        return array($done, array_values(array_unique($skipped)));
    }

    /** The scores of a subject now: [exam_student_id][assessment_type_id] => array(marks, absent). */
    public function snapshot($timetable_id)
    {
        $out = array();
        foreach ($this->CI->db->select('cbse_exam_student_id, cbse_exam_assessment_type_id, marks, is_absent')
            ->where('cbse_exam_timetable_id', (int) $timetable_id)->get('cbse_student_subject_marks')->result_array() as $r) {
            $out[(int) $r['cbse_exam_student_id']][(int) $r['cbse_exam_assessment_type_id']] = array($this->num($r['marks']), (int) $r['is_absent']);
        }
        return $out;
    }

    /** Logs the differences between two snapshots of a subject. */
    public function log_changes($exam_id, $timetable_id, array $before, array $after, $source = 'entry')
    {
        $rows = array();
        $keys = array();
        foreach (array($before, $after) as $snap) {
            foreach ($snap as $es => $types) {
                foreach ($types as $type => $v) {
                    $keys[$es . ':' . $type] = array($es, $type);
                }
            }
        }
        foreach ($keys as $k) {
            list($es, $type) = $k;
            $was = isset($before[$es][$type]) ? $before[$es][$type] : array(null, null);
            $now = isset($after[$es][$type]) ? $after[$es][$type] : array(null, null);
            $same = $was[1] === $now[1] && (($was[0] === null && $now[0] === null) || ($was[0] !== null && $now[0] !== null && abs($was[0] - $now[0]) < 0.001));
            if ($same || ($was[0] === null && $was[1] === null && $now[0] === null && !$now[1])) {
                continue;
            }
            $rows[] = array('cbse_exam_id' => $exam_id, 'cbse_exam_timetable_id' => $timetable_id, 'cbse_exam_student_id' => $es,
                'cbse_exam_assessment_type_id' => $type, 'source' => $source, 'old_marks' => $was[0], 'new_marks' => $now[0],
                'old_absent' => $was[1], 'new_absent' => $now[1]);
        }
        foreach ($rows as $r) {
            $this->log($r);
        }
        return count($rows);
    }

    /** One change of a single score (import, CBT). */
    public function log_one($exam_id, $timetable_id, $es, $type, $old, $new, $source)
    {
        $old = $old ?: array();
        $om = isset($old['marks']) ? $this->num($old['marks']) : null;
        $oa = isset($old['is_absent']) ? (int) $old['is_absent'] : null;
        $nm = $this->num($new['marks']);
        $na = (int) $new['is_absent'];
        if ($oa === $na && $om !== null && $nm !== null && abs($om - $nm) < 0.001) {
            return;
        }
        $this->log(array('cbse_exam_id' => $exam_id, 'cbse_exam_timetable_id' => $timetable_id, 'cbse_exam_student_id' => $es,
            'cbse_exam_assessment_type_id' => $type, 'source' => $source, 'old_marks' => $om, 'new_marks' => $nm, 'old_absent' => $oa, 'new_absent' => $na));
    }

    public function log(array $row)
    {
        $row += array('action' => 'marks', 'source' => 'entry', 'staff_id' => (int) $this->CI->customlib->getStaffID() ?: null, 'created_at' => date('Y-m-d H:i:s'));
        $this->CI->db->insert('cbse_marks_audit', $row);
    }

    /** The change log of a subject (or of a whole exam), newest first. */
    public function history($timetable_id = null, $exam_id = null, $limit = 500)
    {
        $db = $this->CI->db;
        $db->select("a.*, CONCAT_WS(' ', st.firstname, st.middlename, st.lastname) AS student_name, st.admission_no, t.name AS assessment_name,
                sub.name AS subject_name, CONCAT_WS(' ', sf.name, sf.surname) AS staff_name", false)
            ->from('cbse_marks_audit a')
            ->join('cbse_exam_students es', 'es.id = a.cbse_exam_student_id', 'left')
            ->join('student_session ss', 'ss.id = es.student_session_id', 'left')
            ->join('students st', 'st.id = ss.student_id', 'left')
            ->join('cbse_exam_assessment_types t', 't.id = a.cbse_exam_assessment_type_id', 'left')
            ->join('cbse_exam_timetable tt', 'tt.id = a.cbse_exam_timetable_id', 'left')
            ->join('subjects sub', 'sub.id = tt.subject_id', 'left')
            ->join('staff sf', 'sf.id = a.staff_id', 'left');
        if ($timetable_id) {
            $db->where('a.cbse_exam_timetable_id', (int) $timetable_id);
        }
        if ($exam_id) {
            $db->where('a.cbse_exam_id', (int) $exam_id);
        }
        return $db->order_by('a.id', 'desc')->limit($limit)->get()->result_array();
    }

    private function num($v)
    {
        return ($v === null || $v === '') ? null : round((float) $v, 2);
    }
}
