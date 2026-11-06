<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Student and parent passwords were stored as plain text. They are now hashed like staff passwords.
 * Everyone on a password that someone else knows (generated and sent to them) must choose their own
 * at the next sign-in: must_change_password on users, staff and guest (school owners).
 *
 * Going down removes the flags but can't bring the old passwords back: students and parents would
 * need new ones.
 */
class Migration_Hash_user_passwords extends CI_Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE `users` MODIFY `password` VARCHAR(255) NULL DEFAULT NULL'); // tenancy-lint: ok schema change
        foreach (array('users', 'staff', 'guest') as $table) {
            $this->db->query("ALTER TABLE `$table` ADD COLUMN `must_change_password` TINYINT(1) NOT NULL DEFAULT 0"); // tenancy-lint: ok schema change
        }
        $this->db->without_tenant(function ($db) {
            $rows = $db->select('id, password')->where('password IS NOT NULL', null, false)->where('password <>', '')
                ->where("password NOT LIKE '\$2y\$%'", null, false)->get('users')->result_array();
            foreach ($rows as $row) {
                $db->where('id', (int) $row['id'])->update('users', array(
                    'password' => password_hash($row['password'], PASSWORD_DEFAULT),
                    'must_change_password' => 1,
                ));
            }
        });
    }

    public function down()
    {
        foreach (array('users', 'staff', 'guest') as $table) {
            $this->db->query("ALTER TABLE `$table` DROP COLUMN `must_change_password`"); // tenancy-lint: ok schema change
        }
    }
}
