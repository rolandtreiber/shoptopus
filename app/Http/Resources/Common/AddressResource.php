<?php

namespace App\Http\Resources\Common;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Address
 */
class AddressResource extends JsonResource
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
            'name' => $this->name,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'town' => $this->town,
            'post_code' => $this->post_code,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'country' => $this->country,
            'composite' => $this->getComposite()
        ];
    }
}
