<?php

namespace App\Providers;

use App\Services\Local\AccessToken\AccessTokenService;
use App\Services\Local\AccessToken\AccessTokenServiceInterface;
use App\Services\Local\Address\AddressService;
use App\Services\Local\Address\AddressServiceInterface;
use App\Services\Local\Auth\AuthService;
use App\Services\Local\Auth\AuthServiceInterface;
use App\Services\Local\Auth\SocialAccountService;
use App\Services\Local\Auth\SocialAccountServiceInterface;
use App\Services\Local\Cart\CartService;
use App\Services\Local\Cart\CartServiceInterface;
use App\Services\Local\Checkout\CheckoutService;
use App\Services\Local\Checkout\CheckoutServiceInterface;
use App\Services\Local\DeliveryRule\DeliveryRuleService;
use App\Services\Local\DeliveryRule\DeliveryRuleServiceInterface;
use App\Services\Local\DeliveryType\DeliveryTypeService;
use App\Services\Local\DeliveryType\DeliveryTypeServiceInterface;
use App\Services\Local\Error\ErrorService;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\HomePage\HomePageService;
use App\Services\Local\HomePage\HomePageServiceInterface;
use App\Services\Local\ModelServiceInterface;
use App\Services\Local\Notification\NotificationService;
use App\Services\Local\Notification\NotificationServiceInterface;
use App\Services\Local\Order\OrderService;
use App\Services\Local\Order\OrderServiceInterface;
use App\Services\Local\PaymentProvider\PaymentProviderService;
use App\Services\Local\PaymentProvider\PaymentProviderServiceInterface;
use App\Services\Local\Product\ProductService;
use App\Services\Local\Product\ProductServiceInterface;
use App\Services\Local\ProductAttribute\ProductAttributeService;
use App\Services\Local\ProductAttribute\ProductAttributeServiceInterface;
use App\Services\Local\ProductCategory\ProductCategoryService;
use App\Services\Local\ProductCategory\ProductCategoryServiceInterface;
use App\Services\Local\ProductTag\ProductTagService;
use App\Services\Local\ProductTag\ProductTagServiceInterface;
use App\Services\Local\Rating\RatingService;
use App\Services\Local\Rating\RatingServiceInterface;
use App\Services\Local\Report\ReportService;
use App\Services\Local\Report\ReportServiceInterface;
use App\Services\Local\User\UserService;
use App\Services\Local\User\UserServiceInterface;
use App\Services\Local\VoucherCode\VoucherCodeService;
use App\Services\Local\VoucherCode\VoucherCodeServiceInterface;
use App\Services\Local\Banner\BannerService;
use App\Services\Local\Banner\BannerServiceInterface;
use App\Services\Remote\Payment\Amazon\AmazonPaymentService;
use App\Services\Remote\Payment\Amazon\AmazonPaymentServiceInterface;
use App\Services\Remote\Payment\PaymentService;
use App\Services\Remote\Payment\PaymentServiceInterface;
use App\Services\Remote\Payment\PayPal\PayPalPaymentService;
use App\Services\Remote\Payment\PayPal\PayPalPaymentServiceInterface;
use App\Services\Remote\Payment\Stripe\StripePaymentService;
use App\Services\Remote\Payment\Stripe\StripePaymentServiceInterface;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ErrorServiceInterface::class, ErrorService::class);
        $this->app->bind(ModelServiceInterface::class, 'App\Services\ModelService');

        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(SocialAccountServiceInterface::class, SocialAccountService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(AccessTokenServiceInterface::class, AccessTokenService::class);
        $this->app->bind(AddressServiceInterface::class, AddressService::class);
        $this->app->bind(RatingServiceInterface::class, RatingService::class);
        $this->app->bind(ReportServiceInterface::class, ReportService::class);
        $this->app->bind(CartServiceInterface::class, CartService::class);
        $this->app->bind(DeliveryRuleServiceInterface::class, DeliveryRuleService::class);
        $this->app->bind(DeliveryTypeServiceInterface::class, DeliveryTypeService::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(ProductAttributeServiceInterface::class, ProductAttributeService::class);
        $this->app->bind(ProductCategoryServiceInterface::class, ProductCategoryService::class);
        $this->app->bind(ProductTagServiceInterface::class, ProductTagService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
        $this->app->bind(PaymentProviderServiceInterface::class, PaymentProviderService::class);
        $this->app->bind(VoucherCodeServiceInterface::class, VoucherCodeService::class);
        $this->app->bind(CheckoutServiceInterface::class, CheckoutService::class);
        $this->app->bind(BannerServiceInterface::class, BannerService::class);
        $this->app->bind(HomePageServiceInterface::class, HomePageService::class);

        // remote
        $this->app->bind(StripePaymentServiceInterface::class, StripePaymentService::class);
        $this->app->bind(PayPalPaymentServiceInterface::class, PayPalPaymentService::class);
        $this->app->bind(AmazonPaymentServiceInterface::class, AmazonPaymentService::class);
    }
}
