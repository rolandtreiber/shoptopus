<?php

namespace Database\Seeders\TestData;

use App\Enums\DiscountType;
use App\Models\DiscountRule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DiscountRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $dr = new DiscountRule();
        $dr->type = DiscountType::Amount;
        $dr->name = [
            'en' => '£2 off',
            'de' => '£2 Rabatt',
            'fr' => '£2 réduction',
        ];
        $dr->amount = 2;
        $dr->valid_from = Carbon::now();
        $dr->valid_until = Carbon::now()->addMonth();
        $dr->save();

        $dr = new DiscountRule();
        $dr->type = DiscountType::Percentage;
        $dr->name = [
            'en' => '10% off',
            'de' => '10% Rabatt',
            'fr' => '10% réduction',
        ];
        $dr->amount = 10;
        $dr->valid_from = Carbon::now();
        $dr->valid_until = Carbon::now()->addMonth();
        $dr->save();
    }
}
