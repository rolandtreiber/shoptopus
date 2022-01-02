<?php

namespace App\Providers;

use App\Repositories\Admin\EventLogRepository;
use App\Repositories\Admin\Interfaces\EventLogRepositoryInterface;
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
        $this->app->bind(EventLogRepositoryInterface::class, EventLogRepository::class);
    }
}
