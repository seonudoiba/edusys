<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Front CMS > Pages > Edit page: the block editor (backend/js/block_editor.js).
 * Saving keeps a draft; Publish makes it what visitors see. Each save is kept as a revision.
 * See Page_widgets for the blocks.
 */
class Page_builder extends Admin_Controller
{
    const MAX_REVISIONS = 50;   // per page; older ones are removed
    const MAX_PATTERNS = 100;   // per school

    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('Page_widgets', 'Form_token'));
    }

    public function edit($id = null)
    {
        $this->require_edit();
        $page = $this->page_or_404($id);
        $this->session->set_userdata('top_menu', 'Front CMS');
        $this->session->set_userdata('sub_menu', 'admin/front/page');
        $json = $page['builder_draft'] ?: $page['builder_layout'];
        $is_platform = Tenant::is_platform();
        $this->load->view('layout/header');
        $this->load->view('admin/front/page_builder/edit', array(
            'page' => $page,
            'layout' => $json ? Page_widgets::normalize($json) : $this->from_classic($page),
            'catalogue' => Page_widgets::available($is_platform),
            'patterns' => Page_widgets::patterns($is_platform),
            'saved_patterns' => $this->saved_patterns(),
            'layouts' => Page_widgets::LAYOUTS,
            'forms_allowed' => empty($page['is_homepage']),
            'unpublished' => $page['builder_draft'] && $page['builder_draft'] !== $page['builder_layout'],
            'token' => $this->form_token->get(),
            'preview_url' => !empty($page['is_homepage']) ? site_url('?preview=1') : site_url('page/' . $page['slug'] . '?preview=1'),
        ));
        $this->load->view('layout/footer');
    }

    /**
     * Saves the layout (JSON): always as the draft; with publish=1 also what visitors see.
     * autosave=1 keeps one autosave revision per page instead of adding another.
     */
    public function save($id = null)
    {
        $this->require_edit();
        $this->form_token->require_post();
        $page = $this->page_or_404($id);
        try {
            $clean = Page_widgets::clean((string) $this->input->post('layout', false), Tenant::is_platform(), empty($page['is_homepage']));
        } catch (InvalidArgumentException $e) {
            return $this->json(array('ok' => false, 'message' => $e->getMessage()));
        }
        $publish = (bool) $this->input->post('publish');
        $autosave = !$publish && (bool) $this->input->post('autosave');
        if ($publish && ($problems = Page_widgets::publish_problems($clean))) {
            return $this->json(array('ok' => false, 'problems' => $problems, 'message' => 'Not published yet: ' . implode(' ', $problems)));
        }
        $json = json_encode($clean);
        $data = array('builder_draft' => $json);
        if ($publish) {
            $data['builder_layout'] = $json;
        }
        $this->db->where('id', (int) $page['id'])->update('front_cms_pages', $data);
        $this->add_revision((int) $page['id'], $json, $publish ? 'publish' : ($autosave ? 'autosave' : 'draft'));
        $this->json(array('ok' => true, 'published' => $publish, 'layout' => $clean, 'saved_at' => date('H:i'),
            'message' => $publish ? 'Published: visitors now see this page.' : ($autosave ? 'Draft saved automatically.' : 'Draft saved. Visitors still see the published version.')));
    }

    /** Previews of blocks for the editor: {"blocks":[...]} -> {"html":[one per block]}. */
    public function render()
    {
        $this->require_edit();
        $this->form_token->require_post();
        $blocks = json_decode((string) $this->input->post('blocks', false), true);
        $html = array();
        foreach (array_slice(is_array($blocks) ? $blocks : array(), 0, 60) as $b) {
            try {
                $clean = Page_widgets::clean(array('v' => 2, 'blocks' => array($b)), Tenant::is_platform(), true);
            } catch (InvalidArgumentException $e) {
                $clean = array('blocks' => array());
            }
            $out = $clean['blocks'] ? $this->page_widgets->render_blocks($clean['blocks'], true) : '';
            // The real form is only built on the page itself.
            $html[] = preg_replace('/\[form-builder:[a-z_]+\]/', '<div class="pw-empty">The form appears here on the page.</div>', $out);
        }
        $this->json(array('ok' => true, 'html' => $html));
    }

    /** The page's earlier versions, newest first. */
    public function revisions($id = null)
    {
        $this->require_edit();
        $page = $this->page_or_404($id);
        $rows = $this->db->select('front_cms_page_revisions.id, front_cms_page_revisions.kind, front_cms_page_revisions.created_at, staff.name, staff.surname')
            ->from('front_cms_page_revisions')->join('staff', 'staff.id = front_cms_page_revisions.staff_id', 'left')
            ->where('front_cms_page_revisions.page_id', (int) $page['id'])->order_by('front_cms_page_revisions.id', 'desc')->get()->result_array();
        $labels = array('publish' => 'Published', 'draft' => 'Draft saved', 'autosave' => 'Saved automatically');
        $out = array();
        foreach ($rows as $r) {
            $out[] = array('id' => (int) $r['id'], 'what' => isset($labels[$r['kind']]) ? $labels[$r['kind']] : $r['kind'],
                'when' => date('j M Y, H:i', strtotime($r['created_at'])), 'who' => trim($r['name'] . ' ' . $r['surname']));
        }
        $this->json(array('ok' => true, 'revisions' => $out));
    }

    /** One earlier version, to load into the editor (it becomes the draft only when saved). */
    public function revision($id = null, $revision_id = null)
    {
        $this->require_edit();
        $page = $this->page_or_404($id);
        $row = $this->db->where(array('id' => (int) $revision_id, 'page_id' => (int) $page['id']))->get('front_cms_page_revisions')->row_array();
        if (!$row) {
            return $this->json(array('ok' => false, 'message' => 'That version no longer exists.'));
        }
        $this->json(array('ok' => true, 'layout' => Page_widgets::normalize($row['layout'])));
    }

    /** Saves blocks as a pattern the school can insert on any page (a copy each time). */
    public function save_pattern()
    {
        $this->require_edit();
        $this->form_token->require_post();
        $title = mb_substr(trim(strip_tags((string) $this->input->post('title'))), 0, 120);
        if ($title === '') {
            return $this->json(array('ok' => false, 'message' => 'Give the pattern a name.'));
        }
        if ($this->db->count_all_results('front_cms_block_patterns') >= self::MAX_PATTERNS) {
            return $this->json(array('ok' => false, 'message' => 'You have ' . self::MAX_PATTERNS . ' saved patterns. Delete some first.'));
        }
        try {
            $clean = Page_widgets::clean(array('v' => 2, 'blocks' => json_decode((string) $this->input->post('blocks', false), true)), Tenant::is_platform(), true);
        } catch (InvalidArgumentException $e) {
            return $this->json(array('ok' => false, 'message' => $e->getMessage()));
        }
        if (!$clean['blocks']) {
            return $this->json(array('ok' => false, 'message' => 'There is nothing to save.'));
        }
        $this->db->insert('front_cms_block_patterns', array('title' => $title, 'blocks' => json_encode($clean['blocks']),
            'staff_id' => (int) $this->customlib->getStaffID(), 'created_at' => date('Y-m-d H:i:s')));
        $this->json(array('ok' => true, 'patterns' => $this->saved_patterns(), 'message' => 'Saved as "' . $title . '". Find it under Patterns.'));
    }

    public function delete_pattern($pattern_id = null)
    {
        $this->require_edit();
        $this->form_token->require_post();
        $this->db->where('id', (int) $pattern_id)->delete('front_cms_block_patterns');
        $this->json(array('ok' => true, 'patterns' => $this->saved_patterns()));
    }

    /** Back to the page's plain text editor content (its text was kept). */
    public function classic($id = null)
    {
        $this->require_edit();
        $this->form_token->require_post();
        $page = $this->page_or_404($id);
        $this->db->where('id', (int) $page['id'])->update('front_cms_pages', array('builder_layout' => null, 'builder_draft' => null));
        $this->session->set_flashdata('msg', '<div class="alert alert-success">This page uses its plain text content again.</div>');
        redirect('admin/front/page');
    }

    /** Who appears in the public Staff directory block (nobody by default). */
    public function staff()
    {
        $this->require_edit();
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

    /**
     * A page written in the plain text editor opens with its text as a block, and its form (if it
     * had one) as a form block, so nothing is lost when a school switches to blocks.
     */
    private function from_classic(array $page)
    {
        $html = (string) $page['description'];
        $blocks = array();
        $text = Page_widgets::clean_html(preg_replace('/\[form-builder:[a-z_]+\]/', '', $html));
        if (trim(strip_tags($text)) !== '') {
            $blocks[] = array('type' => 'text', 'attrs' => array('title' => '', 'body' => $text, 'align' => 'left'));
        }
        if (empty($page['is_homepage']) && preg_match('/\[form-builder:([a-z_]+)\]/', $html, $m)) {
            $type = array_search($m[1], Page_widgets::FORMS, true);
            if ($type) {
                $blocks[] = array('type' => $type, 'attrs' => array('title' => ''));
            }
        }
        return array('v' => 2, 'blocks' => $blocks);
    }

    private function add_revision($page_id, $json, $kind)
    {
        $last = $this->db->select('id, layout, kind')->where('page_id', $page_id)->order_by('id', 'desc')->limit(1)->get('front_cms_page_revisions')->row_array();
        if ($last && $last['layout'] === $json && ($kind !== 'publish' || $last['kind'] === 'publish')) {
            return; // nothing changed since the last version
        }
        if ($kind === 'autosave' && $last && $last['kind'] === 'autosave') {
            $this->db->where('id', (int) $last['id'])->update('front_cms_page_revisions', array('layout' => $json, 'created_at' => date('Y-m-d H:i:s'),
                'staff_id' => (int) $this->customlib->getStaffID()));
            return;
        }
        $this->db->insert('front_cms_page_revisions', array('page_id' => $page_id, 'layout' => $json, 'kind' => $kind,
            'staff_id' => (int) $this->customlib->getStaffID(), 'created_at' => date('Y-m-d H:i:s')));
        $keep = $this->db->select('id')->where('page_id', $page_id)->order_by('id', 'desc')->limit(1, self::MAX_REVISIONS - 1)->get('front_cms_page_revisions')->row();
        if ($keep) {
            $this->db->where('page_id', $page_id)->where('id <', (int) $keep->id)->delete('front_cms_page_revisions');
        }
    }

    private function saved_patterns()
    {
        $out = array();
        foreach ($this->db->order_by('title', 'asc')->get('front_cms_block_patterns')->result_array() as $r) {
            $out[] = array('id' => (int) $r['id'], 'title' => $r['title'], 'blocks' => json_decode($r['blocks'], true));
        }
        return $out;
    }

    private function require_edit()
    {
        if (!$this->rbac->hasPrivilege('pages', 'can_edit')) {
            access_denied();
        }
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
