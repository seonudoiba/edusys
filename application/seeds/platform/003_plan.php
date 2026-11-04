<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Seeds `plan` for platform.
 *
 * Source: smartschool (central database), table `plan`.
 * Rows: 4
 */
class Seed_platform_plan extends Seeder
{
    public $table = 'plan';

    public $columns = ['plan_id', 'title', 'text', 'list', 'status', 'price', 'days', 'oprice', 'created_at'];

    public function rows()
    {
        return [
            [8, '365 Days Basic Plan (For Small Schools & Startups)', '30 Days Money Back Guarantee', '["20","21"]', 1, 300000, 365, 280000, '2025-04-05 07:41:47'],
            [9, '365 Days Standard Plan (For Growing Schools)', '30 Days Money Back Guarantee', '["20","22"]', 1, 400000, 365, 350000, '2025-04-05 07:41:21'],
            [10, '365 Days Premium Plan (For Large Institutions, Schools)', '30 Days Money Back Guarantee', '["20","21","22","24"]', 1, 600000, 365, 550000, '2025-04-05 07:40:39'],
            [11, '45 DAYS FREE TRIAL', 'FREE TRIAL', '["20","21","22","23"]', 1, 0, 45, 0, '2025-05-20 11:13:45'],
        ];
    }
}
