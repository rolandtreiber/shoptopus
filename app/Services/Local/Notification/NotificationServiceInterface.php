<?php

namespace App\Services\Local\Notification;

interface NotificationServiceInterface
{
    /**
     * Get currently authenticated user's notifications
     *
     *
     * @throws \Exception
     */
    public function getAllUnreadNotificationsForUser(): array;

    /**
     * Mark a notification read
     */
    public function markRead(array $payload): array;
}
