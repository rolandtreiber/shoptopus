<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            DeliveryTypeSeeder::class,
            UserSeeder::class,
            DiscountRuleSeeder::class,
            ProductTagSeeder::class,
            ProductCategorySeeder::class,
            ProductAttributeSeeder::class,
            ProductAttributeOptionSeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
