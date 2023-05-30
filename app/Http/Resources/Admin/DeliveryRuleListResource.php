<?php

namespace App\Http\Resources\Admin;

use App\Models\DeliveryRule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DeliveryRule
 */
class DeliveryRuleListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'postcodes' => $this->postcodes,
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
