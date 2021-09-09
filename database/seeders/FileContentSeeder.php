<?php

namespace Database\Seeders;

use App\FileContent;
use Illuminate\Database\Seeder;

class FileContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FileContent::factory()->count(5)->create();
    }
}
