<?php

namespace App\Http\Resources\Admin;

use App\Models\DeliveryRule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DeliveryRule
 */
class DeliveryRuleDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'postcodes' => $this->postcodes,
            'countries' => $this->countries,
            'min_weight' => $this->min_weight,
            'max_weight' => $this->max_weight,
            'min_distance' => $this->min_distance,
            'max_distance' => $this->max_distance,
            'distance_unit' => $this->distance_unit,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'status' => $this->status,
        ];
    }
}
