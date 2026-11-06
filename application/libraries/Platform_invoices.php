<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Invoices (receipts) for paid subscriptions: platform_invoices.
 *
 *   issue($subscription_id)  one numbered invoice per paid subscription with an amount > 0
 *                            (INV-<year>-0001 ...); idempotent; emails the owner the receipt
 *   pdf($invoice)            the invoice as a PDF (string)
 *   url($invoice)            the owner's private download link (central host)
 */
class Platform_invoices
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        $this->CI->load->library(array('Platform_settings', 'Platform_mailer'));
    }

    /** @return array|null the invoice (existing or new); null when nothing is due on the subscription */
    public function issue($subscription_id, $send_receipt = true)
    {
        $db = $this->CI->db;
        $existing = $db->where('subscription_id', (int) $subscription_id)->get('platform_invoices')->row_array();
        if ($existing) {
            return $existing;
        }
        $sub = $db->select('subscription.*, plan.title AS plan_title')->from('subscription')
            ->join('plan', 'plan.plan_id = subscription.plan_id', 'left')
            ->where('subscription.id', (int) $subscription_id)->get()->row_array();
        if (!$sub || (int) $sub['payment_status'] !== 1 || (float) $sub['total'] <= 0) {
            return null;
        }
        $school = $sub['school_id'] ? $db->where('id', (int) $sub['school_id'])->get('school')->row_array() : null;
        $year = date('Y');
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $last = $db->select('invoice_no')->like('invoice_no', "INV-$year-", 'after')->order_by('id', 'desc')->limit(1)->get('platform_invoices')->row();
            $n = $last ? (int) substr($last->invoice_no, strlen("INV-$year-")) + 1 : 1;
            $row = array(
                'invoice_no' => sprintf('INV-%s-%04d', $year, $n + $attempt),
                'subscription_id' => (int) $sub['id'],
                'school_ref' => $sub['school_id'] ? (int) $sub['school_id'] : null,
                'bill_to_name' => mb_substr(trim(($school ? $school['school_name'] : $sub['school_name']) . ' (' . $sub['owner_name'] . ')'), 0, 255),
                'bill_to_email' => mb_substr((string) ($school ? $school['email_address'] : $sub['email_address']), 0, 255),
                'description' => mb_substr($this->line($sub), 0, 255),
                'price' => (float) $sub['price'],
                'discount' => (float) $sub['discount_amt'],
                'total' => (float) $sub['total'],
                'method' => (string) $sub['payment_method'],
                'reference' => $sub['reference'] ?: ($sub['coupon_code'] ? 'Coupon ' . $sub['coupon_code'] : null),
                'token' => bin2hex(random_bytes(20)),
                'issued_at' => date('Y-m-d H:i:s'),
            );
            $debug = $db->db_debug;
            $db->db_debug = false;
            $ok = $db->insert('platform_invoices', $row);
            $db->db_debug = $debug;
            if ($ok) {
                $invoice = $db->where('id', (int) $db->insert_id())->get('platform_invoices')->row_array();
                if ($send_receipt) {
                    $this->send_receipt($invoice, $sub);
                }
                return $invoice;
            }
            $again = $db->where('subscription_id', (int) $subscription_id)->get('platform_invoices')->row_array();
            if ($again) {
                return $again; // issued by a concurrent request
            }
        }
        return null;
    }

    private function line(array $sub)
    {
        $this->CI->load->library('Plan_pricing');
        $line = Plan_pricing::describe($sub, 'NGN ');
        return $line ?: (($sub['plan_title'] ?: 'Subscription') . ' - ' . (int) $sub['days'] . ' days');
    }

    public function send_receipt(array $invoice, array $sub = null)
    {
        $sub = $sub ?: $this->CI->db->where('id', (int) $invoice['subscription_id'])->get('subscription')->row_array();
        $plan = $this->CI->db->select('title')->where('plan_id', (int) $sub['plan_id'])->get('plan')->row();
        return $this->CI->platform_mailer->send('payment_receipt', $invoice['bill_to_email'], array(
            'owner_name' => $sub['owner_name'],
            'school_name' => $sub['school_name'],
            'plan' => $plan ? $plan->title : '',
            'amount' => $this->money($invoice['total']),
            'invoice_no' => $invoice['invoice_no'],
            'invoice_url' => $this->url($invoice),
        ), $invoice['school_ref']);
    }

    public function url(array $invoice)
    {
        $this->CI->load->library('School_provisioner');
        $platform = $this->CI->db->where('is_platform', 1)->get('school')->row_array();
        $base = $platform ? $this->CI->school_provisioner->url_for($platform) : site_url();
        return rtrim($base, '/') . '/subscription/invoice/' . $invoice['token'];
    }

    public function pdf(array $invoice)
    {
        $s = $this->CI->platform_settings;
        $html = $this->CI->load->view('admin/platform/invoice_pdf', array(
            'invoice' => $invoice,
            'seller' => array(
                'name' => $s->get('invoice_company') ?: $s->get('platform_name'),
                'address' => $s->get('invoice_address'),
                'tax_id' => $s->get('invoice_tax_id'),
                'email' => $s->get('support_email'),
                'phone' => $s->get('support_phone'),
                'footer' => $s->get('invoice_footer'),
            ),
            'money' => function ($v) { return $this->money($v); },
        ), true);
        $this->CI->load->library('m_pdf');
        $mpdf = $this->CI->m_pdf->load();
        $mpdf->SetTitle($invoice['invoice_no']);
        $mpdf->WriteHTML($html);
        return $mpdf->Output('', 'S');
    }

    public function money($v)
    {
        return 'NGN ' . number_format((float) $v, 2);
    }
}
