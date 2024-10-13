<?php

namespace App\Providers;

use App\Repositories\Admin\Banner\BannerRepository;
use App\Repositories\Admin\Banner\BannerRepositoryInterface;
use App\Repositories\Admin\DeliveryType\DeliveryTypeRepository;
use App\Repositories\Admin\DeliveryType\DeliveryTypeRepositoryInterface;
use App\Repositories\Admin\DiscountRule\DiscountRuleRepository;
use App\Repositories\Admin\DiscountRule\DiscountRuleRepositoryInterface;
use App\Repositories\Admin\Eventlog\EventLogRepository;
use App\Repositories\Admin\Eventlog\EventLogRepositoryInterface;
use App\Repositories\Admin\File\FileRepository;
use App\Repositories\Admin\File\FileRepositoryInterface;
use App\Repositories\Admin\Note\NoteRepository;
use App\Repositories\Admin\Note\NoteRepositoryInterface;
use App\Repositories\Admin\Order\OrderRepository;
use App\Repositories\Admin\Order\OrderRepositoryInterface;
use App\Repositories\Admin\Payment\PaymentRepository;
use App\Repositories\Admin\Payment\PaymentRepositoryInterface;
use App\Repositories\Admin\Product\ProductRepository;
use App\Repositories\Admin\Product\ProductRepositoryInterface;
use App\Repositories\Admin\ProductAttribute\ProductAttributeRepository;
use App\Repositories\Admin\ProductAttribute\ProductAttributeRepositoryInterface;
use App\Repositories\Admin\ProductCategory\ProductCategoryRepository;
use App\Repositories\Admin\ProductCategory\ProductCategoryRepositoryInterface;
use App\Repositories\Admin\ProductTag\ProductTagRepository;
use App\Repositories\Admin\ProductTag\ProductTagRepositoryInterface;
use App\Repositories\Admin\Rating\RatingRepository;
use App\Repositories\Admin\Rating\RatingRepositoryInterface;
use App\Repositories\Admin\Report\ReportRepository;
use App\Repositories\Admin\Report\ReportRepositoryInterface;
use App\Repositories\Admin\SystemService\SystemServiceRepository;
use App\Repositories\Admin\SystemService\SystemServiceRepositoryInterface;
use App\Repositories\Admin\VoucherCode\VoucherCodeRepository;
use App\Repositories\Admin\VoucherCode\VoucherCodeRepositoryInterface;
use App\Repositories\Local\AccessToken\AccessTokenRepository;
use App\Repositories\Local\AccessToken\AccessTokenRepositoryInterface;
use App\Repositories\Local\Address\AddressRepository;
use App\Repositories\Local\Address\AddressRepositoryInterface;
use App\Repositories\Local\Cart\CartRepository;
use App\Repositories\Local\Cart\CartRepositoryInterface;
use App\Repositories\Local\Checkout\CheckoutRepository;
use App\Repositories\Local\Checkout\CheckoutRepositoryInterface;
use App\Repositories\Local\DeliveryRule\DeliveryRuleRepository;
use App\Repositories\Local\DeliveryRule\DeliveryRuleRepositoryInterface;
use App\Repositories\Local\HomePage\HomePageRepository;
use App\Repositories\Local\HomePage\HomePageRepositoryInterface;
use App\Repositories\Local\ModelRepositoryInterface;
use App\Repositories\Local\PaymentProvider\PaymentProviderRepository;
use App\Repositories\Local\PaymentProvider\PaymentProviderRepositoryInterface;
use App\Repositories\Local\Transaction\Amazon\AmazonTransactionRepository;
use App\Repositories\Local\Transaction\Amazon\AmazonTransactionRepositoryInterface;
use App\Repositories\Local\Transaction\PayPal\PayPalTransactionRepository;
use App\Repositories\Local\Transaction\PayPal\PayPalTransactionRepositoryInterface;
use App\Repositories\Local\Transaction\Stripe\StripeTransactionRepository;
use App\Repositories\Local\Transaction\Stripe\StripeTransactionRepositoryInterface;
use App\Repositories\Local\User\UserRepository;
use App\Repositories\Local\User\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ModelRepositoryInterface::class, 'App\Repositories\ModelRepository');

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AccessTokenRepositoryInterface::class, AccessTokenRepository::class);
        $this->app->bind(AddressRepositoryInterface::class, AddressRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
        $this->app->bind(DeliveryRuleRepositoryInterface::class, DeliveryRuleRepository::class);
        $this->app->bind(CheckoutRepositoryInterface::class, CheckoutRepository::class);
        $this->app->bind(\App\Repositories\Local\DeliveryType\DeliveryTypeRepositoryInterface::class, \App\Repositories\Local\DeliveryType\DeliveryTypeRepository::class);
        $this->app->bind(\App\Repositories\Local\Order\OrderRepositoryInterface::class, \App\Repositories\Local\Order\OrderRepository::class);
        $this->app->bind(\App\Repositories\Local\Product\ProductRepositoryInterface::class, \App\Repositories\Local\Product\ProductRepository::class);
        $this->app->bind(\App\Repositories\Local\ProductAttribute\ProductAttributeRepositoryInterface::class, \App\Repositories\Local\ProductAttribute\ProductAttributeRepository::class);
        $this->app->bind(\App\Repositories\Local\ProductCategory\ProductCategoryRepositoryInterface::class, \App\Repositories\Local\ProductCategory\ProductCategoryRepository::class);
        $this->app->bind(\App\Repositories\Local\ProductTag\ProductTagRepositoryInterface::class, \App\Repositories\Local\ProductTag\ProductTagRepository::class);
        $this->app->bind(\App\Repositories\Local\Rating\RatingRepositoryInterface::class, \App\Repositories\Local\Rating\RatingRepository::class);
        $this->app->bind(\App\Repositories\Local\Banner\BannerRepositoryInterface::class, \App\Repositories\Local\Banner\BannerRepository::class);
        $this->app->bind(PaymentProviderRepositoryInterface::class, PaymentProviderRepository::class);
        $this->app->bind(StripeTransactionRepositoryInterface::class, StripeTransactionRepository::class);
        $this->app->bind(PayPalTransactionRepositoryInterface::class, PayPalTransactionRepository::class);
        $this->app->bind(AmazonTransactionRepositoryInterface::class, AmazonTransactionRepository::class);

        $this->app->bind(\App\Repositories\Local\VoucherCode\VoucherCodeRepositoryInterface::class, \App\Repositories\Local\VoucherCode\VoucherCodeRepository::class);
        $this->app->bind(EventLogRepositoryInterface::class, EventLogRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
        $this->app->bind(BannerRepositoryInterface::class, BannerRepository::class);
        $this->app->bind(DeliveryTypeRepositoryInterface::class, DeliveryTypeRepository::class);
        $this->app->bind(DiscountRuleRepositoryInterface::class, DiscountRuleRepository::class);
        $this->app->bind(FileRepositoryInterface::class, FileRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductAttributeRepositoryInterface::class, ProductAttributeRepository::class);
        $this->app->bind(ProductCategoryRepositoryInterface::class, ProductCategoryRepository::class);
        $this->app->bind(ProductTagRepositoryInterface::class, ProductTagRepository::class);
        $this->app->bind(RatingRepositoryInterface::class, RatingRepository::class);
        $this->app->bind(VoucherCodeRepositoryInterface::class, VoucherCodeRepository::class);
        $this->app->bind(NoteRepositoryInterface::class, NoteRepository::class);
        $this->app->bind(HomePageRepositoryInterface::class, HomePageRepository::class);
        $this->app->bind(SystemServiceRepositoryInterface::class, SystemServiceRepository::class);
    }
}
