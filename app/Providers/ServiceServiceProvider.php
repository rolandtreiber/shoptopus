<?php

namespace App\Providers;

use App\Services\Local\Report\ReportService;
use App\Services\Local\Report\ReportServiceInterface;
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
        $this->app->bind(ReportServiceInterface::class, ReportService::class);
        $this->app->bind('App\Services\Local\Cart\CartServiceInterface', 'App\Services\Local\Cart\CartService');
        $this->app->bind('App\Services\Local\DeliveryRule\DeliveryRuleServiceInterface', 'App\Services\Local\DeliveryRule\DeliveryRuleService');
        $this->app->bind('App\Services\Local\DeliveryType\DeliveryTypeServiceInterface', 'App\Services\Local\DeliveryType\DeliveryTypeService');
        $this->app->bind('App\Services\Local\Order\OrderServiceInterface', 'App\Services\Local\Order\OrderService');
        $this->app->bind('App\Services\Local\VoucherCode\VoucherCodeServiceInterface', 'App\Services\Local\VoucherCode\VoucherCodeService');
    }

}

