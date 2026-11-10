<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Billing from the school admin panel (admin/billing_access/open -> course/from_school/<token>):
 * a school's super admin opens the owner's subscription pages on the platform without a second
 * sign-in. Like platform_impersonation_tokens, the other way round: the school creates a one-time
 * token (stored hashed, 2 minutes), the platform checks it and signs in the school's owner account.
 *
 * Platform table (no school_id). References: school.
 */
class Migration_Create_platform_billing_tokens extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('platform_billing_tokens')) {
            $this->db->query(<<<'SQL'
CREATE TABLE `platform_billing_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token_hash` char(64) NOT NULL,
  `school_ref` int NOT NULL,
  `staff_id` int NOT NULL COMMENT 'the school staff member who opened billing',
  `created_by` varchar(255) NOT NULL COMMENT 'their name and email',
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_platform_billing_token` (`token_hash`),
  KEY `idx_platform_billing_tokens_school` (`school_ref`),
  CONSTRAINT `fk_platform_billing_tokens_school` FOREIGN KEY (`school_ref`) REFERENCES `school` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );
        }
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `platform_billing_tokens`');
    }
}
