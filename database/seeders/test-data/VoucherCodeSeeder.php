<?php

namespace Database\Seeders\TestData;

use App\Models\VoucherCode;
use Illuminate\Database\Seeder;

class VoucherCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VoucherCode::factory()->count(5)->create();
    }
}
