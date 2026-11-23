<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Report Behaviour (behaviour/log): pick a student, what happened (one or more incident types, good
 * or bad) and a note, in one step; the latest reports of the session are listed underneath. The
 * records are the usual student incidents (Student Incidents, reports and the student's profile
 * show them) and the note is the incident's first comment.
 */
class Log extends MY_Addon_BRController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('studentsession_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('behaviour_records_assign_incident', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'behaviour');
        $this->session->set_userdata('sub_menu', 'behaviour/log');
        $session = (int) $this->setting_model->getCurrentSession();
        $class_id = (int) $this->input->get('class_id');

        $students = $this->db->select("ss.student_id AS id, CONCAT_WS(' ', st.firstname, st.middlename, st.lastname) AS name, st.admission_no, c.class, s.section", false)
            ->from('student_session ss')->join('students st', 'st.id = ss.student_id')
            ->join('classes c', 'c.id = ss.class_id')->join('sections s', 's.id = ss.section_id')
            ->where('ss.session_id', $session)->where('st.is_active', 'yes')
            ->order_by('c.id')->order_by('s.section')->order_by('st.firstname')->get()->result_array();

        $this->db->select("si.id, si.created_at, si.student_id, CONCAT_WS(' ', st.firstname, st.middlename, st.lastname) AS student, st.admission_no, sb.title, sb.point,
                CONCAT_WS(' ', sf.name, sf.surname) AS staff, c.class, s.section,
                (SELECT cm.comment FROM student_incident_comments cm WHERE cm.student_incident_id = si.id ORDER BY cm.id LIMIT 1) AS note", false) // tenancy-lint: ok subquery on the incident's own comments
            ->from('student_incidents si')
            ->join('students st', 'st.id = si.student_id')
            ->join('student_behaviour sb', 'sb.id = si.incident_id')
            ->join('staff sf', 'sf.id = si.assign_by', 'left')
            ->join('student_session ss', 'ss.student_id = si.student_id AND ss.session_id = si.session_id', 'left')
            ->join('classes c', 'c.id = ss.class_id', 'left')
            ->join('sections s', 's.id = ss.section_id', 'left')
            ->where('si.session_id', $session);
        if ($class_id) {
            $this->db->where('ss.class_id', $class_id);
        }
        $recent = $this->db->order_by('si.id', 'DESC')->limit(100)->get()->result_array();

        $data = array(
            'title' => 'Report Behaviour',
            'students' => $students,
            'incidents' => $this->studentbehaviour_model->get(),
            'recent' => $recent,
            'classes' => $this->class_model->get(),
            'class_id' => $class_id,
            'can_add' => $this->rbac->hasPrivilege('behaviour_records_assign_incident', 'can_add'),
            'can_delete' => $this->rbac->hasPrivilege('behaviour_records_assign_incident', 'can_delete'),
        );
        $this->load->view('layout/header', $data);
        $this->load->view('behaviour/log/index', $data);
        $this->load->view('layout/footer', $data);
    }

    /** POST student_id, incident_id[], note */
    public function save()
    {
        if (!$this->rbac->hasPrivilege('behaviour_records_assign_incident', 'can_add')) {
            access_denied();
        }
        $session = (int) $this->setting_model->getCurrentSession();
        $student_id = (int) $this->input->post('student_id');
        $incident_ids = array_filter(array_map('intval', (array) $this->input->post('incident_id')));
        $note = trim((string) $this->input->post('note', true));
        $in_session = $student_id && $this->db->where(array('student_id' => $student_id, 'session_id' => $session))->count_all_results('student_session') > 0;
        $types = $incident_ids ? $this->db->where_in('id', $incident_ids)->get('student_behaviour')->result_array() : array();
        if (!$in_session || !$types) {
            $this->session->set_flashdata('beh_msg', array('danger', !$in_session ? 'Choose the student.' : 'Choose what happened.'));
            redirect('behaviour/log');
        }
        $user = $this->customlib->getUserData();
        $student = $this->student_model->get($student_id);
        foreach ($types as $t) {
            $id = $this->studentincidents_model->add(array(
                'student_id' => $student_id,
                'session_id' => $session,
                'incident_id' => (int) $t['id'],
                'assign_by' => (int) $user['id'],
            ));
            if ($id && $note !== '') {
                $this->studentincidents_model->addmessage(array(
                    'student_incident_id' => (int) $id,
                    'comment' => mb_substr($note, 0, 2000),
                    'type' => 'staff',
                    'staff_id' => (int) $user['id'],
                ));
            }
            if ($student) {
                $this->behaviour_mail_sms->mailsms('behaviour_incident_assigned', array(
                    'id' => $student['id'], 'incident_title' => $t['title'], 'incident_point' => $t['point'],
                    'student_name' => $student['firstname'] . ' ' . $student['lastname'], 'class' => $student['class'], 'section' => $student['section'],
                    'admission_no' => $student['admission_no'], 'mobileno' => $student['mobileno'], 'email' => $student['email'],
                    'guardian_name' => $student['guardian_name'], 'guardian_phone' => $student['guardian_phone'], 'guardian_email' => $student['guardian_email'],
                    'parent_app_key' => $student['parent_app_key'], 'app_key' => $student['app_key'],
                ));
            }
        }
        $st = $this->db->select('firstname, lastname')->where('id', $student_id)->get('students')->row_array();
        $name = $st ? trim($st['firstname'] . ' ' . $st['lastname']) : 'the student';
        $this->session->set_flashdata('beh_msg', array('success', 'Reported for ' . $name . ': ' . implode(', ', array_column($types, 'title')) . '.'));
        redirect('behaviour/log');
    }

    /** POST id: removes a report (and its comments). */
    public function remove()
    {
        if (!$this->rbac->hasPrivilege('behaviour_records_assign_incident', 'can_delete')) {
            access_denied();
        }
        $id = (int) $this->input->post('id');
        if ($id && $this->db->where('id', $id)->count_all_results('student_incidents')) {
            $this->db->where('student_incident_id', $id)->delete('student_incident_comments');
            $this->studentincidents_model->delete($id);
            $this->session->set_flashdata('beh_msg', array('success', 'The report was removed.'));
        }
        redirect('behaviour/log');
    }
}
