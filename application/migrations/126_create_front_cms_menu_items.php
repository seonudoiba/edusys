<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `front_cms_menu_items`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: front_cms_menus, school.
 */
class Migration_Create_front_cms_menu_items extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `front_cms_menu_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `menu_id` int NOT NULL,
  `menu` varchar(100) DEFAULT NULL,
  `page_id` int NOT NULL,
  `parent_id` int NOT NULL,
  `ext_url` text,
  `open_new_tab` int DEFAULT '0',
  `ext_url_link` text,
  `slug` varchar(200) DEFAULT NULL,
  `weight` int DEFAULT NULL,
  `publish` int NOT NULL DEFAULT '0',
  `description` text,
  `is_active` varchar(10) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  KEY `idx_front_cms_menu_items_school_id` (`school_id`),
  CONSTRAINT `front_cms_menu_items_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `front_cms_menus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_front_cms_menu_items_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `front_cms_menu_items`');
    }
}
