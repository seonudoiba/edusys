<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The hostel fee: a student given a room is charged the room's cost per bed, once per session, as an
 * ordinary fee (so it shows on the student's and parents' Fees page, in Pay all dues, receipts,
 * reminders and reports). Used by Hostel > Assign Students and Edit Student.
 *
 *   fee item       feetype "Hostel fee" (made once per school)
 *   one per room   fee_groups "Hostel · <hostel> · room <no>" (tagged by its description) with a
 *                  fee_session_groups row and the room's price for the current session
 *   who pays       student_fees_master (the student's session)
 *
 * Moving to another room or leaving the hostel takes the unpaid hostel fee off; a hostel fee with
 * payments is never removed (move() answers 'paid' and changes nothing).
 */
class Hostel_fees
{
    const TAG = 'Hostel fee (charged with the room)';

    /** @var CI_Controller */
    private $CI;
    private $session_id;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model(array('setting_model', 'feesessiongroup_model', 'studentfeemaster_model'));
        $this->session_id = (int) $this->CI->setting_model->getCurrentSession();
    }

    /**
     * The student's hostel fee follows them from $old_room to $new_room (0 = no room).
     * @return string 'paid' (their hostel fee has payments: nothing changed), 'charged', 'removed' or 'none'
     */
    public function move($student_session_id, $old_room, $new_room, $charge = true)
    {
        $db = $this->CI->db;
        $current = $this->charges((int) $student_session_id);
        if ($current && $db->where_in('student_fees_master_id', array_column($current, 'id'))->count_all_results('student_fees_deposite') > 0) {
            return 'paid';
        }
        $new_fsg = ($charge && (int) $new_room) ? $this->room_group((int) $new_room) : 0;
        if ($current && count($current) === 1 && (int) $current[0]['fee_session_group_id'] === $new_fsg) {
            return 'none'; // already charged for this room
        }
        if ($current) {
            $db->where_in('id', array_column($current, 'id'))->delete('student_fees_master');
        }
        if ($new_fsg) {
            $this->CI->studentfeemaster_model->add(array('student_session_id' => (int) $student_session_id, 'fee_session_group_id' => $new_fsg, 'is_system' => 0));
            return 'charged';
        }
        return $current ? 'removed' : 'none';
    }

    /** The student's hostel fee charges this session: student_fees_master rows (id, fee_session_group_id). */
    private function charges($student_session_id)
    {
        return $this->CI->db->select('sfm.id, sfm.fee_session_group_id')->from('student_fees_master sfm')
            ->join('fee_session_groups fsg', 'fsg.id = sfm.fee_session_group_id')
            ->join('fee_groups g', 'g.id = fsg.fee_groups_id')
            ->where(array('sfm.student_session_id' => $student_session_id, 'fsg.session_id' => $this->session_id, 'g.description' => self::TAG))
            ->get()->result_array();
    }

    /** The room's fee for this session (fee_session_groups id), made the first time; 0 when the room costs nothing. */
    private function room_group($room_id)
    {
        $db = $this->CI->db;
        $room = $db->select('hr.room_no, hr.cost_per_bed, h.hostel_name')->from('hostel_rooms hr')->join('hostel h', 'h.id = hr.hostel_id')
            ->where('hr.id', $room_id)->get()->row_array();
        if (!$room || (float) $room['cost_per_bed'] <= 0) {
            return 0;
        }
        $amount = round((float) $room['cost_per_bed'], 2);
        $name = 'Hostel · ' . trim($room['hostel_name']) . ' · room ' . trim($room['room_no']);
        // this room at this price, this session
        $found = $db->select('fsg.id')->from('fee_groups g')
            ->join('fee_session_groups fsg', 'fsg.fee_groups_id = g.id')
            ->join('fee_groups_feetype gft', 'gft.fee_session_group_id = fsg.id')
            ->where_in('g.name', array($name, $name . ' (' . $amount . ')'))
            ->where(array('g.description' => self::TAG, 'fsg.session_id' => $this->session_id, 'gft.amount' => $amount))
            ->get()->row_array();
        if ($found) {
            return (int) $found['id'];
        }
        $type = $db->select('id')->where('type', 'Hostel fee')->get('feetype')->row_array();
        if ($type) {
            $type_id = (int) $type['id'];
        } else {
            $db->insert('feetype', array('type' => 'Hostel fee', 'code' => 'HOSTEL', 'is_system' => 0, 'feecategory_id' => null, 'description' => '', 'is_active' => 'no'));
            $type_id = (int) $db->insert_id();
        }
        $db->insert('fee_groups', array('name' => $name . (count($db->select('id')->where('name', $name)->get('fee_groups')->result_array()) ? ' (' . $amount . ')' : ''),
            'is_system' => 0, 'description' => self::TAG, 'is_active' => 'no'));
        $group_id = (int) $db->insert_id();
        $this->CI->feesessiongroup_model->add(array(
            'fee_groups_id' => $group_id, 'feetype_id' => $type_id, 'amount' => $amount, 'due_date' => null,
            'session_id' => $this->session_id, 'fine_type' => 'none', 'fine_percentage' => 0, 'fine_amount' => 0,
        ));
        $fsg = $db->select('id')->where(array('fee_groups_id' => $group_id, 'session_id' => $this->session_id))->get('fee_session_groups')->row_array();
        return $fsg ? (int) $fsg['id'] : 0;
    }
}
