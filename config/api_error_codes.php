<?php

return [
    'controllers' => [
        'response' => [
            'get' => 1100,
            'generic' => 1101,
            'pagination' => 1104,
            'filter' => 1105,
        ],
    ],

    'services' => [
        'auth' => [
            'login' => 1000,
            'login_user_incorrect' => 1001,
            'register' => 1002,
            'verify' => 1003,
            'resendVerification' => 1004,
            'logout' => 1005,
            'mustResetPassword' => 1006,
            'loginUserIncorrect' => 1007,
            'sendPasswordReset' => 1008,
            'resetPassword' => 1009,
            'getOAuthProviderTargetUrl' => 1010,
            'handleOAuthProviderCallback' => 1011,
            'details' => 1012,
            'must_reset_password' => 1013,
            'email_address_taken' => 1014,
            'not_verified' => 1015,
        ],
        'access_token' => [
            'get' => 1200,
            'getAll' => 1201,
            'post' => 1202,
            'update' => 1203,
            'delete' => 1204,
            'getByToken' => 1205,
        ],
        'address' => [
            'get' => 1300,
            'getAll' => 1301,
            'post' => 1302,
            'update' => 1303,
            'delete' => 1304,
            'getByToken' => 1305,
        ],
        'cart' => [
            'get' => 1500,
            'update' => 1501,
            'addItem' => 1502,
            'removeItem' => 1503,
            'mergeUserCarts' => 1504,
            'getCartForUser' => 1505,
            'updateQuantity' => 1506,
            'productNotFound' => 1507,
            'removeAll' => 1508,
        ],
        'notification' => [
            'getAllUnreadNotificationsForUser' => 1900,
            'markRead' => 1901,
        ],
        'product' => [
            'get' => 2000,
            'getAll' => 2001,
            'getAllInCategory' => 2002,
            'favorite' => 2003,
            'review' => 2004,
        ],
        'product_attribute' => [
            'get' => 2100,
            'getAll' => 2101,
            'getAllForProductCategory' => 2102,
        ],
        'product_category' => [
            'get' => 2200,
            'getAll' => 2201,
        ],
        'invoices' => [
            'download' => 3000,
        ],
        'remote' => [
            'payment' => [
                'getClientSettings' => 3000,
                'execute' => 3001,
                'formatPaymentResponse' => 3002,
            ],
        ],
        'user' => [
            'get' => 4000,
            'getAll' => 4001,
            'post' => 4002,
            'update' => 4003,
            'delete' => 4004,
            'getCurrentUser' => 4005,
            'getNotifications' => 4006,
            'getFavoritedProductIds' => 4007,
            'favorites' => 4008,
        ],
    ],
];
