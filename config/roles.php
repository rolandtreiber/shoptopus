<?php

use App\Enums\Permissions;

return [
    'super_admin' => [
        Permissions::CanActAsCustomer,
        Permissions::UsersCanCreate,
        Permissions::UsersCanUpdate,
        Permissions::UsersCanChangeRole,
        Permissions::UsersCanDelete,
        Permissions::ProductsCanCreate,
        Permissions::ProductsCanUpdate,
        Permissions::ProductsCanDelete,
        Permissions::DeliveryRulesCanCreate,
        Permissions::DeliveryRulesCanUpdate,
        Permissions::DeliveryRulesCanDelete
    ],
    'admin' => [
        Permissions::CanActAsCustomer,
        Permissions::ProductsCanCreate,
        Permissions::ProductsCanUpdate,
        Permissions::ProductsCanDelete,
        Permissions::DeliveryRulesCanCreate,
        Permissions::DeliveryRulesCanUpdate,
        Permissions::DeliveryRulesCanDelete
    ],
    'store_manager' => [
        Permissions::CanActAsCustomer,
        Permissions::ProductsCanCreate,
        Permissions::ProductsCanUpdate,
        Permissions::ProductsCanDelete,
        Permissions::DeliveryRulesCanCreate,
        Permissions::DeliveryRulesCanUpdate,
        Permissions::DeliveryRulesCanDelete
    ],
    'store_assistant' => [
        Permissions::ProductsCanCreate,
        Permissions::ProductsCanUpdate,
        Permissions::ProductsCanDelete,
        Permissions::CanActAsCustomer,
    ],
    'customer' => [
        Permissions::CanShop,
        Permissions::CanViewOwnAccount,
    ]
];
