<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|\Laravel\Passport\HasApiTokens  $user
     * @param array $scopes
     * @return $this
     */
    protected function signIn($user = null, array $scopes = []) : TestCase
    {
        if (is_null($user)) {
            $user = User::factory()->create();
        } else if (is_string($user)) {
            $user = User::find($user);
        }

        Passport::actingAs($user, $scopes);

        return $this;
    }

    /**
     * @return User
     */
    public function getRandomNonSuperAdminOrStoreManager(): User
    {
        $nonAuthorizedUserRoleNames = Role::whereNotIn('name', ['super_admin', 'store_manager', 'customer'])->pluck('name')->toArray();
        $unAuthorizedUsers = User::all()->map(function (User $user) use ($nonAuthorizedUserRoleNames) {
            if (array_intersect($nonAuthorizedUserRoleNames, $user->getRoleNames()->toArray())) {
                return $user;
            }
            return null;
        })->filter(function($item) {
            return $item !== null;
        });
        return $unAuthorizedUsers->random();
    }

}
