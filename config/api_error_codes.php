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
        'access_token' => [
            "get" => 1200,
            "getAll" => 1201,
            "post" => 1202,
            "update" => 1203,
            "delete" => 1204,
            "getByToken" => 1205,
        ],
        'address' => [
            "get" => 1300,
            "getAll" => 1301,
            "post" => 1302,
            "update" => 1303,
            "delete" => 1304,
            "getByToken" => 1305,
        ],
        'user' => [
            "get" => 2000,
            "getAll" => 2001,
            "post" => 2002,
            "update" => 2003,
            "delete" => 2004,
            "getCurrentUser" => 2005,
            "getNotifications" => 2006,
        ],
        'voucher_codes' => [
            "get" => 5000,
            "getAll" => 5001,
            "post" => 5002,
            "update" => 5003,
            "delete" => 5004,
        ]
    ]
];
