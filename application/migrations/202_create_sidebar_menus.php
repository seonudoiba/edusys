<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `sidebar_menus`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: permission_group, school.
 */
class Migration_Create_sidebar_menus extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `sidebar_menus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `permission_group_id` int DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `menu` varchar(500) DEFAULT NULL,
  `activate_menu` varchar(100) DEFAULT NULL,
  `lang_key` varchar(250) NOT NULL,
  `system_level` int DEFAULT '0',
  `level` int DEFAULT NULL,
  `sidebar_display` int DEFAULT '0',
  `access_permissions` text,
  `is_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `permission_group_id` (`permission_group_id`),
  KEY `idx_sidebar_menus_school_id` (`school_id`),
  CONSTRAINT `sidebar_menus_ibfk_1` FOREIGN KEY (`permission_group_id`) REFERENCES `permission_group` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_sidebar_menus_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `sidebar_menus`');
    }
}
