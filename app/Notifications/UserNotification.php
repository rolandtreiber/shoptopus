<?php

namespace App\Notifications;

interface UserNotification
{
    public function createMessage($data): string;
}
