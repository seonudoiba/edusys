<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `online_fee_payments`: one row per online fee payment, per school.
 *
 *   - Gateways using Online_fee_payment (Paystack, Flutterwave, Monnify, Interswitch, Remita)
 *     create the row as `pending` before sending the payer to the gateway, with the amount and
 *     the fees being paid; the row is completed once, after the gateway's server confirms it.
 *   - The other gateways record a `paid` row when they deposit (fee_deposit_online()), so the
 *     same transaction reference can never be deposited twice (reloading the return page).
 *
 * School-owned table (school_id). References: school.
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Create_online_fee_payments extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `online_fee_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `gateway` varchar(30) NOT NULL,
  `reference` varchar(100) NOT NULL COMMENT 'our reference, or the gateway transaction id for older gateways',
  `gateway_ref` varchar(191) DEFAULT NULL COMMENT 'the gateway''s own id (Remita RRR, Monnify transaction reference...)',
  `student_id` int DEFAULT NULL,
  `student_session_id` int DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `payer_email` varchar(255) DEFAULT NULL,
  `payer_phone` varchar(50) DEFAULT NULL,
  `fees` mediumtext COMMENT 'JSON: the fees being paid (student_fees_master_array)',
  `status` varchar(10) NOT NULL DEFAULT 'pending' COMMENT 'pending, paid, failed',
  `result` mediumtext COMMENT 'JSON: the invoices created by the deposit',
  `message` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `paid_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_online_fee_payments_ref` (`school_id`, `gateway`, `reference`),
  KEY `idx_online_fee_payments_status` (`school_id`, `status`, `created_at`),
  CONSTRAINT `fk_online_fee_payments_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `online_fee_payments`');
    }
}
