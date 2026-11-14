<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Setting up a new school: the checklist (index) and the classes step (classes).
 * See libraries/School_onboarding.php.
 */
class Onboarding extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (Tenant::is_platform()) {
            show_404(); // the platform school has no classes, fees or admissions to set up
        }
        $this->load->library('School_onboarding');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('general_setting', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'admin/onboarding');
        $data['title'] = 'Set up your school';
        $data['steps'] = $this->school_onboarding->steps();
        $data['progress'] = $this->school_onboarding->progress($data['steps']);
        $data['state'] = $this->school_onboarding->state();
        $data['can_edit'] = $this->rbac->hasPrivilege('general_setting', 'can_edit');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/onboarding/index', $data);
        $this->load->view('layout/footer', $data);
    }

    /** POST step, how (done, skipped, todo). */
    public function mark()
    {
        $this->require_edit();
        $this->school_onboarding->mark((string) $this->input->post('step'), (string) $this->input->post('how'));
        redirect('admin/onboarding');
    }

    /** POST hidden (1 hides the dashboard card, 0 shows it again). */
    public function hide()
    {
        $this->require_edit();
        $hidden = (string) $this->input->post('hidden') === '1';
        $this->school_onboarding->set_hidden($hidden);
        redirect($hidden ? 'dashboard' : 'admin/onboarding');
    }

    public function classes()
    {
        if (!$this->rbac->hasPrivilege('class', 'can_add')) {
            access_denied();
        }
        if ($this->input->method() === 'post') {
            return $this->save_classes();
        }
        $this->session->set_userdata('top_menu', 'Academics');
        $this->session->set_userdata('sub_menu', 'classes/index');
        $data['title'] = 'Set up classes and arms';
        $data['stages'] = School_onboarding::nigerian_classes();
        $data['departments'] = School_onboarding::SS_DEPARTMENTS;
        $data['samples'] = $this->rbac->hasPrivilege('class', 'can_delete') ? $this->school_onboarding->removable_samples() : array();
        $data['existing'] = $this->classsection_model->getByID();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/onboarding/classes', $data);
        $this->load->view('layout/footer', $data);
    }

    private function save_classes()
    {
        $picked = (array) $this->input->post('classes');
        $classes = array();
        $ss = array();
        foreach ($picked as $row) {
            if (!is_array($row) || empty($row['on'])) {
                continue;
            }
            $name = isset($row['name']) ? (string) $row['name'] : '';
            $classes[] = $name;
            if (isset($row['stage']) && $row['stage'] === 'ss') {
                $ss[] = $name;
            }
        }
        $arms = preg_split('/[,\n]/', (string) $this->input->post('arms'));
        $remove = $this->input->post('remove_samples') && $this->rbac->hasPrivilege('class', 'can_delete');
        if (!$classes && !$remove) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Tick at least one class.</div>');
            redirect('admin/onboarding/classes');
        }
        try {
            $r = $this->school_onboarding->create_classes($classes, $arms, (bool) $this->input->post('ss_departments'), $ss, $remove);
        } catch (RuntimeException $e) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">' . html_escape($e->getMessage()) . '</div>');
            redirect('admin/onboarding/classes');
        }
        $parts = array();
        foreach (array('created' => '%d class%s created', 'updated' => '%d existing class%s given new arms', 'removed' => '%d sample class%s removed') as $key => $text) {
            if ($r[$key]) {
                $parts[] = sprintf($text, $r[$key], $r[$key] === 1 ? '' : 'es');
            }
        }
        $msg = $parts ? implode(', ', $parts) . '.' : 'Those classes and arms were already set up.';
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . html_escape($msg) . ' Next, add subjects to each class under Academics.</div>');
        redirect('admin/onboarding');
    }

    private function require_edit()
    {
        if ($this->input->method() !== 'post' || !$this->rbac->hasPrivilege('general_setting', 'can_edit')) {
            access_denied();
        }
    }
}
