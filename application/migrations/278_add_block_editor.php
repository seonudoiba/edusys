<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The block editor for school websites:
 *   front_cms_page_revisions  earlier versions of a page's layout, to look at and restore
 *   front_cms_block_patterns  blocks a school saved to reuse on other pages
 * Layouts themselves stay in front_cms_pages.builder_layout / builder_draft. Older layouts (sections of
 * widgets) are converted when read, so no page needs converting here.
 */
class Migration_Add_block_editor extends CI_Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `front_cms_page_revisions` (
            `id` int NOT NULL AUTO_INCREMENT,
            `school_id` int NOT NULL,
            `page_id` int NOT NULL,
            `layout` longtext NOT NULL,
            `kind` varchar(20) NOT NULL DEFAULT 'draft',
            `staff_id` int DEFAULT NULL,
            `created_at` datetime NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_front_cms_page_revisions_page` (`school_id`, `page_id`, `id`),
            CONSTRAINT `fk_front_cms_page_revisions_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"); // tenancy-lint: ok schema change
        $this->db->query("CREATE TABLE IF NOT EXISTS `front_cms_block_patterns` (
            `id` int NOT NULL AUTO_INCREMENT,
            `school_id` int NOT NULL,
            `title` varchar(120) NOT NULL,
            `blocks` longtext NOT NULL,
            `staff_id` int DEFAULT NULL,
            `created_at` datetime NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_front_cms_block_patterns_school` (`school_id`),
            CONSTRAINT `fk_front_cms_block_patterns_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"); // tenancy-lint: ok schema change
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `front_cms_block_patterns`'); // tenancy-lint: ok schema change
        $this->db->query('DROP TABLE IF EXISTS `front_cms_page_revisions`'); // tenancy-lint: ok schema change
    }
}
