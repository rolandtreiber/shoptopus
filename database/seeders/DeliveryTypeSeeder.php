<?php

namespace Database\Seeders;

use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use Illuminate\Database\Seeder;

class DeliveryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deliveryTypes = [
            [
                'name' => [
                    'en' => 'Local small',
                    'de' => 'Lokal klein',
                    'fr' => 'Petit local'
                ],
                'description' => [
                    'en' => 'under 5kg',
                    'de' => 'unter 5kg',
                    'fr' => 'moins de 5kg'
                ],
                'price' => 2,
                'rules' => [
                    [
                        'lat' => '51.4545',
                        'lon' => '-2.5879',
                        'max_distance' => 10,
                        'max_weight' => 5000
                    ],
                ]
            ],
            [
                'name' => [
                    'en' => 'Heavy',
                    'de' => 'Lourd',
                    'fr' => 'Schwer'
                ],
                'description' => [
                    'en' => 'over 5kg',
                    'de' => 'über 5 kg',
                    'fr' => 'plus que 5kg'
                ],
                'price' => 8,
                'rules' => [
                    [
                        'lat' => '51.4545',
                        'lon' => '-2.5879',
                        'min_weight' => 5000
                    ],
                ]
            ]
        ];

        foreach ($deliveryTypes as $deliveryType) {
            $d = new DeliveryType();
            $d->name = $deliveryType['name'];
            $d->description = $deliveryType['description'];
            $d->price = $deliveryType['price'];
            $d->save();
            foreach ($deliveryType['rules'] as $rule) {
                $keys = ['lat', 'lon', 'min_weight', 'max_weight', 'min_distance', 'max_distance'];
                $dr = new DeliveryRule();
                $dr->delivery_type_id = $d->id;
                foreach ($keys as $key) {
                    if (array_key_exists($key, $rule)) {
                        $dr->$key = $rule[$key];
                    }
                }
                $dr->save();
            }
        }
    }
}
