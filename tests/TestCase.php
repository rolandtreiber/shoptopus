<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshApplication();
        $this->runDatabaseMigrations();
        $this->seed();
        Config::set('locales_supported', [
                'en' => ['English'],
                'de' => ['Deutsch']
            ]);
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
