<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/Tenant.php';

/**
 * Binds every session to one school.
 *
 * All schools share one session store, so a session id from one school's
 * subdomain could otherwise be replayed on another's. Cookies are host-only
 * (cookie_domain is empty), so browsers don't send them across subdomains;
 * this class enforces the same rule on the server:
 *
 *  - A new session is stamped with the current school id (Tenant::id()).
 *  - A session stamped with another school, or a logged-in session with no
 *    stamp, is not used: the request gets a fresh, empty session under a new
 *    id, so it is logged out here. The other school's session is left untouched.
 *  - Logging in (setting the 'admin' or 'student' session key, which every
 *    portal does) issues a new session id, to prevent session fixation.
 *
 * Portals: admin, super admin, teacher, accountant, receptionist and librarian
 * store 'admin'; student and parent store 'student'.
 */
class MY_Session extends CI_Session
{
    /** Session key holding the school id the session belongs to. */
    const SCHOOL_KEY = '__tenant_school_id';

    /** Session keys that mean "logged in". */
    const AUTH_KEYS = array('admin', 'student');

    public function __construct(array $params = array())
    {
        parent::__construct($params);
        if (is_cli() || session_status() !== PHP_SESSION_ACTIVE) {
            return; // CI_Session doesn't start sessions on the CLI
        }
        $this->bind_to_school();
    }

    /** Applies the school binding to the session that was just started. */
    private function bind_to_school()
    {
        if (!Tenant::has()) {
            return; // only possible on the CLI; web requests always resolve a school first
        }
        $school_id = Tenant::id();
        $stamped = isset($_SESSION[self::SCHOOL_KEY]) ? (int) $_SESSION[self::SCHOOL_KEY] : null;

        if ($stamped === $school_id) {
            return;
        }
        if ($stamped === null && !$this->is_authenticated()) {
            $_SESSION[self::SCHOOL_KEY] = $school_id; // new or anonymous session: claim it
            return;
        }

        // Another school's session, or a logged-in session that predates the binding.
        $this->log_rejection($stamped);
        // New id first (the old id's data stays as it was), then start empty here.
        $this->sess_regenerate(false);
        $_SESSION = array(self::SCHOOL_KEY => $school_id, '__ci_last_regenerate' => time());
    }

    public function set_userdata($data, $value = null)
    {
        $keys = is_array($data) ? array_keys($data) : array($data);
        if (session_status() === PHP_SESSION_ACTIVE && !$this->is_authenticated() && array_intersect($keys, self::AUTH_KEYS)) {
            // Login: new session id; the pre-login id (which an attacker may have planted) is discarded.
            $this->sess_regenerate(true);
            if (Tenant::has()) {
                $_SESSION[self::SCHOOL_KEY] = Tenant::id();
            }
        }
        parent::set_userdata($data, $value);
    }

    /** True when the current session holds a logged-in user. */
    private function is_authenticated()
    {
        foreach (self::AUTH_KEYS as $key) {
            if (!empty($_SESSION[$key])) {
                return true;
            }
        }
        return false;
    }

    private function log_rejection($stamped)
    {
        $line = date('Y-m-d H:i:s')
            . "\tsession rejected\thost=" . (Tenant::host() ?: '-')
            . "\tschool=" . Tenant::id()
            . "\tsession_school=" . ($stamped === null ? 'none' : $stamped)
            . "\tip=" . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '-')
            . "\turi=" . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '-') . PHP_EOL;
        @file_put_contents(APPPATH . 'logs/tenancy-security-' . date('Y-m-d') . '.log', $line, FILE_APPEND | LOCK_EX);
    }
}
