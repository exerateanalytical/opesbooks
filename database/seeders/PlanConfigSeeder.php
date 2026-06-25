<?php

namespace Database\Seeders;

use App\Models\PlanConfig;
use Illuminate\Database\Seeder;

class PlanConfigSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['name' => 'FREE',       'slug' => 'free',       'price_xaf_monthly' => 0,     'price_xaf_yearly' => 0,      'max_users' => 1,  'max_invoices_per_month' => 20, 'api_calls_per_hour' => 0,     'sort_order' => 0],
            ['name' => 'STARTER',    'slug' => 'starter',    'price_xaf_monthly' => 5000,  'price_xaf_yearly' => 50000,  'max_users' => 3,  'max_invoices_per_month' => -1, 'api_calls_per_hour' => 1000,  'sort_order' => 1],
            ['name' => 'BUSINESS',   'slug' => 'business',   'price_xaf_monthly' => 15000, 'price_xaf_yearly' => 150000, 'max_users' => 10, 'max_invoices_per_month' => -1, 'api_calls_per_hour' => 10000, 'sort_order' => 2],
            ['name' => 'ENTERPRISE', 'slug' => 'enterprise', 'price_xaf_monthly' => 0,     'price_xaf_yearly' => 0,      'max_users' => -1, 'max_invoices_per_month' => -1, 'api_calls_per_hour' => -1,    'sort_order' => 3],
        ];

        foreach ($plans as $p) {
            PlanConfig::updateOrCreate(['slug' => $p['slug']], array_merge($p, ['is_active' => true]));
        }
    }
}
