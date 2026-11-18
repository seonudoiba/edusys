<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Invoices of subscriptions: platform_invoices.
 *
 *   issue_unpaid($subscription_id)  an invoice before payment (registration by bank transfer, or a
 *                                   school billed by the platform): status unpaid, due date
 *   issue($subscription_id)         the invoice of a paid subscription: an unpaid one becomes
 *                                   paid, or a new paid one is issued; emails the receipt
 *   draft(array $sub)               an unsaved invoice (previews: registration form, billing form)
 *   page($invoice), pdf($invoice)   the invoice as a web page (print, download, pay) or a PDF
 *   send_invoice($invoice)          emails the owner the invoice (template invoice_issued)
 *   url($invoice)                   the owner's private link (central host)
 *
 * Numbers: INV-<year>-0001 ..., one invoice per subscription.
 */
class Platform_invoices
{
    const DUE_DAYS = 7;
    const STATUSES = array('unpaid' => 'Unpaid', 'paid' => 'Paid', 'void' => 'Cancelled');
    const METHODS = array('paystack' => 'Paystack (card / transfer online)', 'flutterwave' => 'Flutterwave', 'monnify' => 'Monnify',
        'interswitch' => 'Interswitch', 'remita' => 'Remita', 'bank_transfer' => 'Bank transfer', 'cash' => 'Cash', 'cheque' => 'Cheque',
        'manual' => 'Recorded by the platform', 'invoice' => 'Invoice', '' => '-');

    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        $this->CI->load->library(array('Platform_settings', 'Platform_mailer'));
    }

    // ------------------------------------------------------------------ issuing

    /** @return array|null the invoice (existing or new); null when nothing is due on the subscription */
    public function issue($subscription_id, $send_receipt = true)
    {
        $db = $this->CI->db;
        $sub = $this->subscription($subscription_id);
        $existing = $db->where('subscription_id', (int) $subscription_id)->get('platform_invoices')->row_array();
        if ($existing) {
            if ($existing['status'] !== 'paid' && $sub && (int) $sub['payment_status'] === 1) {
                // Paid: the invoice becomes the receipt.
                $db->where('id', (int) $existing['id'])->update('platform_invoices', array(
                    'status' => 'paid',
                    'paid_at' => date('Y-m-d H:i:s'),
                    'method' => (string) $sub['payment_method'],
                    'reference' => $sub['reference'] ?: $existing['reference'],
                ));
                $existing = $db->where('id', (int) $existing['id'])->get('platform_invoices')->row_array();
                if ($send_receipt) {
                    $this->send_receipt($existing, $sub);
                }
            }
            return $existing;
        }
        if (!$sub || (int) $sub['payment_status'] !== 1 || (float) $sub['total'] <= 0) {
            return null;
        }
        $invoice = $this->create($sub, array('status' => 'paid', 'paid_at' => date('Y-m-d H:i:s')));
        if ($invoice && $send_receipt) {
            $this->send_receipt($invoice, $sub);
        }
        return $invoice;
    }

    /**
     * An invoice for a subscription not paid yet (or its existing invoice).
     * @param string|null $due_date Y-m-d; empty: in DUE_DAYS days
     */
    public function issue_unpaid($subscription_id, $due_date = null, $notes = null)
    {
        $db = $this->CI->db;
        $existing = $db->where('subscription_id', (int) $subscription_id)->get('platform_invoices')->row_array();
        if ($existing) {
            return $existing;
        }
        $sub = $this->subscription($subscription_id);
        if (!$sub || (int) $sub['payment_status'] === 1 || (float) $sub['total'] <= 0) {
            return null;
        }
        return $this->create($sub, array(
            'status' => 'unpaid',
            'due_date' => $due_date ?: date('Y-m-d', strtotime('+' . self::DUE_DAYS . ' days')),
            'notes' => $notes !== null && trim((string) $notes) !== '' ? trim((string) $notes) : null,
            'method' => '',
            'reference' => null,
        ));
    }

    /** Cancels an unpaid invoice (its subscription stays unpaid). */
    public function void(array $invoice)
    {
        if ($invoice['status'] !== 'unpaid') {
            return false;
        }
        $this->CI->db->where('id', (int) $invoice['id'])->update('platform_invoices', array('status' => 'void'));
        return true;
    }

    /** An unsaved invoice for a subscription row that isn't saved (previews). */
    public function draft(array $sub, $due_date = null, $notes = null)
    {
        $row = $this->row($sub, array('status' => 'draft', 'due_date' => $due_date ?: date('Y-m-d', strtotime('+' . self::DUE_DAYS . ' days')),
            'notes' => $notes, 'method' => '', 'reference' => null));
        $row['id'] = 0;
        $row['invoice_no'] = 'DRAFT';
        $row['token'] = '';
        return $row;
    }

    private function subscription($id)
    {
        return $this->CI->db->select('subscription.*, plan.title AS plan_title')->from('subscription')
            ->join('plan', 'plan.plan_id = subscription.plan_id', 'left')
            ->where('subscription.id', (int) $id)->get()->row_array();
    }

    private function create(array $sub, array $extra)
    {
        $db = $this->CI->db;
        $year = date('Y');
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $last = $db->select('invoice_no')->like('invoice_no', "INV-$year-", 'after')->order_by('id', 'desc')->limit(1)->get('platform_invoices')->row();
            $n = $last ? (int) substr($last->invoice_no, strlen("INV-$year-")) + 1 : 1;
            $row = $this->row($sub, $extra);
            $row['invoice_no'] = sprintf('INV-%s-%04d', $year, $n + $attempt);
            $row['token'] = bin2hex(random_bytes(20));
            $debug = $db->db_debug;
            $db->db_debug = false;
            $ok = $db->insert('platform_invoices', $row);
            $db->db_debug = $debug;
            if ($ok) {
                return $db->where('id', (int) $db->insert_id())->get('platform_invoices')->row_array();
            }
            $again = $db->where('subscription_id', (int) $sub['id'])->get('platform_invoices')->row_array();
            if ($again) {
                return $again; // issued by a concurrent request
            }
        }
        return null;
    }

    /** The invoice's columns from a subscription (saved or not) and the school. */
    private function row(array $sub, array $extra)
    {
        $school = !empty($sub['school_id']) ? $this->CI->db->where('id', (int) $sub['school_id'])->get('school')->row_array() : null;
        $name = $school ? $school['school_name'] : (string) $sub['school_name'];
        $owner = trim((string) ($school && !empty($school['owner_name']) ? $school['owner_name'] : $sub['owner_name']));
        $lines = $this->lines($sub);
        return array_merge(array(
            'subscription_id' => isset($sub['id']) ? (int) $sub['id'] : 0,
            'school_ref' => !empty($sub['school_id']) ? (int) $sub['school_id'] : null,
            'bill_to_name' => mb_substr($name . ($owner !== '' ? ' (' . $owner . ')' : ''), 0, 255),
            'bill_to_email' => mb_substr((string) ($school && !empty($school['email_address']) ? $school['email_address'] : $sub['email_address']), 0, 255),
            'bill_to_phone' => mb_substr((string) ($school && !empty($school['phone_number']) ? $school['phone_number'] : $sub['phone_number']), 0, 60) ?: null,
            'bill_to_address' => $school && !empty($school['school_address']) ? mb_substr((string) $school['school_address'], 0, 500) : null,
            'description' => mb_substr($this->line($sub), 0, 255),
            'lines' => json_encode($lines),
            'price' => (float) $sub['price'],
            'discount' => (float) $sub['discount_amt'],
            'total' => (float) $sub['total'],
            'method' => (string) $sub['payment_method'],
            'reference' => !empty($sub['reference']) ? $sub['reference'] : (!empty($sub['coupon_code']) ? 'Coupon ' . $sub['coupon_code'] : null),
            'issued_at' => date('Y-m-d H:i:s'),
        ), $extra);
    }

    private function line(array $sub)
    {
        $this->CI->load->library('Plan_pricing');
        $line = Plan_pricing::describe($sub, 'NGN ');
        return $line ?: ((!empty($sub['plan_title']) ? $sub['plan_title'] : 'Subscription') . ' - ' . (int) $sub['days'] . ' days');
    }

    /**
     * Itemised lines: [description, detail, quantity, unit price, amount].
     * Per-student subscriptions: the subscription (students x rate x terms) and the setup fee apart.
     */
    public function lines(array $sub)
    {
        $price = (float) $sub['price'];
        $kind = isset($sub['kind']) ? (string) $sub['kind'] : '';
        $students = (int) $sub['student_count'];
        $rate = (float) $sub['rate_per_term'];
        $terms = (int) $sub['terms'];
        $setup = (float) $sub['setup_fee'];
        $period = isset($sub['billing_period']) && $sub['billing_period'] === 'year' ? 'Yearly' : ($terms > 1 ? $terms . ' terms' : 'Termly');
        $setup_line = array('Implementation, onboarding and training', 'One-time setup fee', 1, $setup, $setup);
        if ($kind === 'setup') {
            $setup_line[1] = 'One-time setup fee for ' . number_format($students) . ' students; the subscription is billed separately';
            return array(array($setup_line[0], $setup_line[1], 1, $price, $price));
        }
        if ($students > 0 && $rate > 0 && $terms > 0) {
            $per_student = round($rate * $terms, 2);
            $label = $kind === 'topup' ? 'EduSYS subscription top-up' : 'EduSYS school management subscription';
            $detail = number_format($students) . ' students at NGN ' . number_format($rate, 2) . ' per student per term, '
                . $terms . ' term' . ($terms === 1 ? '' : 's') . ' (' . $period . ')';
            if ($kind === 'topup') {
                $sub_amount = $price;
                $qty = $per_student > 0 ? (int) round($price / $per_student) : $students;
                $detail = number_format($qty) . ' extra students (total ' . number_format($students) . ') at NGN ' . number_format($rate, 2) . ' per student per term, '
                    . $terms . ' term' . ($terms === 1 ? '' : 's') . ' left in the period';
                return array(array($label, $detail, $qty, $per_student, $sub_amount));
            }
            $out = array(array($label, $detail, $students, $per_student, round($price - ($setup > 0 ? $setup : 0), 2)));
            if ($setup > 0) {
                $out[] = $setup_line;
            }
            return $out;
        }
        $days = (int) $sub['days'];
        $title = !empty($sub['plan_title']) ? $sub['plan_title'] : 'Subscription';
        return array(array('EduSYS ' . $title, $days > 0 ? $days . ' days' : '', 1, $price, $price));
    }

    // ------------------------------------------------------------------ emails

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

    /** Emails the invoice (unpaid: to pay; paid: the receipt) to $to, or to its bill-to address. */
    public function send_invoice(array $invoice, $to = null)
    {
        if ($invoice['status'] === 'paid') {
            return $this->send_receipt($invoice);
        }
        $sub = $this->CI->db->where('id', (int) $invoice['subscription_id'])->get('subscription')->row_array();
        $name = $sub ? (string) $sub['school_name'] : $invoice['bill_to_name'];
        $owner = $sub ? (string) $sub['owner_name'] : '';
        return $this->CI->platform_mailer->send('invoice_issued', $to ?: $invoice['bill_to_email'], array(
            'owner_name' => $owner !== '' ? $owner : $name,
            'school_name' => $name,
            'amount' => $this->money($invoice['total']),
            'invoice_no' => $invoice['invoice_no'],
            'due_date' => $invoice['due_date'] ? date('d M Y', strtotime($invoice['due_date'])) : 'receipt',
            'invoice_url' => $this->url($invoice),
        ), $invoice['school_ref']);
    }

    // ------------------------------------------------------------------ the document

    public function url(array $invoice)
    {
        $this->CI->load->library('School_provisioner');
        $platform = $this->CI->db->where('is_platform', 1)->get('school')->row_array();
        $base = $platform ? $this->CI->school_provisioner->url_for($platform) : site_url();
        return rtrim($base, '/') . '/subscription/invoice/' . $invoice['token'];
    }

    /** What the invoice views need. */
    public function view_data(array $invoice, $for_pdf = false)
    {
        $s = $this->CI->platform_settings;
        $lines = json_decode((string) (isset($invoice['lines']) ? $invoice['lines'] : ''), true);
        if (!is_array($lines) || !$lines) {
            $lines = array(array($invoice['description'], '', 1, (float) $invoice['price'], (float) $invoice['price']));
        }
        $this->CI->load->model('setting_model');
        $settings = $this->CI->setting_model->getSetting();
        $logo = 'backend/themes/platform/edusys-logo.png';
        return array(
            'invoice' => $invoice,
            'lines' => $lines,
            'status' => $invoice['status'] === 'draft' ? 'draft' : (string) $invoice['status'],
            'overdue' => $invoice['status'] === 'unpaid' && !empty($invoice['due_date']) && $invoice['due_date'] < date('Y-m-d'),
            'seller' => array(
                'name' => $s->get('invoice_company') ?: $s->get('platform_name'),
                'address' => $s->get('invoice_address'),
                'tax_id' => $s->get('invoice_tax_id'),
                'email' => $s->get('support_email'),
                'phone' => $s->get('support_phone'),
                'footer' => $s->get('invoice_footer'),
                'logo' => is_file(FCPATH . $logo) ? ($for_pdf ? FCPATH . $logo : base_url($logo)) : null,
            ),
            'bank_instructions' => $settings ? (string) $settings->offline_bank_payment_instruction : '',
            'methods' => self::METHODS,
            'money' => function ($v) { return $this->money($v); },
            'for_pdf' => $for_pdf,
        );
    }

    /** The invoice document (HTML fragment): the PDF's content and the web page's. */
    public function document(array $invoice, $for_pdf = false)
    {
        return $this->CI->load->view('admin/platform/invoice_pdf', $this->view_data($invoice, $for_pdf), true);
    }

    /**
     * The invoice as a web page with Print, Download PDF and, when unpaid, how to pay.
     * @param array $opts pdf_url, pay_url (POST target for paying online / bank transfer), online_methods, notice, admin
     */
    public function page(array $invoice, array $opts = array())
    {
        $data = $this->view_data($invoice, false);
        $data['document'] = $this->document($invoice, false);
        $data['opts'] = $opts;
        return $this->CI->load->view('admin/platform/invoice_page', $data, true);
    }

    public function pdf(array $invoice)
    {
        $html = $this->document($invoice, true);
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
