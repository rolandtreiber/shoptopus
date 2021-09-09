<?php

namespace Database\Seeders;

use App\ProductTag;
use Illuminate\Database\Seeder;

class ProductTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductTag::factory()->count(5)->create();
    }
}
