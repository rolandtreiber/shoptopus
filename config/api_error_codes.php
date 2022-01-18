<?php

return [
    'controllers' => [
        'response' => [
            "get" => 1100,
            'generic' => 1101,
            "pagination" => 1104,
            "filter" => 1105
        ]
    ],

    'services' => [
        'auth' => [
            "login" => 1000,
            "register" => 1001,
            "verify" => 1002,
            "resendVerification" => 1003,
            "logout" => 1004,
            "mustResetPassword" => 1005,
            "loginUserIncorrect" => 1006,
            "sendPasswordReset" => 1007,
            "resetPassword" => 1008,
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
