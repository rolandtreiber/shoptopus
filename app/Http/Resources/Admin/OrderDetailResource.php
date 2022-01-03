<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GeneralHelper;
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
            'total_price' => GeneralHelper::displayPrice($this->total_price),
            'address' => new AddressResource($this->address),
            'products' => OrderProductResource::collection($this->products),
            'user' => new UserListResource($this->user),
            'payments' => PaymentRelationResource::collection($this->payments),
            'delivery' => $this->delivery,
            'status' => $this->status,
            'delivery_type' => [
                'name' => $dt->getTranslations('name'),
                'description' => $dt->getTranslations('description')
            ],
            'event_logs' => EventLogResource::collection($this->eventLogs)
        ];
    }
}
