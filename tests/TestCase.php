<?php

namespace Tests;

use App\Enums\Permission as PermissionOptions;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /**
     * @param  \Illuminate\Contracts\Auth\Authenticatable|\Laravel\Passport\HasApiTokens  $user
     * @return $this
     */
    protected function signIn($user = null, array $scopes = []): TestCase
    {
        if (is_null($user)) {
            $user = User::factory()->create();
        } elseif (is_string($user)) {
            $user = User::find($user);
        }

        Passport::actingAs($user, $scopes);

        return $this;
    }

    public function getRandomNonSuperAdminOrStoreManager(): User
    {
        $nonAuthorizedUserRoleNames = Role::whereNotIn('name', ['super_admin', 'store_manager', 'customer'])->pluck('name')->toArray();
        $unAuthorizedUsers = User::all()->map(function (User $user) use ($nonAuthorizedUserRoleNames) {
            if (array_intersect($nonAuthorizedUserRoleNames, $user->getRoleNames()->toArray())) {
                return $user;
            }

            return null;
        })->filter(function ($item) {
            return $item !== null;
        });

        return $unAuthorizedUsers->random();
    }

    public static function flushRolesAndPermissions(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $roleRecords = array_map(function($role) use ($now) {
            return [
                'name' => $role,
                'guard_name' => 'api',
                'created_at' => $now,
                'updated_at' => $now
            ];
        }, array_keys(config('roles')));
        DB::table('roles')->insert($roleRecords);

        $permissionRecords = array_map(function($permission) use ($now) {
            return [
                'name' => $permission,
                'guard_name' => 'api',
                'created_at' => $now,
                'updated_at' => $now
            ];
        }, PermissionOptions::getValues());

        DB::table('permissions')->insert($permissionRecords);

        $roles = DB::table('roles')->select(['id', 'name'])->get();
        $permissions = DB::table('permissions')->select(['id', 'name'])->get();

        $roleHasPermissions = [];
        foreach ($roles as $role) {
            $associatedPermissions = config('roles')[$role['name']];
            $associatedPermissionIds = $permissions->whereIn('name', $associatedPermissions)->pluck('id')->toArray();
            foreach ($associatedPermissionIds as $associatedPermissionId) {
                $roleHasPermissions[] = [
                    'permission_id' => $associatedPermissionId,
                    'role_id' => $role['id'],
                ];
            }
        }
        DB::table('role_has_permissions')->insert($roleHasPermissions);
        Schema::enableForeignKeyConstraints();
    }
}
