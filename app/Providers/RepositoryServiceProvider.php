<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Admin\EventLogRepository;
use App\Repositories\Admin\Interfaces\EventLogRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Repositories\Local\ModelRepositoryInterface', 'App\Repositories\ModelRepository');

        $this->app->bind('App\Repositories\Local\User\UserRepositoryInterface', 'App\Repositories\Local\User\UserRepository');
        $this->app->bind('App\Repositories\Local\AccessToken\AccessTokenRepositoryInterface', 'App\Repositories\Local\AccessToken\AccessTokenRepository');
        $this->app->bind('App\Repositories\Local\Address\AddressRepositoryInterface', 'App\Repositories\Local\Address\AddressRepository');
        $this->app->bind('App\Repositories\Local\Order\OrderRepositoryInterface', 'App\Repositories\Local\Order\OrderRepository');
        $this->app->bind('App\Repositories\Local\VoucherCode\VoucherCodeRepositoryInterface', 'App\Repositories\Local\VoucherCode\VoucherCodeRepository');
        $this->app->bind(EventLogRepositoryInterface::class, EventLogRepository::class);
    }
}
