<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Platform settings (platform admin): settings, system health, email templates, email log.
 */
class Platform extends Admin_Controller
{
    const PERMISSION = 'platform_settings';

    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();
        $this->load->library(array('Form_token', 'Platform_settings', 'Platform_mailer', 'Platform_audit'));
    }

    // ------------------------------------------------------------------ settings and health

    public function settings()
    {
        $this->require_privilege('can_view');
        $this->menu('admin/platform/settings');
        $s = $this->platform_settings;
        $values = array();
        foreach (array_keys(Platform_settings::DEFAULTS) as $name) {
            $values[$name] = $this->db->where('name', $name)->get('platform_settings')->row('value');
        }
        $data = array(
            'values' => $values,
            'effective' => array(
                'support_email' => $s->get('support_email'),
                'notify_email' => $s->get('notify_email'),
            ),
            'health' => $this->health(),
            'token' => $this->form_token->field(),
            'message' => $this->session->flashdata('platform_msg'),
            'error' => $this->session->flashdata('platform_error'),
        );
        $this->load->view('layout/header');
        $this->load->view('admin/platform/settings', $data);
        $this->load->view('layout/footer');
    }

    public function save_settings()
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $in = function ($k) { return trim((string) $this->input->post($k)); };
        $errors = array();
        foreach (array('support_email', 'notify_email') as $k) {
            if ($in($k) !== '' && !filter_var($in($k), FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Enter a valid ' . str_replace('_', ' ', $k) . ' (or leave it empty).';
            }
        }
        if ($in('platform_name') === '') {
            $errors[] = 'Enter the platform name (used in emails).';
        }
        $days = array_filter(array_map('trim', explode(',', $in('reminder_days'))), 'strlen');
        foreach ($days as $d) {
            if (!ctype_digit($d) || (int) $d < 1 || (int) $d > 365) {
                $errors[] = 'Reminder days are numbers between 1 and 365, separated by commas (e.g. 30,7,1).';
                break;
            }
        }
        $reserved = array_filter(preg_split('/[\s,]+/', strtolower($in('extra_reserved_subdomains'))), 'strlen');
        foreach ($reserved as $r) {
            if (!Tenant_resolver::is_valid_label($r)) {
                $errors[] = "'" . $r . "' is not a valid subdomain.";
                break;
            }
        }
        if (!ctype_digit($in('grace_days')) || (int) $in('grace_days') > 365) {
            $errors[] = 'The grace period is a number of days (0 to 365).';
        }
        if ($errors) {
            $this->session->set_flashdata('platform_error', implode(' ', $errors));
            redirect('admin/platform/settings');
        }
        $s = $this->platform_settings;
        $s->set('platform_name', mb_substr($in('platform_name'), 0, 100));
        $s->set('support_email', $in('support_email'));
        $s->set('support_phone', mb_substr($in('support_phone'), 0, 50));
        $s->set('notify_email', $in('notify_email'));
        $s->set('reminders_enabled', $this->input->post('reminders_enabled') ? '1' : '0');
        $s->set('expired_notice', $this->input->post('expired_notice') ? '1' : '0');
        $s->set('reminder_days', implode(',', array_unique(array_map('intval', $days))));
        $s->set('extra_reserved_subdomains', implode(', ', array_unique($reserved)));
        $s->set('grace_days', (string) (int) $in('grace_days'));
        $s->set('expiry_action', $in('expiry_action') === 'disable' ? 'disable' : 'warn');
        $s->set('invoice_company', mb_substr($in('invoice_company'), 0, 150));
        $s->set('invoice_address', mb_substr($in('invoice_address'), 0, 500));
        $s->set('invoice_tax_id', mb_substr($in('invoice_tax_id'), 0, 60));
        $s->set('invoice_footer', mb_substr($in('invoice_footer'), 0, 500));
        $this->platform_audit->record('settings.save', 'Saved the platform settings');
        $this->session->set_flashdata('platform_msg', 'Settings saved.');
        redirect('admin/platform/settings');
    }

    /** Sends a test email (the "set-password link" template with sample values). */
    public function test_email()
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $to = trim((string) $this->input->post('to'));
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('platform_error', 'Enter the address to send the test to.');
        } elseif ($this->platform_mailer->send_test('password_link', $to)) {
            $this->session->set_flashdata('platform_msg', 'Test email sent to ' . $to . '. Check that it arrived (and not in spam).');
        } else {
            $last = $this->db->order_by('id', 'desc')->limit(1)->get('platform_email_log')->row_array();
            $this->session->set_flashdata('platform_error', 'The test email could not be sent. ' . ($last ? mb_substr((string) $last['error'], 0, 300) : ''));
        }
        redirect($this->input->post('back') === 'emails' ? 'admin/platform/emails' : 'admin/platform/settings');
    }

    // ------------------------------------------------------------------ email templates and log

    public function emails()
    {
        $this->require_privilege('can_view');
        $this->menu('admin/platform/emails');
        $data = array(
            'templates' => $this->db->order_by('recipient', 'asc')->order_by('name', 'asc')->get('platform_email_templates')->result_array(),
            'stats' => $this->db->select('status, COUNT(*) AS n', false)->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')))->group_by('status')->get('platform_email_log')->result_array(),
            'token' => $this->form_token->field(),
            'message' => $this->session->flashdata('platform_msg'),
            'error' => $this->session->flashdata('platform_error'),
        );
        $this->load->view('layout/header');
        $this->load->view('admin/platform/emails', $data);
        $this->load->view('layout/footer');
    }

    public function email_edit($key = '')
    {
        $this->require_privilege('can_edit');
        $this->menu('admin/platform/emails');
        $template = $this->template_or_404($key);
        if ($this->input->method() === 'post') {
            $this->form_token->require_post();
            $subject = trim((string) $this->input->post('subject'));
            $body = (string) $this->input->post('body', false);
            if ($subject === '' || trim(strip_tags($body)) === '') {
                $this->session->set_flashdata('platform_error', 'The subject and the message are required.');
                redirect('admin/platform/email_edit/' . rawurlencode($template['template_key']));
            }
            $this->db->where('template_key', $template['template_key'])->update('platform_email_templates', array(
                'subject' => mb_substr($subject, 0, 255),
                'body' => $this->clean_html($body),
                'is_active' => $this->input->post('is_active') ? 1 : 0,
            ));
            $this->platform_audit->record('email.edit', 'Edited the email "' . $template['name'] . '"');
            $this->session->set_flashdata('platform_msg', 'Saved "' . $template['name'] . '".');
            redirect('admin/platform/emails');
        }
        $data = array(
            'template' => $template,
            'preview' => $this->platform_mailer->render($template, $this->sample_values($template)),
            'token' => $this->form_token->field(),
            'error' => $this->session->flashdata('platform_error'),
        );
        $this->load->view('layout/header');
        $this->load->view('admin/platform/email_edit', $data);
        $this->load->view('layout/footer');
    }

    /** Restores a template's original text (from the migration that created it). */
    public function email_reset($key = '')
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $template = $this->template_or_404($key);
        require_once APPPATH . 'migrations/253_create_platform_email_templates.php';
        $defaults = Migration_Create_platform_email_templates::defaults();
        if (isset($defaults[$template['template_key']])) {
            $d = $defaults[$template['template_key']];
            $this->db->where('template_key', $template['template_key'])->update('platform_email_templates', array('subject' => $d[2], 'body' => $d[3]));
            $this->platform_audit->record('email.reset', 'Restored the original text of "' . $template['name'] . '"');
            $this->session->set_flashdata('platform_msg', 'Restored the original text of "' . $template['name'] . '".');
        }
        redirect('admin/platform/email_edit/' . rawurlencode($template['template_key']));
    }

    public function email_test($key = '')
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $template = $this->template_or_404($key);
        $to = trim((string) $this->input->post('to'));
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('platform_error', 'Enter the address to send the test to.');
        } elseif ($this->platform_mailer->send_test($template['template_key'], $to)) {
            $this->session->set_flashdata('platform_msg', 'Test of "' . $template['name'] . '" sent to ' . $to . '.');
        } else {
            $this->session->set_flashdata('platform_error', 'The test email could not be sent: see the email log.');
        }
        redirect('admin/platform/email_edit/' . rawurlencode($template['template_key']));
    }

    public function email_log()
    {
        $this->require_privilege('can_view');
        $this->menu('admin/platform/email_log');
        $status = (string) $this->input->get('status');
        $school = (int) $this->input->get('school');
        $q = trim((string) $this->input->get('q'));
        $this->db->from('platform_email_log');
        if (in_array($status, array('sent', 'failed', 'disabled'), true)) {
            $this->db->where('status', $status);
        }
        if ($school > 0) {
            $this->db->where('school_ref', $school);
        }
        if ($q !== '') {
            $this->db->group_start()->like('recipient', $q)->or_like('subject', $q)->group_end();
        }
        $data = array(
            'rows' => $this->db->order_by('id', 'desc')->limit(300)->get()->result_array(),
            'status' => $status,
            'school' => $school,
            'q' => $q,
        );
        $this->load->view('layout/header');
        $this->load->view('admin/platform/email_log', $data);
        $this->load->view('layout/footer');
    }

    // ------------------------------------------------------------------ helpers

    private function health()
    {
        $h = array();
        $h['php'] = PHP_VERSION;
        $h['mysql'] = $this->db->query('SELECT VERSION() AS v')->row('v');
        $this->config->load('migration', true);
        $h['migration_target'] = (int) $this->config->item('migration_version', 'migration');
        $h['migration_current'] = (int) $this->db->get('migrations')->row('version');
        $h['base_domain'] = $this->config->item('tenancy_base_domain');
        $h['central_hosts'] = implode(', ', (array) $this->config->item('tenancy_central_hosts'));
        $h['force_https'] = (bool) $this->config->item('tenancy_force_https');
        $h['raw_sql_mode'] = getenv('EDUSYS_TENANCY_RAW_SQL') ?: 'log';
        $h['smtp_host'] = $this->config->item('smtp_host') . ':' . $this->config->item('smtp_port');
        $h['smtp_login'] = $this->config->item('smtp_pass') !== '' ? 'set' : 'none';
        $h['from_email'] = $this->config->item('from_email');
        $h['paystack'] = trim((string) getenv('EDUSYS_PAYSTACK_SECRET_KEY')) !== '' ? (strpos(getenv('EDUSYS_PAYSTACK_SECRET_KEY'), 'sk_test') === 0 ? 'test key' : 'live key') : 'not set';
        $h['disk_free'] = @disk_free_space(FCPATH);
        $h['disk_total'] = @disk_total_space(FCPATH);
        $h['cron'] = array();
        foreach ($this->db->like('name', 'cron_last_', 'after')->get('platform_settings')->result_array() as $r) {
            $h['cron'][substr($r['name'], 10)] = $r['value'];
        }
        $since = date('Y-m-d H:i:s', strtotime('-7 days'));
        $h['mail_sent'] = $this->db->where(array('status' => 'sent', 'created_at >=' => $since))->count_all_results('platform_email_log');
        $h['mail_failed'] = $this->db->where(array('status' => 'failed', 'created_at >=' => $since))->count_all_results('platform_email_log');
        $h['uploads'] = $this->upload_usage();
        $h['error_log'] = $this->tail_latest(APPPATH . 'logs/log-*.php', 40);
        $h['sql_guard_log'] = $this->tail_latest(APPPATH . 'logs/tenancy-raw-sql-*.log', 20);
        return $h;
    }

    /** Size of each school's upload folder (largest first). */
    private function upload_usage()
    {
        $out = array();
        foreach (glob(FCPATH . 'uploads/s*', GLOB_ONLYDIR) as $dir) {
            if (!preg_match('~/s(\d+)$~', str_replace('\\', '/', $dir), $m)) {
                continue;
            }
            $bytes = 0;
            $files = 0;
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                if ($f->isFile()) {
                    $bytes += $f->getSize();
                    $files++;
                }
            }
            $out[(int) $m[1]] = array('bytes' => $bytes, 'files' => $files);
        }
        uasort($out, function ($a, $b) { return $b['bytes'] <=> $a['bytes']; });
        if ($out) {
            $names = $this->db->select('id, school_name')->where_in('id', array_keys($out))->get('school')->result_array();
            foreach ($names as $n) {
                $out[(int) $n['id']]['name'] = $n['school_name'];
            }
        }
        return $out;
    }

    /** The last lines of the newest file matching $pattern. */
    private function tail_latest($pattern, $lines)
    {
        $files = glob($pattern);
        if (!$files) {
            return array('file' => null, 'lines' => array());
        }
        usort($files, function ($a, $b) { return filemtime($b) <=> filemtime($a); });
        $content = (string) @file_get_contents($files[0], false, null, max(0, filesize($files[0]) - 200000));
        $all = array_values(array_filter(explode("\n", str_replace("\r", '', $content)), function ($l) {
            return trim($l) !== '' && strpos($l, "defined('BASEPATH')") === false;
        }));
        return array('file' => basename($files[0]), 'lines' => array_slice($all, -$lines));
    }

    private function sample_values(array $template)
    {
        $vars = array();
        foreach (explode(',', $template['placeholders']) as $p) {
            $name = trim($p, " {}\t");
            if ($name !== '') {
                $vars[$name] = substr($name, -4) === '_url' ? site_url() : '[' . $name . ']';
            }
        }
        return $vars;
    }

    /** Email HTML without scripts, event handlers or javascript: links. */
    private function clean_html($html)
    {
        $html = preg_replace('~<\s*(script|style|iframe|object|embed|form|input|button)\b[^>]*>.*?<\s*/\s*\1\s*>~is', '', $html);
        $html = preg_replace('~<\s*(script|style|iframe|object|embed|form|input|button|meta|link)\b[^>]*>~i', '', $html);
        $html = preg_replace('~\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)~i', '', $html);
        $html = preg_replace('~(href|src)\s*=\s*(["\']?)\s*javascript:[^"\'>\s]*\2~i', '$1="#"', $html);
        return trim($html);
    }

    private function template_or_404($key)
    {
        $template = $this->platform_mailer->template(rawurldecode((string) $key));
        if (!$template) {
            show_404();
        }
        return $template;
    }

    private function menu($sub)
    {
        $this->session->set_userdata('top_menu', 'platform_admin');
        $this->session->set_userdata('sub_menu', $sub);
    }

    private function require_privilege($action)
    {
        if (!$this->rbac->hasPrivilege(self::PERMISSION, $action)) {
            access_denied();
        }
    }
}
