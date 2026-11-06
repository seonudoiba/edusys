<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Billing (platform admin): invoices, coupons, revenue and growth reports.
 */
class Billing extends Admin_Controller
{
    const PERMISSION = 'subscription_list';

    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();
        $this->load->library(array('Form_token', 'Platform_invoices', 'Platform_audit', 'Platform_coupons'));
        $this->load->model('plan_model');
    }

    // ------------------------------------------------------------------ pricing bands

    public function bands()
    {
        $this->require_privilege('can_view');
        $this->session->set_userdata('top_menu', 'subscription');
        $this->session->set_userdata('sub_menu', 'admin/billing/bands');
        $this->load->library('Plan_pricing');
        $this->load->view('layout/header');
        $this->load->view('admin/billing/bands', array('bands' => $this->plan_pricing->bands(), 'token' => $this->form_token->field(),
            'message' => $this->session->flashdata('billing_msg'), 'error' => $this->session->flashdata('billing_error')));
        $this->load->view('layout/footer');
    }

    /** New prices apply to new payments; what schools have already paid doesn't change. */
    public function save_bands()
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $this->load->library('Plan_pricing');
        $changes = array();
        foreach ($this->plan_pricing->bands() as $b) {
            $id = (int) $b['id'];
            $setup = (float) $this->input->post('setup_' . $id);
            $rate = $b['rate_per_term'] === null ? null : (float) $this->input->post('rate_' . $id);
            if ($setup < 0 || ($rate !== null && $rate <= 0)) {
                $this->session->set_flashdata('billing_error', 'Rates must be above zero and setup fees can\'t be negative. Nothing was saved.');
                redirect('admin/billing/bands');
            }
            if ((float) $b['setup_fee'] !== $setup || ($rate !== null && (float) $b['rate_per_term'] !== $rate)) {
                $changes[$id] = array('rate_per_term' => $rate, 'setup_fee' => $setup);
            }
        }
        foreach ($changes as $id => $row) {
            $this->db->where('id', $id)->update('platform_plan_bands', $row);
        }
        if ($changes) {
            $this->platform_audit->record('billing.bands', 'Changed the pricing bands');
        }
        $this->session->set_flashdata('billing_msg', $changes ? 'Prices saved. They apply to new payments.' : 'Nothing changed.');
        redirect('admin/billing/bands');
    }

    // ------------------------------------------------------------------ invoices

    public function invoices()
    {
        $this->require_privilege('can_view');
        $this->menu('admin/billing/invoices');
        $q = trim((string) $this->input->get('q'));
        $this->db->from('platform_invoices');
        if ($q !== '') {
            $this->db->group_start()->like('invoice_no', $q)->or_like('bill_to_name', $q)->or_like('bill_to_email', $q)->group_end();
        }
        $rows = $this->db->order_by('id', 'desc')->limit(500)->get()->result_array();
        if ($this->input->get('export') === 'csv') {
            return $this->csv('invoices', array('Invoice', 'Issued', 'Billed to', 'Email', 'Description', 'Price', 'Discount', 'Total', 'Method', 'Reference'),
                array_map(function ($r) { return array($r['invoice_no'], $r['issued_at'], $r['bill_to_name'], $r['bill_to_email'], $r['description'],
                    $r['price'], $r['discount'], $r['total'], $r['method'], $r['reference']); }, $rows));
        }
        $data = array('rows' => $rows, 'q' => $q, 'token' => $this->form_token->field(),
            'total' => array_sum(array_column($rows, 'total')),
            'message' => $this->session->flashdata('billing_msg'), 'error' => $this->session->flashdata('billing_error'));
        $this->load->view('layout/header');
        $this->load->view('admin/billing/invoices', $data);
        $this->load->view('layout/footer');
    }

    public function invoice($id = null)
    {
        $this->require_privilege('can_view');
        $invoice = $this->invoice_or_404($id);
        $pdf = $this->platform_invoices->pdf($invoice);
        header('Content-Type: application/pdf');
        header('Content-Disposition: ' . ($this->input->get('download') ? 'attachment' : 'inline') . '; filename="' . $invoice['invoice_no'] . '.pdf"');
        header('Cache-Control: private, no-store');
        echo $pdf;
        exit;
    }

    public function resend_invoice($id = null)
    {
        $this->require_privilege('can_view');
        $this->form_token->require_post();
        $invoice = $this->invoice_or_404($id);
        $ok = $this->platform_invoices->send_receipt($invoice);
        $this->platform_audit->record('invoice.resend', 'Sent invoice ' . $invoice['invoice_no'] . ' again', $invoice['school_ref']);
        $this->session->set_flashdata($ok ? 'billing_msg' : 'billing_error', $ok ? 'Invoice ' . $invoice['invoice_no'] . ' sent to ' . $invoice['bill_to_email'] . '.' : 'The email could not be sent: see the email log.');
        redirect('admin/billing/invoices');
    }

    /** Issues the invoices missing for paid subscriptions (e.g. paid before invoices existed). */
    public function issue_missing()
    {
        $this->require_privilege('can_view');
        $this->form_token->require_post();
        $ids = array_column($this->db->select('subscription.id')->from('subscription')
            ->join('platform_invoices', 'platform_invoices.subscription_id = subscription.id', 'left')
            ->where(array('subscription.payment_status' => 1, 'subscription.total >' => 0))
            ->where('platform_invoices.id IS NULL', null, false)->get()->result_array(), 'id');
        $n = 0;
        foreach ($ids as $sid) {
            if ($this->platform_invoices->issue((int) $sid, false)) {
                $n++;
            }
        }
        $this->platform_audit->record('invoice.issue_missing', "Issued $n missing invoices");
        $this->session->set_flashdata('billing_msg', $n ? "Issued $n invoices (no emails sent)." : 'Every paid subscription already has an invoice.');
        redirect('admin/billing/invoices');
    }

    // ------------------------------------------------------------------ coupons

    public function coupons()
    {
        $this->require_privilege('can_view');
        $this->menu('admin/billing/coupons');
        $data = array(
            'coupons' => $this->db->order_by('id', 'desc')->get('platform_coupons')->result_array(),
            'plans' => array_column($this->plan_model->get(), 'title', 'plan_id'),
            'token' => $this->form_token->field(),
            'message' => $this->session->flashdata('billing_msg'), 'error' => $this->session->flashdata('billing_error'),
        );
        $this->load->view('layout/header');
        $this->load->view('admin/billing/coupons', $data);
        $this->load->view('layout/footer');
    }

    public function coupon($id = null)
    {
        $this->require_privilege('can_edit');
        $this->menu('admin/billing/coupons');
        $coupon = ctype_digit((string) $id) ? $this->db->where('id', (int) $id)->get('platform_coupons')->row_array() : null;
        if ($id !== null && !$coupon) {
            show_404();
        }
        $data = array('coupon' => $coupon, 'plans' => $this->plan_model->get(), 'token' => $this->form_token->field(),
            'error' => $this->session->flashdata('billing_error'));
        $this->load->view('layout/header');
        $this->load->view('admin/billing/coupon_form', $data);
        $this->load->view('layout/footer');
    }

    public function save_coupon($id = null)
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $id = ctype_digit((string) $id) ? (int) $id : null;
        $in = function ($k) { return trim((string) $this->input->post($k)); };
        $code = Platform_coupons::normalize($in('code'));
        $type = $in('discount_type') === 'fixed' ? 'fixed' : 'percent';
        $value = (float) $in('value');
        $errors = array();
        if (!preg_match('/^[A-Z0-9_-]{3,40}$/', $code)) {
            $errors[] = 'The code is 3 to 40 letters, digits, - or _.';
        } elseif ($this->db->where('code', $code)->where('id <>', (int) $id)->count_all_results('platform_coupons') > 0) {
            $errors[] = 'Another coupon already has this code.';
        }
        if ($value <= 0 || ($type === 'percent' && $value > 100)) {
            $errors[] = $type === 'percent' ? 'The percentage is between 1 and 100.' : 'The amount must be more than 0.';
        }
        foreach (array('starts_on', 'ends_on') as $d) {
            if ($in($d) !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $in($d))) {
                $errors[] = 'Dates are YYYY-MM-DD.';
            }
        }
        if ($in('starts_on') !== '' && $in('ends_on') !== '' && $in('ends_on') < $in('starts_on')) {
            $errors[] = 'The end date is before the start date.';
        }
        if ($errors) {
            $this->session->set_flashdata('billing_error', implode(' ', $errors));
            redirect('admin/billing/coupon' . ($id ? '/' . $id : ''));
        }
        $plan_ids = array_values(array_filter(array_map('intval', (array) $this->input->post('plan_ids'))));
        $row = array(
            'code' => $code,
            'description' => mb_substr($in('description'), 0, 255),
            'discount_type' => $type,
            'value' => $value,
            'plan_ids' => $plan_ids && !$this->input->post('all_plans') ? json_encode($plan_ids) : null,
            'max_uses' => max(0, (int) $in('max_uses')),
            'starts_on' => $in('starts_on') ?: null,
            'ends_on' => $in('ends_on') ?: null,
            'is_active' => $this->input->post('is_active') ? 1 : 0,
        );
        if ($id) {
            $this->db->where('id', $id)->update('platform_coupons', $row);
        } else {
            $this->db->insert('platform_coupons', $row);
        }
        $this->platform_audit->record($id ? 'coupon.edit' : 'coupon.create', ($id ? 'Edited' : 'Created') . ' the coupon ' . $code);
        $this->session->set_flashdata('billing_msg', 'Saved the coupon ' . $code . '.');
        redirect('admin/billing/coupons');
    }

    public function toggle_coupon($id = null)
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $c = $this->coupon_or_404($id);
        $on = !(int) $c['is_active'];
        $this->db->where('id', (int) $c['id'])->update('platform_coupons', array('is_active' => $on ? 1 : 0));
        $this->platform_audit->record('coupon.toggle', ($on ? 'Switched on' : 'Switched off') . ' the coupon ' . $c['code']);
        $this->session->set_flashdata('billing_msg', $c['code'] . ($on ? ' can be used again.' : ' is switched off.'));
        redirect('admin/billing/coupons');
    }

    public function delete_coupon($id = null)
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $c = $this->coupon_or_404($id);
        if ((int) $c['used_count'] > 0 || $this->db->where('coupon_code', $c['code'])->count_all_results('subscription') > 0) {
            $this->session->set_flashdata('billing_error', $c['code'] . ' has been used, so it is kept for the records. Switch it off instead.');
        } else {
            $this->db->where('id', (int) $c['id'])->delete('platform_coupons');
            $this->platform_audit->record('coupon.delete', 'Deleted the coupon ' . $c['code']);
            $this->session->set_flashdata('billing_msg', 'Deleted ' . $c['code'] . '.');
        }
        redirect('admin/billing/coupons');
    }

    // ------------------------------------------------------------------ reports

    public function reports()
    {
        $this->require_privilege('can_view');
        $this->menu('admin/billing/reports');
        $months = in_array((int) $this->input->get('months'), array(3, 6, 12, 24), true) ? (int) $this->input->get('months') : 12;
        $keys = array();
        for ($i = $months - 1; $i >= 0; $i--) {
            $keys[] = date('Y-m', strtotime("first day of -$i month"));
        }
        $from = $keys[0] . '-01';
        $blank = array('revenue' => 0.0, 'paid' => 0, 'renewals' => 0, 'registrations' => 0, 'setups' => 0, 'expiries' => 0);
        $monthly = array_fill_keys($keys, $blank);

        $subs = $this->db->select('subscription.*, plan.title AS plan_title')->from('subscription')
            ->join('plan', 'plan.plan_id = subscription.plan_id', 'left')->order_by('subscription.id')->get()->result_array();
        $by_plan = array();
        $by_method = array();
        $seen_school = array();
        $top = array();
        foreach ($subs as $s) {
            $ym = substr($s['created_at'], 0, 7);
            $paid = (int) $s['payment_status'] === 1;
            if ($paid) {
                $renewal = $s['school_id'] && isset($seen_school[$s['school_id']]);
                if ($s['school_id']) {
                    $seen_school[$s['school_id']] = true;
                }
                if (isset($monthly[$ym])) {
                    $monthly[$ym]['revenue'] += (float) $s['total'];
                    $monthly[$ym]['paid']++;
                    $monthly[$ym]['renewals'] += $renewal ? 1 : 0;
                    $p = $s['plan_title'] ?: 'Plan #' . $s['plan_id'];
                    $m = $s['payment_method'] ?: 'free';
                    $by_plan[$p] = array('count' => ($by_plan[$p]['count'] ?? 0) + 1, 'revenue' => ($by_plan[$p]['revenue'] ?? 0) + (float) $s['total']);
                    $by_method[$m] = array('count' => ($by_method[$m]['count'] ?? 0) + 1, 'revenue' => ($by_method[$m]['revenue'] ?? 0) + (float) $s['total']);
                    if ($s['school_id']) {
                        $top[$s['school_id']] = ($top[$s['school_id']] ?? 0) + (float) $s['total'];
                    }
                }
                $ends = date('Y-m', strtotime($s['created_at']) + (int) $s['days'] * 86400);
                if (isset($monthly[$ends])) {
                    $monthly[$ends]['expiries']++;
                }
            }
        }
        foreach ($this->db->select("DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS n", false)->where(array('is_platform' => 0, 'created_at >=' => $from))->group_by('ym')->get('school')->result_array() as $r) {
            if (isset($monthly[$r['ym']])) {
                $monthly[$r['ym']]['registrations'] = (int) $r['n'];
            }
        }
        foreach ($this->db->select("DATE_FORMAT(time, '%Y-%m') AS ym, COUNT(*) AS n", false)->where(array('action' => 'platform:school.setup', 'time >=' => $from))->group_by('ym')->get('logs')->result_array() as $r) {
            if (isset($monthly[$r['ym']])) {
                $monthly[$r['ym']]['setups'] = (int) $r['n'];
            }
        }
        // Churn: set-up schools whose latest paid subscription has ended and that haven't renewed.
        $this->load->model('Platform_school_model');
        $churn = array_values(array_filter($this->Platform_school_model->all(), function ($s) { return $s['set_up'] && $s['expiry'] === 'expired'; }));
        arsort($top);
        $top = array_slice($top, 0, 10, true);
        $names = $top ? array_column($this->db->select('id, school_name')->where_in('id', array_keys($top))->get('school')->result_array(), 'school_name', 'id') : array();

        $export = $this->input->get('export');
        if ($export === 'monthly') {
            return $this->csv('monthly', array('Month', 'Revenue', 'Paid subscriptions', 'Renewals', 'Registrations', 'Set up', 'Expiries'),
                array_map(function ($k, $m) { return array($k, $m['revenue'], $m['paid'], $m['renewals'], $m['registrations'], $m['setups'], $m['expiries']); }, array_keys($monthly), $monthly));
        }
        if ($export === 'churn') {
            return $this->csv('churn', array('School', 'Email', 'Plan', 'Ended', 'Days ago'),
                array_map(function ($s) { return array($s['school_name'], $s['email_address'], $s['plan_title'], $s['expires_at'], abs((int) $s['days_left'])); }, $churn));
        }
        $data = array('months' => $months, 'monthly' => $monthly, 'by_plan' => $by_plan, 'by_method' => $by_method,
            'churn' => $churn, 'top' => $top, 'names' => $names, 'currency_symbol' => $this->customlib->getSchoolCurrencyFormat());
        $this->load->view('layout/header');
        $this->load->view('admin/billing/reports', $data);
        $this->load->view('layout/footer');
    }

    // ------------------------------------------------------------------ helpers

    private function csv($name, array $head, array $rows)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $name . '-' . date('Y-m-d') . '.csv"');
        header('Cache-Control: no-store');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $head);
        foreach ($rows as $r) {
            fputcsv($out, array_map(function ($v) { $v = (string) $v; return preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v; }, $r));
        }
        fclose($out);
        exit;
    }

    private function invoice_or_404($id)
    {
        $i = ctype_digit((string) $id) ? $this->db->where('id', (int) $id)->get('platform_invoices')->row_array() : null;
        if (!$i) {
            show_404();
        }
        return $i;
    }

    private function coupon_or_404($id)
    {
        $c = ctype_digit((string) $id) ? $this->db->where('id', (int) $id)->get('platform_coupons')->row_array() : null;
        if (!$c) {
            show_404();
        }
        return $c;
    }

    private function menu($sub)
    {
        $this->session->set_userdata('top_menu', 'subscription');
        $this->session->set_userdata('sub_menu', $sub);
    }

    private function require_privilege($action)
    {
        if (!$this->rbac->hasPrivilege(self::PERMISSION, $action)) {
            access_denied();
        }
    }
}
