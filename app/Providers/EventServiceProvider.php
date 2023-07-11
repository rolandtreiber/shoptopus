<?php

namespace App\Providers;

use App\Events\UserInteraction;
use App\Facades\Module;
use App\Listeners\UpdateLastSeenUser;
use App\Models\AccessToken;
use App\Models\Banner;
use App\Models\FileContent;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use App\Models\Rating;
use App\Models\User;
use App\Models\VariantAttribute;
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
use App\Observers\ProductObserver;
use App\Observers\ProductTagObserver;
use App\Observers\ProductVariantObserver;
use App\Observers\RatingObserver;
use App\Observers\UserObserver;
use App\Observers\VariantAttributeObserver;
use App\Observers\VoucherCodeObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserInteraction::class => [
            UpdateLastSeenUser::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Event::listen(StatementPrepared::class, function ($event) {
            $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
        });

        User::observe(UserObserver::class);
        AccessToken::observe(AccessTokenObserver::class);
        OrderProduct::observe(OrderProductObserver::class);
        Product::observe(ProductObserver::class);
        ProductCategory::observe(ProductCategoryObserver::class);
        ProductAttribute::observe(ProductAttributeObserver::class);
        ProductVariant::observe(ProductVariantObserver::class);
        ProductTag::observe(ProductTagObserver::class);
        ProductAttributeOption::observe(ProductAttributeOptionObserver::class);
        FileContent::observe(FileContentObserver::class);
        VoucherCode::observe(VoucherCodeObserver::class);
        Order::observe(OrderObserver::class);
        Payment::observe(PaymentObserver::class);
        Banner::observe(BannerObserver::class);
        Module::enabled('ratings') && Rating::observe(RatingObserver::class);
        VariantAttribute::observe(VariantAttributeObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
