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
                'features' => 'Basic features, 1 user, email support',
                'is_active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Monthly Plan',
                'slug' => 'monthly',
                'type' => 'monthly',
                'price' => 2000.000,
                'features' => 'All features, monthly billing, premium support',
                'is_active' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'One-Time Payment',
                'slug' => 'one-time',
                'type' => 'one_time',
                'price' => 10000.000,
                'features' => 'All basic features, lifetime access, priority support',
                'is_active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
