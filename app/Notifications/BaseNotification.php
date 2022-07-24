<?php

namespace App\Notifications;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class BaseNotification extends Notification implements ShouldBroadcastNow {

    use Queueable, SerializesModels, InteractsWithSockets;

    public string $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

}
