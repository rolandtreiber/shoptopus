<?php

namespace Database\Seeders;

use App\Enums\Permissions;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = config('roles');

        foreach (Permissions::getValues() as $permission) {
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
