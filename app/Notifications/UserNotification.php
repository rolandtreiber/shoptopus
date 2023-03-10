<?php

namespace App\Notifications;

interface UserNotification
{
    /**
     * @param $data
     * @return string
     */
    public function createMessage($data): string;
}
