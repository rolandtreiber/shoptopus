<?php

namespace App\Providers;

use App\Models\FileContent;
use App\Models\OrderProduct;
use App\Models\ProductCategory;
use App\Models\User;
use App\Observers\FileContentObserver;
use App\Observers\OrderProductObserver;
use App\Observers\ProductCategoryObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        OrderProduct::observe(OrderProductObserver::class);
        ProductCategory::observe(ProductCategoryObserver::class);
        FileContent::observe(FileContentObserver::class);
    }
}
