<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DumpImportSeederFixUrls extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $files = scandir(database_path() . '/seeders/test-data/db-dump');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($files as $file) {
            if (($file !== '.') && ($file !== '..') && str_contains($file, ".sql")) {
                $sql = file_get_contents(database_path() . '/seeders/test-data/db-dump/' . $file);
                $sql = str_replace('https://shoptopus.test', config('app.url'), $sql);
                if ($sql) {
                    DB::unprepared($sql);
                }
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }}
