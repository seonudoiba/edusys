<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Report Cards > Promotion: rules (minimum annual average, core subjects to pass, attendance),
 * each student's annual figures with a suggestion (promote / borderline / repeat) and the recorded
 * decision with its reason. Library: Cbse_promotion.
 */
class Promotion extends MY_Addon_CBSEController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('cbse_promotion');
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    private function guard($edit = false)
    {
        if (!$this->rbac->hasPrivilege('cbse_exam', $edit ? 'can_edit' : 'can_view')) {
            access_denied();
        }
    }

    public function index()
    {
        $this->guard();
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbseexam/promotion');
        $data['class_sections'] = $this->db->select('cs.id, c.id AS class_id, c.class, s.section')->from('class_sections cs')
            ->join('classes c', 'c.id = cs.class_id')->join('sections s', 's.id = cs.section_id')->order_by('c.class')->order_by('s.section')->get()->result_array();
        $data['cs'] = (int) $this->input->get('cs');
        $data['session_id'] = (int) ($this->input->get('session_id') ?: $this->current_session);
        $data['sessions'] = $this->db->select('id, session')->order_by('id', 'desc')->get('sessions')->result_array();
        $data['subjects'] = $this->db->select('id, name')->order_by('name')->get('subjects')->result_array();
        $data['can_edit'] = $this->rbac->hasPrivilege('cbse_exam', 'can_edit');
        $data['result'] = null;
        $data['class_id'] = 0;
        foreach ($data['class_sections'] as $c) {
            if ((int) $c['id'] === $data['cs']) {
                $data['class_id'] = (int) $c['class_id'];
                $data['class_label'] = $c['class'] . ' (' . $c['section'] . ')';
            }
        }
        if ($data['class_id']) {
            $data['result'] = $this->cbse_promotion->evaluate($data['cs'], $data['session_id']);
        }
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/promotion/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function rules()
    {
        $this->guard(true);
        $class_id = $this->input->post('scope') === 'class' ? (int) $this->input->post('class_id') : 0;
        $this->cbse_promotion->save_rule($class_id, array(
            'min_average' => $this->input->post('min_average'), 'core_subject_ids' => (array) $this->input->post('core_subject_ids'),
            'core_pass_mark' => $this->input->post('core_pass_mark'), 'min_attendance' => $this->input->post('min_attendance'),
            'borderline_margin' => $this->input->post('borderline_margin'),
        ));
        $this->session->set_flashdata('pr_msg', 'Rules saved' . ($class_id ? ' for this class.' : ' for every class without its own rules.'));
        redirect('cbseexam/promotion?' . http_build_query(array('cs' => (int) $this->input->post('cs'), 'session_id' => (int) $this->input->post('session_id'))));
    }

    public function decide()
    {
        $this->guard(true);
        $averages = array();
        foreach ((array) $this->input->post('average') as $ss => $v) {
            $averages[(int) $ss] = $v === '' ? null : (float) $v;
        }
        $n = $this->cbse_promotion->decide((array) $this->input->post('d'), (int) $this->customlib->getStaffID() ?: null, $averages);
        $this->session->set_flashdata('pr_msg', $n ? $n . ' decision(s) recorded.' : 'No change.');
        redirect('cbseexam/promotion?' . http_build_query(array('cs' => (int) $this->input->post('cs'), 'session_id' => (int) $this->input->post('session_id'))));
    }
}
