<?php

namespace App\Repositories\Admin\Eventlog;

use App\Enums\OrderStatuses;
use App\Models\EventLog;
use App\Models\Order;

class EventLogRepository implements EventLogRepositoryInterface {

    public function create(string $modelClass, $model, $type, $data = null)
    {
        $eventLog = new EventLog();
        switch ($modelClass) {
            case (Order::class):
                /** @var Order $model */
                $eventLog->message = 'Order status was updated to ' . OrderStatuses::getKey($model->status);
                $eventLog->type = $type;
                $eventLog->data = $data;
                break;
        }
        $eventLog->eventable_type = $modelClass;
        $eventLog->eventable_id = $model->id;
        $eventLog->save();
    }
}
