<?php

use App\Enums\Permission;

return [
    'super_admin' => [
        // System Users
        Permission::UsersCanInvite,
        Permission::UsersCanUpdate,
        Permission::UsersCanSee,
        Permission::UsersCanList,
        Permission::UsersCanDelete,
        // Customers
        Permission::CustomersCanSee,
        Permission::CustomersCanList,
        Permission::CustomersCanCreate,
        Permission::CustomersCanDelete,
        Permission::CustomersCanUpdate,
        // Products
        Permission::ProductsCanSee,
        Permission::ProductsCanList,
        Permission::ProductsCanCreate,
        Permission::ProductsCanDelete,
        Permission::ProductsCanUpdate,
        // Product Variants
        Permission::ProductVariantsCanSee,
        Permission::ProductVariantsCanList,
        Permission::ProductVariantsCanCreate,
        Permission::ProductVariantsCanDelete,
        Permission::ProductVariantsCanUpdate,
        // Product Categories
        Permission::ProductCategoriesCanSee,
        Permission::ProductCategoriesCanList,
        Permission::ProductCategoriesCanCreate,
        Permission::ProductCategoriesCanDelete,
        Permission::ProductCategoriesCanUpdate,
        // Product Attributes
        Permission::ProductAttributesCanSee,
        Permission::ProductAttributesCanList,
        Permission::ProductAttributesCanCreate,
        Permission::ProductAttributesCanDelete,
        Permission::ProductAttributesCanUpdate,
        // Product Attribute Options
        Permission::ProductAttributeOptionsCanSee,
        Permission::ProductAttributeOptionsCanList,
        Permission::ProductAttributeOptionsCanCreate,
        Permission::ProductAttributeOptionsCanDelete,
        Permission::ProductAttributeOptionsCanUpdate,
        // Product Tags
        Permission::ProductTagsCanSee,
        Permission::ProductTagsCanList,
        Permission::ProductTagsCanCreate,
        Permission::ProductTagsCanDelete,
        Permission::ProductTagsCanUpdate,
        // Orders
        Permission::OrdersCanSee,
        Permission::OrdersCanList,
        Permission::OrdersCanCreate,
        Permission::OrdersCanDelete,
        Permission::OrdersCanUpdate,
        // Discount Rules
        Permission::DiscountRulesCanSee,
        Permission::DiscountRulesCanList,
        Permission::DiscountRulesCanCreate,
        Permission::DiscountRulesCanDelete,
        Permission::DiscountRulesCanUpdate,
        // Voucher Codes
        Permission::VoucherCodesCanSee,
        Permission::VoucherCodesCanList,
        Permission::VoucherCodesCanCreate,
        Permission::VoucherCodesCanDelete,
        Permission::VoucherCodesCanUpdate,
        // DeliveryTypes
        Permission::DeliveryTypesCanSee,
        Permission::DeliveryTypesCanList,
        Permission::DeliveryTypesCanCreate,
        Permission::DeliveryTypesCanDelete,
        Permission::DeliveryTypesCanUpdate,
        // DeliveryRules
        Permission::DeliveryRulesCanSee,
        Permission::DeliveryRulesCanList,
        Permission::DeliveryRulesCanCreate,
        Permission::DeliveryRulesCanDelete,
        Permission::DeliveryRulesCanUpdate,
        // Payments
        Permission::PaymentsCanSee,
        Permission::PaymentsCanList,
        Permission::PaymentsCanCreate,
        Permission::PaymentsCanDelete,
        Permission::PaymentsCanUpdate,
        // Notes
        Permission::NotesCanSee,
        Permission::NotesCanList,
        Permission::NotesCanCreate,
        Permission::NotesCanDelete,
        Permission::NotesCanUpdate,
        // Ratings
        Permission::RatingsCanSee,
        Permission::RatingsCanList,
        Permission::RatingsCanCreate,
        Permission::RatingsCanDelete,
        Permission::RatingsCanUpdate,
        // Banners
        Permission::BannersCanSee,
        Permission::BannersCanList,
        Permission::BannersCanCreate,
        Permission::BannersCanDelete,
        Permission::BannersCanUpdate,
        // Files
        Permission::FilesCanSee,
        Permission::FilesCanList,
        Permission::FilesCanCreate,
        Permission::FilesCanDelete,
        Permission::FilesCanUpdate,
        // Paid Files
        Permission::PaidFilesCanSee,
        Permission::PaidFilesCanList,
        Permission::PaidFilesCanCreate,
        Permission::PaidFilesCanDelete,
        Permission::PaidFilesCanUpdate,
        // Reports
        Permission::ReportsCanSee,
        Permission::ReportsCanList,
        Permission::ReportsCanCreate,
        Permission::ReportsCanDelete,
        Permission::ReportsCanUpdate,
        // Emails
        Permission::EmailsCanSee,
        Permission::EmailsCanSend,
        // Export
        Permission::ExportCanCreate,
        // Import
        Permission::CanMassImportRecords,
    ],
    'admin' => [
        // Customers
        Permission::CustomersCanSee,
        Permission::CustomersCanList,
        Permission::CustomersCanCreate,
        Permission::CustomersCanDelete,
        Permission::CustomersCanUpdate,
        // Products
        Permission::ProductsCanSee,
        Permission::ProductsCanList,
        Permission::ProductsCanCreate,
        Permission::ProductsCanDelete,
        Permission::ProductsCanUpdate,
        // Product Variants
        Permission::ProductVariantsCanSee,
        Permission::ProductVariantsCanList,
        Permission::ProductVariantsCanCreate,
        Permission::ProductVariantsCanDelete,
        Permission::ProductVariantsCanUpdate,
        // Product Categories
        Permission::ProductCategoriesCanSee,
        Permission::ProductCategoriesCanList,
        Permission::ProductCategoriesCanCreate,
        Permission::ProductCategoriesCanDelete,
        Permission::ProductCategoriesCanUpdate,
        // Product Attributes
        Permission::ProductAttributesCanSee,
        Permission::ProductAttributesCanList,
        Permission::ProductAttributesCanCreate,
        Permission::ProductAttributesCanDelete,
        Permission::ProductAttributesCanUpdate,
        // Product Attribute Options
        Permission::ProductAttributeOptionsCanSee,
        Permission::ProductAttributeOptionsCanList,
        Permission::ProductAttributeOptionsCanCreate,
        Permission::ProductAttributeOptionsCanDelete,
        Permission::ProductAttributeOptionsCanUpdate,
        // Product Tags
        Permission::ProductTagsCanSee,
        Permission::ProductTagsCanList,
        Permission::ProductTagsCanCreate,
        Permission::ProductTagsCanDelete,
        Permission::ProductTagsCanUpdate,
        // Orders
        Permission::OrdersCanSee,
        Permission::OrdersCanList,
        Permission::OrdersCanCreate,
        Permission::OrdersCanDelete,
        Permission::OrdersCanUpdate,
        // Discount Rules
        Permission::DiscountRulesCanSee,
        Permission::DiscountRulesCanList,
        Permission::DiscountRulesCanCreate,
        Permission::DiscountRulesCanDelete,
        Permission::DiscountRulesCanUpdate,
        // Voucher Codes
        Permission::VoucherCodesCanSee,
        Permission::VoucherCodesCanList,
        Permission::VoucherCodesCanCreate,
        Permission::VoucherCodesCanDelete,
        Permission::VoucherCodesCanUpdate,
        // DeliveryTypes
        Permission::DeliveryTypesCanSee,
        Permission::DeliveryTypesCanList,
        Permission::DeliveryTypesCanCreate,
        Permission::DeliveryTypesCanDelete,
        Permission::DeliveryTypesCanUpdate,
        // DeliveryRules
        Permission::DeliveryRulesCanSee,
        Permission::DeliveryRulesCanList,
        Permission::DeliveryRulesCanCreate,
        Permission::DeliveryRulesCanDelete,
        Permission::DeliveryRulesCanUpdate,
        // Payments
        Permission::PaymentsCanSee,
        Permission::PaymentsCanList,
        Permission::PaymentsCanCreate,
        Permission::PaymentsCanDelete,
        Permission::PaymentsCanUpdate,
        // Notes
        Permission::NotesCanSee,
        Permission::NotesCanList,
        Permission::NotesCanCreate,
        Permission::NotesCanDelete,
        Permission::NotesCanUpdate,
        // Ratings
        Permission::RatingsCanSee,
        Permission::RatingsCanList,
        Permission::RatingsCanCreate,
        Permission::RatingsCanDelete,
        Permission::RatingsCanUpdate,
        // Banners
        Permission::BannersCanSee,
        Permission::BannersCanList,
        Permission::BannersCanCreate,
        Permission::BannersCanDelete,
        Permission::BannersCanUpdate,
        // Files
        Permission::FilesCanSee,
        Permission::FilesCanList,
        Permission::FilesCanCreate,
        Permission::FilesCanDelete,
        Permission::FilesCanUpdate,
        // Paid Files
        Permission::PaidFilesCanSee,
        Permission::PaidFilesCanList,
        Permission::PaidFilesCanCreate,
        Permission::PaidFilesCanDelete,
        Permission::PaidFilesCanUpdate,
        // Reports
        Permission::ReportsCanSee,
        Permission::ReportsCanList,
        Permission::ReportsCanCreate,
        Permission::ReportsCanDelete,
        Permission::ReportsCanUpdate,
        // Emails
        Permission::EmailsCanSee,
        Permission::EmailsCanSend,
        // Export
        Permission::ExportCanCreate,
        // Import
        Permission::CanMassImportRecords,
    ],
    'store_manager' => [
        // Customers
        Permission::CustomersCanSee,
        Permission::CustomersCanList,
        Permission::CustomersCanCreate,
        Permission::CustomersCanDelete,
        Permission::CustomersCanUpdate,
        // Products
        Permission::ProductsCanSee,
        Permission::ProductsCanList,
        Permission::ProductsCanCreate,
        Permission::ProductsCanDelete,
        Permission::ProductsCanUpdate,
        // Product Variants
        Permission::ProductVariantsCanSee,
        Permission::ProductVariantsCanList,
        Permission::ProductVariantsCanCreate,
        Permission::ProductVariantsCanDelete,
        Permission::ProductVariantsCanUpdate,
        // Orders
        Permission::OrdersCanSee,
        Permission::OrdersCanList,
        Permission::OrdersCanCreate,
        Permission::OrdersCanDelete,
        Permission::OrdersCanUpdate,
        // Discount Rules
        Permission::DiscountRulesCanSee,
        Permission::DiscountRulesCanList,
        Permission::DiscountRulesCanCreate,
        Permission::DiscountRulesCanDelete,
        Permission::DiscountRulesCanUpdate,
        // Voucher Codes
        Permission::VoucherCodesCanSee,
        Permission::VoucherCodesCanList,
        Permission::VoucherCodesCanCreate,
        Permission::VoucherCodesCanDelete,
        Permission::VoucherCodesCanUpdate,
        // Payments
        Permission::PaymentsCanSee,
        Permission::PaymentsCanList,
        Permission::PaymentsCanCreate,
        Permission::PaymentsCanDelete,
        Permission::PaymentsCanUpdate,
        // Notes
        Permission::NotesCanSee,
        Permission::NotesCanList,
        Permission::NotesCanCreate,
        Permission::NotesCanDelete,
        Permission::NotesCanUpdate,
        // Ratings
        Permission::RatingsCanSee,
        Permission::RatingsCanList,
        Permission::RatingsCanCreate,
        Permission::RatingsCanDelete,
        Permission::RatingsCanUpdate,
        // Banners
        Permission::BannersCanSee,
        Permission::BannersCanList,
        Permission::BannersCanCreate,
        Permission::BannersCanDelete,
        Permission::BannersCanUpdate,
        // Files
        Permission::FilesCanSee,
        Permission::FilesCanList,
        Permission::FilesCanCreate,
        Permission::FilesCanDelete,
        Permission::FilesCanUpdate,
        // Paid Files
        Permission::PaidFilesCanSee,
        Permission::PaidFilesCanList,
        Permission::PaidFilesCanCreate,
        Permission::PaidFilesCanDelete,
        Permission::PaidFilesCanUpdate,
        // Reports
        Permission::ReportsCanSee,
        Permission::ReportsCanList,
        Permission::ReportsCanCreate,
        Permission::ReportsCanDelete,
        Permission::ReportsCanUpdate,
        // Emails
        Permission::EmailsCanSee,
        Permission::EmailsCanSend,
        // Export
        Permission::ExportCanCreate,
        // Import
        Permission::CanMassImportRecords,
    ],
    'store_assistant' => [
        // Customers
        Permission::CustomersCanSee,
        Permission::CustomersCanList,
        Permission::CustomersCanCreate,
        Permission::CustomersCanDelete,
        Permission::CustomersCanUpdate,
        // Products
        Permission::ProductsCanSee,
        Permission::ProductsCanList,
        Permission::ProductsCanCreate,
        Permission::ProductsCanDelete,
        Permission::ProductsCanUpdate,
        // Product Variants
        Permission::ProductVariantsCanSee,
        Permission::ProductVariantsCanList,
        Permission::ProductVariantsCanCreate,
        Permission::ProductVariantsCanDelete,
        Permission::ProductVariantsCanUpdate,
        // Orders
        Permission::OrdersCanSee,
        Permission::OrdersCanList,
        Permission::OrdersCanCreate,
        Permission::OrdersCanDelete,
        Permission::OrdersCanUpdate,
        // Voucher Codes
        Permission::VoucherCodesCanSee,
        Permission::VoucherCodesCanList,
        Permission::VoucherCodesCanCreate,
        Permission::VoucherCodesCanDelete,
        Permission::VoucherCodesCanUpdate,
        // Notes
        Permission::NotesCanSee,
        Permission::NotesCanList,
        Permission::NotesCanCreate,
        Permission::NotesCanDelete,
        Permission::NotesCanUpdate,
        // Ratings
        Permission::RatingsCanSee,
        Permission::RatingsCanList,
        Permission::RatingsCanCreate,
        Permission::RatingsCanDelete,
        Permission::RatingsCanUpdate,
        // Banners
        Permission::BannersCanSee,
        Permission::BannersCanList,
        Permission::BannersCanCreate,
        Permission::BannersCanDelete,
        Permission::BannersCanUpdate,
        // Reports
        Permission::ReportsCanSee,
        Permission::ReportsCanList,
        Permission::ReportsCanCreate,
        Permission::ReportsCanDelete,
        Permission::ReportsCanUpdate,
        // Emails
        Permission::EmailsCanSee,
        Permission::EmailsCanSend,
    ],
    'customer' => []
];
