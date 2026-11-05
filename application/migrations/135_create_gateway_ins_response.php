<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `gateway_ins_response`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: gateway_ins, school.
 */
class Migration_Create_gateway_ins_response extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `gateway_ins_response` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `gateway_ins_id` int DEFAULT NULL,
  `posted_data` text,
  `response` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `gateway_ins_id` (`gateway_ins_id`),
  KEY `idx_gateway_ins_response_school_id` (`school_id`),
  CONSTRAINT `gateway_ins_response_ibfk_1` FOREIGN KEY (`gateway_ins_id`) REFERENCES `gateway_ins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gateway_ins_response_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `gateway_ins_response`');
    }
}
