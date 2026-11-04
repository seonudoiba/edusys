<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
#[AllowDynamicProperties]
class Subscription_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function add($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->insert('subscription', $data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }
    public function get($id = null)
    {
        $this->db->select('*')->from('subscription');

        if ($id != null) {
            $this->db->where('subscription.id', $id);
        } else {
            // $this->db->order_by('students.id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function removebySchool($schoolid = 0) {
        $this->db->reset_query();
        $this->db->where('school_id', $schoolid);
        $this->db->delete('subscription');
    }
    //add new
    public function payment()
    {
        $payment = $this->db->select_sum('subscription.total')->where('subscription.payment_status', 1)->get('subscription')->row()->total;
        return $payment;
    }
}