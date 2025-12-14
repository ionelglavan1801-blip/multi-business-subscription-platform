<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for individuals and small teams getting started',
                'price_monthly' => 0,
                'max_businesses' => 1,
                'max_users_per_business' => 3,
                'max_projects' => 3,
                'stripe_price_id' => null,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'For growing teams that need more flexibility',
                'price_monthly' => 2900, // $29.00
                'max_businesses' => 5,
                'max_users_per_business' => 10,
                'max_projects' => 50,
                'stripe_price_id' => env('STRIPE_PRICE_PRO'),
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Unlimited everything for large organizations',
                'price_monthly' => 9900, // $99.00
                'max_businesses' => null, // unlimited
                'max_users_per_business' => null,
                'max_projects' => null,
                'stripe_price_id' => env('STRIPE_PRICE_ENTERPRISE'),
            ],
        ];

        foreach ($plans as $planData) {
            $existing = Plan::where('slug', $planData['slug'])->first();

            if ($existing) {
                // Don't overwrite stripe_price_id if env is empty and DB has a value
                if (empty($planData['stripe_price_id']) && ! empty($existing->stripe_price_id)) {
                    unset($planData['stripe_price_id']);
                }
                $existing->update($planData);
            } else {
                Plan::create($planData);
            }
        }
    }
}
