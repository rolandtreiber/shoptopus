<?php

namespace App\Providers;

use App\Services\Local\AccessToken\AccessTokenServiceInterface;
use App\Services\Local\Address\AddressServiceInterface;
use App\Services\Local\Auth\AuthServiceInterface;
use App\Services\Local\Auth\SocialAccountServiceInterface;
use App\Services\Local\Cart\CartServiceInterface;
use App\Services\Local\DeliveryRule\DeliveryRuleServiceInterface;
use App\Services\Local\DeliveryType\DeliveryTypeServiceInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelServiceInterface;
use App\Services\Local\Notification\NotificationServiceInterface;
use App\Services\Local\Order\OrderServiceInterface;
use App\Services\Local\PaymentProvider\PaymentProviderServiceInterface;
use App\Services\Local\Product\ProductServiceInterface;
use App\Services\Local\ProductAttribute\ProductAttributeServiceInterface;
use App\Services\Local\ProductCategory\ProductCategoryServiceInterface;
use App\Services\Local\Report\ReportService;
use App\Services\Local\Report\ReportServiceInterface;
use App\Services\Local\User\UserServiceInterface;
use App\Services\Local\VoucherCode\VoucherCodeServiceInterface;
use App\Services\Remote\Payment\Amazon\AmazonPaymentServiceInterface;
use App\Services\Remote\Payment\PaymentServiceInterface;
use App\Services\Remote\Payment\PayPal\PayPalPaymentServiceInterface;
use App\Services\Remote\Payment\Stripe\StripePaymentServiceInterface;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ErrorServiceInterface::class, \App\Services\Local\Error\ErrorService::class);
        $this->app->bind(ModelServiceInterface::class, 'App\Services\ModelService');

        $this->app->bind(AuthServiceInterface::class, \App\Services\Local\Auth\AuthService::class);
        $this->app->bind(SocialAccountServiceInterface::class, \App\Services\Local\Auth\SocialAccountService::class);
        $this->app->bind(UserServiceInterface::class, \App\Services\Local\User\UserService::class);
        $this->app->bind(AccessTokenServiceInterface::class, \App\Services\Local\AccessToken\AccessTokenService::class);
        $this->app->bind(AddressServiceInterface::class, \App\Services\Local\Address\AddressService::class);
        $this->app->bind(ReportServiceInterface::class, ReportService::class);
        $this->app->bind(CartServiceInterface::class, \App\Services\Local\Cart\CartService::class);
        $this->app->bind(DeliveryRuleServiceInterface::class, \App\Services\Local\DeliveryRule\DeliveryRuleService::class);
        $this->app->bind(DeliveryTypeServiceInterface::class, \App\Services\Local\DeliveryType\DeliveryTypeService::class);
        $this->app->bind(OrderServiceInterface::class, \App\Services\Local\Order\OrderService::class);
        $this->app->bind(NotificationServiceInterface::class, \App\Services\Local\Notification\NotificationService::class);
        $this->app->bind(ProductServiceInterface::class, \App\Services\Local\Product\ProductService::class);
        $this->app->bind(ProductAttributeServiceInterface::class, \App\Services\Local\ProductAttribute\ProductAttributeService::class);
        $this->app->bind(ProductCategoryServiceInterface::class, \App\Services\Local\ProductCategory\ProductCategoryService::class);
        $this->app->bind(PaymentServiceInterface::class, \App\Services\Remote\Payment\PaymentService::class);
        $this->app->bind(PaymentProviderServiceInterface::class, \App\Services\Local\PaymentProvider\PaymentProviderService::class);
        $this->app->bind(VoucherCodeServiceInterface::class, \App\Services\Local\VoucherCode\VoucherCodeService::class);

        // remote
        $this->app->bind(StripePaymentServiceInterface::class, \App\Services\Remote\Payment\Stripe\StripePaymentService::class);
        $this->app->bind(PayPalPaymentServiceInterface::class, \App\Services\Remote\Payment\PayPal\PayPalPaymentService::class);
        $this->app->bind(AmazonPaymentServiceInterface::class, \App\Services\Remote\Payment\Amazon\AmazonPaymentService::class);
    }
}
