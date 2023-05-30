<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\MailMessage;

class ProductRunningLow extends BaseNotification implements UserNotification
{
    public Product $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Product $data, $userId)
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
        return 'product-running-low';
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

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello '.$notifiable->first_name.'!')
            ->line($this->createMessage($this->data));
    }

    public function createMessage($data): string
    {
        return 'Product '.$data->name.' is running low (less than 5 available).';
    }
}
