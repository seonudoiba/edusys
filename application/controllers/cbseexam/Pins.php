<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Report Cards > Result checker PINs: generate batches of scratch-card PINs, see which are used and
 * by whom, print them as cards, export them (CSV), disable a PIN or a batch. Library: Result_pins.
 * Parents use them on the school website: Exam result (welcome/cbseexam).
 */
class Pins extends MY_Addon_CBSEController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('result_pins');
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    private function guard()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_setting', 'can_view') && !$this->rbac->hasPrivilege('cbse_exam', 'can_edit')) {
            access_denied();
        }
    }

    public function index()
    {
        $this->guard();
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbseexam/pins');
        $data['batches'] = $this->result_pins->batches();
        $data['batch'] = (string) $this->input->get('batch');
        if ($data['batch'] === '' && $data['batches']) {
            $data['batch'] = $data['batches'][0]['batch'];
        }
        $data['pins'] = $data['batch'] !== '' ? $this->result_pins->pins($data['batch']) : array();
        $data['sessions'] = $this->db->select('id, session')->order_by('id', 'desc')->get('sessions')->result_array();
        $data['current_session'] = $this->current_session;
        $data['checker_url'] = site_url('cbseexam');
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/pins/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function generate()
    {
        $this->guard();
        if ($this->input->method() !== 'post') {
            redirect('cbseexam/pins');
        }
        $count = (int) $this->input->post('count');
        if ($count < 1 || $count > 2000) {
            $this->session->set_flashdata('pin_msg', array('danger', 'Make between 1 and 2000 PINs at a time.'));
            redirect('cbseexam/pins');
        }
        $batch = $this->result_pins->generate($count, (int) $this->input->post('max_uses') ?: 5, (int) $this->input->post('session_id'), (int) $this->customlib->getStaffID() ?: null);
        $this->session->set_flashdata('pin_msg', array('success', $count . ' PINs made (batch ' . $batch . '). Print them as cards or export them.'));
        redirect('cbseexam/pins?batch=' . rawurlencode($batch));
    }

    public function toggle()
    {
        $this->guard();
        $active = (int) $this->input->post('active') === 1;
        if ($this->input->post('batch') !== null && $this->input->post('batch') !== '' && !$this->input->post('id')) {
            $this->result_pins->set_batch_active($this->input->post('batch'), $active);
        } else {
            $this->result_pins->set_active(array($this->input->post('id')), $active);
        }
        redirect('cbseexam/pins?batch=' . rawurlencode((string) $this->input->post('batch')));
    }

    public function export($batch = '')
    {
        $this->guard();
        $batch = rawurldecode($batch);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="result-pins-' . preg_replace('/[^A-Za-z0-9-]/', '', $batch) . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, array('Serial no', 'PIN', 'Uses allowed', 'Used', 'Student', 'Admission no', 'Active'));
        foreach ($this->result_pins->pins($batch) as $p) {
            fputcsv($out, array($p['serial_no'], "'" . $p['pin'], $p['max_uses'], $p['used_count'], $p['student_name'], $p['admission_no'], $p['is_active'] ? 'Yes' : 'No'));
        }
        fclose($out);
        exit;
    }

    /** Printable cards: school name and logo, serial, PIN, how to check. */
    public function cards($batch = '')
    {
        $this->guard();
        $data['pins'] = $this->result_pins->pins(rawurldecode($batch));
        $data['brand'] = $this->customlib->brand();
        $data['checker_url'] = site_url('cbseexam');
        $this->load->view('cbseexam/pins/cards', $data);
    }
}
