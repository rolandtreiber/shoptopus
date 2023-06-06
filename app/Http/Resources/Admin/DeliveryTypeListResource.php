<?php

namespace App\Http\Resources\Admin;

use App\Models\DeliveryType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DeliveryType
 */
class DeliveryTypeListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'status' => $this->status,
            'enabled_by_default_on_creation' => $this->enabled_by_default_on_creation,
            'enabled' => $this->enabled,
            'price' => $this->price,
            'order_count' => $this->getOrderCount(),
            'total_revenue' => $this->getTotalRevenue(),
        ];
    }
}
