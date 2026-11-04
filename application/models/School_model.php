<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class School_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function get($id = null)
    {
        // $this->db->select('*')->from('school');
        $this->db->select('subscription.*,school.*')
        ->from('school')
        ->join('subscription', 'subscription.school_id = school.id', 'left')
        ->order_by('subscription.created_at', 'desc'); 

        if ($id != null) {
            $this->db->where('school.id', $id);
        } else {
            $this->db->order_by('school.created_at', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function delete($id)
    {
        // Check if ID is valid (non-empty)
        if (empty($id)) {
            return false; // No ID provided
        }
        $this->db->reset_query();

        $this->db->where('id', $id);
        $result = $this->db->delete('school');
        
        // Return the result of the delete operation
        return $result; // TRUE if successful, FALSE if failed
    }

    //add new

    public function schoolCount()
    {
        $query = $this->db->select('*')->from('school')->get();
        return $query->num_rows();
    }

    public function activeSchool()
    {
        $query = $this->db->select_sum('school.cron_flag')->where('school.cron_flag', 1)->get('school')->row()->cron_flag;
        return $query;
    }

    public function unactiveSchool()
    {
        $query = $this->db->select_sum('school.cron_flag')->where('school.cron_flag', 0)->get('school')->row()->cron_flag;
        return $query;
    }

    // Approval state of a school application (cron_flag: 0 = pending, 2 = approved, 1 = set up).
    public function update_status($id, $status)
    {
        $this->db->where('id', $id);
        return $this->db->update('school', ['cron_flag' => $status]);
    }

    public function is_email_taken($email)
    {
        $this->db->where('email_address', $email);
        $query = $this->db->get('school');

        return $query->num_rows() > 0;
    }
}
