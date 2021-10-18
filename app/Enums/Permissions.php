<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class Permissions extends Enum
{
    // Users
    const UsersCanCreate = 'users.can.create';
    const UsersCanChangeRole = 'users.can.change.role';
    const UsersCanDelete = 'users.can.delete';
    const UsersCanUpdate = 'users.can.update';

    // Products
    const ProductsCanCreate = 'products.can.create';
    const ProductsCanDelete = 'products.can.delete';
    const ProductsCanUpdate = 'products.can.update';

    // DeliveryRules
    const DeliveryRulesCanCreate = 'delivery-rules.can.create';
    const DeliveryRulesCanDelete = 'delivery-rules.can.delete';
    const DeliveryRulesCanUpdate = 'delivery-rules.can.update';

    // Customer
    const CanViewOwnAccount = 'can-view-own-account';
    const CanShop = 'can-shop';
    const CanActAsCustomer = 'can-act-as-customer';

    // Seller
    const OwnProductsCanUpdate = 'own-products.can.create';
    const OwnProductsCanDelete = 'own-products.can.delete';

    // Seller
    const CanAccessAuditables = 'auditables.can.view';

}
