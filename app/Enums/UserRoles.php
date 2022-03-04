<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class UserRoles
 * @package App\Enums
 */
final class UserRoles extends Enum
{
    public const SuperAdmin = 'super_admin';
    public const Admin = 'admin';
    public const StoreManager = 'store_manager';
    public const StoreAssistant = 'store_assistant';
    public const Seller = 'seller';
    public const Customer = 'customer';
    public const Auditor = 'auditor';

}
