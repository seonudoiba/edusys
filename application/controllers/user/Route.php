<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Route extends Student_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pickuppoint_model');
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'Transport');
        $this->session->set_userdata('sub_menu', 'route/index');
        $student_id                  = $this->customlib->getStudentSessionUserID();
        $studentList                 = $this->student_model->get($student_id);
        $studentList['pickup_point'] = $this->pickuppoint_model->getPickupPointByRouteID($studentList['route_id']);
        $data['listroute']           = $studentList;
        $this->load->library('Student_places');
        $data['my_transport']        = $this->student_places->transport($this->customlib->getStudentCurrentClsSection()->student_session_id);
        $this->load->view('layout/student/header');
        $this->load->view('user/route/index', $data);
        $this->load->view('layout/student/footer');
    }

    public function getbusdetail()
    {
        $vehrouteid = $this->input->post('vehrouteid');
        $result     = $this->vehroute_model->getVechileDetailByVecRouteID($vehrouteid);
        echo json_encode($result);
    }
}
