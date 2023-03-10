<?php

use App\Enums\Permission;

return [
    'super_admin' => [
        Permission::CanActAsCustomer,
        Permission::UsersCanCreate,
        Permission::UsersCanUpdate,
        Permission::UsersCanChangeRole,
        Permission::UsersCanDelete,
        Permission::ProductsCanCreate,
        Permission::ProductsCanUpdate,
        Permission::ProductsCanDelete,
        Permission::DeliveryRulesCanCreate,
        Permission::DeliveryRulesCanUpdate,
        Permission::DeliveryRulesCanDelete,
    ],
    'admin' => [
        Permission::CanActAsCustomer,
        Permission::ProductsCanCreate,
        Permission::ProductsCanUpdate,
        Permission::ProductsCanDelete,
        Permission::DeliveryRulesCanCreate,
        Permission::DeliveryRulesCanUpdate,
        Permission::DeliveryRulesCanDelete,
    ],
    'store_manager' => [
        Permission::CanActAsCustomer,
        Permission::ProductsCanCreate,
        Permission::ProductsCanUpdate,
        Permission::ProductsCanDelete,
        Permission::DeliveryRulesCanCreate,
        Permission::DeliveryRulesCanUpdate,
        Permission::DeliveryRulesCanDelete,
    ],
    'store_assistant' => [
        Permission::ProductsCanCreate,
        Permission::ProductsCanUpdate,
        Permission::ProductsCanDelete,
        Permission::CanActAsCustomer,
    ],
    'seller' => [
        Permission::ProductsCanCreate,
        Permission::OwnProductsCanUpdate,
        Permission::OwnProductsCanDelete,
        Permission::CanActAsCustomer,
    ],
    'customer' => [
        Permission::CanShop,
        Permission::CanViewOwnAccount,
    ],
    'auditor' => [
        Permission::CanAccessAuditables,
    ],
];
