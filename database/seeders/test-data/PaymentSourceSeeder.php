<?php

namespace Database\Seeders\TestData;

use App\Models\PaymentSource;
use Illuminate\Database\Seeder;

class PaymentSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentSource::factory()->count(5)->create();
    }
}
