<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Transport > Assign Students and Hostel > Assign Students: a class (or section) at a time, each
 * student's route, vehicle and pickup point, or hostel and room, chosen in a list and saved together
 * (before, only one student at a time on Edit Student).
 *
 * Transport is the same data as Edit Student (student_session.vehroute_id / route_pickup_point_id;
 * the session's transport fee months in student_transport_fees). A student whose transport fees
 * have payments keeps the pickup point (moving it would lose those payments): change it after
 * the payments are sorted out. Hostel is students.hostel_room_id; a room never gets more students
 * than its beds.
 */
class Studentplaces extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('class_model', 'section_model'));
    }

    public function transport()
    {
        $this->need('transport');
        $this->page('transport');
    }

    public function hostel()
    {
        $this->need('hostel');
        $this->page('hostel');
    }

    /** POST class_id, section_id, place[student_session_id] = "vehroute_id:route_pickup_point_id" or "", charge */
    public function save_transport()
    {
        $this->need('transport');
        $session = (int) $this->setting_model->getCurrentSession();
        $options = $this->transport_options();
        $months = array_column($this->db->select('id')->where('session_id', $session)->get('transport_feemaster')->result_array(), 'id');
        $charge = (bool) $this->input->post('charge');
        $done = 0;
        $kept = array();
        foreach ((array) $this->input->post('place') as $ss_id => $value) {
            $ss = $this->db->select('ss.id, ss.vehroute_id, ss.route_pickup_point_id, st.firstname, st.lastname', false)->from('student_session ss')
                ->join('students st', 'st.id = ss.student_id')->where(array('ss.id' => (int) $ss_id, 'ss.session_id' => $session))->get()->row_array();
            if (!$ss) {
                continue;
            }
            $value = (string) $value;
            if ($value !== '' && !isset($options[$value])) {
                continue; // not a route of this school
            }
            list($vehroute, $rpp) = $value === '' ? array(null, null) : array_map('intval', explode(':', $value));
            if ((int) $ss['route_pickup_point_id'] === (int) $rpp && (int) $ss['vehroute_id'] === (int) $vehroute) {
                continue;
            }
            $rows = array_column($this->db->select('id')->where('student_session_id', (int) $ss['id'])->get('student_transport_fees')->result_array(), 'id');
            $paid = $rows && (int) $ss['route_pickup_point_id'] !== (int) $rpp
                && $this->db->where_in('student_transport_fee_id', $rows)->count_all_results('student_fees_deposite') > 0;
            if ($paid) {
                $kept[] = trim($ss['firstname'] . ' ' . $ss['lastname']);
                continue;
            }
            $this->db->trans_start();
            $this->db->where('id', (int) $ss['id'])->update('student_session', array('vehroute_id' => $vehroute, 'route_pickup_point_id' => $rpp));
            if ($rpp === null) {
                if ($rows) {
                    $this->db->where_in('id', $rows)->delete('student_transport_fees');
                }
            } elseif ($rows) {
                $this->db->where_in('id', $rows)->update('student_transport_fees', array('route_pickup_point_id' => $rpp));
            } elseif ($charge) {
                foreach ($months as $m) {
                    $this->db->insert('student_transport_fees', array('student_session_id' => (int) $ss['id'], 'route_pickup_point_id' => $rpp, 'transport_feemaster_id' => (int) $m));
                }
            }
            $this->db->trans_complete();
            $done++;
        }
        $msg = $done . ' student' . ($done === 1 ? '' : 's') . ' saved.';
        if ($kept) {
            $msg .= ' Not moved (their transport fees have payments): ' . implode(', ', $kept) . '.';
        }
        $this->session->set_flashdata('sp_msg', array($kept ? 'warning' : 'success', $msg));
        redirect('admin/studentplaces/transport?' . $this->back());
    }

    /** POST class_id, section_id, room[student_id] = hostel_room_id or "" */
    public function save_hostel()
    {
        $this->need('hostel');
        $rooms = $this->rooms();
        $this->load->library('Hostel_fees');
        $charge = (bool) $this->input->post('charge');
        $session = (int) $this->setting_model->getCurrentSession();
        $done = 0;
        $full = array();
        $kept = array();
        foreach ((array) $this->input->post('room') as $student_id => $room_id) {
            $st = $this->db->select('id, hostel_room_id, firstname, lastname')->where('id', (int) $student_id)->get('students')->row_array();
            if (!$st) {
                continue;
            }
            $room_id = (int) $room_id;
            if ($room_id && !isset($rooms[$room_id])) {
                continue;
            }
            if ((int) $st['hostel_room_id'] === $room_id) {
                continue;
            }
            if ($room_id && $rooms[$room_id]['beds'] > 0 && $rooms[$room_id]['used'] >= $rooms[$room_id]['beds']) {
                $full[] = trim($st['firstname'] . ' ' . $st['lastname']) . ' (room ' . $rooms[$room_id]['room_no'] . ' is full)';
                continue;
            }
            // the hostel fee follows the room; a student whose hostel fee has payments stays put
            $ss = $this->db->select('id')->where(array('student_id' => (int) $st['id'], 'session_id' => $session))->get('student_session')->row_array();
            if ($ss && $this->hostel_fees->move((int) $ss['id'], (int) $st['hostel_room_id'], $room_id, $charge) === 'paid') {
                $kept[] = trim($st['firstname'] . ' ' . $st['lastname']);
                continue;
            }
            $this->db->where('id', (int) $st['id'])->update('students', array('hostel_room_id' => $room_id));
            if ((int) $st['hostel_room_id'] && isset($rooms[(int) $st['hostel_room_id']])) {
                $rooms[(int) $st['hostel_room_id']]['used']--;
            }
            if ($room_id) {
                $rooms[$room_id]['used']++;
            }
            $done++;
        }
        $msg = $done . ' student' . ($done === 1 ? '' : 's') . ' saved.';
        if ($full) {
            $msg .= ' Not placed: ' . implode(', ', $full) . '.';
        }
        if ($kept) {
            $msg .= ' Not moved (their hostel fee has payments): ' . implode(', ', $kept) . '.';
        }
        $this->session->set_flashdata('sp_msg', array($full || $kept ? 'warning' : 'success', $msg));
        redirect('admin/studentplaces/hostel?' . $this->back());
    }

    private function page($kind)
    {
        $this->session->set_userdata('top_menu', $kind === 'transport' ? 'Transport' : 'Hostel');
        $this->session->set_userdata('sub_menu', 'studentplaces/' . $kind);
        $class_id = (int) $this->input->get('class_id');
        $section_id = (int) $this->input->get('section_id');
        $session = (int) $this->setting_model->getCurrentSession(); // before building a query: it runs one of its own
        $students = array();
        if ($class_id) {
            $this->db->select("ss.id AS ss_id, st.id AS student_id, CONCAT_WS(' ', st.firstname, st.middlename, st.lastname) AS name, st.admission_no,
                    s.section, ss.vehroute_id, ss.route_pickup_point_id, st.hostel_room_id", false)
                ->from('student_session ss')->join('students st', 'st.id = ss.student_id')->join('sections s', 's.id = ss.section_id')
                ->where(array('ss.session_id' => $session, 'ss.class_id' => $class_id, 'st.is_active' => 'yes'));
            if ($section_id) {
                $this->db->where('ss.section_id', $section_id);
            }
            $students = $this->db->order_by('s.section')->order_by('st.firstname')->order_by('st.lastname')->get()->result_array();
        }
        $data = array(
            'kind' => $kind,
            'classes' => $this->class_model->get(),
            'sections' => $class_id ? $this->section_model->getClassBySectionAll($class_id) : array(),
            'class_id' => $class_id,
            'section_id' => $section_id,
            'students' => $students,
            'options' => $kind === 'transport' ? $this->transport_options() : array(),
            'rooms' => $kind === 'hostel' ? $this->rooms() : array(),
            'months' => $kind === 'transport' ? $this->db->where('session_id', $session)->count_all_results('transport_feemaster') : 0,
        );
        $this->load->view('layout/header', $data);
        $this->load->view('admin/studentplaces/index', $data);
        $this->load->view('layout/footer', $data);
    }

    /** "vehroute_id:route_pickup_point_id" => [route, vehicle, pickup point, fees, time] */
    private function transport_options()
    {
        $out = array();
        $rows = $this->db->select('vr.id AS vehroute_id, rpp.id AS rpp_id, r.route_title, v.vehicle_no, p.name AS point, rpp.fees, rpp.pickup_time', false)
            ->from('vehicle_routes vr')
            ->join('transport_route r', 'r.id = vr.route_id')
            ->join('vehicles v', 'v.id = vr.vehicle_id')
            ->join('route_pickup_point rpp', 'rpp.transport_route_id = r.id')
            ->join('pickup_point p', 'p.id = rpp.pickup_point_id')
            ->order_by('r.route_title')->order_by('v.vehicle_no')->order_by('rpp.order_number')->get()->result_array();
        foreach ($rows as $r) {
            $out[(int) $r['vehroute_id'] . ':' . (int) $r['rpp_id']] = $r;
        }
        return $out;
    }

    /** room id => [hostel, room_no, beds, used] */
    private function rooms()
    {
        $out = array();
        $rows = $this->db->select('hr.id, hr.room_no, hr.no_of_bed, h.hostel_name, (SELECT COUNT(*) FROM students st WHERE st.hostel_room_id = hr.id AND st.is_active = "yes") AS used', false) // tenancy-lint: ok count of the room's own students
            ->from('hostel_rooms hr')->join('hostel h', 'h.id = hr.hostel_id')->order_by('h.hostel_name')->order_by('hr.room_no')->get()->result_array();
        foreach ($rows as $r) {
            $out[(int) $r['id']] = array('hostel' => $r['hostel_name'], 'room_no' => $r['room_no'], 'beds' => (int) $r['no_of_bed'], 'used' => (int) $r['used']);
        }
        return $out;
    }

    private function back()
    {
        return http_build_query(array('class_id' => (int) $this->input->post('class_id'), 'section_id' => (int) $this->input->post('section_id')));
    }

    /** Staff who may edit students, or manage transport fees / hostel rooms. */
    private function need($kind)
    {
        // the same rule as the menu entries (School_tidy)
        $ok = $this->rbac->hasPrivilege('student', 'can_edit')
            || ($kind === 'transport' ? $this->rbac->hasPrivilege('student_transport_fees', 'can_view') : $this->rbac->hasPrivilege('hostel_rooms', 'can_view'));
        if (!$ok) {
            access_denied();
        }
    }
}
