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
    public function updating(Order $order)
    {
        if($order->isDirty('voucher_code_id') || $order->isDirty('delivery_rule_id')){
            $order->recalculatePrices();
        }
    }
}
