<?php

namespace App\Http\Requests\Local\Payment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $orderId
 * @property string|null $cartId
 * @property string|null $voucherCode
 * @property string|null $deliveryTypeId
 */
class GetClientSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'orderId' => 'string|exists:orders,id',
            'cartId' => 'string|exists:carts,id',
            'voucherCode' => 'string|exists:voucher_codes,code',
            'deliveryTypeId' => 'string|exists:delivery_types,id',
        ];
    }
}
