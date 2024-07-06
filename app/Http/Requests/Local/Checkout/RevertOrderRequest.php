<?php

namespace App\Http\Requests\Local\Checkout;

use App\Enums\OrderStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

/**
 * @property string $order_id
 * @property string|null $user_id
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

        // If no order id was specified, then it is a validation error, therefore we are letting it through authorization phase on purpose.
        if (!$order_id) {
            return false;
        }

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
            if ($this->user_id) {
                $user = DB::table('users')
                    ->where('id', "=", $this->user_id)
                    ->where('temporary', "=", 1)
                    ->first();
                if ($user) {
                    $userOrderIds = DB::table('orders')
                        ->where('user_id', $user['id'])
                        ->where('status', OrderStatus::AwaitingPayment)
                        ->pluck('id')
                        ->toArray();
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
            'user_id' => 'nullable|string|exists:users,id',
        ];
    }
}
