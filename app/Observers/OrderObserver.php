<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Listen to the Order updating event.
     *
     * @param Order $order
     * @return void
     */
    public function creating(Order $order)
    {
        if ($order->deliveryType) {
            $order->delivery = $order->deliveryType->price;
        }
    }

    /**
     * Listen to the Order updating event.
     *
     * @param Order $order
     * @return void
     */
    public function updating(Order $order)
    {
        if($order->isDirty('voucher_code_id') || $order->isDirty('delivery_type_id')){
            $order->delivery = $order->deliveryType->price;
            $order->recalculatePrices();
        }
    }
}
