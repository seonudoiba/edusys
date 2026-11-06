<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Adds `subscription.gateway_ref`: the gateway's own id for a subscription payment that
 * verification needs (Remita RRR, Monnify transaction reference). `reference` stays our reference.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_subscription_gateway_ref extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('gateway_ref', 'subscription')) {
            $this->db->query("ALTER TABLE `subscription` ADD COLUMN `gateway_ref` varchar(191) DEFAULT NULL
                COMMENT 'the gateway''s own id (Remita RRR...), see Payment_gateways' AFTER `reference`");
        }
    }

    public function down()
    {
        if ($this->db->field_exists('gateway_ref', 'subscription')) {
            $this->db->query('ALTER TABLE `subscription` DROP COLUMN `gateway_ref`');
        }
    }
}
