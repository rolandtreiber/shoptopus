<?php

namespace Database\Seeders\TestData;

use App\Models\FileContent;
use Illuminate\Database\Seeder;

class FileContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FileContent::factory()->count(5)->create();
    }
}
