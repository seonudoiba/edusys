<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `plan`.
 *
 * Platform table: one shared copy, no school_id.
 * References: none.
 */
class Migration_Create_plan extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `plan` (
  `plan_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` int NOT NULL,
  `price` int NOT NULL,
  `days` int DEFAULT NULL,
  `oprice` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`plan_id`),
  CONSTRAINT `plan_chk_1` CHECK (json_valid(`list`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `plan`');
    }
}
