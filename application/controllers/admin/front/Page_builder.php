<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Front CMS > Pages > Page builder: a page as sections of widgets, dragged into place.
 * Saving keeps a draft; Publish makes it what visitors see. See Page_widgets.
 */
class Page_builder extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('Page_widgets', 'Form_token'));
    }

    public function edit($id = null)
    {
        if (!$this->rbac->hasPrivilege('pages', 'can_edit')) {
            access_denied();
        }
        $page = $this->page_or_404($id);
        $this->session->set_userdata('top_menu', 'Front CMS');
        $this->session->set_userdata('sub_menu', 'admin/front/page');
        $json = $page['builder_draft'] ?: $page['builder_layout'];
        $is_platform = Tenant::is_platform();
        $this->load->view('layout/header');
        $this->load->view('admin/front/page_builder/edit', array(
            'page' => $page,
            'layout' => $json ? json_decode($json, true) : array('sections' => array()),
            'catalogue' => Page_widgets::available($is_platform),
            'layouts' => array_keys(Page_widgets::LAYOUTS),
            'backgrounds' => Page_widgets::BACKGROUNDS,
            'forms_allowed' => empty($page['is_homepage']),
            'unpublished' => $page['builder_draft'] && $page['builder_draft'] !== $page['builder_layout'],
            'token' => $this->form_token->get(),
            'preview_url' => !empty($page['is_homepage']) ? site_url('?preview=1') : site_url('page/' . $page['slug'] . '?preview=1'),
        ));
        $this->load->view('layout/footer');
    }

    /** Saves the layout (JSON): always as draft; with publish=1 also what visitors see. */
    public function save($id = null)
    {
        if (!$this->rbac->hasPrivilege('pages', 'can_edit')) {
            access_denied();
        }
        $this->form_token->require_post();
        $page = $this->page_or_404($id);
        try {
            $clean = Page_widgets::clean((string) $this->input->post('layout', false), Tenant::is_platform(), empty($page['is_homepage']));
        } catch (InvalidArgumentException $e) {
            return $this->json(array('ok' => false, 'message' => $e->getMessage()));
        }
        $json = json_encode($clean);
        $data = array('builder_draft' => $json);
        $publish = (bool) $this->input->post('publish');
        if ($publish) {
            $data['builder_layout'] = $json;
        }
        $this->db->where('id', (int) $page['id'])->update('front_cms_pages', $data);
        $this->json(array('ok' => true, 'published' => $publish, 'layout' => $clean,
            'message' => $publish ? 'Published: visitors now see this page.' : 'Draft saved. Visitors still see the published version.'));
    }

    /** The ready-made layout, as a new draft. */
    public function starter($id = null)
    {
        if (!$this->rbac->hasPrivilege('pages', 'can_edit')) {
            access_denied();
        }
        $this->form_token->require_post();
        $page = $this->page_or_404($id);
        $this->db->where('id', (int) $page['id'])->update('front_cms_pages', array('builder_draft' => json_encode(Page_widgets::starter_home(Tenant::is_platform()))));
        $this->session->set_flashdata('pb_msg', 'The starter layout is loaded as a draft. Publish it when you are happy.');
        redirect('admin/front/page_builder/edit/' . (int) $page['id']);
    }

    /** Back to the page's plain text editor content (its text was kept). */
    public function classic($id = null)
    {
        if (!$this->rbac->hasPrivilege('pages', 'can_edit')) {
            access_denied();
        }
        $this->form_token->require_post();
        $page = $this->page_or_404($id);
        $this->db->where('id', (int) $page['id'])->update('front_cms_pages', array('builder_layout' => null, 'builder_draft' => null));
        $this->session->set_flashdata('msg', '<div class="alert alert-success">This page uses its plain text content again.</div>');
        redirect('admin/front/page');
    }

    /** Who appears in the public Staff directory widget (nobody by default). */
    public function staff()
    {
        if (!$this->rbac->hasPrivilege('pages', 'can_edit')) {
            access_denied();
        }
        if ($this->input->method() === 'post') {
            $this->form_token->require_post();
            $ids = array_map('intval', (array) $this->input->post('show'));
            $this->db->update('staff', array('show_on_website' => 0));
            if ($ids) {
                $this->db->where_in('id', $ids)->update('staff', array('show_on_website' => 1));
            }
            $this->session->set_flashdata('pb_msg', count($ids) . ' staff shown on the website.');
            redirect('admin/front/page_builder/staff');
        }
        $this->session->set_userdata('top_menu', 'Front CMS');
        $this->session->set_userdata('sub_menu', 'admin/front/page');
        $staff = $this->db->select('staff.id, staff.name, staff.surname, staff.show_on_website, staff_designation.designation')->from('staff')
            ->join('staff_designation', 'staff_designation.id = staff.designation', 'left')
            ->where('staff.is_active', 1)->order_by('staff.name', 'asc')->get()->result_array();
        $this->load->view('layout/header');
        $this->load->view('admin/front/page_builder/staff', array('staff' => $staff, 'token' => $this->form_token->field(),
            'message' => $this->session->flashdata('pb_msg')));
        $this->load->view('layout/footer');
    }

    private function page_or_404($id)
    {
        $page = ctype_digit((string) $id) ? $this->db->where('id', (int) $id)->get('front_cms_pages')->row_array() : null;
        if (!$page) {
            show_404();
        }
        return $page;
    }

    private function json(array $data)
    {
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}
