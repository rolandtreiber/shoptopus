<?php

namespace App\Providers;

use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\ElasticsearchHandler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//       $this->app->bind(ElasticsearchHandler::class, function ($app) {
//           return new ElasticsearchHandler(ClientBuilder::create()->setHosts([env('ELASTIC_HOST')])->build(), [
//               'index' => env('ELASTIC_LOGS_INDEX'),
//               'type' => '_doc',
//               'ignore_error' => true,
//           ]);
//       });
    }
}
