<?php

namespace App\Notifications;

interface UserNotification {

    /**
     * @param $data
     * @return string
     */
    function createMessage($data): string;

}
