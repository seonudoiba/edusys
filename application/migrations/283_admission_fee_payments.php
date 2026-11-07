<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Online admission form fees go through the same gateway drivers as school fees
 * (libraries/Admission_payment.php): a pending row in online_fee_payments with the application's
 * id, completed once after the gateway's server confirms it.
 */
class Migration_Admission_fee_payments extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('online_admission_id', 'online_fee_payments')) {
            $this->db->query('ALTER TABLE `online_fee_payments` ADD COLUMN `online_admission_id` int DEFAULT NULL AFTER `student_session_id`, ADD KEY `idx_online_fee_payments_admission` (`school_id`, `online_admission_id`)'); // tenancy-lint: ok schema change
        }
    }

    public function down()
    {
        if ($this->db->field_exists('online_admission_id', 'online_fee_payments')) {
            $this->db->query('ALTER TABLE `online_fee_payments` DROP KEY `idx_online_fee_payments_admission`, DROP COLUMN `online_admission_id`'); // tenancy-lint: ok schema change
        }
    }
}
