<?php

namespace App\Services\Local\Notification;

interface NotificationServiceInterface
{
    /**
     * Get currently authenticated user's notifications
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getAllUnreadNotificationsForUser(): array;

    /**
     * Mark a notification read
     *
     * @param  array  $payload
     * @return array
     */
    public function markRead(array $payload): array;
}
