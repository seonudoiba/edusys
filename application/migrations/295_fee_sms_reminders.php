<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * fee_sms_reminders: every fee reminder SMS sent from a class's fees page ("Remind all who owe",
 * libraries/Fee_reminders.php), so the page can show what was sent and skip guardians already reminded
 * that day. Also refreshes the fees workspace menu entries so the new pages (repeat, edit, remove,
 * remind) keep their menu entry highlighted.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Fee_sms_reminders extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('fee_sms_reminders')) {
            $this->db->query(<<<'SQL'
CREATE TABLE `fee_sms_reminders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `student_session_id` int NOT NULL,
  `fee_session_group_id` int NOT NULL,
  `phone` varchar(30) NOT NULL,
  `message` text NOT NULL,
  `balance` decimal(12,2) NOT NULL DEFAULT 0,
  `status` varchar(10) NOT NULL,
  `sent_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fee_sms_reminders_school` (`school_id`, `fee_session_group_id`, `created_at`),
  KEY `idx_fee_sms_reminders_student` (`student_session_id`),
  CONSTRAINT `fk_fee_sms_reminders_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            ); // tenancy-lint: ok schema change
        }
        $this->db->without_tenant(function () {
            $this->load->library('Fees_menu');
            $this->fees_menu->refresh();
        });
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `fee_sms_reminders`'); // tenancy-lint: ok schema change
    }
}
