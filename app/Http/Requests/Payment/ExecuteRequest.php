<?php

namespace App\Http\Requests\Payment;

use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class ExecuteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return DB::table('orders')
            ->where('id', $this->orderId)
            ->where('user_id', $this->user()->id)
            ->where('status', OrderStatus::AwaitingPayment)
            ->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'provider_payload' => 'sometimes|array',
            'provider' => 'required|in:paypal,amazon,stripe',
            'orderId' => 'required|string|exists:orders,id'
        ];
    }
}
