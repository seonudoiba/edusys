<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class schools extends Admin_Controller
{
    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();

        $this->load->model('School_model');
        $this->load->model('Subscription_model');
        $this->load->model('course_model');
        $this->load->model('Studentcourse_model');
        $this->load->model('setting_model');
        $this->load->model('Guest_model');
        $this->load->library('Enc_lib');
    }
    public function index()
    {
        $data['title'] = 'School List';
        $schools_data = $this->School_model->get();
        $data['schools_data'] = $schools_data;
        foreach ($data['schools_data'] as &$datas) {
            if (isset($datas['created_at']) && isset($datas['days'])) {
                try {
                    $date = new DateTime($datas['created_at']);

                    $days_to_add = (int) $datas['days'];

                    $date->modify("+$days_to_add days");

                    $datas['expiry_date'] = $date->format('Y-m-d H:i:s');
                    
                    $currentDate = new DateTime();
                    $futureDate = new DateTime($datas['expiry_date']);
                    $interval = $currentDate->diff($futureDate);
                    $remainingDays = $interval->days;
                    if ($currentDate == $futureDate) {
                        $remainingDays = 0;
                    } else {
                        $remainingDays += 1;
                    }
                    
                    $datas['remainingDays'] = $remainingDays;


                } catch (Exception $e) {
                    error_log('Error in processing date for school ID ' . $datas['id'] . ': ' . $e->getMessage());
                    $datas['expiry_date'] = null;
                }
            } else {
                error_log('Missing data for school ID ' . $datas['id']);
                $datas['expiry_date'] = null;
                $datas['remainingDays'] = null;
            }
        }
        $this->load->view('layout/header', $data);
        $this->load->view('admin/school/school_list', $data);
        $this->load->view('layout/footer', $data);
    }
    public function create()
    {
        $data = array();

        $this->load->view('layout/header');
        $this->load->view('admin/school/schoolform', $data);
        $this->load->view('layout/footer');
    }

    public function save($id = null)
    {
        $this->load->helper('form');

        $post_data = $this->input->post();

        $this->form_validation->set_rules('school_name', $this->lang->line('school_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('school_code', $this->lang->line('school_code'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subdomain', 'Subdomain', 'trim|strtolower|callback_check_subdomain[' . (int) $id . ']');
        $this->form_validation->set_rules('school_address', $this->lang->line('school_address'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('phone_number', $this->lang->line('phone_number'), 'trim|required|xss_clean|regex_match[/^(\+?\d{1,4}[- ]?)?\(?\d{1,4}\)?[- ]?\d{1,4}[- ]?\d{1,4}$/]');
        $this->form_validation->set_rules('email_address', $this->lang->line('email_address'), 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('website', $this->lang->line('website'), 'trim|required|regex_match[/^https?:\/\/(www\.)?[a-z0-9\-]+\.[a-z]{2,6}(\/[a-z0-9\-\/]*)*$/]');
        $this->form_validation->set_rules('principal_name', $this->lang->line('principal_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('owner_name', $this->lang->line('owner_name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'school_name' => "School name is required",
                'school_code' => "School code is required",
                'school_address' => "School address is required",
                'phone_number' => "Phone Number is required",
                'email_address' => "Email-address is required",
                'principal_name' => "Principal Name is required",
                'owner_name' => "Owner Name is required",
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

            // print_r($array);
            $this->load->view('layout/header');
            $this->load->view('admin/school/schoolform', ['errors' => $array]);
            $this->load->view('layout/footer');
        } else {
            $data = array(
                'school_name' => $post_data['school_name'],
                'school_code' => $post_data['school_code'],
                'subdomain' => ($sub = strtolower(trim((string) $this->input->post('subdomain')))) !== '' ? $sub : null,
                'school_type' => $post_data['school_type'],
                'school_category' => $post_data['school_category'],
                'school_address' => $post_data['school_address'],
                'phone_number' => $post_data['phone_number'],
                'email_address' => $post_data['email_address'],
                'website' => $post_data['website'],
                'principal_name' => $post_data['principal_name'],
                'owner_name' => $post_data['owner_name']
            );

            if ($id) {
                $this->db->where('id', $id);
                $this->db->update('school', $data);

                if ($this->db->affected_rows() > 0) {
                    $array = array('status' => 'success', 'error' => '', 'message' => 'School record updated successfully!');
                }
            } else {
                $this->db->insert('school', $data);

                if ($this->db->affected_rows() > 0) {
                    $array = array('status' => 'success', 'error' => '', 'message' => 'New school record inserted successfully!');
                } else {
                }
            }
            $this->session->set_flashdata('message', 'School record saved successfully!');

            redirect('admin/schools');
        }

    }

    public function edit($id)
    {
        $data['school'] = $this->School_model->get($id);
        if (empty($data)) {
            $this->session->set_flashdata('message', 'School not found!');
            redirect('admin/schools');
        }

        $data['title'] = 'Edit School';

        $this->load->view('layout/header', $data);
        $this->load->view('admin/school/schoolform', $data);
        $this->load->view('layout/footer');
    }

    public function delete($id)
    {

        if (empty($id) || !ctype_digit((string) $id)) {
            $this->session->set_flashdata('message', 'Invalid school ID!');
            redirect('admin/schools');
        }
        $school = $this->School_model->get($id);
        if (empty($school)) {
            $this->session->set_flashdata('message', 'School not found!');
            redirect('admin/schools');
        }
        if (!empty($school['is_platform'])) {
            $this->session->set_flashdata('message', 'The platform school cannot be deleted.');
            redirect('admin/schools');
        }
        // A school that has been set up owns data in every school table (protected by foreign keys).
        // Such a school is disabled, never deleted from here; only applications that were never set up are removed.
        $set_up = $this->db->without_tenant(function ($db) use ($id) {
            return $db->where('school_id', (int) $id)->count_all_results('sch_settings') > 0;
        });
        if ($set_up) {
            $this->session->set_flashdata('message', 'This school has been set up and has data. Disable it instead of deleting it.');
            redirect('admin/schools');
        }

        $this->Subscription_model->removebySchool($id);

        $this->Guest_model->removebySchool($id);

        $result = $this->School_model->delete($id);
        if ($result) {
            $this->session->set_flashdata('message', 'School record deleted successfully!');
        } else {
            $this->session->set_flashdata('message', 'Failed to delete school record!');
        }

        // Redirect to the schools list page
        redirect('admin/schools');
    }

    // Approve a school application (cron_flag = 2). Schools live in the shared database:
    // approval never creates a folder or a database.
    public function update_status()
    {
        $id = $this->input->post('id');
        if (!$id || !ctype_digit((string) $id)) {
            return $this->json(['status' => 'error', 'message' => 'Invalid school.']);
        }
        $this->load->library('School_provisioner');
        try {
            $result = $this->school_provisioner->provision((int) $id);
        } catch (RuntimeException $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        $mailed = $this->send_setup_email($result);
        $message = 'School set up at ' . $this->school_provisioner->school_url($result['subdomain']) . '. '
            . ($mailed ? 'A set-password link was emailed to ' . $result['admin_email'] . '.'
                       : 'The email could not be sent; give the owner this set-password link: ' . $result['set_password_url']);
        return $this->json(['status' => 'success', 'message' => $message, 'set_password_url' => $result['set_password_url']]);
    }

    private function json(array $data)
    {
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /** Emails the school owner the one-time set-password link. */
    private function send_setup_email(array $result)
    {
        $school = $this->db->where('id', $result['school_id'])->get('school')->row_array();
        $this->load->library('email');
        $this->email->initialize(array(
            'protocol' => $this->config->item('protocol'),
            'smtp_host' => $this->config->item('smtp_host'),
            'smtp_user' => $this->config->item('smtp_user'),
            'smtp_pass' => $this->config->item('smtp_pass'),
            'smtp_port' => $this->config->item('smtp_port'),
            'mailtype' => 'html',
            'charset' => $this->config->item('charset') ?: 'utf-8',
            'newline' => $this->config->item('newline') ?: "\r\n",
        ));
        $url = htmlspecialchars($result['set_password_url'], ENT_QUOTES);
        $site = htmlspecialchars($this->school_provisioner->school_url($result['subdomain']), ENT_QUOTES);
        $name = htmlspecialchars($school['school_name'], ENT_QUOTES);
        $this->email->from($this->config->item('from_email'), 'Edusys');
        $this->email->to($result['admin_email']);
        $this->email->subject('Your school is ready: ' . $school['school_name']);
        $this->email->message("<p>Hello,</p><p><b>$name</b> is set up at <a href=\"$site\">$site</a>.</p>"
            . "<p>Your super admin login is <b>" . htmlspecialchars($result['admin_email'], ENT_QUOTES) . "</b>. "
            . "Choose your password with this one-time link:</p><p><a href=\"$url\">$url</a></p>"
            . "<p>The Edusys Team</p>");
        return (bool) @$this->email->send();
    }

    /** Form validation: an empty subdomain may be set later; a non-empty one must be usable. */
    public function check_subdomain($subdomain, $school_id)
    {
        if ($subdomain === '' || $subdomain === null) {
            return true;
        }
        $this->load->library('School_provisioner');
        $error = $this->school_provisioner->subdomain_error($subdomain, (int) $school_id ?: null);
        if ($error !== null) {
            $this->form_validation->set_message('check_subdomain', $error);
            return false;
        }
        return true;
    }

    // Enable/disable a school via school.status. The tenant resolver (phase 2 of
    // SINGLE_DB_TENANCY_PLAN.md) refuses requests for disabled schools.
    public function status_update($id, $status)
    {
        $status = $status == 1 ? 0 : 1;

        $data = array(
            'status' => $status
        );

        $this->db->where('id', $id);
        $this->db->update('school', $data);

        $this->session->set_flashdata('msg', 'School record updated successfully!');

        $response = array(
            'status' => 'success',
            'msg' => 'School record updated successfully!'
        );

        echo json_encode($response);
        exit;
    }
}
