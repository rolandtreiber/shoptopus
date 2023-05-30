<?php

namespace App\Providers;

use App\Services\Module\ModuleService;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('module', function ($app) {
            return new ModuleService($app->config['shoptopus']['modules']);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): string
    {
        return 'module';
    }
}
