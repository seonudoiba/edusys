<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Schools (platform admin): the list, each school's page and the actions on a school.
 *
 * Every change is a POST carrying the page's form token (the application's CSRF protection is
 * off) and checks the "schools" permission. Schools live in the shared database: setting one up
 * loads its default data and creates its super admin (School_provisioner), never a folder or a
 * database.
 */
#[AllowDynamicProperties]
class Schools extends Admin_Controller
{
    const PERMISSION = 'schools';
    const EXTEND_MAX_DAYS = 3650;

    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();
        $this->load->model(array('School_model', 'Subscription_model', 'Guest_model', 'Platform_school_model', 'plan_model'));
        $this->load->library(array('School_provisioner', 'Platform_audit', 'Platform_mailer', 'Plan_limits'));
    }

    // ------------------------------------------------------------------ pages

    public function index()
    {
        $this->require_privilege('can_view');
        $this->menu();
        $schools = $this->Platform_school_model->all();
        $tabs = array('all', 'waiting_approval', 'active', 'disabled', 'expiring', 'expired', 'awaiting_payment');
        $tab = in_array($this->input->get('tab'), $tabs, true) ? $this->input->get('tab') : 'all';
        $q = trim((string) $this->input->get('q'));

        $shown = array_values(array_filter($schools, function ($s) use ($tab, $q) {
            if ($tab === 'expiring' || $tab === 'expired') {
                if ($s['expiry'] !== $tab) {
                    return false;
                }
            } elseif ($tab !== 'all' && $s['state'] !== $tab) {
                return false;
            }
            if ($q === '') {
                return true;
            }
            $hay = strtolower($s['school_name'] . ' ' . $s['owner_name'] . ' ' . $s['email_address'] . ' ' . $s['phone_number'] . ' ' . $s['subdomain']);
            return strpos($hay, strtolower($q)) !== false;
        }));

        $data = array(
            'title' => 'Schools',
            'schools' => $shown,
            'counts' => $this->Platform_school_model->counts($schools),
            'tab' => $tab,
            'q' => $q,
            'currency_symbol' => $this->customlib->getSchoolCurrencyFormat(),
            'base_domain' => $this->config->item('tenancy_base_domain'),
            'message' => $this->session->flashdata('schools_msg'),
            'error' => $this->session->flashdata('schools_error'),
        );
        $this->load->view('layout/header', $data);
        $this->load->view('admin/school/school_list', $data);
        $this->load->view('layout/footer', $data);
    }

    public function view($id = null)
    {
        $this->require_privilege('can_view');
        $this->menu();
        $school = $this->school_or_404($id);
        $data = array(
            'title' => $school['school_name'],
            'school' => $school,
            'site_url' => $this->site_url_of($school),
            'subscriptions' => $this->Platform_school_model->subscriptions($school['id']),
            'bank_transfers' => $this->Platform_school_model->bank_transfers($school['id']),
            'usage' => $school['set_up'] ? $this->Platform_school_model->usage($school['id']) : null,
            'admins' => $school['set_up'] ? $this->Platform_school_model->super_admins($school['id']) : array(),
            'logins' => $school['set_up'] ? $this->Platform_school_model->recent_logins($school['id']) : array(),
            'plans' => $this->plan_model->get(),
            'currency_symbol' => $this->customlib->getSchoolCurrencyFormat(),
            'base_domain' => $this->config->item('tenancy_base_domain'),
            'form_token' => $this->form_token(),
            'message' => $this->session->flashdata('schools_msg'),
            'error' => $this->session->flashdata('schools_error'),
            'set_password_url' => $this->session->flashdata('schools_link'),
            'notes' => $this->db->where('school_ref', (int) $school['id'])->order_by('id', 'desc')->get('platform_school_notes')->result_array(),
            'activity' => $this->platform_audit->for_school($school['id']),
            'modules' => $school['set_up'] && empty($school['is_platform']) ? $this->Platform_school_model->modules($school['id']) : array(),
            'emails' => $this->db->where('school_ref', (int) $school['id'])->order_by('id', 'desc')->limit(10)->get('platform_email_log')->result_array(),
            'limits' => $this->plan_limits->current($school['id']),
            'domains' => $this->db->where('school_ref', (int) $school['id'])->order_by('id')->get('platform_school_domains')->result_array(),
            'storage_bytes' => $school['set_up'] ? $this->plan_limits->storage_bytes($school['id']) : 0,
            'invoices' => $this->db->where('school_ref', (int) $school['id'])->order_by('id', 'desc')->get('platform_invoices')->result_array(),
        );
        $this->load->view('layout/header', $data);
        $this->load->view('admin/school/school_view', $data);
        $this->load->view('layout/footer', $data);
    }

    public function create()
    {
        $this->require_privilege('can_add');
        $this->menu();
        $this->load->view('layout/header');
        $this->load->view('admin/school/schoolform', array());
        $this->load->view('layout/footer');
    }

    public function edit($id = null)
    {
        $this->require_privilege('can_edit');
        $this->menu();
        $school = $this->school_or_404($id);
        $data = array('title' => 'Edit School', 'school' => $school);
        $this->load->view('layout/header', $data);
        $this->load->view('admin/school/schoolform', $data);
        $this->load->view('layout/footer');
    }

    public function save($id = null)
    {
        $id = ctype_digit((string) $id) ? (int) $id : null;
        $this->require_privilege($id ? 'can_edit' : 'can_add');
        $this->load->helper('form');
        if ($id) {
            $this->school_or_404($id);
        }

        $this->form_validation->set_rules('school_name', $this->lang->line('school_name'), 'trim|required|max_length[255]|xss_clean');
        $this->form_validation->set_rules('school_code', $this->lang->line('school_code'), 'trim|max_length[255]|xss_clean');
        $this->form_validation->set_rules('subdomain', 'Subdomain', 'trim|strtolower|callback_check_subdomain[' . (int) $id . ']');
        $this->form_validation->set_rules('school_address', $this->lang->line('school_address'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('phone_number', $this->lang->line('phone_number'), 'trim|required|max_length[30]|xss_clean|regex_match[/^\+?[0-9 ()\-.]{5,30}$/]');
        $this->form_validation->set_rules('email_address', $this->lang->line('email_address'), 'trim|required|valid_email|max_length[255]');
        $this->form_validation->set_rules('website', $this->lang->line('website'), 'trim|max_length[255]|callback_check_website');
        $this->form_validation->set_rules('principal_name', $this->lang->line('principal_name'), 'trim|max_length[255]|xss_clean');
        $this->form_validation->set_rules('owner_name', $this->lang->line('owner_name'), 'trim|required|max_length[255]|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->menu();
            $data = array('school' => $id ? $this->Platform_school_model->get($id) : null);
            $this->load->view('layout/header');
            $this->load->view('admin/school/schoolform', $data);
            $this->load->view('layout/footer');
            return;
        }

        $subdomain = strtolower(trim((string) $this->input->post('subdomain')));
        $data = array(
            'school_name' => $this->input->post('school_name'),
            'school_code' => (string) $this->input->post('school_code'),
            'subdomain' => $subdomain !== '' ? $subdomain : null,
            'school_type' => (int) $this->input->post('school_type') ?: null,
            'school_category' => (int) $this->input->post('school_category') ?: null,
            'school_address' => $this->input->post('school_address'),
            'phone_number' => $this->input->post('phone_number'),
            'email_address' => $this->input->post('email_address'),
            'website' => (string) $this->input->post('website'),
            'principal_name' => (string) $this->input->post('principal_name'),
            'owner_name' => $this->input->post('owner_name'),
        );
        if ($id) {
            $existing = $this->Platform_school_model->get($id);
            if ($existing['set_up'] && $existing['subdomain'] !== $data['subdomain']) {
                // Changing the address of a running school breaks its users' links and logins.
                $this->session->set_flashdata('schools_error', 'The subdomain of a school that is set up can\'t be changed here.');
                redirect('admin/schools/view/' . $id);
            }
            $this->db->where('id', $id)->update('school', $data);
            $this->platform_audit->record('school.edit', 'Edited the details of ' . $data['school_name'], $id);
            $this->session->set_flashdata('schools_msg', 'School details saved.');
        } else {
            $data['status'] = 0;
            $data['payment_status'] = 1;
            $data['cron_flag'] = 0;
            $this->db->insert('school', $data);
            $id = (int) $this->db->insert_id();
            $this->platform_audit->record('school.create', 'Added ' . $data['school_name'], $id);
            $this->session->set_flashdata('schools_msg', 'School added. Set its subdomain and approve it to set it up.');
        }
        redirect('admin/schools/view/' . $id);
    }

    // ------------------------------------------------------------------ actions (POST)

    /** Sets the subdomain (if given) and sets the school up: default data, super admin, set-password link. */
    public function setup($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        if ($school['set_up']) {
            $this->back($school['id'], null, 'This school is already set up.');
        }
        if ($school['status_reason'] === 'purged') {
            $this->back($school['id'], null, 'This school was purged; register it again as a new school instead.');
        }
        $subdomain = strtolower(trim((string) $this->input->post('subdomain')));
        if ($subdomain !== '' && $subdomain !== (string) $school['subdomain']) {
            $error = $this->school_provisioner->subdomain_error($subdomain, (int) $school['id']);
            if ($error !== null) {
                $this->back($school['id'], null, $error);
            }
            $this->db->where('id', (int) $school['id'])->update('school', array('subdomain' => $subdomain));
        }
        try {
            $result = $this->school_provisioner->provision((int) $school['id']);
        } catch (RuntimeException $e) {
            $this->back($school['id'], null, $e->getMessage());
        }
        $mailed = $this->send_access_email($result['admin_email'], $school, $result['set_password_url'], true);
        $this->platform_audit->record('school.setup', 'Approved and set up ' . $school['school_name'] . ' at ' . $this->school_provisioner->school_url($result['subdomain']), $school['id']);
        $this->session->set_flashdata('schools_link', $result['set_password_url']);
        $this->back($school['id'], 'School set up at ' . $this->school_provisioner->school_url($result['subdomain']) . '. '
            . ($mailed ? 'The set-password link was emailed to ' . $result['admin_email'] . '.' : 'The email could not be sent: give the owner the link below.'));
    }

    public function toggle_status($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        if (!empty($school['is_platform'])) {
            $this->back($school['id'], null, 'The platform school can\'t be disabled.');
        }
        if (!$school['set_up']) {
            $this->back($school['id'], null, 'Set the school up first; it is enabled when it is set up.');
        }
        $enable = !$school['enabled'];
        $this->db->where('id', (int) $school['id'])->update('school', array('status' => $enable ? 1 : 0));
        $this->platform_audit->record($enable ? 'school.enable' : 'school.disable', ($enable ? 'Enabled ' : 'Disabled ') . $school['school_name'], $school['id']);
        $this->back($school['id'], $enable ? 'School enabled: its users can log in again.' : 'School disabled: its site now shows "School unavailable" and nobody can log in.');
    }

    /** A new one-time set-password link for one of the school's super admins. */
    public function resend_access($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        $admin_id = (int) $this->input->post('staff_id');
        $admin = null;
        foreach ($this->Platform_school_model->super_admins($school['id']) as $a) {
            if ((int) $a['id'] === $admin_id) {
                $admin = $a;
            }
        }
        if (!$admin || trim((string) $admin['email']) === '') {
            $this->back($school['id'], null, 'Choose one of the school\'s super admins.');
        }
        $code = bin2hex(random_bytes(20));
        $this->db->without_tenant(function ($db) use ($admin, $school, $code) {
            $db->where(array('id' => (int) $admin['id'], 'school_id' => (int) $school['id']))->update('staff', array('verification_code' => $code));
        });
        $url = $this->school_provisioner->url_for($school) . 'site/admin_resetpassword/' . $code;
        $mailed = $this->send_access_email($admin['email'], $school, $url, false);
        $this->platform_audit->record('school.password_link', 'Created a new set-password link for ' . $admin['email'], $school['id']);
        $this->session->set_flashdata('schools_link', $url);
        $this->back($school['id'], 'New set-password link created for ' . $admin['email'] . '. '
            . ($mailed ? 'It was emailed to them.' : 'The email could not be sent: give them the link below.')
            . ' Any earlier link stops working.');
    }

    /**
     * Adds days to the school's current paid subscription, or records a new paid subscription
     * (manual payment, chosen plan) when it has none.
     */
    public function extend($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        $days = (int) $this->input->post('days');
        if ($days < 1 || $days > self::EXTEND_MAX_DAYS) {
            $this->back($school['id'], null, 'Enter between 1 and ' . self::EXTEND_MAX_DAYS . ' days.');
        }
        $paid = $school['paid_subscription'];
        if ($paid) {
            $this->db->where('id', (int) $paid['id'])->set('days', 'days + ' . $days, false)->update('subscription');
            $this->platform_audit->record('subscription.extend', "Extended the subscription by $days days", $school['id']);
            if ($school['status_reason'] === 'expired' && $this->plan_limits->current($school['id'])['state'] !== 'lapsed') {
                $this->db->where('id', (int) $school['id'])->update('school', array('status' => 1, 'status_reason' => null));
            }
            $this->back($school['id'], "Subscription extended by $days days.");
        }
        $plan_id = (int) $this->input->post('plan_id');
        $plan = $plan_id > 0 ? $this->plan_model->get($plan_id) : null; // get(0) would return every plan
        if (empty($plan) || !isset($plan['plan_id'])) {
            $this->back($school['id'], null, 'Choose the plan for this subscription.');
        }
        $this->db->insert('subscription', array(
            'plan_id' => (int) $plan['plan_id'],
            'school_id' => (int) $school['id'],
            'end_date' => date('Y-m-d H:i:s', strtotime("+$days days")),
            'price' => 0,
            'discount_amt' => 0,
            'payment_method' => 'manual',
            'payment_status' => 1,
            'status' => 1,
            'renewal_count' => 0,
            'total' => 0,
            'school_name' => $school['school_name'],
            'email_address' => $school['email_address'],
            'phone_number' => $school['phone_number'],
            'owner_name' => $school['owner_name'],
            'cron_flag' => 0,
            'days' => $days,
        ));
        $this->db->where('id', (int) $school['id'])->update('school', array('payment_status' => 1));
        if ($school['set_up']) {
            $this->plan_limits->apply_modules($school['id'], true);
        }
        $this->platform_audit->record('subscription.manual', "Recorded a manual subscription: {$plan['title']}, $days days", $school['id']);
        $this->back($school['id'], "Subscription recorded: {$plan['title']}, $days days (no payment collected).");
    }

    /** Deletes a school that was never set up (a set-up school has data; disable it instead). */
    public function delete($id = null)
    {
        $this->require_privilege('can_delete');
        $school = $this->checked_post($id);
        if (!empty($school['is_platform'])) {
            $this->back($school['id'], null, 'The platform school can\'t be deleted.');
        }
        $has_data = $this->db->without_tenant(function ($db) use ($school) {
            return $db->where('school_id', (int) $school['id'])->count_all_results('sch_settings') > 0;
        });
        if ($school['set_up'] || $has_data) {
            $this->back($school['id'], null, 'This school has been set up and has data. Disable it instead of deleting it.');
        }
        $this->Subscription_model->removebySchool($school['id']);
        $this->Guest_model->removebySchool($school['id']);
        $this->School_model->delete($school['id']);
        $this->platform_audit->record('school.delete', 'Deleted ' . $school['school_name'] . ' (#' . (int) $school['id'] . ')', null);
        $this->session->set_flashdata('schools_msg', 'Deleted ' . $school['school_name'] . '.');
        redirect('admin/schools');
    }

    /**
     * Opens the school's admin panel as one of its super admins (support). A one-time token,
     * valid for two minutes on that school's host only, is handed over in the redirect; both the
     * platform's and the school's audit trails record it.
     */
    public function impersonate($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        if (!$school['set_up'] || !empty($school['is_platform'])) {
            $this->back($school['id'], null, 'Only a school that is set up can be opened.');
        }
        if (!$school['enabled']) {
            $this->back($school['id'], null, 'Enable the school first: a disabled school can\'t be opened.');
        }
        $admin = null;
        foreach ($this->Platform_school_model->super_admins($school['id']) as $a) {
            if ((int) $a['is_active'] === 1 && ($admin === null || (int) $a['id'] === (int) $this->input->post('staff_id'))) {
                $admin = $a;
            }
        }
        if (!$admin) {
            $this->back($school['id'], null, 'The school has no active super admin to open.');
        }
        $me = $this->session->userdata('admin');
        $by = trim(($me['username'] ?? 'Platform admin') . ' <' . ($me['email'] ?? '') . '>');
        $token = bin2hex(random_bytes(32));
        $this->db->insert('platform_impersonation_tokens', array(
            'token_hash' => hash('sha256', $token),
            'school_ref' => (int) $school['id'],
            'staff_id' => (int) $admin['id'],
            'created_by' => mb_substr($by, 0, 255),
            'expires_at' => date('Y-m-d H:i:s', time() + 120),
        ));
        $this->platform_audit->record('school.impersonate', 'Opened the admin panel of ' . $school['school_name'] . ' as ' . $admin['email'], $school['id']);
        redirect($this->school_provisioner->school_url($school['subdomain']) . 'site/impersonate/' . $token);
    }

    /** Internal note on a school (never shown to the school). */
    public function add_note($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        $note = trim((string) $this->input->post('note'));
        if ($note === '' || mb_strlen($note) > 5000) {
            $this->back($school['id'], null, 'Write the note (up to 5000 characters).');
        }
        $me = $this->session->userdata('admin');
        $this->db->insert('platform_school_notes', array(
            'school_ref' => (int) $school['id'],
            'note' => $note,
            'created_by' => mb_substr(trim(($me['username'] ?? '') . ' <' . ($me['email'] ?? '') . '>'), 0, 255),
        ));
        $this->back($school['id'], 'Note added.');
    }

    public function delete_note($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        $this->db->where(array('id' => (int) $this->input->post('note_id'), 'school_ref' => (int) $school['id']))->delete('platform_school_notes');
        $this->back($school['id'], 'Note deleted.');
    }

    /** Switches one of the school's modules on or off (what its Modules page does, from the platform). */
    public function toggle_module($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        if (!$school['set_up'] || !empty($school['is_platform'])) {
            $this->back($school['id'], null, 'Modules can be changed once the school is set up.');
        }
        $group_id = (int) $this->input->post('module_id');
        $module = $this->db->without_tenant(function ($db) use ($group_id, $school) {
            return $db->where(array('id' => $group_id, 'school_id' => (int) $school['id']))->get('permission_group')->row_array();
        });
        if (!$module || (int) $module['system'] === 1) {
            $this->back($school['id'], null, 'This module can\'t be switched off.');
        }
        $on = (int) $module['is_active'] !== 1;
        $this->db->without_tenant(function ($db) use ($module, $on) {
            $db->where('id', (int) $module['id'])->update('permission_group', array('is_active' => $on ? 1 : 0));
        });
        $this->platform_audit->record('school.module', ($on ? 'Switched on ' : 'Switched off ') . $module['name'] . ' for ' . $school['school_name'], $school['id']);
        $this->back($school['id'], $module['name'] . ($on ? ' switched on.' : ' switched off.'));
    }

    // ------------------------------------------------------------------ data export and purge

    /**
     * Downloads the school's data: an SQL file of its rows and, when it has uploaded files, its
     * uploads folder (.tar.gz). For handing data back to a school or archiving it before a purge.
     */
    public function export($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        if (!empty($school['is_platform'])) {
            $this->back($school['id'], null, 'Back up the platform from Platform > Backups instead.');
        }
        $this->load->library('Data_dump');
        @set_time_limit(0);
        $base = 'school-' . (int) $school['id'] . '-' . ($school['subdomain'] ?: 'export') . '-' . date('Ymd-His');
        $tmp = rtrim(str_replace('\\', '/', sys_get_temp_dir()), '/') . '/' . $base . '-' . bin2hex(random_bytes(4));
        $sql = $tmp . '.sql.gz';
        try {
            $counts = $this->data_dump->school($school['id'], $sql);
        } catch (Exception $ex) {
            @unlink($sql);
            $this->back($school['id'], null, 'The export failed: ' . $ex->getMessage());
        }
        $download = $sql;
        $name = $base . '.sql.gz';
        $uploads = FCPATH . 'uploads/s' . (int) $school['id'];
        if (is_dir($uploads)) {
            try {
                $tar = new PharData($tmp . '.tar');
                $tar->addFile($sql, $base . '.sql.gz');
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploads, FilesystemIterator::SKIP_DOTS));
                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $local = 'uploads/' . str_replace('\\', '/', substr($file->getPathname(), strlen($uploads) + 1));
                        $tar->addFile($file->getPathname(), $local);
                    }
                }
                $tar->compress(Phar::GZ);
                unset($tar);
                @unlink($tmp . '.tar');
                $download = $tmp . '.tar.gz';
                $name = $base . '.tar.gz';
            } catch (Exception $ex) {
                // Files could not be packed (no Phar, too large...): hand over the SQL alone.
                @unlink($tmp . '.tar');
                @unlink($tmp . '.tar.gz');
            }
        }
        $this->platform_audit->record('school.export', 'Exported the data of ' . $school['school_name'] . ' (' . array_sum($counts) . ' rows' . ($download !== $sql ? ', with files' : '') . ')', $school['id']);
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/gzip');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Length: ' . filesize($download));
        header('Cache-Control: no-store');
        readfile($download);
        @unlink($sql);
        @unlink($tmp . '.tar.gz');
        exit;
    }

    /**
     * Deletes all of a disabled school's data and files for good. The school row stays (marked
     * purged, without its subdomain) with its subscriptions and invoices, for the accounts.
     */
    public function purge($id = null)
    {
        $this->require_privilege('can_delete');
        $school = $this->checked_post($id);
        if (!empty($school['is_platform'])) {
            $this->back($school['id'], null, 'The platform school can\'t be purged.');
        }
        if ($school['status_reason'] === 'purged' || (string) $school['subdomain'] === '') {
            $this->back($school['id'], null, 'This school has already been purged.');
        }
        if ($school['enabled'] || $school['state'] !== 'disabled') {
            $this->back($school['id'], null, 'Disable the school first; only a disabled school can be purged.');
        }
        if (strtolower(trim((string) $this->input->post('confirm'))) !== strtolower((string) $school['subdomain'])) {
            $this->back($school['id'], null, 'Type the school\'s subdomain exactly to confirm the purge.');
        }
        $this->load->library('Data_dump');
        @set_time_limit(0);
        $rows = $this->data_dump->purge_school($school['id']);
        $files = $this->remove_tree(FCPATH . 'uploads/s' . (int) $school['id']);
        @unlink(APPPATH . 'cache/storage_s' . (int) $school['id'] . '.txt');
        $this->db->where('id', (int) $school['id'])->update('school', array(
            'status' => 0, 'cron_flag' => 0, 'subdomain' => null, 'status_reason' => 'purged',
        ));
        $this->platform_audit->record('school.purge', 'Purged ' . $school['school_name'] . ' (' . $rows . ' rows, ' . $files . ' files; subdomain ' . $school['subdomain'] . ' released)', $school['id']);
        $this->back($school['id'], 'Purged: ' . $rows . ' rows and ' . $files . ' files deleted. The subdomain ' . $school['subdomain'] . ' is free again.');
    }

    /** Deletes a folder and its contents; returns the number of files removed. */
    private function remove_tree($dir)
    {
        if (!is_dir($dir)) {
            return 0;
        }
        $n = 0;
        $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getPathname());
            } elseif (@unlink($item->getPathname())) {
                $n++;
            }
        }
        @rmdir($dir);
        return $n;
    }

    // ------------------------------------------------------------------ custom domains

    /** Adds a domain the school will be served on (it works once verified). */
    public function add_domain($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        $domain = strtolower(trim((string) $this->input->post('domain')));
        $domain = preg_replace('~^https?://~', '', rtrim($domain, '/'));
        $base = (string) $this->config->item('tenancy_base_domain');
        if (!preg_match('/^(?=.{4,253}$)([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/', $domain)) {
            $this->back($school['id'], null, 'Enter a domain like portal.greenfield.com (no http://, no path).');
        }
        if ($domain === $base || substr($domain, -strlen('.' . $base)) === '.' . $base || in_array($domain, (array) $this->config->item('tenancy_central_hosts'), true)) {
            $this->back($school['id'], null, 'That is one of the platform\'s own addresses; schools get their subdomain automatically.');
        }
        if ($this->db->where('domain', $domain)->count_all_results('platform_school_domains') > 0) {
            $this->back($school['id'], null, $domain . ' is already added (to this or another school).');
        }
        $this->db->insert('platform_school_domains', array('school_ref' => (int) $school['id'], 'domain' => $domain, 'verify_token' => bin2hex(random_bytes(16))));
        $this->platform_audit->record('domain.add', 'Added the domain ' . $domain . ' to ' . $school['school_name'], $school['id']);
        $this->back($school['id'], 'Added ' . $domain . '. Ask the school to create the DNS record shown, then click Verify.');
    }

    /** Checks the DNS record that proves the school controls the domain. */
    public function verify_domain($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        $d = $this->domain_row($school, (int) $this->input->post('domain_id'));
        $target = $school['subdomain'] . '.' . $this->config->item('tenancy_base_domain');
        $ok = false;
        foreach ((array) @dns_get_record('_edusys.' . $d['domain'], DNS_TXT) as $r) {
            if (isset($r['txt']) && trim($r['txt']) === $d['verify_token']) {
                $ok = true;
            }
        }
        foreach ((array) @dns_get_record($d['domain'], DNS_CNAME) as $r) {
            if (isset($r['target']) && strtolower(rtrim($r['target'], '.')) === strtolower($target)) {
                $ok = true;
            }
        }
        if (!$ok) {
            $this->back($school['id'], null, 'The DNS record for ' . $d['domain'] . ' was not found yet. DNS changes can take up to an hour; try again later.');
        }
        $this->db->where('id', (int) $d['id'])->update('platform_school_domains', array('verified_at' => date('Y-m-d H:i:s')));
        $this->platform_audit->record('domain.verify', 'Verified the domain ' . $d['domain'] . ' for ' . $school['school_name'], $school['id']);
        $this->back($school['id'], $d['domain'] . ' is verified and serves the school (it still needs a TLS certificate on the server for https).');
    }

    /** Uses a verified domain in links and emails instead of the subdomain (or stops doing so). */
    public function primary_domain($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        $d = $this->domain_row($school, (int) $this->input->post('domain_id'));
        if (!$d['verified_at']) {
            $this->back($school['id'], null, 'Verify the domain first.');
        }
        $on = !(int) $d['is_primary'];
        $this->db->where('school_ref', (int) $school['id'])->update('platform_school_domains', array('is_primary' => 0));
        if ($on) {
            $this->db->where('id', (int) $d['id'])->update('platform_school_domains', array('is_primary' => 1));
        }
        $this->platform_audit->record('domain.primary', ($on ? 'Made ' . $d['domain'] . ' the main address of ' : 'Made the subdomain the main address of ') . $school['school_name'], $school['id']);
        $this->back($school['id'], $on ? $d['domain'] . ' is now used in links and emails.' : 'Links and emails use the subdomain again.');
    }

    public function remove_domain($id = null)
    {
        $this->require_privilege('can_edit');
        $school = $this->checked_post($id);
        $d = $this->domain_row($school, (int) $this->input->post('domain_id'));
        $this->db->where('id', (int) $d['id'])->delete('platform_school_domains');
        $this->platform_audit->record('domain.remove', 'Removed the domain ' . $d['domain'] . ' from ' . $school['school_name'], $school['id']);
        $this->back($school['id'], 'Removed ' . $d['domain'] . '; it no longer opens the school.');
    }

    private function domain_row(array $school, $domain_id)
    {
        $d = $this->db->where(array('id' => $domain_id, 'school_ref' => (int) $school['id']))->get('platform_school_domains')->row_array();
        if (!$d) {
            $this->back($school['id'], null, 'Domain not found.');
        }
        return $d;
    }

    // ------------------------------------------------------------------ validation callbacks

    /** Form validation: an empty subdomain may be set later; a non-empty one must be usable. */
    public function check_subdomain($subdomain, $school_id)
    {
        if ($subdomain === '' || $subdomain === null) {
            return true;
        }
        $error = $this->school_provisioner->subdomain_error($subdomain, (int) $school_id ?: null);
        if ($error !== null) {
            $this->form_validation->set_message('check_subdomain', $error);
            return false;
        }
        return true;
    }

    public function check_website($website)
    {
        if ($website === '' || $website === null || preg_match('~^https?://[^\s/$.?#].[^\s]*$~i', $website)) {
            return true;
        }
        $this->form_validation->set_message('check_website', 'Enter the website as a full address, e.g. https://www.school.com');
        return false;
    }

    // ------------------------------------------------------------------ helpers

    private function menu()
    {
        $this->session->set_userdata('top_menu', 'school_information');
        $this->session->set_userdata('sub_menu', 'admin/schools');
    }

    private function require_privilege($action)
    {
        if (!$this->rbac->hasPrivilege(self::PERMISSION, $action)) {
            access_denied();
        }
    }

    private function school_or_404($id)
    {
        $school = ctype_digit((string) $id) ? $this->Platform_school_model->get((int) $id) : null;
        if (!$school) {
            show_404();
        }
        return $school;
    }

    private function site_url_of(array $school)
    {
        if (!empty($school['is_platform'])) {
            return $this->school_provisioner->url_for($school);
        }
        // Its primary custom domain when it has one, else its subdomain.
        return $school['subdomain'] ? $this->school_provisioner->url_for($school) : null;
    }

    /** A POST from a school page with its form token. */
    private function checked_post($id)
    {
        $school = $this->school_or_404($id);
        $token = (string) $this->input->post('form_token');
        $expected = (string) $this->session->userdata('schools_form_token');
        if ($this->input->method() !== 'post' || $expected === '' || !hash_equals($expected, $token)) {
            show_error('This form has expired. Open the school again and retry.', 403, 'Request refused');
        }
        return $school;
    }

    /** Per-session token for the action forms (the application's CSRF protection is off). */
    private function form_token()
    {
        $token = (string) $this->session->userdata('schools_form_token');
        if ($token === '') {
            $token = bin2hex(random_bytes(20));
            $this->session->set_userdata('schools_form_token', $token);
        }
        return $token;
    }

    private function back($id, $message = null, $error = null)
    {
        if ($message !== null) {
            $this->session->set_flashdata('schools_msg', $message);
        }
        if ($error !== null) {
            $this->session->set_flashdata('schools_error', $error);
        }
        redirect('admin/schools/view/' . (int) $id);
    }

    /** Emails a one-time set-password link (template school_ready or password_link). */
    private function send_access_email($to, array $school, $url, $new_school)
    {
        $site = preg_replace('~site/admin_resetpassword/.*$~', '', $url);
        return $this->platform_mailer->send($new_school ? 'school_ready' : 'password_link', $to, array(
            'school_name' => $school['school_name'],
            'school_url' => $site,
            'email' => $to,
            'set_password_url' => $url,
            'login_url' => $site . 'site/login',
        ), $school['id']);
    }
}
