<?php

namespace App\Observers;

use App\Enums\EventLogType;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Repositories\Admin\Eventlog\EventLogRepository;
use App\Repositories\Admin\Eventlog\EventLogRepositoryInterface;
use App\Repositories\Admin\Order\OrderRepository;
use App\Repositories\Admin\Order\OrderRepositoryInterface;

class OrderObserver
{
    private EventLogRepositoryInterface $eventLogRepository;

    private OrderRepositoryInterface $orderRepository;

    public function __construct(EventLogRepository $eventLogRepository, OrderRepository $orderRepository)
    {
        $this->eventLogRepository = $eventLogRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Listen to the Order updating event.
     *
     * @return void
     */
    public function creating(Order $order): void
    {
        if ($order->delivery_type) {
            $order->delivery_cost = $order->delivery_type->price;
        }
        $this->eventLogRepository->create(Order::class, $order, EventLogType::StatusChange, ['status' => $order->status]);
    }

    /**
     * Listen to the Order updating event.
     *
     * @return void
     */
    public function created(Order $order): void
    {
        $this->orderRepository->triggerNewOrderNotification($order);
    }

    /**
     * Listen to the Order updating event.
     *
     * @return void
     */
    public function updating(Order $order): void
    {
        if ($order->isDirty(['voucher_code_id', 'delivery_type_id'])) {
            $dt = DeliveryType::find($order->delivery_type_id);

            if ($dt) {
                $order->delivery_cost = $dt->price;
                $order->recalculatePrices();
            }
        }

        if ($order->isDirty('status')) {
            $this->eventLogRepository->create(Order::class, $order, EventLogType::StatusChange, ['status' => $order->status]);
        }
    }
}
