<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Adds `guest.registered_school_id`.
 *
 * A school owner who registers through the platform gets a guest account on the platform
 * school. The legacy code stored the registered school in `guest.school_id`, which is now
 * the tenant key (the school the guest row belongs to: the platform school). The link to
 * the school the owner registered gets its own column.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Add_guest_registered_school_id extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
ALTER TABLE `guest`
  ADD COLUMN `registered_school_id` int DEFAULT NULL COMMENT 'School this platform guest (school owner) registered' AFTER `school_id`,
  ADD KEY `idx_guest_registered_school_id` (`registered_school_id`),
  ADD CONSTRAINT `fk_guest_registered_school` FOREIGN KEY (`registered_school_id`) REFERENCES `school` (`id`) ON DELETE SET NULL
SQL
        );
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `guest` DROP FOREIGN KEY `fk_guest_registered_school`, DROP KEY `idx_guest_registered_school_id`, DROP COLUMN `registered_school_id`');
    }
}
