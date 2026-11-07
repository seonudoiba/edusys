<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Manual bank transfer for schools (System Settings > Payment Methods > Bank transfer):
 *
 *   - sch_settings.bank_transfer: JSON with the school's bank details and whether the admission form
 *     fee can be paid by transfer (school fees use the existing is_offline_fee_payment switch).
 *   - admission_bank_transfers: an applicant's transfer of the form fee, with the receipt, waiting
 *     for the school to approve or reject it (like subscription_bank_transfers for the platform).
 *
 * School-owned table (school_id). References: school, online_admissions.
 */
class Migration_School_bank_transfers extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('bank_transfer', 'sch_settings')) {
            $this->db->query('ALTER TABLE `sch_settings` ADD COLUMN `bank_transfer` TEXT NULL'); // tenancy-lint: ok schema change
        }
        if (!$this->db->table_exists('admission_bank_transfers')) {
            $this->db->query(<<<'SQL'
CREATE TABLE `admission_bank_transfers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `online_admission_id` int NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `payer_name` varchar(200) NOT NULL,
  `payer_bank` varchar(200) NOT NULL,
  `payment_date` date NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `receipt_file` varchar(64) NOT NULL,
  `receipt_name` varchar(255) DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'pending' COMMENT 'pending, approved, rejected',
  `review_note` varchar(500) DEFAULT NULL,
  `reviewed_by` int DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admission_bank_transfers_status` (`school_id`, `status`, `created_at`),
  KEY `idx_admission_bank_transfers_admission` (`online_admission_id`),
  CONSTRAINT `fk_admission_bank_transfers_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`),
  CONSTRAINT `fk_admission_bank_transfers_admission` FOREIGN KEY (`online_admission_id`) REFERENCES `online_admissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );
        }
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `admission_bank_transfers`');
        if ($this->db->field_exists('bank_transfer', 'sch_settings')) {
            $this->db->query('ALTER TABLE `sch_settings` DROP COLUMN `bank_transfer`'); // tenancy-lint: ok schema change
        }
    }
}
