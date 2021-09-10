<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $userId;
    public $payload;
    public $timestamp;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId, $payload)
    {
        $this->userId = $userId;
        $this->payload = $payload;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('user-'.$this->userId.'-event');
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'user-notification';
    }
}
