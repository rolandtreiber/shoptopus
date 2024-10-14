<?php

namespace App\Http\Requests\Local\Payment;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ExecuteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->user()) {
            return DB::table('orders')
                ->where('id', $this->orderId)
                ->where('user_id', $this->user()->id)
                ->where('status', OrderStatus::AwaitingPayment)
                ->exists();
        } else {
            return DB::table('orders')
                ->where('id', $this->orderId)
                ->where('status', OrderStatus::AwaitingPayment)
                ->exists();
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'provider_payload' => 'sometimes|array',
            'provider' => 'required|in:paypal,amazon,stripe',
            'orderId' => 'required|string|exists:orders,id',
        ];
    }
}
