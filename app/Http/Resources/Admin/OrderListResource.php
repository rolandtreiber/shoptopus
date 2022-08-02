<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GeneralHelper;
use App\Models\DeliveryType;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * @mixin Order
 */
class OrderListResource extends JsonResource
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
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
            'total_price' => GeneralHelper::displayPrice($this->total_price),
            'status' => $this->status,
            'user' => $this->user->name,
            'delivery_type' => $this->delivery_type->getTranslations('name'),
            'delivery_cost' => $this->delivery_cost,
            'town' => $this->address->town
        ];
    }
}
