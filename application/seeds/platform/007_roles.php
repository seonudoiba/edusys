<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Seeds `roles` for platform.
 *
 * Source: smartschool (central database), table `roles`. System roles (school_id NULL) shared by every school; role ids are hard-coded in the application.
 * Rows: 6
 */
class Seed_platform_roles extends Seeder
{
    public $table = 'roles';

    public $columns = ['id', 'school_id', 'name', 'slug', 'is_active', 'is_system', 'is_superadmin', 'created_at', 'updated_at'];

    public function rows()
    {
        return [
            [1, null, 'Admin', null, 0, 1, 0, '2018-06-30 15:39:11', '0000-00-00'],
            [2, null, 'Teacher', null, 0, 1, 0, '2018-06-30 15:39:14', '0000-00-00'],
            [3, null, 'Accountant', null, 0, 1, 0, '2018-06-30 15:39:17', '0000-00-00'],
            [4, null, 'Librarian', null, 0, 1, 0, '2018-06-30 15:39:21', '0000-00-00'],
            [6, null, 'Receptionist', null, 0, 1, 0, '2018-07-02 05:39:03', '0000-00-00'],
            [7, null, 'Super Admin', null, 0, 1, 1, '2018-07-11 14:11:29', '0000-00-00'],
        ];
    }
}
