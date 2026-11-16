<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Roles extends Admin_Controller
{
    private $perm_category = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->config('mailsms');
        $this->perm_category = $this->config->item('perm_category');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('superadmin', 'can_view')) {
            access_denied();
        }

        $data['title'] = 'Add Role';
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'admin/roles');

        $this->form_validation->set_rules(
            'name', $this->lang->line('name'), array(
                'required',
                array('check_exists', array($this->role_model, 'valid_check_exists')),
            )
        );
        if ($this->form_validation->run() == false) {
            $listroute         = $this->role_model->get();
            $data['listroute'] = $listroute;
            $this->load->view('layout/header');
            $this->load->view('admin/roles/create', $data);
            $this->load->view('layout/footer');
        } else {
            $data = array(
                'name' => $this->input->post('name'),
            );
            $this->role_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/roles');
        }
    }

    public function permission($id)
    {
        if (!$this->rbac->hasPrivilege('superadmin', 'can_view')) {
            access_denied();
        }
        $data['title'] = 'Add Role';
        $data['id']    = $id;
        $role          = $this->role_model->get($id);

        $data['role']    = $role;
        $role_permission = $this->role_model->find($role['id']);

        $data['role_permission'] = $role_permission;

        if ($this->input->server('REQUEST_METHOD') == "POST" && $this->input->post('perms_json') !== null) {
            // The page sends every permission as one JSON field: the form has more fields than PHP's
            // max_input_vars (1000), and fields past the limit were dropped, which wiped permissions.
            $this->save_permissions((int) $role['id'], json_decode((string) $this->input->post('perms_json', false), true));
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/roles/permission/' . $id);
        }
        if ($this->input->server('REQUEST_METHOD') == "POST") {

            $per_cat_post = $this->input->post('per_cat');
            $role_id      = $this->input->post('role_id');
            $to_be_insert = array();
            $to_be_update = array();
            $to_be_delete = array();
            foreach ($per_cat_post as $per_cat_post_key => $per_cat_post_value) {
                $insert_data = array();
                $ar          = array();
                foreach ($this->perm_category as $per_key => $per_value) {
                    $chk_val = $this->input->post($per_value . "-perm_" . $per_cat_post_value);

                    if (isset($chk_val)) {
                        $insert_data[$per_value] = 1;
                    } else {
                        $ar[$per_value] = 0;
                    }
                }

                $prev_id = $this->input->post('roles_permissions_id_' . $per_cat_post_value);
                if ($prev_id != 0) {

                    if (!empty($insert_data)) {
                        $insert_data['id'] = $prev_id;
                        $to_be_update[]    = array_merge($ar, $insert_data);
                    } else {
                        $to_be_delete[] = $prev_id;
                    }
                } elseif (!empty($insert_data)) {
                    $insert_data['role_id']     = $role_id;
                    $insert_data['perm_cat_id'] = $per_cat_post_value;
                    $to_be_insert[]             = array_merge($ar, $insert_data);
                }
            }

            $this->role_model->getInsertBatch($role_id, $to_be_insert, $to_be_update, $to_be_delete);
            redirect('admin/roles/permission/' . $id);
        }

        $this->load->view('layout/header');
        $this->load->view('admin/roles/allotmodule', $data);
        $this->load->view('layout/footer');
    }

    /**
     * $perms = [perm_cat_id => ['can_view' => 1|0, 'can_add' => ..., 'can_edit' => ..., 'can_delete' => ...]] for the permissions on
     * the page. Only those permissions change; existing rows are found in the database, not trusted
     * from the form.
     */
    private function save_permissions($role_id, $perms)
    {
        if (!is_array($perms) || !$role_id) {
            return;
        }
        $valid = array();
        foreach ($this->db->select('id')->get('permission_category')->result_array() as $r) {
            $valid[(int) $r['id']] = true;
        }
        $existing = array();
        foreach ($this->db->select('id, perm_cat_id')->where('role_id', $role_id)->get('roles_permissions')->result_array() as $r) {
            $existing[(int) $r['perm_cat_id']] = (int) $r['id'];
        }
        $insert = $update = $delete = array();
        foreach ($perms as $cat => $flags) {
            $cat = (int) $cat;
            if (!isset($valid[$cat]) || !is_array($flags)) {
                continue;
            }
            $row = array();
            foreach ($this->perm_category as $name) {
                $row[$name] = !empty($flags[$name]) ? 1 : 0;
            }
            $any = array_sum($row) > 0;
            if (isset($existing[$cat])) {
                if ($any) {
                    $update[] = $row + array('id' => $existing[$cat]);
                } else {
                    $delete[] = $existing[$cat];
                }
            } elseif ($any) {
                $insert[] = $row + array('role_id' => $role_id, 'perm_cat_id' => $cat);
            }
        }
        $this->role_model->getInsertBatch($role_id, $insert, $update, $delete);
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('superadmin', 'can_view')) {
            access_denied();
        }
        $data['title']    = 'Edit Role';
        $data['id']       = $id;
        $editrole         = $this->role_model->get($id);
        $data['editrole'] = $editrole;
        $data['name']     = $editrole["name"];

        $this->form_validation->set_rules(
            'name', $this->lang->line('name'), array(
                'required',
                array('check_exists', array($this->role_model, 'valid_check_exists')),
            )
        );
        if ($this->form_validation->run() == false) {
            $listroute         = $this->role_model->get();
            $data['listroute'] = $listroute;
            $this->load->view('layout/header');
            $this->load->view('admin/roles/edit', $data);
            $this->load->view('layout/footer');
        } else {
            $data = array(
                'id'   => $id,
                'name' => $this->input->post('name'),
            );
            $this->role_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/roles/index');
        }
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('superadmin', 'can_view')) {
            access_denied();
        }
        // Built-in roles (Super Admin, Teacher, ...) can't be deleted; the page only offers it for others.
        $role = $this->db->select('id, is_system')->where('id', (int) $id)->get('roles')->row_array();
        if (!$role || !empty($role['is_system'])) {
            access_denied();
        }
        $this->role_model->remove((int) $id);
        redirect('admin/roles/index');
    }

}
