<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `subscription_bank_transfers`: manual (bank transfer) payments for subscriptions.
 *
 * A school that registers with "Bank transfer" gets a private link (token) where it sees the
 * platform's bank details and uploads its receipt; the platform admin reviews the receipt and
 * approves (the subscription is activated as after an online payment) or rejects it (the
 * school can upload a new receipt).
 *
 * status: awaiting_receipt -> pending -> approved | rejected (rejected -> pending again).
 *
 * Platform table: one shared copy, no school_id.
 * References: subscription.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_subscription_bank_transfers extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `subscription_bank_transfers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subscription_id` int NOT NULL,
  `token` char(40) NOT NULL COMMENT 'Secret in the school''s link to the payment page',
  `status` varchar(20) NOT NULL DEFAULT 'awaiting_receipt' COMMENT 'awaiting_receipt, pending, approved, rejected',
  `amount` decimal(12,2) DEFAULT NULL COMMENT 'Amount the school says it paid',
  `payment_date` date DEFAULT NULL,
  `payer_name` varchar(200) DEFAULT NULL COMMENT 'Account name the transfer came from',
  `payer_bank` varchar(200) DEFAULT NULL,
  `reference` varchar(200) DEFAULT NULL COMMENT 'Bank transaction reference',
  `receipt_file` varchar(100) DEFAULT NULL COMMENT 'File name in uploads/subscription_receipts/',
  `receipt_name` varchar(255) DEFAULT NULL COMMENT 'Original file name',
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `reviewed_by` int DEFAULT NULL COMMENT 'staff.id of the platform admin',
  `review_note` text COMMENT 'Reason shown to the school when rejected',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_subscription_bank_transfers_subscription` (`subscription_id`),
  UNIQUE KEY `uq_subscription_bank_transfers_token` (`token`),
  KEY `idx_subscription_bank_transfers_status` (`status`),
  CONSTRAINT `fk_subscription_bank_transfers_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `subscription` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `subscription_bank_transfers`');
    }
}
