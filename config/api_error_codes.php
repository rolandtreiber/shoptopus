<?php

return [
    'services' => [
        'auth' => [
            "login" => 1000,
            "register" => 1001,
            "verify" => 1002,
            "resendVerification" => 1003,
            "logout" => 1004,
            "mustResetPassword" => 1005,
            "loginUserIncorrect" => 1006
        ],
        'user' => [
            "get" => 2000,
            "getAll" => 2001,
            "post" => 2002,
            "update" => 2003,
            "delete" => 2004,
            "getCurrentUser" => 2005,
            "getByEmail" => 2006,
            "getNotifications" => 2007,
        ],
    ]
];
