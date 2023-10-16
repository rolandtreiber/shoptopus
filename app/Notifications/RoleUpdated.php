<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;

class RoleUpdated extends BaseNotification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        parent::__construct($userId);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(): array
    {
        return ['broadcast', 'database'];
    }

    /**
     * @return array<PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('user-'.$this->userId.'-notifications')];
    }

    public function broadcastType()
    {
        return 'role-updated';
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(): array
    {
        return [
            'message' => $this->createMessage(),
        ];
    }

    public function createMessage(): string
    {
        return 'User role updated.';
    }
}
