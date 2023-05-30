<?php

namespace App\Notifications;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\MailMessage;

class NewOrder extends BaseNotification implements UserNotification
{
    public Order $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $data, $userId)
    {
        parent::__construct($userId);
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(): array
    {
        return ['broadcast', 'database', 'mail'];
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('user-'.$this->userId.'-notifications');
    }

    public function broadcastType()
    {
        return 'new-order';
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => $this->createMessage($this->data),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Hello '.$notifiable->first_name.'!')
            ->line($this->createMessage($this->data));
    }

    public function createMessage($data): string
    {
        return 'New order of ('.$data->currency_code.' '.$data->total_price.') was placed by ('.$data->user->name.') <'.$data->user->email.'> at '.Carbon::parse($data->created_at)->format('d/m/Y H:i').'.';
    }
}
