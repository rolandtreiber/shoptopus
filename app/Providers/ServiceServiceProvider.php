<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Services\Local\Error\ErrorServiceInterface', 'App\Services\Local\Error\ErrorService');
        $this->app->bind('App\Services\Local\ModelServiceInterface', 'App\Services\ModelService');

        $this->app->bind('App\Services\Local\Auth\AuthServiceInterface', 'App\Services\Local\Auth\AuthService');
        $this->app->bind('App\Services\Local\User\UserServiceInterface', 'App\Services\Local\User\UserService');
        $this->app->bind('App\Services\Local\AccessToken\AccessTokenServiceInterface', 'App\Services\Local\AccessToken\AccessTokenService');
        $this->app->bind('App\Services\Local\Address\AddressServiceInterface', 'App\Services\Local\Address\AddressService');
    }

}

