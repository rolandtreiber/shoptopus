<?php

namespace App\Http\Requests\Local\Checkout;

use App\Enums\OrderStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

/**
 * @property string $order_id
 * @property string|null $client_ref
 */
class RevertOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $order_id = $this->order_id;

        if ($user) {
            $userOrderIds = DB::table('orders')
                ->where('user_id', $user->id)
                ->where('status', OrderStatus::AwaitingPayment)
                ->pluck('id')->toArray();
            if (in_array($order_id, $userOrderIds)) {
                return true;
            }
            return false;
        } else {
            if ($this->client_ref) {
                $user = DB::table('users')
                    ->where('client_ref', "=", $this->client_ref)
                    ->where('temporary', "=", 1)
                    ->first();
                if ($user) {
                    $userOrderIds = DB::table('orders')
                        ->where('user_id', $user->id)
                        ->where('status', OrderStatus::AwaitingPayment)
                        ->pluck('id')->toArray();
                    if (in_array($order_id, $userOrderIds)) {
                        return true;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|string|exists:orders,id',
            'client_ref' => 'nullable|string|exists:users,client_ref',
        ];
    }
}
