<?php

namespace Database\Seeders;

use App\Facades\Module;
use Database\Seeders\TestData\AddressSeeder;
use Database\Seeders\TestData\BannerSeeder;
use Database\Seeders\TestData\CartSeeder;
use Database\Seeders\TestData\DeliveryTypeSeeder;
use Database\Seeders\TestData\DiscountRuleSeeder;
use Database\Seeders\TestData\OrderSeeder;
use Database\Seeders\TestData\ProductAttributeOptionSeeder;
use Database\Seeders\TestData\ProductAttributeSeeder;
use Database\Seeders\TestData\ProductCategorySeeder;
use Database\Seeders\TestData\ProductSeeder;
use Database\Seeders\TestData\ProductTagSeeder;
use Database\Seeders\TestData\ProductVariantSeeder;
use Database\Seeders\TestData\RatingSeeder;
use Database\Seeders\TestData\UserSeeder;
use Database\Seeders\TestData\VoucherCodeSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        Mail::fake();
        NotificationFacade::fake();
        switch (config('app.env')) {
            case 'testing':
                $this->call([
                    RoleSeeder::class,
                    UserSeeder::class,
                    PaymentProviderSeeder::class,
                ]);
                break;
            case 'production':
                $this->call([
                    RoleSeeder::class,
                    SuperUserSeeder::class,
                    PaymentProviderSeeder::class,
                ]);
                break;
            default:

                $this->call([
                    ApiProductsTestSeeder::class,
                    RoleSeeder::class,
                    UserSeeder::class,
                    PaymentProviderSeeder::class,
                    //                    BannerSeeder::class,
                    //                    DeliveryTypeSeeder::class,
                    //                    AddressSeeder::class,
                    //                    DiscountRuleSeeder::class,
                    //                    VoucherCodeSeeder::class,
                    //                    ProductTagSeeder::class,
                    //                    ProductCategorySeeder::class,
                    //                    ProductAttributeSeeder::class,
                    //                    ProductAttributeOptionSeeder::class,
                    //                    ProductSeeder::class,
                    //                    ProductVariantSeeder::class,
                    //                    CartSeeder::class,
                    //                    OrderSeeder::class,
                ]);

                Module::enabled('ratings') && $this->call([RatingSeeder::class]);
        }
    }
}
