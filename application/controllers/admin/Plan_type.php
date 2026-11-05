<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Plan features (platform admin): the lines listed on each plan's card on /plans (table
 * plan_list). Hidden features disappear from the sign-up page; a feature used by a plan can't
 * be deleted (hide it, or remove it from the plans first).
 */
class Plan_type extends Admin_Controller
{
    const PERMISSION = 'plan_list';

    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();
        $this->load->model(array('plan_type_model', 'plan_model'));
        $this->load->library('Form_token');
    }

    public function index()
    {
        $this->require_privilege('can_view');
        $this->session->set_userdata('top_menu', 'plan_list');
        $this->session->set_userdata('sub_menu', 'admin/plan_type');
        $features = $this->plan_type_model->get();
        usort($features, function ($a, $b) { return (int) $a['sort_order'] <=> (int) $b['sort_order'] ?: (int) $a['id'] <=> (int) $b['id']; });
        $usage = $this->usage();
        foreach ($features as &$f) {
            $f['plans'] = isset($usage[(int) $f['id']]) ? $usage[(int) $f['id']] : array();
        }
        unset($f);
        $next = $features ? max(array_map(function ($f) { return (int) $f['sort_order']; }, $features)) + 1 : 1;
        $data = array(
            'features' => $features,
            'next_order' => $next,
            'token' => $this->form_token->field(),
            'message' => $this->session->flashdata('feature_msg'),
            'error' => $this->session->flashdata('feature_error'),
        );
        $this->load->view('layout/header');
        $this->load->view('admin/plan/features', $data);
        $this->load->view('layout/footer');
    }

    /** Adds a feature, or renames / reorders one ($id). */
    public function save($id = null)
    {
        $id = ctype_digit((string) $id) ? (int) $id : null;
        $this->require_privilege($id ? 'can_edit' : 'can_add');
        $this->form_token->require_post();
        if ($id) {
            $this->feature_or_404($id);
        }
        $name = trim((string) $this->input->post('name'));
        $order = $this->input->post('sort_order');
        if ($name === '' || mb_strlen($name) > 255) {
            $this->back(null, 'Enter the feature (up to 255 characters).');
        }
        if (!preg_match('/^\d{1,5}$/', (string) $order)) {
            $this->back(null, 'The order is a whole number (lower comes first).');
        }
        $row = array('name' => $name, 'sort_order' => (int) $order);
        if ($id) {
            $this->plan_type_model->plan_type_update($id, $row);
            $this->back('Saved "' . $name . '".');
        }
        $row['status'] = 1;
        $this->plan_type_model->add($row);
        $this->back('Added "' . $name . '". Tick it on the plans that include it.');
    }

    public function toggle($id = null)
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $f = $this->feature_or_404($id);
        $show = (int) $f['status'] !== 1;
        $this->plan_type_model->plan_type_update($f['id'], array('status' => $show ? 1 : 0));
        $this->back('"' . $f['name'] . '" is now ' . ($show ? 'shown on' : 'hidden from') . ' the sign-up page.');
    }

    public function delete($id = null)
    {
        $this->require_privilege('can_delete');
        $this->form_token->require_post();
        $f = $this->feature_or_404($id);
        $usage = $this->usage();
        if (!empty($usage[(int) $f['id']])) {
            $this->back(null, '"' . $f['name'] . '" is listed on ' . implode(', ', $usage[(int) $f['id']]) . '. Remove it from those plans or hide it.');
        }
        $this->plan_type_model->plan_type_delete($f['id']);
        $this->back('Deleted "' . $f['name'] . '".');
    }

    /** feature id => names of the plans listing it */
    private function usage()
    {
        $out = array();
        foreach ($this->plan_model->get() as $p) {
            $ids = json_decode((string) $p['list'], true);
            foreach (is_array($ids) ? $ids : array() as $fid) {
                $out[(int) $fid][] = $p['title'];
            }
        }
        return $out;
    }

    private function feature_or_404($id)
    {
        $f = ctype_digit((string) $id) && (int) $id > 0 ? $this->plan_type_model->get((int) $id) : null;
        if (empty($f) || !isset($f['id'])) {
            show_404();
        }
        return $f;
    }

    private function back($message = null, $error = null)
    {
        if ($message !== null) {
            $this->session->set_flashdata('feature_msg', $message);
        }
        if ($error !== null) {
            $this->session->set_flashdata('feature_error', $error);
        }
        redirect('admin/plan_type');
    }

    private function require_privilege($action)
    {
        if (!$this->rbac->hasPrivilege(self::PERMISSION, $action)) {
            access_denied();
        }
    }
}
