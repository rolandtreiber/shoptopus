<?php

namespace App\Traits;

trait NotificationTrait
{
    public function markNotificationsAsRead()
    {
        $this->unreadNotifications->markAsRead();
    }

    public function markIndividualNotificationAsRead($id)
    {
        $notification = $this->notifications()->findOrFail($id);

        $notification->markAsRead();
    }

    public function deleteAllNotifications()
    {
        $this->notifications()->delete();
    }

    public function deleteNotification($id)
    {
        $notification = $this->notifications()->findOrFail($id);

        $notification->delete();
    }
}
