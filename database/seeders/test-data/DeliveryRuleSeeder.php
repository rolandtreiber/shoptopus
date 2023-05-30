<?php

namespace Database\Seeders\TestData;

use App\Models\DeliveryRule;
use Illuminate\Database\Seeder;

class DeliveryRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryRule::factory()->count(5)->create();
    }
}
