<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Subscription plans (platform admin): the plans schools choose from on /plans.
 *
 * price  = normal price; oprice = offer price when set (> 0): what the school pays.
 * status = 1 shown on /plans, 0 hidden (existing subscriptions keep working).
 * list   = JSON array of feature ids (plan_list, managed in admin/plan_type).
 *
 * A plan that schools subscribed to can't be deleted (its history would lose its name): hide it.
 */
class Plan extends Admin_Controller
{
    const PERMISSION = 'plan_list';

    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();
        $this->load->model(array('plan_model', 'plan_type_model'));
        $this->load->library(array('Form_token', 'Plan_limits', 'Platform_audit'));
    }

    public function index()
    {
        $this->require_privilege('can_view');
        $this->menu();
        $plans = $this->plan_model->get();
        usort($plans, function ($a, $b) {
            return $this->effective_price($a) <=> $this->effective_price($b) ?: $a['plan_id'] <=> $b['plan_id'];
        });
        $features = $this->features_by_id();
        $stats = $this->stats();
        foreach ($plans as &$p) {
            $p['effective_price'] = $this->effective_price($p);
            $p['feature_names'] = array();
            foreach ($this->feature_ids($p) as $fid) {
                if (isset($features[$fid])) {
                    $p['feature_names'][] = $features[$fid];
                }
            }
            $p['stats'] = isset($stats[$p['plan_id']]) ? $stats[$p['plan_id']] : array('active' => 0, 'paid' => 0, 'pending' => 0, 'revenue' => 0.0);
        }
        unset($p);
        $this->load->library('Plan_pricing');
        $data = array(
            'default_period' => $this->plan_pricing->default_period(),
            'plans' => $plans,
            'currency_symbol' => $this->customlib->getSchoolCurrencyFormat(),
            'token' => $this->form_token->field(),
            'message' => $this->session->flashdata('plan_msg'),
            'error' => $this->session->flashdata('plan_error'),
        );
        $this->load->view('layout/header');
        $this->load->view('admin/plan/plans', $data);
        $this->load->view('layout/footer');
    }

    public function create()
    {
        $this->require_privilege('can_add');
        $this->show_form(null);
    }

    public function edit($id = null)
    {
        $this->require_privilege('can_edit');
        $this->show_form($this->plan_or_404($id));
    }

    public function save($id = null)
    {
        $id = ctype_digit((string) $id) ? (int) $id : null;
        $this->require_privilege($id ? 'can_edit' : 'can_add');
        $this->form_token->require_post();
        $plan = $id ? $this->plan_or_404($id) : null;

        $this->form_validation->set_rules('title', 'Plan name', 'trim|required|max_length[255]|xss_clean');
        $this->form_validation->set_rules('text', 'Short description', 'trim|required|max_length[255]|xss_clean');
        $this->form_validation->set_rules('price', 'Price', 'trim|required|is_natural');
        $this->form_validation->set_rules('oprice', 'Offer price', 'trim|is_natural|callback_check_offer');
        $this->form_validation->set_rules('days', 'Duration', 'trim|required|is_natural_no_zero|less_than_equal_to[3650]');
        foreach (array('max_students' => 'Maximum students', 'max_staff' => 'Maximum staff', 'max_storage_mb' => 'Storage') as $field => $label) {
            $this->form_validation->set_rules($field, $label, 'trim|is_natural|less_than_equal_to[10000000]');
        }
        if (!$this->form_validation->run()) {
            return $this->show_form($plan);
        }
        $known = $this->features_by_id();
        $features = array_values(array_filter(array_map('intval', (array) $this->input->post('features')), function ($f) use ($known) {
            return isset($known[$f]);
        }));
        $row = array(
            'title' => $this->input->post('title'),
            'text' => $this->input->post('text'),
            'price' => (int) $this->input->post('price'),
            'oprice' => (int) $this->input->post('oprice'),
            'days' => (int) $this->input->post('days'),
            'list' => json_encode(array_map('strval', $features)),
            'status' => $this->input->post('status') ? 1 : 0,
            'modules' => $this->posted_modules(),
            'max_students' => (int) $this->input->post('max_students'),
            'max_staff' => (int) $this->input->post('max_staff'),
            'max_storage_mb' => (int) $this->input->post('max_storage_mb'),
        );
        if ($id) {
            $this->plan_model->plan_update($id, $row);
            $changed = $this->apply_to_schools($id);
            $this->platform_audit->record('plan.edit', 'Edited the plan "' . $row['title'] . '"');
            $this->session->set_flashdata('plan_msg', 'Saved "' . $row['title'] . '". Price and days apply to new sign-ups; modules and limits apply to its schools now'
                . ($changed ? ' (' . $changed . ' module' . ($changed == 1 ? '' : 's') . ' switched off in schools on this plan).' : '.'));
        } else {
            $this->plan_model->add($row);
            $this->session->set_flashdata('plan_msg', 'Added "' . $row['title'] . '"' . ($row['status'] ? ': it is now on the sign-up page.' : ' (hidden from the sign-up page).'));
        }
        redirect('admin/plan');
    }

    /** Show on / hide from the sign-up page. */
    public function toggle($id = null)
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $plan = $this->plan_or_404($id);
        $show = (int) $plan['status'] !== 1;
        $this->plan_model->plan_update($plan['plan_id'], array('status' => $show ? 1 : 0));
        $this->session->set_flashdata('plan_msg', '"' . $plan['title'] . '" is now ' . ($show ? 'shown on' : 'hidden from') . ' the sign-up page.');
        redirect('admin/plan');
    }

    /** The payment period selected first on the public pricing and registration pages (POST period). */
    public function default_period()
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $this->load->library(array('Platform_settings', 'Plan_pricing'));
        $period = (string) $this->input->post('period');
        if (!isset(Plan_pricing::PERIODS[$period])) {
            $this->session->set_flashdata('plan_error', 'Choose "Each term" or "For the year".');
            redirect('admin/plan');
        }
        $this->platform_settings->set('default_billing_period', $period);
        $this->session->set_flashdata('plan_msg', 'The public pages now select "' . ($period === 'term' ? 'Each term' : 'For the year') . '" first.');
        redirect('admin/plan');
    }

    /** A hidden copy to edit (e.g. a new price without touching the plan schools signed up for). */
    public function duplicate($id = null)
    {
        $this->require_privilege('can_add');
        $this->form_token->require_post();
        $plan = $this->plan_or_404($id);
        $copy = array(
            'title' => mb_substr($plan['title'] . ' (copy)', 0, 255),
            'text' => $plan['text'],
            'price' => (int) $plan['price'],
            'oprice' => (int) $plan['oprice'],
            'days' => (int) $plan['days'],
            'list' => $plan['list'] ?: '[]',
            'status' => 0,
            'modules' => $plan['modules'],
            'max_students' => (int) $plan['max_students'],
            'max_staff' => (int) $plan['max_staff'],
            'max_storage_mb' => (int) $plan['max_storage_mb'],
        );
        $new_id = $this->plan_model->add($copy);
        $this->session->set_flashdata('plan_msg', 'Copied as "' . $copy['title'] . '" (hidden). Edit it, then show it.');
        redirect($new_id ? 'admin/plan/edit/' . (int) $new_id : 'admin/plan');
    }

    public function delete($id = null)
    {
        $this->require_privilege('can_delete');
        $this->form_token->require_post();
        $plan = $this->plan_or_404($id);
        $used = $this->db->where('plan_id', (int) $plan['plan_id'])->count_all_results('subscription');
        if ($used > 0) {
            $this->session->set_flashdata('plan_error', '"' . $plan['title'] . '" has ' . $used . ' subscription' . ($used == 1 ? '' : 's') . ' and can\'t be deleted. Hide it instead.');
            redirect('admin/plan');
        }
        $this->plan_model->delete($plan['plan_id']);
        $this->session->set_flashdata('plan_msg', 'Deleted "' . $plan['title'] . '".');
        redirect('admin/plan');
    }

    /** Form validation: an offer price is lower than the price (0 or empty = no offer). */
    public function check_offer($oprice)
    {
        if ($oprice === '' || $oprice === null || (int) $oprice === 0) {
            return true;
        }
        if ((int) $oprice >= (int) $this->input->post('price')) {
            $this->form_validation->set_message('check_offer', 'The offer price must be lower than the price (leave it empty for no offer).');
            return false;
        }
        return true;
    }

    // ------------------------------------------------------------------ helpers

    private function show_form($plan)
    {
        $this->menu();
        $features = $this->plan_type_model->get();
        usort($features, function ($a, $b) { return (int) $a['sort_order'] <=> (int) $b['sort_order'] ?: (int) $a['id'] <=> (int) $b['id']; });
        $modules = json_decode((string) ($plan ? $plan['modules'] : ''), true);
        $data = array(
            'plan' => $plan,
            'all_modules' => $this->plan_limits->all_modules(),
            'plan_modules' => is_array($modules) ? $modules : null,
            'selected' => $plan ? $this->feature_ids($plan) : array(),
            'features' => $features,
            'currency_symbol' => $this->customlib->getSchoolCurrencyFormat(),
            'token' => $this->form_token->field(),
        );
        $this->load->view('layout/header');
        $this->load->view('admin/plan/plan_form', $data);
        $this->load->view('layout/footer');
    }

    /** JSON of the modules ticked, or null for "every module". */
    private function posted_modules()
    {
        if ($this->input->post('all_modules')) {
            return null;
        }
        $known = array_column($this->plan_limits->all_modules(), 'short_code');
        return json_encode(array_values(array_intersect((array) $this->input->post('modules'), $known)));
    }

    /** Applies the plan's modules to the schools whose current plan it is. Returns modules switched off. */
    private function apply_to_schools($plan_id)
    {
        $changed = 0;
        foreach ($this->db->select('id')->where(array('is_platform' => 0, 'cron_flag' => 1))->get('school')->result() as $school) {
            $current = $this->plan_limits->current($school->id);
            if ($current['plan'] && (int) $current['plan']['plan_id'] === (int) $plan_id) {
                $changed += $this->plan_limits->apply_modules($school->id, false);
            }
        }
        return $changed;
    }

    /** Per plan: active subscriptions (paid, not expired), paid and pending counts, revenue. */
    private function stats()
    {
        $out = array();
        $rows = $this->db->select('plan_id, payment_status, total, days, created_at')->get('subscription')->result_array();
        foreach ($rows as $r) {
            $id = $r['plan_id'];
            if (!isset($out[$id])) {
                $out[$id] = array('active' => 0, 'paid' => 0, 'pending' => 0, 'revenue' => 0.0);
            }
            if ((int) $r['payment_status'] === 1) {
                $out[$id]['paid']++;
                $out[$id]['revenue'] += (float) $r['total'];
                if (strtotime($r['created_at']) + (int) $r['days'] * 86400 >= time()) {
                    $out[$id]['active']++;
                }
            } else {
                $out[$id]['pending']++;
            }
        }
        return $out;
    }

    private function features_by_id()
    {
        $out = array();
        foreach ($this->plan_type_model->get() as $f) {
            $out[(int) $f['id']] = $f['name'];
        }
        return $out;
    }

    private function feature_ids(array $plan)
    {
        $ids = json_decode((string) $plan['list'], true);
        return is_array($ids) ? array_map('intval', $ids) : array();
    }

    private function effective_price(array $plan)
    {
        return (int) $plan['oprice'] > 0 ? (int) $plan['oprice'] : (int) $plan['price'];
    }

    private function plan_or_404($id)
    {
        $plan = ctype_digit((string) $id) && (int) $id > 0 ? $this->plan_model->get((int) $id) : null;
        if (empty($plan) || !isset($plan['plan_id'])) {
            show_404();
        }
        return $plan;
    }

    private function menu()
    {
        $this->session->set_userdata('top_menu', 'plan_list');
        $this->session->set_userdata('sub_menu', 'admin/plan');
    }

    private function require_privilege($action)
    {
        if (!$this->rbac->hasPrivilege(self::PERMISSION, $action)) {
            access_denied();
        }
    }
}
