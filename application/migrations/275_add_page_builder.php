<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Page builder (Front CMS > Pages > Page builder):
 *   front_cms_pages.builder_layout  the published layout (JSON); when set, it replaces `description`
 *   front_cms_pages.builder_draft   the layout being edited (JSON)
 *   staff.show_on_website           1 = listed by the public Staff directory widget (off by default)
 * Schools already set up get the starter homepage (new schools get it from School_provisioner).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_page_builder extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('builder_layout', 'front_cms_pages')) {
            $this->db->query('ALTER TABLE `front_cms_pages` ADD COLUMN `builder_layout` longtext NULL AFTER `description`, ADD COLUMN `builder_draft` longtext NULL AFTER `builder_layout`');
        }
        if (!$this->db->field_exists('show_on_website', 'staff')) {
            $this->db->query('ALTER TABLE `staff` ADD COLUMN `show_on_website` tinyint(1) NOT NULL DEFAULT 0');
        }
        require_once APPPATH . 'libraries/Page_widgets.php';
        $this->db->without_tenant(function ($db) {
            foreach ($db->select('id, is_platform')->where('cron_flag', 1)->get('school')->result() as $school) {
                $json = json_encode(Page_widgets::starter_home(!empty($school->is_platform)));
                $db->where(array('school_id' => (int) $school->id, 'is_homepage' => 1))->where('builder_layout IS NULL', null, false)
                    ->update('front_cms_pages', array('builder_layout' => $json, 'builder_draft' => $json));
            }
        });
    }

    public function down()
    {
        if ($this->db->field_exists('builder_layout', 'front_cms_pages')) {
            $this->db->query('ALTER TABLE `front_cms_pages` DROP COLUMN `builder_layout`, DROP COLUMN `builder_draft`');
        }
        if ($this->db->field_exists('show_on_website', 'staff')) {
            $this->db->query('ALTER TABLE `staff` DROP COLUMN `show_on_website`');
        }
    }
}
