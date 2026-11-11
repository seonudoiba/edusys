<?php

define('THEMES_DIR', 'themes');
define('BASE_URI', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));

class MY_Controller extends CI_Controller
{

    protected $langs = array();

    public function __construct()
    {

        parent::__construct();

        $this->load->library('Db_manager');
        $this->config->load('license');
        $this->load->helper(array('language', 'directory', 'customfield', 'custom', 'mime'));
        $this->load->model(array('session_model', 'staff_model', 'section_model', 'setting_model', 'class_model', 'classsection_model', 'category_model', 'student_model', 'feemaster_model', 'feecategory_model', 'feetype_model', 'studentfee_model', 'stuattendence_model', 'attendencetype_model', 'studentsession_model', 'language_model', 'admin_model', 'smsconfig_model', 'langpharses_model', 'subject_model', 'teacher_model', 'teachersubject_model', 'exam_model', 'mark_model', 'examschedule_model', 'examresult_model', 'expense_model', 'expensehead_model', 'studenttransportfee_model', 'book_model', 'grade_model', 'timetable_model', 'hostel_model', 'route_model', 'content_model', 'user_model', 'notification_model', 'paymentsetting_model', 'payroll_model', 'roomtype_model', 'department_model', 'designation_model', 'hostelroom_model', 'vehicle_model', 'vehroute_model', 'librarian_model', 'accountant_model', 'homework_model', 'librarymanagement_model', 'librarymember_model', 'bookissue_model', 'feegroup_model', 'feegrouptype_model', 'feesessiongroup_model', 'studentfeemaster_model', 'feediscount_model', 'emailconfig_model', 'income_model', 'incomehead_model', 'itemcategory_model', 'schoolhouse_model', 'item_model', 'messages_model', 'itemstore_model', 'itemsupplier_model', 'notificationsetting_model', 'itemstock_model', 'itemissue_model', 'userlog_model', 'cms_program_model', 'cms_menu_model', 'cms_media_model', 'cms_page_model', 'cms_menuitems_model', 'cms_page_content_model', 'role_model', 'calendar_model', 'userpermission_model', 'staffroles_model', 'staffattendancemodel', 'rolepermission_model', 'Certificate_model', 'classteacher_model', 'Generatecertificate_model', 'Student_id_card_model', 'timeline_model', 'Generateidcard_model', 'Module_model', 'subjectgroup_model', 'studentsubjectgroup_model', 'subjecttimetable_model', 'studentsubjectattendence_model', 'audit_model', 'Chat_model', 'apply_leave_model', 'disable_reason_model', 'question_model', 'leavetypes_model', 'alumni_model', 'lessonplan_model', 'syllabus_model', 'Staffidcard_model', 'Generatestaffidcard_model', 'visitors_model', 'video_tutorial_model'));
        $this->load->model(array('customfield_model', 'onlinestudent_model', 'houselist_model', 'onlineexam_model', 'onlineexamquestion_model', 'onlineexamresult_model', 'examstudent_model', 'admitcard_model', 'marksheet_model', 'chatuser_model', 'examgroupstudent_model', 'examgroup_model', 'batchsubject_model', 'filetype_model', 'currency_model', 'examsubject_model', 'feereminder_model'));
        $this->load->library(array('Role', 'Smsgateway', 'QDMailer', 'Adler32', 'Aes'));
        $this->load->library(array('auth', 'module_lib', 'pushnotification', 'jsonlib'));
        $this->load->library(array('version_control'));

        if ($this->session->has_userdata('admin')) {

            $admin = $this->session->userdata('admin');
            $language = ($admin['language']['language']);
        } else if ($this->session->has_userdata('student')) {

            $student = $this->session->userdata('student');

            $language = ($student['language']['language']);
        } else {
            $this->school_details = $this->setting_model->getSchoolDetail();
            $language = ($this->school_details->language);
        }

        $this->config->set_item('language', $language);
        $lang_array = array('form_validation_lang');
        $map = directory_map(APPPATH . "./language/" . $language . "/app_files");
        foreach ($map as $lang_key => $lang_value) {
            $lang_array[] = 'app_files/' . str_replace(".php", "", $lang_value);
        }

        $this->load->language($lang_array, $language);
    }

}

/** A student, parent or school owner still on a password someone else knows goes to the change-password page. */
function require_own_password($CI)
{
    $user = $CI->session->userdata('student');
    if (empty($user['id'])) {
        return;
    }
    $class = $CI->router->fetch_class();
    $method = $CI->router->fetch_method();
    $guest = isset($user['role']) && $user['role'] === 'guest';
    if ($guest && $CI->session->userdata('billing_from_school')) {
        return; // a school's super admin opened billing from the admin panel (course/from_school)
    }
    if ($guest ?($class === 'studentcourse' && in_array($method, array('changeguestpass', 'updateguestpass'), true)) : ($class === 'user' && in_array($method, array('changepass', 'choose'), true))) {
        return;
    }
    $row = $CI->db->select('must_change_password')->where('id', (int) $user['id'])->get($guest ? 'guest' : 'users')->row();
    if ($row && (int) $row->must_change_password === 1) {
        $CI->session->set_flashdata('msg', '<div class="alert alert-info">Please choose your own password to continue.</div>');
        redirect($guest ? 'user/studentcourse/changeguestpass' : 'user/user/changepass');
    }
}

class Admin_Controller extends MY_Controller
{

    protected $aaaa = false;

    public function __construct()
    {
        parent::__construct();
        $this->auth->is_logged_in();
        $this->require_own_staff_password();
        $this->check_license();
        $this->load->library('rbac');
        $this->config->load('app-config');
        $this->config->load('ci-blog');
        $this->config->load('custom_filed-config');
        $this->guard_action();
    }

    /**
     * Two checks before an admin action runs:
     *   - an action that changes data by its name (delete, remove, status, enable...) is refused when
     *     the request comes from another website, so a link planted elsewhere can't trigger it for a
     *     signed-in staff member (the browser's Sec-Fetch-Site, or else the Referer);
     *   - actions listed in config/permission_guards.php need that permission (the action falls back
     *     to "can_edit" when the category has no such action, e.g. no delete).
     */
    private function guard_action()
    {
        $method = strtolower((string) $this->router->fetch_method());
        if (preg_match('/^(delete|remove|bulkdelete|disable|enable|toggle|change_?status|status|approve|reject|revert|inactive|active|deactivate|activate|purge|reset)/', $method)) {
            $site = strtolower((string) $this->input->server('HTTP_SEC_FETCH_SITE'));
            $cross = $site === 'cross-site';
            if ($site === '') {
                $referer = (string) $this->input->server('HTTP_REFERER');
                $host = strtolower((string) parse_url($referer, PHP_URL_HOST));
                $cross = $referer !== '' && $host !== '' && $host !== strtolower((string) $this->input->server('HTTP_HOST'))
                    && $host !== strtolower((string) parse_url('http://' . $this->input->server('HTTP_HOST'), PHP_URL_HOST));
            }
            if ($cross) {
                log_message('error', 'Refused cross-site request to ' . uri_string());
                show_error('This action must be started from within EduSYS. Open the page and try again.', 403, 'Not allowed');
            }
        }
        $guards = $this->config->item('permission_guards');
        if ($guards === null) {
            $this->config->load('permission_guards', false, true);
            $guards = (array) $this->config->item('permission_guards');
        }
        $key = strtolower((string) $this->router->fetch_directory() . $this->router->fetch_class() . '/' . $method);
        if (!isset($guards[$key])) {
            return;
        }
        list($category, $action) = $guards[$key];
        if ($this->rbac->hasPrivilege($category, $action)) {
            return;
        }
        if ($action !== 'can_edit' && $action !== 'can_view') {
            // A category without that action (no "delete" column offered) uses "edit".
            $field = 'enable_' . substr($action, 4);
            $cat = $this->db->select($field)->where('short_code', $category)->get('permission_category')->row_array();
            if ($cat && empty($cat[$field]) && $this->rbac->hasPrivilege($category, 'can_edit')) {
                return;
            }
        }
        access_denied();
    }

    public function check_license()
    {

        $license = $this->config->item('SSLK');

        if (!Tenant::is_platform()) {
            return;
        }
        if (!empty($license)) {

            $regex = "/^[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}-/";

            if (preg_match($regex, $license)) {
                $valid_string = $this->aes->validchk('encrypt', base_url());

                if (strpos($license, $valid_string) !== false) {

                    true; //valid
                } else {
                    $this->update_ss_routine();
                }
            } else {

                $this->update_ss_routine();
            }
        }
    }

    public function update_ss_routine()
    {

        $license = $this->config->item('SSLK');
        $fname = APPPATH . 'config/license.php';
        $update_handle = fopen($fname, "r");
        $content = fread($update_handle, filesize($fname));
        $file_contents = str_replace('$config[\'SSLK\'] = \'' . $license . '\'', '$config[\'SSLK\'] = \'\'', $content);
        $update_handle = fopen($fname, 'w') or die("can't open file");
        if (fwrite($update_handle, $file_contents)) {

        }
        fclose($update_handle);

        $this->config->set_item('SSLK', '');
    }


    /** Staff still on the generated password choose their own. Platform support signed in as them doesn't. */
    private function require_own_staff_password()
    {
        if ($this->session->userdata('impersonated_by') || ($this->router->fetch_class() === 'admin' && $this->router->fetch_method() === 'changepass')) {
            return;
        }
        $admin = $this->session->userdata('admin');
        $row = $this->db->select('must_change_password')->where('id', (int) $admin['id'])->get('staff')->row();
        if ($row && (int) $row->must_change_password === 1) {
            $this->session->set_flashdata('msg', '<div class="alert alert-info">Please choose your own password to continue.</div>');
            redirect('admin/admin/changepass');
        }
    }
}

class Student_Controller extends MY_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->school_details = $this->setting_model->getSchoolDetail();
        if ($this->school_details->maintenance_mode) {
            echo $this->load->view('maintenance', '', true);
            exit;
        }

        $this->load->library('studentmodule_lib');
        $this->load->library('cart');
        $this->config->load('app-config');
        $this->auth->is_logged_in_user('student');
        require_own_password($this);
        $is_lock_panel = check_lock_enabled();

        if ($is_lock_panel) {

            $active_class = $this->router->fetch_class();
            $active_method = $this->router->fetch_method();
            if (
                ($active_class == "user" && (
                    $active_method == "fees"
                    || $active_method == "getcollectfee"
                    || $active_method == "change_currency"
                    || $active_method == "user_language"
                    || $active_method == "changepass"

                ))

                || ($active_class == "studentcourse" && in_array($active_method, array("changeguestpass", "updateguestpass"), true))

                || ($active_class == "offlinepayment" && (
                    $active_method == "index"

                ))
            ) {

            } else {
                redirect('user/user/fees');
            }

        }

    }

}

class Studentgateway_Controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('studentmodule_lib');
        $this->config->load('app-config');
        $this->auth->is_logged_in_user('student');
        require_own_password($this);

    }

}

class Public_Controller extends MY_Controller
{

    public function __construct()
    {

        parent::__construct();
        $active_class = $this->router->fetch_class();
        $active_method = $this->router->fetch_method();

        if (($active_class == "site" || $active_class == "gauthenticate") && ($active_method == "userlogin")) {

            $this->school_details = $this->setting_model->getSchoolDetail();
            if ($this->school_details->maintenance_mode) {
                echo $this->load->view('maintenance', '', true);
                exit;
            }

        }

    }

}

class OnlineAdmission_Controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('custom');
    }

}

class Parent_Controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->auth->is_logged_in_user('parent');
        require_own_password($this);
        $this->config->load('app-config');
        $this->load->library('parentmodule_lib');
    }

}

class Front_Controller extends CI_Controller
{

    protected $data = array();
    protected $school_details = array();
    protected $parent_menu = '';
    protected $page_title = '';
    protected $theme_path = '';
    protected $front_setting = '';

    public function __construct()
    {

        parent::__construct();
        $this->check_installation();
        $this->load->database();
        $this->load->library(array('Smsgateway', 'QDMailer'));
        $this->load->model(array('setting_model', 'language_model', 'Module_model', 'cms_program_model', 'cms_menu_model', 'cms_menuitems_model', 'cms_page_model', 'cms_page_content_model', 'class_model', 'category_model', 'notificationsetting_model'));

        if ($this->config->item('installed') == true) {

            $this->db->reconnect();
        }
        $this->load->helper('language');
        $this->school_details = $this->setting_model->getSchoolDetail();
        $this->customlib->initFrontSession();

        if ($this->school_details->maintenance_mode) {
            echo $this->load->view('maintenance', '', true);
            exit;
        }

        $this->load->model('frontcms_setting_model');

        $this->front_setting = $this->frontcms_setting_model->get();

        $front_cms_class = $this->router->fetch_class();
        $front_cms_method = $this->router->fetch_method();
        $website_on = $this->front_setting && $this->front_setting->is_active_front_cms;
        $this->config->set_item('front_layout', (bool) $website_on);

        if (!$website_on) {
            // With the website off, only online admission stays reachable (when it is open);
            // everything else gets a "website not available" page with the admission and login links.
            $admission_pages = array(
                'welcome' => array('admission', 'editonlineadmission', 'online_admission_review', 'getsections',
                    'submitadmission', 'checkadmissionstatus', 'admission_bank_transfer', 'setsitecookies'),
                'checkout' => array('index', 'successinvoice', 'paymentfailed'),
            );
            $class = strtolower((string) $front_cms_class);
            $allowed = $this->front_setting && $this->school_details->online_admission
                && isset($admission_pages[$class]) && in_array(strtolower((string) $front_cms_method), $admission_pages[$class], true);
            if (!$allowed) {
                $this->website_off_page();
            }
        }
        $this->theme_path = $this->front_setting->theme;
        //================
        $language = ($this->school_details->language);
        $this->config->set_item('language', $language);
        $this->load->helper(array('directory', 'custom'));
        $lang_array = array('form_validation_lang');
        $map = directory_map(APPPATH . "./language/" . $language . "/app_files");
        foreach ($map as $lang_key => $lang_value) {
            $lang_array[] = 'app_files/' . str_replace(".php", "", $lang_value);
        }

        $this->load->language($lang_array, $language);
        //===============

        $this->load->config('ci-blog');
    }

    /** The "website not available" page (website switched off), then stop. */
    private function website_off_page()
    {
        $setting = $this->setting_model->get();
        $school = $setting ? $setting[0] : array();
        echo $this->load->view('website_off', array(
            'school_name' => isset($school['name']) ? $school['name'] : '',
            'admission' => $this->front_setting && !empty($this->school_details->online_admission),
            'user_login' => !empty($school['student_panel_login']),
        ), true);
        exit;
    }

    protected function load_theme($content = null, $layout = true)
    {
        // School websites: every theme is a preset of one design (views/themes/modern, Site_theme).
        // Views it doesn't have (admission forms, online courses, exam results) come from material_pink.
        $this->load->library('Site_theme');
        $content_theme = $this->theme_path;
        if ($this->site_theme->is_theme($this->theme_path)) {
            $override = null;
            $preview = $this->session->userdata('site_theme_preview');
            if ($this->input->get('st_preview') && $this->session->userdata('admin') && is_array($preview)) {
                $override = $preview; // an admin trying unsaved choices on Website design
            }
            $st = $this->site_theme->options($this->front_setting, $override);
            $this->data['st'] = $st;
            $this->data['st_css'] = $this->site_theme->css($st);
            $this->data['st_fonts_url'] = $this->site_theme->fonts_url($st);
            $this->data['st_classes'] = $this->site_theme->body_classes($st);
            $this->theme_path = Site_theme::ENGINE;
            $content_theme = Site_theme::ENGINE;
            if ($content !== null && !is_file(VIEWPATH . THEMES_DIR . '/' . Site_theme::ENGINE . '/' . $content . '.php')) {
                $content_theme = 'material_pink';
            }
            $this->base_assets_url = 'backend/' . THEMES_DIR . '/material_pink/';
        } elseif ($content !== null && !is_file(VIEWPATH . THEMES_DIR . '/' . $this->theme_path . '/' . $content . '.php')) {
            // The platform theme only has its public pages (the course pages keep the standard design).
            $this->theme_path = 'material_pink';
            $content_theme = 'material_pink';
        }
        $this->data['main_menus'] = '';
        $this->data['school_setting'] = $this->school_details;
        $this->data['front_setting'] = $this->front_setting;
        $menu_list = $this->cms_menu_model->getBySlug('main-menu');

        $footer_menu_list = $this->cms_menu_model->getBySlug('bottom-menu');
        if (count($menu_list) > 0) {
            $this->data['main_menus'] = $this->cms_menuitems_model->getMenus($menu_list['id']);
        }

        if (count($footer_menu_list) > 0) {
            $this->data['footer_menus'] = $this->cms_menuitems_model->getMenus($footer_menu_list['id']);
        }
        $this->data['layout_type'] = $layout;
        if (!isset($this->base_assets_url) || $this->theme_path !== Site_theme::ENGINE) {
            $this->base_assets_url = 'backend/' . THEMES_DIR . '/' . $this->theme_path . '/';
        }
        $this->data['base_assets_url'] = base_url() . $this->base_assets_url;
        $this->data['header'] = $this->load->view('themes/' . $this->theme_path . '/header', $this->data, true);
        $this->data['slider'] = $this->load->view('themes/' . $this->theme_path . '/home_slider', $this->data, true);
        $this->data['footer'] = $this->load->view('themes/' . $this->theme_path . '/footer', $this->data, true);
        $this->data["is_captcha"] = $this->captchalib->is_captcha('admission');
        $this->data['content'] = is_null($content) ? '' : $this->load->view(THEMES_DIR . '/' . $content_theme . '/' . $content, $this->data, true);
        $this->load->view(THEMES_DIR . '/' . $this->theme_path . '/' . ($layout == true ? 'layout' : 'base_layout'), $this->data);
    }

    protected function load_theme_form($content = null, $layout = true)
    {

        $this->data['main_menus'] = '';
        $this->data['school_setting'] = $this->school_details;
        $this->data['front_setting'] = $this->front_setting;
        $menu_list = $this->cms_menu_model->getBySlug('main-menu');
        $footer_menu_list = $this->cms_menu_model->getBySlug('bottom-menu');
        if (count($menu_list > 0)) {
            $this->data['main_menus'] = $this->cms_menuitems_model->getMenus($menu_list['id']);
        }

        if (count($footer_menu_list > 0)) {
            $this->data['footer_menus'] = $this->cms_menuitems_model->getMenus($footer_menu_list['id']);
        }
        $this->data['header'] = $this->load->view('themes/' . $this->theme_path . '/header', $this->data, true);

        $this->data['slider'] = $this->load->view('themes/' . $this->theme_path . '/home_slider', $this->data, true);

        $this->data['footer'] = $this->load->view('themes/' . $this->theme_path . '/footer', $this->data, true);

        $this->base_assets_url = 'backend/' . THEMES_DIR . '/' . $this->theme_path . '/';

        $this->data['base_assets_url'] = BASE_URI . $this->base_assets_url;

        $this->data['content'] = (is_null($content)) ? '' : $this->load->view(THEMES_DIR . '/' . $this->theme_path . '/' . $content, $this->data, true);
        $this->load->view(THEMES_DIR . '/' . $this->theme_path . '/layout', $this->data);

    }

    private function check_installation()
    {

        if ($this->uri->segment(1) !== 'install') {
            $this->load->config('migration');
            if ($this->config->item('installed') == false && $this->config->item('migration_enabled') == false) {
                redirect(base_url() . 'install/start');
            } else {
                if (is_dir(APPPATH . 'controllers/install')) {
                    echo '<h3>Delete the install folder from application/controllers/install</h3>';
                    die;
                }
            }
        }
    }

}
