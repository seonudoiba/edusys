<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Billing from the school admin panel: the school's super admin opens the owner's subscription
 * pages on the platform (renew, pay for more students, payments) without signing in again.
 *
 * open() creates a one-time token (platform_billing_tokens, hashed, 2 minutes) and sends the
 * browser to the platform's course/from_school/<token>, which signs in the owner account
 * registered for this school (guest.registered_school_id) and opens user/plan/planlist.
 */
class Billing_access extends Admin_Controller
{
    const LIFETIME = 120; // seconds

    public function __construct()
    {
        parent::__construct();
        if (Tenant::is_platform()) {
            show_404(); // the platform school has no subscription of its own
        }
        require_once APPPATH . 'libraries/Billing_link.php';
    }

    public function open()
    {
        if ($this->session->userdata('impersonated_by')) {
            show_error('Billing is for the school\'s own super admin. Support sessions can\'t open it.', 403, 'Not available');
        }
        if (!Billing_link::allowed()) {
            access_denied();
        }
        $platform = $this->db->without_tenant(function ($db) {
            return $db->where('is_platform', 1)->get('school')->row_array();
        });
        if (!$platform) {
            show_error('Billing is not available right now. Please contact EduSYS support.', 503, 'Not available');
        }
        $admin = $this->session->userdata('admin');
        $token = bin2hex(random_bytes(32));
        $this->db->insert('platform_billing_tokens', array(
            'token_hash' => hash('sha256', $token),
            'school_ref' => (int) Tenant::id(),
            'staff_id' => (int) $admin['id'],
            'created_by' => mb_substr(trim($admin['username'] . ' <' . $admin['email'] . '>'), 0, 255),
            'expires_at' => date('Y-m-d H:i:s', time() + self::LIFETIME),
        ));
        $this->load->library('School_provisioner');
        // The school's log gets "Billing opened from the admin panel" when the platform uses the token.
        redirect($this->school_provisioner->url_for($platform) . 'course/from_school/' . $token);
    }
}
