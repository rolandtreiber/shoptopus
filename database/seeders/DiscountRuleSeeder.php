<?php

namespace Database\Seeders;

use App\Enums\DiscountTypes;
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
    public function run()
    {
        $dr = new DiscountRule();
        $dr->type = DiscountTypes::Amount;
        $dr->amount = 2;
        $dr->valid_from = Carbon::now();
        $dr->valid_until = Carbon::now()->addMonth();
        $dr->save();

        $dr = new DiscountRule();
        $dr->type = DiscountTypes::Percentage;
        $dr->amount = 10;
        $dr->valid_from = Carbon::now();
        $dr->valid_until = Carbon::now()->addMonth();
        $dr->save();

    }
}
