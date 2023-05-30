<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GeneralHelper;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin Order
 */
class OrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
            'total_price' => GeneralHelper::displayPrice($this->total_price),
            'original_price' => GeneralHelper::displayPrice($this->original_price),
            'total_discount' => GeneralHelper::displayPrice($this->total_discount),
            'status' => $this->status,
            'user' => $this->user->name,
            'delivery_type' => $this->delivery_type->getTranslations('name'),
            'delivery_cost' => $this->delivery_cost,
            'town' => $this->address->town,
        ];
    }
}
