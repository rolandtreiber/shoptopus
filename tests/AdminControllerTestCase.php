<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;

abstract class AdminControllerTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.locales_supported' => [
            'en' => ['English'],
            'de' => ['Deutsch'],
            'fr' => ['French'],
        ]]);
        config(['app.default_currency' => [
            'name' => 'GBP',
            'symbol' => '£',
            'side' => 'left',
        ]]);
        $this->seed();
    }

    /**
     * @param  \Illuminate\Contracts\Auth\Authenticatable|\Laravel\Passport\HasApiTokens  $user
     * @param  array  $scopes
     * @return $this
     */
    protected function signIn($user = null, array $scopes = []): TestCase
    {
        $user = $user ?? User::factory()->create();

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
        })->filter(function ($item) {
            return $item !== null;
        });

        return $unAuthorizedUsers->random();
    }
}
