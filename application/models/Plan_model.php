<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Plan_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function get($id = null, $check_status = false)
    {
        $this->db->select('*')->from('plan');

        if ($id != null) {
            $this->db->where('plan.plan_id', $id);
        }

        if ($check_status) {
            $this->db->where('plan.status', 1);
        }

        if ($id == null) {
            $this->db->order_by('plan.created_at', 'desc');
        }

        $query = $this->db->get();
        
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function add($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->insert('plan', $data);
        $insert_id = (int) $this->db->insert_id(); // read before the commit

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return $insert_id;
    }

    public function delete($id)
    {
        $this->db->where('plan_id', $id);
        $this->db->delete('plan');

        return $this->db->affected_rows() > 0;
    }

    public function get_plan()
    {
        $this->db->select('plan.plan_id, plan.title, plan.text, plan.status, plan.list, plan.created_at');
        $this->db->from('plan');
        $query = $this->db->get();


        foreach ($query->result_array() as $result) {
            $result_list = json_decode($result, true);
        }
    }

    public function plan_update($id, $data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('plan_id', $id);
        $this->db->update('plan', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }
}
