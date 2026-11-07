<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Front CMS > Website design: choose a theme, then change its colours, fonts, header, banner and
 * footer, with a live preview of the school's own website. See libraries/Site_theme.php.
 */
class Design extends Admin_Controller
{
    const PREVIEW_KEY = 'site_theme_preview';

    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('Site_theme', 'Form_token'));
    }

    public function index()
    {
        $this->require_privilege('can_view');
        $this->session->set_userdata('top_menu', 'Front CMS');
        $this->session->set_userdata('sub_menu', 'admin/front/design');
        $setting = $this->setting_row();
        $this->session->unset_userdata(self::PREVIEW_KEY);
        $is_platform = Tenant::is_platform();
        $this->load->view('layout/header');
        $this->load->view('admin/front/design/index', array(
            'themes' => $this->site_theme->themes(),
            'fields' => $this->site_theme->fields(),
            'fonts' => $this->site_theme->fonts(),
            'current' => $this->site_theme->options($setting),
            'uses_theme' => $this->site_theme->is_theme($setting->theme),
            'is_platform' => $is_platform,
            'can_edit' => $this->rbac->hasPrivilege('front_cms_setting', 'can_edit'),
            'token' => $this->form_token->get(),
            'site_url' => base_url(),
            'message' => $this->session->flashdata('design_msg'),
        ));
        $this->load->view('layout/footer');
    }

    /** Remembers unsaved choices for this admin's preview (?st_preview=1 on the website). */
    public function preview()
    {
        $this->require_privilege('can_view');
        $this->form_token->require_post();
        $theme = (string) $this->input->post('theme');
        if (!$this->site_theme->is_theme($theme)) {
            return $this->json(array('ok' => false));
        }
        $options = json_decode((string) $this->input->post('options', false), true);
        $this->session->set_userdata(self::PREVIEW_KEY, array('theme' => $theme, 'options' => $this->site_theme->clean($options)));
        $this->json(array('ok' => true));
    }

    public function save()
    {
        $this->require_privilege('can_edit');
        $this->form_token->require_post();
        $theme = (string) $this->input->post('theme');
        if (!$this->site_theme->is_theme($theme)) {
            $this->session->set_flashdata('design_msg', 'Choose one of the themes.');
            redirect('admin/front/design');
        }
        $options = $this->site_theme->clean(json_decode((string) $this->input->post('options', false), true));
        $setting = $this->setting_row();
        $this->db->where('id', (int) $setting->id)->update('front_cms_settings', array(
            'theme' => $theme,
            'theme_options' => json_encode(array('theme' => $theme, 'options' => $options)),
        ));
        $this->session->unset_userdata(self::PREVIEW_KEY);
        $this->session->set_flashdata('design_msg', 'Saved. Visitors now see the new design.');
        redirect('admin/front/design');
    }

    private function setting_row()
    {
        $row = $this->db->get('front_cms_settings')->row();
        if (!$row) {
            show_error('Front CMS is not set up for this school yet.', 404);
        }
        return $row;
    }

    private function require_privilege($action)
    {
        if (!$this->rbac->hasPrivilege('front_cms_setting', $action)) {
            access_denied();
        }
    }

    private function json(array $data)
    {
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}
