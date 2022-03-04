<?php

namespace App\Providers;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user) {
            return $user->hasRole(Role::findByName(UserRoles::SuperAdmin)) ? true : null;
        });

        Gate::define('perform-bulk-action', function (User $user) {
            return $user->hasRole(UserRoles::SuperAdmin) || $user->hasRole(UserRoles::Admin) || $user->hasRole(UserRoles::StoreManager);
        });

        if (! $this->app->routesAreCached()) {
            Passport::routes();
            Passport::loadKeysFrom(__DIR__.'/../../');
        }
    }
}
