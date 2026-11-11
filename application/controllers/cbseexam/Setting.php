<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Setting extends MY_Addon_CBSEController {

    function __construct() {
        parent::__construct();
        $this->load->library('Cbse_nigeria');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_setting', 'can_view')) {
            access_denied();
        }
        $data['version'] = $this->config->item('version');
        $data['status'] = $this->cbse_nigeria->status();
        $data['can_install'] = $this->can_install();
        $menu = $this->db->select('sidebar_display')->where('lang_key', 'examinations')->get('sidebar_menus')->row();
        $data['examinations_menu_shown'] = $menu ? (int) $menu->sidebar_display === 1 : false;
        $data['terms'] = Cbse_nigeria::TERMS;
        $data['schemes'] = Cbse_nigeria::SCHEMES;
        $data['grades'] = Cbse_nigeria::GRADES;
        $data['ratings'] = Cbse_nigeria::RATINGS;
        $this->load->view('layout/header');
        $this->load->view('cbseexam/setting', $data);
        $this->load->view('layout/footer');
    }

    /** POST: adds the Nigerian terms, CA + exam schemes, grade scales and ratings that are missing. */
    public function nigeria()
    {
        if ($this->input->method() !== 'post' || !$this->can_install()) {
            access_denied();
        }
        try {
            $done = $this->cbse_nigeria->install(false);
        } catch (RuntimeException $e) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">' . html_escape($e->getMessage()) . '</div>');
            redirect('cbseexam/setting');
        }
        $added = array();
        foreach (array('terms' => 'term', 'schemes' => 'assessment scheme', 'grades' => 'grade scale', 'ratings' => 'rating group') as $k => $label) {
            if ($done[$k]) {
                $added[] = $done[$k] . ' ' . $label . ($done[$k] === 1 ? '' : 's');
            }
        }
        if ($this->input->post('hide_examinations') && $this->rbac->hasPrivilege('general_setting', 'can_edit')) {
            $this->db->where('lang_key', 'examinations')->update('sidebar_menus', array('sidebar_display' => 0));
            $added[] = 'the Examinations menu hidden';
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . ($added ? 'Done: ' . html_escape(implode(', ', $added)) . '.' : 'Everything was already set up.') . '</div>');
        redirect('cbseexam/setting');
    }

    private function can_install()
    {
        foreach (array('cbse_exam_term', 'cbse_exam_assessment', 'cbse_exam_grade', 'cbse_exam_observation') as $p) {
            if (!$this->rbac->hasPrivilege($p, 'can_add')) {
                return false;
            }
        }
        return true;
    }
}
