<?php

namespace Database\Seeders;

use App\Models\PaymentProvider\PaymentProvider;
use App\Models\PaymentProvider\PaymentProviderConfig;
use Illuminate\Database\Seeder;

class PaymentProviderSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $payment_providers = config('payment_providers.providers');

        foreach ($payment_providers as $provider) {
            if (! PaymentProvider::whereName($provider)->exists()) {
                $payment_provider = PaymentProvider::factory()->create([
                    'name' => $provider,
                    'enabled' => true,
                    'test_mode' => true,
                ]);

                $this->createConfig($payment_provider);
            }
        }
    }

    private function createConfig($payment_provider)
    {
        $config_settings = config("payment_providers.provider_settings.$payment_provider->name.live");

        foreach ($config_settings as $config => $value) {
            PaymentProviderConfig::factory()->create([
                'payment_provider_id' => $payment_provider->id,
                'setting' => $config,
                'value' => $value,
                'test_value' => config("payment_providers.provider_settings.$payment_provider->name.sandbox.$config"),
            ]);
        }
    }
}
