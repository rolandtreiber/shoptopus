<?php

namespace App\Http\Resources\Admin;

use App\Models\VoucherCode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin VoucherCode
 */
class VoucherCodeListResource extends JsonResource
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
            'type' => $this->type,
            'amount' => $this->amount,
            'code' => $this->code,
            'valid_from' => Carbon::parse($this->valid_from),
            'valid_until' => Carbon::parse($this->valid_until),
        ];
    }
}
