<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionOptions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = config('roles');

        foreach (PermissionOptions::getValues() as $permission) {
            Permission::create(['name' => $permission]);
        }

        foreach ($roles as $roleName => $role) {
            Role::create(['name' => $roleName]);
            $selectedRole = Role::findByName($roleName);

            foreach ($role as $permissionName) {
                $selectedRole->givePermissionTo(Permission::findByName($permissionName));
            }
        }
    }
}
