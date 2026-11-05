<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `online_admission_custom_field_value`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: custom_fields, school.
 */
class Migration_Create_online_admission_custom_field_value extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `online_admission_custom_field_value` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `belong_table_id` int DEFAULT NULL,
  `custom_field_id` int DEFAULT NULL,
  `field_value` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_field_id` (`custom_field_id`),
  KEY `idx_belong_table_id` (`belong_table_id`),
  KEY `idx_field_value` (`field_value`(200)),
  KEY `idx_online_admission_custom_field_value_school_id` (`school_id`),
  CONSTRAINT `online_admission_custom_field_value_ibfk_1` FOREIGN KEY (`custom_field_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_online_admission_custom_field_value_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `online_admission_custom_field_value`');
    }
}
