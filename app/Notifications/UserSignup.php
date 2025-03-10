<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;

class UserSignup extends BaseNotification implements UserNotification
{
    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $data, $userId)
    {
        parent::__construct($userId);
        $this->data = $data;
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

    public function broadcastType(): string
    {
        return 'user-signup';
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(): array
    {
        return [
            'message' => $this->createMessage($this->data),
        ];
    }

    public function createMessage($data): string
    {
        return 'New user ('.$data->name.') <'.$data->email.'> signed up';
    }
}
