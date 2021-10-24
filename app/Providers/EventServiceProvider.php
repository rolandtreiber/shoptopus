<?php

namespace App\Providers;

use App\Facades\Module;
use App\Models\AccessToken;
use App\Models\Banner;
use App\Models\FileContent;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\Rating;
use App\Models\User;
use App\Models\VoucherCode;
use App\Observers\AccessTokenObserver;
use App\Observers\BannerObserver;
use App\Observers\FileContentObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderProductObserver;
use App\Observers\PaymentObserver;
use App\Observers\ProductAttributeObserver;
use App\Observers\ProductAttributeOptionObserver;
use App\Observers\ProductCategoryObserver;
use App\Observers\ProductTagObserver;
use App\Observers\RatingObserver;
use App\Observers\UserObserver;
use App\Observers\VoucherCodeObserver;
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
        AccessToken::observe(AccessTokenObserver::class);
        OrderProduct::observe(OrderProductObserver::class);
        ProductCategory::observe(ProductCategoryObserver::class);
        ProductAttribute::observe(ProductAttributeObserver::class);
        ProductTag::observe(ProductTagObserver::class);
        ProductAttributeOption::observe(ProductAttributeOptionObserver::class);
        FileContent::observe(FileContentObserver::class);
        VoucherCode::observe(VoucherCodeObserver::class);
        Order::observe(OrderObserver::class);
        Payment::observe(PaymentObserver::class);
        Banner::observe(BannerObserver::class);
        Module::enabled('ratings') && Rating::observe(RatingObserver::class);
    }
}
