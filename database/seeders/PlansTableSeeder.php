<?php

namespace Thebrightlabs\IraqPayments\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('plans')->insert([
            [
                'name' => 'Free Plan',
                'slug' => 'free',
                'type' => 'free',
                'price' => 0.000,
                'description' => 'A starter plan suitable for individuals getting started.',
                'features' => json_encode(['Basic features', '1 user', 'Email support']),
                'is_active' => true,
                'order' => 1,
                "unit_count"=>0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Monthly Plan',
                'slug' => 'monthly',
                'type' => 'monthly',
                'price' => 2000.000,
                'description' => 'Full access with monthly billing and priority support.',
                'features' => json_encode(['All features', 'Monthly billing', 'Premium support']),
                'is_active' => true,
                "unit_count"=>30,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'One-Time Payment',
                'slug' => 'one-time',
                'type' => 'one_time',
                'price' => 10000.000,
                "unit_count"=>0,
                'description' => 'Lifetime access to core features with priority support.',
                'features' => json_encode(['All basic features', 'Lifetime access', 'Priority support']),
                'is_active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
