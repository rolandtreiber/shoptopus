<?php

namespace App\Observers;

use App\Models\Order;
use App\Repositories\Admin\Eventlog\EventLogRepository;
use App\Repositories\Admin\Eventlog\EventLogRepositoryInterface;
use App\Enums\EventLogType;

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
        if ($order->delivery_type) {
            $order->delivery_cost = $order->delivery_type->price;
        }
        $this->eventLogRepository->create(Order::class, $order, EventLogType::StatusChange, ['status' => $order->status]);
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
            $order->delivery_cost = $order->delivery_type->price;
            $order->recalculatePrices();
        }

        if($order->isDirty('status')) {
            $this->eventLogRepository->create(Order::class, $order, EventLogType::StatusChange, ['status' => $order->status]);
        }

    }
}
