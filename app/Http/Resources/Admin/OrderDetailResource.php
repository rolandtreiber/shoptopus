<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\AddressResource;
use App\Models\DeliveryType;
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
     * @return array
     */
    public function toArray($request): array
    {
        /** @var DeliveryType $dt */
        $dt = $this->deliveryType;
        return [
            'id' => $this->id,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'original_price' => (float) $this->original_price,
            'total_price' => (float) $this->total_price,
            'total_discount' => (float) $this->total_discount,
            'subtotal' => (float) $this->subtotal,
            'delivery' => (float) $this->delivery,
            'address' => new AddressResource($this->address),
            'user' => new UserListResource($this->user),
            'payments' => PaymentRelationResource::collection($this->payments),
            'status' => $this->status,
            'voucher_code' => new VoucherCodeListResource($this->voucherCode),
            'delivery_type' => [
                'name' => $dt->getTranslations('name'),
                'description' => $dt->getTranslations('description')
            ],
            'products' => OrderProductResource::collection($this->products),
            'event_logs' => EventLogResource::collection($this->eventLogs)
        ];
    }
}
