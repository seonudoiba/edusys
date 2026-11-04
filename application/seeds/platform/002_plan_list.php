<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Seeds `plan_list` for platform.
 *
 * Source: smartschool (central database), table `plan_list`.
 * Rows: 9
 */
class Seed_platform_plan_list extends Seeder
{
    public $table = 'plan_list';

    public $columns = ['id', 'name', 'status', 'sort_order', 'created_at'];

    public function rows()
    {
        return [
            [17, 'Access To Full Website', 1, 1, '2025-02-12 13:03:56'],
            [18, 'No Support', 1, 2, '2025-02-12 13:04:18'],
            [20, 'Email Support', 1, 4, '2025-02-12 13:04:12'],
            [21, '24/7 Priority Support', 1, 3, '2025-02-12 13:03:50'],
            [22, 'Custom Branding & School Domain', 1, 6, '2025-02-12 13:04:00'],
            [23, 'Regular Free Updates', 1, 7, '2025-02-12 13:04:22'],
            [24, 'Developer Support Ready', 1, 8, '2025-02-12 13:04:06'],
            [25, 'White Label Solution', 1, 9, '2025-02-12 13:04:28'],
            [26, 'new_test', 0, 11, '2025-02-13 06:11:41'],
        ];
    }
}
