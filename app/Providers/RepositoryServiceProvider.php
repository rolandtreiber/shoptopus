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
use App\Repositories\Admin\VoucherCode\VoucherCodeRepository;
use App\Repositories\Admin\VoucherCode\VoucherCodeRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Repositories\Local\ModelRepositoryInterface', 'App\Repositories\ModelRepository');

        $this->app->bind('App\Repositories\Local\User\UserRepositoryInterface', 'App\Repositories\Local\User\UserRepository');
        $this->app->bind('App\Repositories\Local\AccessToken\AccessTokenRepositoryInterface', 'App\Repositories\Local\AccessToken\AccessTokenRepository');
        $this->app->bind('App\Repositories\Local\Address\AddressRepositoryInterface', 'App\Repositories\Local\Address\AddressRepository');
        $this->app->bind('App\Repositories\Local\Cart\CartRepositoryInterface', 'App\Repositories\Local\Cart\CartRepository');
        $this->app->bind('App\Repositories\Local\DeliveryRule\DeliveryRuleRepositoryInterface', 'App\Repositories\Local\DeliveryRule\DeliveryRuleRepository');
        $this->app->bind('App\Repositories\Local\DeliveryType\DeliveryTypeRepositoryInterface', 'App\Repositories\Local\DeliveryType\DeliveryTypeRepository');
        $this->app->bind('App\Repositories\Local\Order\OrderRepositoryInterface', 'App\Repositories\Local\Order\OrderRepository');
        $this->app->bind('App\Repositories\Local\Product\ProductRepositoryInterface', 'App\Repositories\Local\Product\ProductRepository');
        $this->app->bind('App\Repositories\Local\ProductAttribute\ProductAttributeRepositoryInterface', 'App\Repositories\Local\ProductAttribute\ProductAttributeRepository');
        $this->app->bind('App\Repositories\Local\ProductCategory\ProductCategoryRepositoryInterface', 'App\Repositories\Local\ProductCategory\ProductCategoryRepository');
        $this->app->bind('App\Repositories\Local\VoucherCode\VoucherCodeRepositoryInterface', 'App\Repositories\Local\VoucherCode\VoucherCodeRepository');
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

    }
}
