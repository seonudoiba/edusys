<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Records what the platform team does (approvals, extensions, disabling, logging in as a school,
 * settings) in the application's audit trail (table logs, shown in Reports > Audit Trail Report).
 *
 * Rows belong to the school of the request (the platform school on the central host). Actions
 * on a school are written with action "platform:<what>" and record_id = that school's id, so a
 * school's page can show its own timeline. record_in_school() also writes the row into the
 * school's own audit trail (used when the platform opens a school's panel).
 */
class Platform_audit
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        $this->CI->load->library('user_agent');
    }

    public function record($what, $message, $school_id = null)
    {
        $row = $this->row($what, $message, $school_id);
        if (class_exists('Tenant', false) && Tenant::has()) {
            $this->CI->db->insert('logs', $row);
            return;
        }
        // No school for this request (CLI jobs): the entry belongs to the platform school.
        $this->CI->db->without_tenant(function ($db) use ($row) {
            $platform = $db->select('id')->where('is_platform', 1)->order_by('id')->limit(1)->get('school')->row();
            if ($platform) {
                $row['school_id'] = (int) $platform->id;
                $db->insert('logs', $row);
            }
        });
    }

    /** The same entry in the school's own audit trail (written across the tenant filter). */
    public function record_in_school($school_id, $what, $message)
    {
        $row = $this->row($what, $message, $school_id);
        $row['school_id'] = (int) $school_id;
        $row['user_id'] = 0;
        $this->CI->db->without_tenant(function ($db) use ($row) {
            $db->insert('logs', $row);
        });
    }

    /** Timeline of platform actions on one school, newest first. */
    public function for_school($school_id, $limit = 25)
    {
        return $this->CI->db->select('logs.message, logs.action, logs.time, staff.name, staff.surname')
            ->from('logs')
            ->join('staff', 'staff.id = logs.user_id', 'left')
            ->like('logs.action', 'platform:', 'after')
            ->where('logs.record_id', (string) (int) $school_id)
            ->order_by('logs.id', 'desc')
            ->limit((int) $limit)
            ->get()->result_array();
    }

    private function row($what, $message, $school_id)
    {
        $CI = $this->CI;
        $agent = $CI->agent->is_browser() ? $CI->agent->browser() . ' ' . $CI->agent->version()
            : ($CI->agent->is_robot() ? $CI->agent->robot() : ($CI->agent->is_mobile() ? $CI->agent->mobile() : 'Unidentified'));
        $staff_id = 0;
        $admin = $CI->session->userdata('admin');
        if (is_array($admin) && isset($admin['id'])) {
            $staff_id = (int) $admin['id'];
        }
        return array(
            'message' => mb_substr((string) $message, 0, 2000),
            'record_id' => $school_id !== null ? (string) (int) $school_id : '',
            'user_id' => $staff_id,
            'action' => mb_substr('platform:' . $what, 0, 50),
            'ip_address' => $CI->input->ip_address(),
            'platform' => mb_substr((string) $CI->agent->platform(), 0, 50),
            'agent' => mb_substr($agent, 0, 50),
            'time' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d'),
        );
    }
}
