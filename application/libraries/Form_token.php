<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Per-session token for admin action forms. The application's CSRF protection is off, so
 * every state-changing POST on the platform pages carries this token (hidden field
 * "form_token") and is refused without it.
 */
class Form_token
{
    const KEY = 'platform_form_token';

    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function get()
    {
        $token = (string) $this->CI->session->userdata(self::KEY);
        if ($token === '') {
            $token = bin2hex(random_bytes(20));
            $this->CI->session->set_userdata(self::KEY, $token);
        }
        return $token;
    }

    /** The hidden input for a form. */
    public function field()
    {
        return '<input type="hidden" name="form_token" value="' . html_escape($this->get()) . '">';
    }

    /** Refuses the request (403) unless it is a POST with this session's token. */
    public function require_post()
    {
        $expected = (string) $this->CI->session->userdata(self::KEY);
        $given = (string) $this->CI->input->post('form_token');
        if ($this->CI->input->method() !== 'post' || $expected === '' || !hash_equals($expected, $given)) {
            show_error('This form has expired. Reload the page and try again.', 403, 'Request refused');
        }
    }
}
