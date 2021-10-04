<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Rating;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $productIds = Product::all()->pluck('id');
        $userIds = User::all()->pluck('id');

        $ratingsToGenerate = random_int(20, 50);
        for ($i = 0; $i < $ratingsToGenerate; $i++) {
            $selectedProductId = random_int(0, sizeof($productIds)-1);
            $selectedUserId = random_int(0, sizeof($userIds)-1);
            Rating::factory()->state([
                'ratable_type' => Product::class,
                'ratable_id' => $productIds[$selectedProductId],
                'user_id' => $userIds[$selectedUserId]
            ])->create();
        }
    }
}
