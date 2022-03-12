<?php

namespace Database\Seeders\TestData;

use App\Models\PaymentSource;
use Illuminate\Database\Seeder;

class PaymentSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentSource::factory()->count(5)->create();
    }
}
