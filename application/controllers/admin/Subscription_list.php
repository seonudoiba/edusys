<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Subscriptions (platform admin): every sign-up and renewal with its payment, filterable,
 * with totals and a CSV export.
 *
 * Status of a subscription:
 *   pending   not paid (online payment not finished, or a bank transfer not approved yet)
 *   active    paid and not expired (created_at + days)
 *   expired   paid, past its days
 * Only an unpaid subscription without a school can be deleted (abandoned sign-ups).
 */
class Subscription_list extends Admin_Controller
{
    const PERMISSION = 'subscription_list';
    const EXPIRING_DAYS = 30;

    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();
        $this->load->model(array('plan_model'));
        $this->load->library('Form_token');
    }

    public function index()
    {
        $this->require_privilege('can_view');
        $this->session->set_userdata('top_menu', 'subscription');
        $this->session->set_userdata('sub_menu', 'admin/subscription_list');

        $f = $this->filters();
        $all = $this->rows();
        $rows = array_values(array_filter($all, function ($r) use ($f) { return $this->matches($r, $f); }));

        if ($this->input->get('export') === 'csv') {
            return $this->csv($rows);
        }

        $totals = array('count' => count($rows), 'paid' => 0, 'pending' => 0, 'revenue' => 0.0, 'outstanding' => 0.0);
        foreach ($rows as $r) {
            if ($r['paid']) {
                $totals['paid']++;
                $totals['revenue'] += (float) $r['total'];
            } else {
                $totals['pending']++;
                $totals['outstanding'] += (float) $r['total'];
            }
        }
        $counts = array('all' => count($all), 'active' => 0, 'expiring' => 0, 'expired' => 0, 'pending' => 0);
        foreach ($all as $r) {
            $counts[$r['state']]++;
            if ($r['expiring']) {
                $counts['expiring']++;
            }
        }
        $data = array(
            'rows' => $rows,
            'filters' => $f,
            'totals' => $totals,
            'counts' => $counts,
            'plans' => $this->plan_model->get(),
            'methods' => array_values(array_unique(array_map(function ($r) { return $r['method']; }, $all))),
            'currency_symbol' => $this->customlib->getSchoolCurrencyFormat(),
            'token' => $this->form_token->field(),
            'message' => $this->session->flashdata('sub_msg'),
            'error' => $this->session->flashdata('sub_error'),
        );
        $this->load->view('layout/header');
        $this->load->view('admin/subscription/subscriptions', $data);
        $this->load->view('layout/footer');
    }

    public function delete($id = null)
    {
        $this->require_privilege('can_delete');
        $this->form_token->require_post();
        $row = ctype_digit((string) $id) ? $this->db->where('id', (int) $id)->get('subscription')->row_array() : null;
        if (!$row) {
            show_404();
        }
        if ((int) $row['payment_status'] === 1 || !empty($row['school_id'])) {
            $this->session->set_flashdata('sub_error', 'Only an unpaid sign-up that never became a school can be deleted.');
        } else {
            $this->db->where('id', (int) $row['id'])->delete('subscription'); // its bank transfer row goes with it (foreign key)
            $this->session->set_flashdata('sub_msg', 'Deleted the unpaid sign-up of ' . $row['school_name'] . '.');
        }
        redirect('admin/subscription_list' . ($this->input->post('back') ? '?' . $this->input->post('back') : ''));
    }

    // ------------------------------------------------------------------ data

    private function rows()
    {
        $rows = $this->db->select('subscription.*, plan.title AS plan_title, school.school_name AS registered_name, school.cron_flag AS school_set_up, t.id AS transfer_id, t.status AS transfer_status')
            ->from('subscription')
            ->join('plan', 'plan.plan_id = subscription.plan_id', 'left')
            ->join('school', 'school.id = subscription.school_id', 'left')
            ->join('subscription_bank_transfers t', 't.subscription_id = subscription.id', 'left')
            ->order_by('subscription.id', 'desc')
            ->get()->result_array();
        $now = time();
        foreach ($rows as &$r) {
            $r['paid'] = (int) $r['payment_status'] === 1;
            $r['method'] = $r['payment_method'] !== '' ? $r['payment_method'] : 'free';
            $r['starts'] = $r['created_at'];
            $r['ends'] = date('Y-m-d H:i:s', strtotime($r['created_at']) + (int) $r['days'] * 86400);
            $r['days_left'] = (int) floor((strtotime($r['ends']) - $now) / 86400);
            $r['state'] = !$r['paid'] ? 'pending' : ($r['days_left'] < 0 ? 'expired' : 'active');
            if ($r['paid'] && $r['kind'] === 'setup') { // a setup fee alone: no period, nothing expires
                $r['state'] = 'active';
                $r['days_left'] = 0;
            }
            $r['expiring'] = $r['state'] === 'active' && $r['kind'] !== 'setup' && $r['days_left'] <= self::EXPIRING_DAYS;
            $r['name'] = $r['registered_name'] ?: $r['school_name'];
        }
        return $rows;
    }

    private function filters()
    {
        $g = function ($k) { return trim((string) $this->input->get($k)); };
        $date = function ($v) { return preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : ''; };
        $status = $g('status');
        return array(
            'status' => in_array($status, array('active', 'expiring', 'expired', 'pending'), true) ? $status : 'all',
            'method' => $g('method'),
            'plan' => ctype_digit($g('plan')) ? (int) $g('plan') : 0,
            'from' => $date($g('from')),
            'to' => $date($g('to')),
            'q' => $g('q'),
        );
    }

    private function matches(array $r, array $f)
    {
        if ($f['status'] === 'expiring' ? !$r['expiring'] : ($f['status'] !== 'all' && $r['state'] !== $f['status'])) {
            return false;
        }
        if ($f['method'] !== '' && $r['method'] !== $f['method']) {
            return false;
        }
        if ($f['plan'] && (int) $r['plan_id'] !== $f['plan']) {
            return false;
        }
        $day = substr($r['created_at'], 0, 10);
        if (($f['from'] && $day < $f['from']) || ($f['to'] && $day > $f['to'])) {
            return false;
        }
        if ($f['q'] !== '') {
            $hay = strtolower($r['name'] . ' ' . $r['owner_name'] . ' ' . $r['email_address'] . ' ' . $r['phone_number'] . ' ' . $r['reference']);
            if (strpos($hay, strtolower($f['q'])) === false) {
                return false;
            }
        }
        return true;
    }

    private function csv(array $rows)
    {
        $name = 'subscriptions-' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Cache-Control: no-store');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
        fputcsv($out, array('ID', 'Date', 'School', 'Owner', 'Email', 'Phone', 'Plan', 'Days', 'Method', 'Price', 'Discount', 'Total', 'Payment', 'Status', 'Starts', 'Ends', 'Reference'));
        $cell = function ($v) { $v = (string) $v; return preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v; }; // no formulas in Excel
        foreach ($rows as $r) {
            fputcsv($out, array_map($cell, array($r['id'], $r['created_at'], $r['name'], $r['owner_name'], $r['email_address'], $r['phone_number'],
                $r['plan_title'], $r['days'], $r['method'], $r['price'], $r['discount_amt'], $r['total'], $r['paid'] ? 'Paid' : 'Unpaid',
                ucfirst($r['state']), $r['starts'], $r['ends'], $r['reference'])));
        }
        fclose($out);
        exit;
    }

    private function require_privilege($action)
    {
        if (!$this->rbac->hasPrivilege(self::PERMISSION, $action)) {
            access_denied();
        }
    }
}
