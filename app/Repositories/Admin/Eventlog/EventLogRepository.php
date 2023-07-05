<?php

namespace App\Repositories\Admin\Eventlog;

use App\Enums\OrderStatus;
use App\Models\EventLog;
use App\Models\Order;

class EventLogRepository implements EventLogRepositoryInterface
{
    public function create(string $modelClass, $model, $type, $data = null): void
    {
        $statusNames = [
            OrderStatus::AwaitingPayment => "Awaiting Payment",
            OrderStatus::Paid => "Paid",
            OrderStatus::Processing => "Processing",
            OrderStatus::InTransit => "In Transit",
            OrderStatus::Completed => "Completed",
            OrderStatus::Cancelled => "Cancelled",
            OrderStatus::OnHold => "On Hold",
        ];

        $eventLog = new EventLog();
        switch ($modelClass) {
            case Order::class:
                /** @var Order $model */
                $eventLog->message = 'Order status was updated to '.$statusNames[$model->status];
                $eventLog->type = $type;
                $eventLog->data = $data;
                break;
        }
        $eventLog->eventable_type = $modelClass;
        $eventLog->eventable_id = $model->id;
        $eventLog->save();
    }
}
