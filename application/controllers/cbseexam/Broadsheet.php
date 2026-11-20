<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Report Cards > Broadsheet: the master score sheet of an exam (all students × all subjects) with
 * totals, averages, grades and positions; a student's transcript across terms and sessions; and an
 * export of every score (CSV). Library: Cbse_broadsheet.
 */
class Broadsheet extends MY_Addon_CBSEController
{
    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->load->library('cbse_broadsheet');
    }

    private function can_view()
    {
        return $this->rbac->hasPrivilege('template_marks_report', 'can_view') || $this->rbac->hasPrivilege('subject_marks_report', 'can_view') || $this->rbac->hasPrivilege('cbse_exam', 'can_view');
    }

    public function index()
    {
        if (!$this->can_view()) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbseexam/broadsheet');
        $data['exams'] = $this->db->select('e.id, e.name, t.name AS term_name')->from('cbse_exams e')->join('cbse_terms t', 't.id = e.cbse_term_id', 'left')
            ->where('e.session_id', $this->current_session)->order_by('e.id', 'desc')->get()->result_array();
        $data['exam_id'] = (int) $this->input->get('exam_id');
        $data['cs'] = (int) $this->input->get('cs');
        $data['sheet'] = null;
        if ($data['exam_id']) {
            $data['exam'] = $this->db->where('id', $data['exam_id'])->get('cbse_exams')->row_array();
            if ($data['exam']) {
                $data['class_sections'] = $this->cbse_broadsheet->class_sections($data['exam_id']);
                $data['sheet'] = $this->cbse_broadsheet->sheet($data['exam_id'], $data['cs']);
            }
        }
        if ($data['sheet'] && $this->input->get('export') === 'csv') {
            return $this->sheet_csv($data);
        }
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/broadsheet/index', $data);
        $this->load->view('layout/footer', $data);
    }

    private function sheet_csv($data)
    {
        list($subjects, $students, $stats) = $data['sheet'];
        $head = array('Position', 'Admission no', 'Name', 'Class');
        foreach ($subjects as $s) {
            $head[] = $s['name'] . ' (' . (float) $s['max'] . ')';
        }
        $rows = array(array_merge($head, array('Total', 'Out of', 'Average', 'Percentage', 'Grade')));
        uasort($students, function ($a, $b) { return (isset($a['position']) ? $a['position'] : 1e9) <=> (isset($b['position']) ? $b['position'] : 1e9); });
        foreach ($students as $st) {
            $r = array(isset($st['position']) ? $st['position'] : '', $st['admission_no'], $st['name'], $st['class'] . ' (' . $st['section'] . ')');
            foreach ($subjects as $tt => $s) {
                $r[] = isset($st['absent'][$tt]) ? 'ABS' : (isset($st['marks'][$tt]) ? $st['marks'][$tt] : '');
            }
            $rows[] = array_merge($r, array($st['total'], $st['max'], $st['average'], $st['percentage'], $st['grade']));
        }
        foreach (array('avg' => 'Subject average', 'high' => 'Highest', 'low' => 'Lowest') as $k => $label) {
            $r = array('', '', $label, '');
            foreach ($subjects as $tt => $s) {
                $r[] = $stats[$tt] ? $stats[$tt][$k] : '';
            }
            $rows[] = $r;
        }
        $this->csv('broadsheet-' . preg_replace('/[^a-z0-9]+/i', '-', $data['exam']['name']) . '.csv', $rows);
    }

    public function transcript($student_id = 0)
    {
        if (!$this->can_view()) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbseexam/broadsheet');
        $q = trim((string) $this->input->get('q'));
        $data['q'] = $q;
        $data['matches'] = array();
        if ($q !== '' && !$student_id) {
            $data['matches'] = $this->db->select('st.id, st.admission_no, st.firstname, st.middlename, st.lastname, c.class, s.section')
                ->from('students st')->join('student_session ss', 'ss.student_id = st.id AND ss.session_id = ' . (int) $this->current_session, 'left') // tenancy-lint: ok int
                ->join('classes c', 'c.id = ss.class_id', 'left')->join('sections s', 's.id = ss.section_id', 'left')
                ->group_start()->like('st.admission_no', $q)->or_like("CONCAT_WS(' ', st.firstname, st.middlename, st.lastname)", $q, 'both', false)->group_end()
                ->group_by('st.id')->limit(30)->get()->result_array();
            if (count($data['matches']) === 1) {
                redirect('cbseexam/broadsheet/transcript/' . $data['matches'][0]['id']);
            }
        }
        $data['student'] = null;
        if ($student_id) {
            $data['student'] = $this->db->select('st.*, c.class, s.section')->from('students st')
                ->join('student_session ss', 'ss.student_id = st.id AND ss.session_id = ' . (int) $this->current_session, 'left') // tenancy-lint: ok int
                ->join('classes c', 'c.id = ss.class_id', 'left')->join('sections s', 's.id = ss.section_id', 'left')
                ->where('st.id', (int) $student_id)->get()->row_array();
            if ($data['student']) {
                list($data['exams'], $data['sessions']) = $this->cbse_broadsheet->transcript($student_id);
            }
        }
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/broadsheet/transcript', $data);
        $this->load->view('layout/footer', $data);
    }

    /** Every score of every exam (this session, or all with ?all=1), CSV. */
    public function export_all()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam', 'can_view')) {
            access_denied();
        }
        $all = (bool) $this->input->get('all');
        $this->csv('exam-scores-' . ($all ? 'all-sessions' : 'this-session') . '-' . date('Y-m-d') . '.csv', $this->cbse_broadsheet->export_rows($all ? 0 : $this->current_session));
    }

    private function csv($name, array $rows)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        foreach ($rows as $r) {
            fputcsv($out, array_map(function ($v) { return is_string($v) && preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v; }, $r));
        }
        fclose($out);
        exit;
    }
}
