<?php

namespace Database\Seeders\TestData;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Banner::factory()->count(15)->create();
    }
}
