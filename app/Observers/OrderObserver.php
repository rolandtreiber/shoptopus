<?php

namespace App\Observers;

use App\Enums\EventLogType;
use App\Enums\OrderStatus;
use App\Events\OrderCompletedEvent;
use App\Events\OrderPlacedEvent;
use App\Events\OrderStatusUpdatedEvent;
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
     */
    public function created(Order $order): void
    {
        $this->orderRepository->triggerNewOrderNotification($order);
    }

    /**
     * Listen to the Order updating event.
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
            switch ($order->status) {
                case OrderStatus::Paid:
                    if ($order->invoice) {
                        event(new OrderPlacedEvent($order->invoice));
                    }
                    break;
                case OrderStatus::Completed:
                    event(new OrderCompletedEvent($order));
                    break;
                default:
                    event(new OrderStatusUpdatedEvent($order));
                    break;
            }
        }
    }
}
