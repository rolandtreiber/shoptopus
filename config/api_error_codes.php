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
            "login_user_incorrect" => 1001,
            "register" => 1002,
            "verify" => 1003,
            "resendVerification" => 1004,
            "logout" => 1005,
            "mustResetPassword" => 1006,
            "loginUserIncorrect" => 1007,
            "sendPasswordReset" => 1008,
            "resetPassword" => 1009,
            "getOAuthProviderTargetUrl" => 1010,
            "handleOAuthProviderCallback" => 1011,
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
        'cart' => [
            "get" => 1500,
            "update" => 1503,
            "getCartForUser" => 1505,
            "addItem" => 1506,
            "removeItem" => 1507,
            "mergeUserCarts" => 1508
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
