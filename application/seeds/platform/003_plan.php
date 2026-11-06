<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Seeds `plan` for platform.
 *
 * Source: smartschool (central database), table `plan`.
 * Rows: 1 (priced by student bands, see Plan_pricing and migration 276)
 */
class Seed_platform_plan extends Seeder
{
    public $table = 'plan';

    public $columns = ['plan_id', 'title', 'text', 'list', 'status', 'price', 'days', 'oprice', 'is_band', 'created_at'];

    public function rows()
    {
        return [
            [12, 'EduSYS Standard Subscription', 'Priced per student, per term', '[]', 1, 0, 365, 0, 1, '2026-11-06 00:00:00'],
        ];
    }
}
