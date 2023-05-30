<?php

namespace App\Providers;

use App\Services\Local\Report\ReportService;
use App\Services\Local\Report\ReportServiceInterface;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Services\Local\Error\ErrorServiceInterface::class, \App\Services\Local\Error\ErrorService::class);
        $this->app->bind(\App\Services\Local\ModelServiceInterface::class, 'App\Services\ModelService');

        $this->app->bind(\App\Services\Local\Auth\AuthServiceInterface::class, \App\Services\Local\Auth\AuthService::class);
        $this->app->bind(\App\Services\Local\Auth\SocialAccountServiceInterface::class, \App\Services\Local\Auth\SocialAccountService::class);
        $this->app->bind(\App\Services\Local\User\UserServiceInterface::class, \App\Services\Local\User\UserService::class);
        $this->app->bind(\App\Services\Local\AccessToken\AccessTokenServiceInterface::class, \App\Services\Local\AccessToken\AccessTokenService::class);
        $this->app->bind(\App\Services\Local\Address\AddressServiceInterface::class, \App\Services\Local\Address\AddressService::class);
        $this->app->bind(ReportServiceInterface::class, ReportService::class);
        $this->app->bind(\App\Services\Local\Cart\CartServiceInterface::class, \App\Services\Local\Cart\CartService::class);
        $this->app->bind(\App\Services\Local\DeliveryRule\DeliveryRuleServiceInterface::class, \App\Services\Local\DeliveryRule\DeliveryRuleService::class);
        $this->app->bind(\App\Services\Local\DeliveryType\DeliveryTypeServiceInterface::class, \App\Services\Local\DeliveryType\DeliveryTypeService::class);
        $this->app->bind(\App\Services\Local\Order\OrderServiceInterface::class, \App\Services\Local\Order\OrderService::class);
        $this->app->bind(\App\Services\Local\Notification\NotificationServiceInterface::class, \App\Services\Local\Notification\NotificationService::class);
        $this->app->bind(\App\Services\Local\Product\ProductServiceInterface::class, \App\Services\Local\Product\ProductService::class);
        $this->app->bind(\App\Services\Local\ProductAttribute\ProductAttributeServiceInterface::class, \App\Services\Local\ProductAttribute\ProductAttributeService::class);
        $this->app->bind(\App\Services\Local\ProductCategory\ProductCategoryServiceInterface::class, \App\Services\Local\ProductCategory\ProductCategoryService::class);
        $this->app->bind(\App\Services\Remote\Payment\PaymentServiceInterface::class, \App\Services\Remote\Payment\PaymentService::class);
        $this->app->bind(\App\Services\Local\PaymentProvider\PaymentProviderServiceInterface::class, \App\Services\Local\PaymentProvider\PaymentProviderService::class);
        $this->app->bind(\App\Services\Local\VoucherCode\VoucherCodeServiceInterface::class, \App\Services\Local\VoucherCode\VoucherCodeService::class);

        // remote
        $this->app->bind(\App\Services\Remote\Payment\Stripe\StripePaymentServiceInterface::class, \App\Services\Remote\Payment\Stripe\StripePaymentService::class);
        $this->app->bind(\App\Services\Remote\Payment\PayPal\PayPalPaymentServiceInterface::class, \App\Services\Remote\Payment\PayPal\PayPalPaymentService::class);
        $this->app->bind(\App\Services\Remote\Payment\Amazon\AmazonPaymentServiceInterface::class, \App\Services\Remote\Payment\Amazon\AmazonPaymentService::class);
    }
}
