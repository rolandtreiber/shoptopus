<?php

namespace Database\Seeders;

use App\Services\Local\Auth\AuthServiceInterface;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(AuthServiceInterface $authService): void
    {
        $authService->flushRolesAndPermissions();
    }
}
