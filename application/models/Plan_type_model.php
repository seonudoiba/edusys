<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class plan_type_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null)
    {
        $this->db->select()->from('plan_list');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('created_at', 'desc');
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

        $this->db->insert('plan_list', $data);
        $id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On  enquiry id " . $id;
        $action = "Insert";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {

        }
    }
    public function plan_type_update($id, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->update('plan_list', $data);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    public function plan_type_delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('plan_list');
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    public function get_plan_type($ids = null)
    {
        if ($ids !== null) {
            $ids = json_decode((string) $ids, true);
            if (is_array($ids) && !$ids) {
                return array(); // a plan without features (where_in() with no values is invalid SQL)
            }
        }
        $query = $this->db->select('*')->from('plan_list')->where('status', 1);
        if (is_array($ids)) {
            $this->db->where_in('id', array_map('intval', $ids));
        } elseif ($ids !== null && $ids !== '') {
            $this->db->where('id', (int) $ids);
        }
        $query = $query->order_by('sort_order', 'asc')->order_by('id', 'asc')->get();
        
        return $query->result_array();
    }
}