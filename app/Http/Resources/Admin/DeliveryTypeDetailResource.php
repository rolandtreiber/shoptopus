<?php

namespace App\Http\Resources\Admin;

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
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'status' => $this->status,
            'enabled_by_default_on_creation' => $this->enabled_by_default_on_creation,
            'enabled' => $this->enabled,
            'price' => $this->price,
            'rules' => DeliveryRuleListResource::collection($this->deliveryRules)
        ];
    }
}
