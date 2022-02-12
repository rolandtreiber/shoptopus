<?php

namespace App\Providers;

use App\Repositories\Admin\Eventlog\EventLogRepository;
use App\Repositories\Admin\Eventlog\EventLogRepositoryInterface;
use App\Repositories\Admin\Report\ReportRepository;
use App\Repositories\Admin\Report\ReportRepositoryInterface;
use Illuminate\Support\ServiceProvider;

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
        $this->app->bind('App\Repositories\Local\Address\AddressRepositoryInterface', 'App\Repositories\Local\Address\AddressRepository');
        $this->app->bind(EventLogRepositoryInterface::class, EventLogRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
    }
}
