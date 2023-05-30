<?php

namespace Database\Seeders\TestData;

use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deliveryTypes = [
            [
                'name' => [
                    'en' => 'Local small',
                    'de' => 'Lokal klein',
                    'fr' => 'Petit local',
                ],
                'description' => [
                    'en' => 'under 5kg',
                    'de' => 'unter 5kg',
                    'fr' => 'moins de 5kg',
                ],
                'price' => 2,
                'rule' => [
                    'lat' => '51.4545',
                    'lon' => '-2.5879',
                    'max_distance' => 10,
                    'max_weight' => 5000,
                ],
            ],
            [
                'name' => [
                    'en' => 'Heavy',
                    'de' => 'Lourd',
                    'fr' => 'Schwer',
                ],
                'description' => [
                    'en' => 'over 5kg',
                    'de' => 'über 5 kg',
                    'fr' => 'plus que 5kg',
                ],
                'price' => 8,
                'rule' => [
                    'lat' => '51.4545',
                    'lon' => '-2.5879',
                    'min_weight' => 5000,
                ],
            ],
        ];

        DB::table('delivery_rules')->delete();
        DB::table('delivery_types')->delete();

        foreach ($deliveryTypes as $deliveryType) {
            $type = DeliveryType::factory()->create([
                'name' => $deliveryType['name'],
                'description' => $deliveryType['description'],
                'price' => $deliveryType['price'],
            ]);

            DeliveryRule::factory()->create([
                'delivery_type_id' => $type->id,
                'lat' => $deliveryType['rule']['lat'] ?? null,
                'lon' => $deliveryType['rule']['lon'] ?? null,
                'min_weight' => $deliveryType['rule']['min_weight'] ?? null,
                'max_weight' => $deliveryType['rule']['max_weight'] ?? null,
                'max_distance' => $deliveryType['rule']['max_distance'] ?? null,
                'min_distance' => $deliveryType['rule']['min_distance'] ?? null,
            ]);
        }
    }
}
