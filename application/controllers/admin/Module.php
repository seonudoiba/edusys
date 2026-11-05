<?php

class Module extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("module_model");
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('superadmin', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'System Settings/module');
        $permissionlist = $this->module_model->getPermission();
        $data["permissionList"] = $permissionlist;
        $studentpermissionList = $this->module_model->getStudentPermission();
        $data["studentpermissionList"] = $studentpermissionList;
        $parentpermissionList = $this->module_model->getParentPermission();
        $data["parentpermissionList"] = $parentpermissionList;
        $this->load->view("layout/header");
        $this->load->view("setting/permission", $data);
        $this->load->view("layout/footer");
    }

    public function changeStatus()
    {

        $id = $this->input->post("id");
        $status = $this->input->post("status");

        if (!empty($id)) {
            // A module the school's plan doesn't include can't be switched on.
            if ((int) $status === 1 && !Tenant::is_platform()) {
                $this->load->library('Plan_limits');
                $group = $this->db->where('id', (int) $id)->get('permission_group')->row();
                if ($group && !$this->plan_limits->module_allowed(Tenant::id(), $group->short_code)) {
                    echo json_encode(array('status' => 0, 'msg' => $group->name . ' is not included in your plan. Upgrade the plan to use it.'));
                    return;
                }
            }
            $data = array('id' => $id, 'is_active' => $status);
            $result = $this->module_model->changeStatus($data);
            $response = array('status' => 1, 'msg' => $this->lang->line("status_change_successfully"));
            echo json_encode($response);
        }
    }

    public function changeParentStatus()
    {
        $id = $this->input->post("id");
        $status = $this->input->post("status");

        if (!empty($id)) {
            $data = array('id' => $id, 'is_active' => $status);
            $result = $this->module_model->changeParentStatus($data);

            $response = array('status' => 1, 'msg' => $this->lang->line("status_change_successfully"));
            echo json_encode($response);
        }
    }

    public function changeStudentStatus()
    {
        $id = $this->input->post("id");
        $status = $this->input->post("status");
        $role = $this->input->post('role');
        if (!empty($id)) {
            $data = array('id' => $id, $role => $status);
            $result = $this->module_model->changeStudentStatus($data);
            $response = array('status' => 1, 'msg' => $this->lang->line("status_change_successfully"));
            echo json_encode($response);
        }
    }

}
