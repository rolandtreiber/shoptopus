<?php

namespace App\Http\Resources\Common;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\ProductStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationMetaInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $enumsMap = [
            "order_statuses" => OrderStatus::class,
            "payment_methods" => PaymentMethod::class,
            "payment_statuses" => PaymentStatus::class,
            "payment_types" => PaymentType::class,
            "product_statuses" => ProductStatus::class
        ];
        $enums = [];
        foreach ($enumsMap as $key => $class) {
            $enums[$key] = array_map(function ($key) use ($class) {
                return [
                    'key' => preg_replace('/(?<!\ )[A-Z]/', ' $0', $key),
                    'value' => $class::fromKey($key)->value
                ];
            }, $class::getKeys());
        }
        return [
            'locales' => config('app.locales_supported'),
            'default_locale' => config('app.locale'),
            'default_currency' => config('app.default_currency'),
            'google_maps_api_key' => config('app.google_maps_api_key'),
            'available_user_prefixes' => config('users.available_prefixes'),
            'enums' => $enums
        ];
    }
}
