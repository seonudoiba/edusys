<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Sends the platform's emails from the editable templates (platform_email_templates) and logs
 * every attempt (platform_email_log).
 *
 *   $this->platform_mailer->send('school_ready', $to, array('school_name' => ..., ...), $school_id);
 *
 * Placeholders are {name}; values are HTML-escaped, except those ending in _url, which are used
 * as links. Common values ({platform_name}, {support_email}, {support_phone}) are always set.
 */
class Platform_mailer
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        $this->CI->load->library('Platform_settings');
    }

    /**
     * @return bool true when the email was handed to the mail server
     */
    public function send($key, $to, array $vars = array(), $school_id = null)
    {
        $template = $this->template($key);
        $to = trim((string) $to);
        if (!$template) {
            $this->log($key, $to, '(missing template)', $school_id, 'failed', 'No template ' . $key);
            return false;
        }
        list($subject, $body) = $this->render($template, $vars);
        if (!(int) $template['is_active']) {
            $this->log($key, $to, $subject, $school_id, 'disabled', null);
            return false;
        }
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->log($key, $to, $subject, $school_id, 'failed', 'No valid recipient address');
            return false;
        }
        $ok = $this->deliver($to, $subject, $body, $error);
        $this->log($key, $to, $subject, $school_id, $ok ? 'sent' : 'failed', $ok ? null : $error);
        return $ok;
    }

    /** Sends a template with sample values to $to (the "send test" button). */
    public function send_test($key, $to)
    {
        $template = $this->template($key);
        if (!$template) {
            return false;
        }
        $vars = array();
        foreach (explode(',', $template['placeholders']) as $p) {
            $name = trim($p, " {}\t");
            if ($name !== '') {
                $vars[$name] = substr($name, -4) === '_url' ? site_url() : '[' . $name . ']';
            }
        }
        list($subject, $body) = $this->render($template, $vars);
        $ok = $this->deliver($to, '[Test] ' . $subject, $body, $error);
        $this->log($key, $to, '[Test] ' . $subject, null, $ok ? 'sent' : 'failed', $ok ? null : $error);
        return $ok;
    }

    public function template($key)
    {
        return $this->CI->db->where('template_key', (string) $key)->get('platform_email_templates')->row_array();
    }

    /** [subject, html body] with the placeholders filled in. */
    public function render(array $template, array $vars)
    {
        $s = $this->CI->platform_settings;
        $vars = array_merge(array(
            'platform_name' => $s->get('platform_name'),
            'support_email' => $s->get('support_email'),
            'support_phone' => $s->get('support_phone'),
        ), $vars);
        $subject = $template['subject'];
        $body = $template['body'];
        foreach ($vars as $name => $value) {
            $value = (string) $value;
            $subject = str_replace('{' . $name . '}', $value, $subject);
            $html = substr($name, -4) === '_url' ? htmlspecialchars($value, ENT_QUOTES) : nl2br(htmlspecialchars($value, ENT_QUOTES));
            $body = str_replace('{' . $name . '}', $html, $body);
        }
        // Unknown placeholders are removed rather than sent as "{...}".
        $subject = preg_replace('/\{[a-z_]+\}/', '', $subject);
        $body = preg_replace('/\{[a-z_]+\}/', '', $body);
        return array(trim(preg_replace('/\s+/', ' ', $subject)), $body);
    }

    private function deliver($to, $subject, $body, &$error)
    {
        $CI = $this->CI;
        $CI->load->library('email');
        $CI->email->clear(true);
        $CI->email->initialize(array(
            'protocol' => $CI->config->item('protocol'),
            'smtp_host' => $CI->config->item('smtp_host'),
            'smtp_user' => $CI->config->item('smtp_user'),
            'smtp_pass' => $CI->config->item('smtp_pass'),
            'smtp_port' => $CI->config->item('smtp_port'),
            'smtp_timeout' => 10,
            'mailtype' => 'html',
            'charset' => $CI->config->item('charset') ?: 'utf-8',
            'newline' => $CI->config->item('newline') ?: "\r\n",
        ));
        $CI->email->from($CI->config->item('from_email'), $CI->platform_settings->get('platform_name'));
        $reply = $CI->platform_settings->get('support_email');
        if ($reply && filter_var($reply, FILTER_VALIDATE_EMAIL)) {
            $CI->email->reply_to($reply);
        }
        $CI->email->to($to);
        $CI->email->subject($subject);
        $CI->email->message('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body style="font-family:Arial,sans-serif;font-size:14px;color:#333">'
            . $body . '</body></html>');
        $ok = (bool) @$CI->email->send(false);
        $error = $ok ? null : trim(strip_tags(substr($CI->email->print_debugger(array()), 0, 1500)));
        return $ok;
    }

    private function log($key, $to, $subject, $school_id, $status, $error)
    {
        $this->CI->db->insert('platform_email_log', array(
            'template_key' => (string) $key,
            'recipient' => mb_substr((string) $to, 0, 255),
            'subject' => mb_substr((string) $subject, 0, 255),
            'school_ref' => $school_id ? (int) $school_id : null,
            'status' => $status,
            'error' => $error,
        ));
    }
}
