<?php

namespace Thebrightlabs\IraqPayments\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansTableSeeder extends Seeder
{
    public function run()
    {
        // Arrange descriptions, features, unit_count, and order based on the plan name.
        // Do not change slug, type, or price as requested.
        DB::table('plans')->insert([
            [
                'name' => 'Free Plan',
                'slug' => 'free',   // unchanged
                'type' => 'free',   // unchanged
                'price' => 0.000,   // unchanged
                'description' => 'Basic access suitable for individuals getting started.',
                'features' => json_encode(['Basic features', '1 user', 'Email support']),
                'is_active' => true,
                'order' => 2, // alphabetical by name
                'unit_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2 week Plan',
                'slug' => 'two_week', // unchanged
                'type' => 'two_week',  // unchanged
                'price' => 20000,      // unchanged
                'description' => 'Access for 14 days with standard support.',
                'features' => json_encode(['14-day access', 'All basic features', 'Standard support']),
                'is_active' => true,
                'order' => 1, // alphabetical by name
                'unit_count' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Monthly Plan',
                'slug' => 'monthly',  // unchanged
                'type' => 'monthly',  // unchanged
                'price' => 50000,     // unchanged
                'description' => 'Full access with monthly billing and priority support.',
                'features' => json_encode(['All features', 'Monthly billing', 'Premium support']),
                'is_active' => true,
                'unit_count' => 30, // keep 30 for monthly per service logic
                'order' => 3, // alphabetical by name
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yearly Plan',
                'slug' => 'yearly',  // unchanged
                'type' => 'yearly',  // unchanged
                'price' => 250000,   // unchanged
                'description' => 'Full access with yearly billing and priority support.',
                'features' => json_encode(['All features', 'Yearly billing', 'Priority support']),
                'is_active' => true,
                'unit_count' => 365,
                'order' => 5, // alphabetical by name
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'One-Time Payment',
                'slug' => 'one-time',  // unchanged
                'type' => 'one_time',  // unchanged
                'price' => 500000,     // unchanged
                'description' => 'Lifetime access to core features with priority support.',
                'features' => json_encode(['All basic features', 'Lifetime access', 'Priority support']),
                'is_active' => true,
                'unit_count' => 0,
                'order' => 4, // alphabetical by name
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
