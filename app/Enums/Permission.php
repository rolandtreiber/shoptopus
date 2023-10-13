<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class Permission extends Enum
{
    // Users
    const UsersCanInvite = 'users.can.invite';
    const UsersCanUpdate = 'users.can.update';
    const UsersCanSee = 'users.can.see';
    const UsersCanList = 'users.can.list';
    const UsersCanDelete = 'users.can.delete';

    // Products
    const ProductsCanSee = 'products.can.see';
    const ProductsCanList = 'products.can.list';
    const ProductsCanCreate = 'products.can.create';
    const ProductsCanDelete = 'products.can.delete';
    const ProductsCanUpdate = 'products.can.update';

    // Product Variants
    const ProductVariantsCanSee = 'product.variants.can.see';
    const ProductVariantsCanList = 'product.variants.can.list';
    const ProductVariantsCanCreate = 'product.variants.can.create';
    const ProductVariantsCanDelete = 'product.variants.can.delete';
    const ProductVariantsCanUpdate = 'product.variants.can.update';

    // Product Categories
    const ProductCategoriesCanSee = 'product.categories.can.see';
    const ProductCategoriesCanList = 'product.categories.can.list';
    const ProductCategoriesCanCreate = 'product.categories.can.create';
    const ProductCategoriesCanDelete = 'product.categories.can.delete';
    const ProductCategoriesCanUpdate = 'product.categories.can.update';

    // Product Attributes
    const ProductAttributesCanSee = 'product.attributes.can.see';
    const ProductAttributesCanList = 'product.attributes.can.list';
    const ProductAttributesCanCreate = 'product.attributes.can.create';
    const ProductAttributesCanDelete = 'product.attributes.can.delete';
    const ProductAttributesCanUpdate = 'product.attributes.can.update';

    // Product Attribute Options
    const ProductAttributeOptionsCanSee = 'product.attribute.options.can.see';
    const ProductAttributeOptionsCanList = 'product.attribute.options.can.list';
    const ProductAttributeOptionsCanCreate = 'product.attribute.options.can.create';
    const ProductAttributeOptionsCanDelete = 'product.attribute.options.can.delete';
    const ProductAttributeOptionsCanUpdate = 'product.attribute.options.can.update';

    // Product Tags
    const ProductTagsCanSee = 'product.tags.can.see';
    const ProductTagsCanList = 'product.tags.can.list';
    const ProductTagsCanCreate = 'product.tags.can.create';
    const ProductTagsCanDelete = 'product.tags.can.delete';
    const ProductTagsCanUpdate = 'product.tags.can.update';

    // Orders
    const OrdersCanSee = 'orders.can.see';
    const OrdersCanList = 'orders.can.list';
    const OrdersCanCreate = 'orders.can.create';
    const OrdersCanDelete = 'orders.can.delete';
    const OrdersCanUpdate = 'orders.can.update';

    // Discount Rules
    const DiscountRulesCanSee = 'discount.rules.can.see';
    const DiscountRulesCanList = 'discount.rules.can.list';
    const DiscountRulesCanCreate = 'discount.rules.can.create';
    const DiscountRulesCanDelete = 'discount.rules.can.delete';
    const DiscountRulesCanUpdate = 'discount.rules.can.update';

    // Voucher Codes
    const VoucherCodesCanSee = 'voucher.codes.can.see';
    const VoucherCodesCanList = 'voucher.codes.can.list';
    const VoucherCodesCanCreate = 'voucher.codes.can.create';
    const VoucherCodesCanDelete = 'voucher.codes.can.delete';
    const VoucherCodesCanUpdate = 'voucher.codes.can.update';

    // Delivery Types
    const DeliveryTypesCanSee = 'delivery.types.can.see';
    const DeliveryTypesCanList = 'delivery.types.can.list';
    const DeliveryTypesCanCreate = 'delivery.types.can.create';
    const DeliveryTypesCanDelete = 'delivery.types.can.delete';
    const DeliveryTypesCanUpdate = 'delivery.types.can.update';

    // Delivery Rules
    const DeliveryRulesCanSee = 'delivery.rules.can.see';
    const DeliveryRulesCanList = 'delivery.rules.can.list';
    const DeliveryRulesCanCreate = 'delivery.rules.can.create';
    const DeliveryRulesCanDelete = 'delivery.rules.can.delete';
    const DeliveryRulesCanUpdate = 'delivery.rules.can.update';

    // Customers
    const CustomersCanSee = 'customers.can.see';
    const CustomersCanList = 'customers.can.list';
    const CustomersCanCreate = 'customers.can.create';
    const CustomersCanDelete = 'customers.can.delete';
    const CustomersCanUpdate = 'customers.can.update';

    // Payments
    const PaymentsCanSee = 'payments.can.see';
    const PaymentsCanList = 'payments.can.list';
    const PaymentsCanCreate = 'payments.can.create';
    const PaymentsCanDelete = 'payments.can.delete';
    const PaymentsCanUpdate = 'payments.can.update';

    // Notes
    const NotesCanSee = 'notes.can.see';
    const NotesCanList = 'notes.can.list';
    const NotesCanCreate = 'notes.can.create';
    const NotesCanDelete = 'notes.can.delete';
    const NotesCanUpdate = 'notes.can.update';

    // Ratings
    const RatingsCanSee = 'ratings.can.see';
    const RatingsCanList = 'ratings.can.list';
    const RatingsCanCreate = 'ratings.can.create';
    const RatingsCanDelete = 'ratings.can.delete';
    const RatingsCanUpdate = 'ratings.can.update';

    // Banners
    const BannersCanSee = 'banners.can.see';
    const BannersCanList = 'banners.can.list';
    const BannersCanCreate = 'banners.can.create';
    const BannersCanDelete = 'banners.can.delete';
    const BannersCanUpdate = 'banners.can.update';

    // Files
    const FilesCanSee = 'files.can.see';
    const FilesCanList = 'files.can.list';
    const FilesCanCreate = 'files.can.create';
    const FilesCanDelete = 'files.can.delete';
    const FilesCanUpdate = 'files.can.update';

    // Paid Files
    const PaidFilesCanSee = 'paid.files.can.see';
    const PaidFilesCanList = 'paid.files.can.list';
    const PaidFilesCanCreate = 'paid.files.can.create';
    const PaidFilesCanDelete = 'paid.files.can.delete';
    const PaidFilesCanUpdate = 'paid.files.can.update';

    // Reports
    const ReportsCanSee = 'reports.can.see';
    const ReportsCanList = 'reports.can.list';
    const ReportsCanCreate = 'reports.can.create';
    const ReportsCanDelete = 'reports.can.delete';
    const ReportsCanUpdate = 'reports.can.update';

    // Email
    const EmailsCanSee = 'emails.can.see';
    const EmailsCanSend = 'emails.can.list';

    // Export
    const ExportCanCreate = 'export.can.create';

    // Import
    const CanMassImportRecords = 'can.import.mass.records';

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
