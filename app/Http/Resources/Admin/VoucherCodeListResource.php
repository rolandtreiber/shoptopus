<?php

namespace App\Http\Resources\Admin;

use App\Enums\OrderStatus;
use App\Models\VoucherCode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @mixin VoucherCode
 */
class VoucherCodeListResource extends JsonResource
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
            'type' => $this->type,
            'amount' => $this->amount,
            'value' => $this->value,
            'code' => $this->code,
            'valid_from' => Carbon::parse($this->valid_from),
            'valid_until' => Carbon::parse($this->valid_until),
            'used' => DB::table('orders')->whereIn('status', [
                OrderStatus::Completed,
                OrderStatus::InTransit,
                OrderStatus::Paid,
                OrderStatus::Processing,
                OrderStatus::OnHold,
            ])->where('voucher_code_id', $this->id)->count(),
            'enabled' => $this->enabled,
            'status' => $this->status,
        ];
    }
}
