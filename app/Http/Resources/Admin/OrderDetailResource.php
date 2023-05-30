<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\AddressResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin Order
 */
class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray(Request $request): array
    {
        $dt = $this->delivery_type;

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'original_price' => $this->original_price,
            'total_price' => $this->total_price,
            'total_discount' => $this->total_discount,
            'subtotal' => $this->subtotal,
            'delivery_cost' => $this->delivery_cost,
            'address' => new AddressResource($this->address),
            'user' => new UserListResource($this->user),
            'payments' => PaymentRelationResource::collection($this->payments),
            'status' => $this->status,
            'voucher_code' => new VoucherCodeListResource($this->voucher_code),
            'delivery_type' => [
                'name' => $dt->getTranslations('name'),
                'description' => $dt->getTranslations('description'),
            ],
            'products' => OrderProductResource::collection($this->products),
            'event_logs' => EventLogResource::collection($this->eventLogs),
        ];
    }
}
