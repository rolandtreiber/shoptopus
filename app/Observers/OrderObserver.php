<?php

namespace App\Observers;

use App\Enums\EventLogTypes;
use App\Models\Order;
use App\Repositories\Admin\EventLogRepository;
use App\Repositories\Admin\Interfaces\EventLogRepositoryInterface;

class OrderObserver
{
    private EventLogRepositoryInterface $eventLogRepository;

    public function __construct(EventLogRepository $eventLogRepository)
    {
        $this->eventLogRepository = $eventLogRepository;
    }

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
        $this->eventLogRepository->create(Order::class, $order, EventLogTypes::StatusChange, ['status' => $order->status]);
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

        if($order->isDirty('status')) {
            $this->eventLogRepository->create(Order::class, $order, EventLogTypes::StatusChange, ['status' => $order->status]);
        }

    }
}
