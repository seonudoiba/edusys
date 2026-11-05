<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Seeds `settings` for platform.
 *
 * Source: smartschool (central database), table `settings`. No rows in source; file kept so the table is accounted for.
 * Rows: 0
 */
class Seed_platform_settings extends Seeder
{
    public $table = 'settings';

    public $columns = ['id', 'key', 'value'];

    public function rows()
    {
        return [

        ];
    }
}
