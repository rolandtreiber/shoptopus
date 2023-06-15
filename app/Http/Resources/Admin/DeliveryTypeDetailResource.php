<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\NoteResource;
use App\Models\DeliveryType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DeliveryType
 */
class DeliveryTypeDetailResource extends JsonResource
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
            'rules' => DeliveryRuleListResource::collection($this->deliveryRules),
            'order_count' => $this->getOrderCount(),
            'total_revenue' => $this->getTotalRevenue(),
            'notes' => NoteResource::collection($this->notes),
        ];
    }
}
