<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * What a student has been given (Transport / Hostel > Assign Students, or Edit Student): their route,
 * vehicle and pickup point, and their hostel room. Shown to the student and their parents on the
 * portal dashboard, Transport Routes (user/route) and Hostel Rooms (user/hostelroom).
 */
class Student_places
{
    /** @return array|null route_title, vehicle_no, vehicle_model, driver_name, driver_contact, point, pickup_time, distance, fees */
    public function transport($student_session_id)
    {
        $db = get_instance()->db;
        return $db->select('r.route_title, v.vehicle_no, v.vehicle_model, v.driver_name, v.driver_contact, p.name AS point, rpp.pickup_time, rpp.destination_distance AS distance, rpp.fees', false)
            ->from('student_session ss')
            ->join('route_pickup_point rpp', 'rpp.id = ss.route_pickup_point_id')
            ->join('pickup_point p', 'p.id = rpp.pickup_point_id')
            ->join('transport_route r', 'r.id = rpp.transport_route_id')
            ->join('vehicle_routes vr', 'vr.id = ss.vehroute_id', 'left')
            ->join('vehicles v', 'v.id = vr.vehicle_id', 'left')
            ->where('ss.id', (int) $student_session_id)->get()->row_array() ?: null;
    }

    /** @return array|null hostel_name, hostel_type, room_no, room_type, cost_per_bed, no_of_bed */
    public function hostel($student_session_id)
    {
        $db = get_instance()->db;
        return $db->select('h.hostel_name, h.type AS hostel_type, hr.room_no, rt.room_type, hr.cost_per_bed, hr.no_of_bed', false)
            ->from('student_session ss')
            ->join('students st', 'st.id = ss.student_id')
            ->join('hostel_rooms hr', 'hr.id = st.hostel_room_id')
            ->join('hostel h', 'h.id = hr.hostel_id')
            ->join('room_types rt', 'rt.id = hr.room_type_id', 'left')
            ->where('ss.id', (int) $student_session_id)->get()->row_array() ?: null;
    }

    /** "2:00 pm" from a pickup time ("14:00:00"), or '' */
    public static function time($t)
    {
        return $t && strtotime($t) ? date('g:i a', strtotime($t)) : '';
    }
}
