<?php

use App\Enums\Permission;
use App\Facades\Module;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DeliveryRuleController;
use App\Http\Controllers\Admin\DeliveryTypeController;
use App\Http\Controllers\Admin\DiscountRuleController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Admin\ProductAttributeOptionController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductTagController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\RatingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleAndPermissionController;
use App\Http\Controllers\Admin\SystemServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VoucherCodeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api', 'set.locale')->group(function () {
    Route::prefix('admin')->group(function () {
        // Products
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->middleware('permission:'. Permission::ProductsCanList)->name('admin.api.index.products');
            Route::get('summary', [ProductController::class, 'summary'])->middleware('permission:'. Permission::ReportsCanSee)->name('admin.api.index.products-page-summary');
            Route::prefix('bulk')->group(function () {
                Route::post('/archive', [ProductController::class, 'bulkArchive'])->middleware('permission:'. Permission::ProductsCanUpdate)->name('admin.api.products.bulk.archive');
                Route::delete('/', [ProductController::class, 'bulkDelete'])->middleware('permission:'. Permission::ProductsCanDelete)->name('admin.api.products.bulk.delete');
            });
        });
        Route::prefix('product')->group(function () {
            Route::post('/', [ProductController::class, 'create'])->middleware('permission:'. Permission::ProductsCanCreate)->name('admin.api.create.product');
            Route::prefix('{product}')->group(function () {
                Route::get('/', [ProductController::class, 'show'])->middleware('permission:'. Permission::ProductsCanSee)->name('admin.api.show.product');
                Route::delete('/', [ProductController::class, 'delete'])->middleware('permission:'. Permission::ProductsCanDelete)->name('admin.api.delete.product');
                Route::patch('/', [ProductController::class, 'update'])->middleware('permission:'. Permission::ProductsCanUpdate)->name('admin.api.update.product');
                Route::get('/insights', [ProductController::class, 'insights'])->middleware('permission:'. Permission::ProductsCanSee)->name('admin.api.show.product-insights');
                Route::get('/paid-contents', [ProductController::class, 'listPaidFiles'])->middleware('permission:'. Permission::PaidFilesCanList)->name('admin.api.list.paid-files');
                Route::post('/paid-content', [ProductController::class, 'savePaidFile'])->middleware('super_user')->middleware('permission:'. Permission::PaidFilesCanCreate)->name('admin.api.save.paid-file');
                Route::patch('/paid-content/{paidFileContent}', [ProductController::class, 'updatePaidFile'])->middleware('super_user')->middleware('permission:'. Permission::PaidFilesCanUpdate)->name('admin.api.update.paid-file');
                Route::delete('/paid-content/{paidFileContent}', [ProductController::class, 'deletePaidFile'])->middleware('super_user')->middleware('permission:'. Permission::PaidFilesCanDelete)->name('admin.api.delete.paid-file');
                Route::get('/paid-content/{paidFileContent}/download', [ProductController::class, 'downloadPaidFileAsAdmin'])->middleware('permission:'. Permission::PaidFilesCanSee)->name('admin.download.paid-file');

                // Product Variants
                Route::get('/variants', [ProductVariantController::class, 'index'])->middleware('permission:'. Permission::ProductVariantsCanList)->name('admin.api.index.product-variants');
                Route::prefix('variant')->group(function () {
                    Route::post('/', [ProductVariantController::class, 'create'])->middleware('permission:'. Permission::ProductVariantsCanCreate)->name('admin.api.create.product-variant');
                    Route::prefix('{variant}')->group(function () {
                        Route::get('/', [ProductVariantController::class, 'show'])->middleware('permission:'. Permission::ProductVariantsCanSee)->name('admin.api.show.product-variant');
                        Route::delete('/', [ProductVariantController::class, 'delete'])->middleware('permission:'. Permission::ProductVariantsCanDelete)->name('admin.api.delete.product-variant');
                        Route::patch('/', [ProductVariantController::class, 'update'])->middleware('permission:'. Permission::ProductVariantsCanUpdate)->name('admin.api.update.product-variant');
                    });
                });
            });
        });

        // Product Categories
        Route::prefix('product-categories')->group(function () {
            Route::get('/', [ProductCategoryController::class, 'index'])->middleware('permission:'. Permission::ProductCategoriesCanList)->name('admin.api.index.product-categories');
            Route::get('select-data', [ProductCategoryController::class, 'getSelectData'])->name('admin.api.index.product-categories-select');
            Route::prefix('bulk')->group(function () {
                Route::post('/availability', [ProductCategoryController::class, 'bulkUpdateAvailability'])->middleware('permission:'. Permission::ProductCategoriesCanUpdate)->name('admin.api.product-categories.bulk.update-availability');
                Route::delete('/', [ProductCategoryController::class, 'bulkDelete'])->middleware('permission:'. Permission::ProductCategoriesCanDelete)->name('admin.api.product-categories.bulk.delete');
            });
        });
        Route::prefix('product-category')->group(function () {
            Route::post('/', [ProductCategoryController::class, 'create'])->middleware('permission:'. Permission::ProductCategoriesCanCreate)->name('admin.api.create.product-category');
            Route::prefix('{category}')->group(function () {
                Route::get('/', [ProductCategoryController::class, 'show'])->middleware('permission:'. Permission::ProductCategoriesCanSee)->name('admin.api.show.product-category');
                Route::delete('/', [ProductCategoryController::class, 'delete'])->middleware('permission:'. Permission::ProductCategoriesCanDelete)->middleware('super_user')->name('admin.api.delete.product-category');
                Route::patch('/', [ProductCategoryController::class, 'update'])->middleware('permission:'. Permission::ProductCategoriesCanUpdate)->name('admin.api.update.product-category');
            });
        });

        // Product Tags
        Route::prefix('product-tags')->group(function () {
            Route::get('/', [ProductTagController::class, 'index'])->middleware('permission:'. Permission::ProductTagsCanList)->name('admin.api.index.product-tags');
            Route::prefix('bulk')->group(function () {
                Route::post('/availability', [ProductTagController::class, 'bulkUpdateAvailability'])->middleware('permission:'. Permission::ProductTagsCanUpdate)->name('admin.api.product-tags.bulk.update-availability');
                Route::delete('/', [ProductTagController::class, 'bulkDelete'])->middleware('permission:'. Permission::ProductTagsCanDelete)->name('admin.api.product-tags.bulk.delete');
            });
        });
        Route::prefix('product-tag')->group(function () {
            Route::post('/', [ProductTagController::class, 'create'])->middleware('permission:'. Permission::ProductTagsCanCreate)->name('admin.api.create.product-tag');
            Route::prefix('{tag}')->group(function () {
                Route::get('/', [ProductTagController::class, 'show'])->middleware('permission:'. Permission::ProductTagsCanSee)->name('admin.api.show.product-tag');
                Route::delete('/', [ProductTagController::class, 'delete'])->middleware('permission:'. Permission::ProductTagsCanDelete)->name('admin.api.delete.product-tag');
                Route::patch('/', [ProductTagController::class, 'update'])->middleware('permission:'. Permission::ProductTagsCanUpdate)->name('admin.api.update.product-tag');
            });
        });

        // Product Attributes
        Route::prefix('product-attributes')->group(function () {
            Route::get('/', [ProductAttributeController::class, 'index'])->middleware('permission:'. Permission::ProductAttributesCanList)->name('admin.api.index.product-attributes');
            Route::prefix('bulk')->group(function () {
                Route::post('/availability', [ProductAttributeController::class, 'bulkUpdateAvailability'])->middleware('permission:'. Permission::ProductAttributesCanUpdate)->name('admin.api.product-attributes.bulk.update-availability');
                Route::delete('/', [ProductAttributeController::class, 'bulkDelete'])->middleware('permission:'. Permission::ProductAttributesCanDelete)->name('admin.api.product-attributes.bulk.delete');
            });
        });
        Route::prefix('product-attribute')->group(function () {
            Route::post('/', [ProductAttributeController::class, 'create'])->middleware('permission:'. Permission::ProductAttributesCanCreate)->name('admin.api.create.product-attribute');
            Route::prefix('{attribute}')->group(function () {
                Route::get('/', [ProductAttributeController::class, 'show'])->middleware('permission:'. Permission::ProductAttributesCanSee)->name('admin.api.show.product-attribute');
                Route::delete('/', [ProductAttributeController::class, 'delete'])->middleware('permission:'. Permission::ProductAttributesCanDelete)->name('admin.api.delete.product-attribute');
                Route::patch('/', [ProductAttributeController::class, 'update'])->middleware('permission:'. Permission::ProductAttributesCanUpdate)->name('admin.api.update.product-attribute');

                // Attribute options
                Route::get('/options', [ProductAttributeOptionController::class, 'index'])->middleware('permission:'. Permission::ProductAttributeOptionsCanList)->name('admin.api.show.product-attribute-options');
                Route::prefix('option')->group(function () {
                    Route::post('/', [ProductAttributeOptionController::class, 'create'])->middleware('permission:'. Permission::ProductAttributeOptionsCanCreate)->name('admin.api.create.product-attribute-option');
                    Route::prefix('{option}')->group(function () {
                        Route::get('/', [ProductAttributeOptionController::class, 'show'])->middleware('permission:'. Permission::ProductAttributeOptionsCanSee)->name('admin.api.show.product-attribute-option');
                        Route::delete('/', [ProductAttributeOptionController::class, 'delete'])->middleware('permission:'. Permission::ProductAttributeOptionsCanDelete)->name('admin.api.delete.product-attribute-option');
                        Route::patch('/', [ProductAttributeOptionController::class, 'update'])->middleware('permission:'. Permission::ProductAttributeOptionsCanUpdate)->name('admin.api.update.product-attribute-option');
                    });
                });
            });
        });

        // Audits
        Route::get('/audits', [AuditController::class, 'index'])->name('admin.api.index.audits');
        Route::get('audit/{audit}', [AuditController::class, 'show'])->name('admin.api.show.audit');

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.api.show.dashboard');

        // Reports
        Route::post('/report', [ReportController::class, 'getChart'])->middleware('permission:'. Permission::ReportsCanSee)->name('admin.api.show.report');

        // Notifications
        Route::get('/notifications', [UserController::class, 'getNotifications'])->name('admin.api.user.get.notifications');
        Route::post('/notifications', [UserController::class, 'clearNotifications'])->name('admin.api.user.clear-notifications');

        // Orders
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->middleware('permission:'. Permission::OrdersCanList)->name('admin.api.index.orders');
            Route::prefix('bulk')->group(function () {
                Route::post('/status', [OrderController::class, 'bulkStatusUpdate'])->middleware('permission:'. Permission::OrdersCanUpdate)->name('admin.api.orders.bulk.status-update');
            });
        });
        Route::prefix('order/{order}')->group(function () {
            Route::get('/', [OrderController::class, 'show'])->middleware('permission:'. Permission::OrdersCanSee)->name('admin.api.show.order');
            Route::middleware(['super_user'])->delete('/', [OrderController::class, 'delete'])->middleware('permission:'. Permission::OrdersCanDelete)->name('admin.api.delete.order');
            Route::patch('/', [OrderController::class, 'updateStatus'])->middleware('permission:'. Permission::OrdersCanUpdate)->name('admin.api.update-status.order');
        });

        // Payments
        Route::prefix('payments')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('admin.api.index.payments');
            Route::prefix('bulk')->group(function () {
                Route::post('/status', [PaymentController::class, 'bulkUpdateStatus'])->middleware('permission:'. Permission::PaymentsCanUpdate)->name('admin.api.payments.bulk.status-update');
            });
        });
        Route::prefix('payment')->group(function () {
            Route::post('/', [PaymentController::class, 'create'])->middleware('super_user')->middleware('permission:'. Permission::PaymentsCanCreate)->name('admin.api.create.payment');
            Route::prefix('{payment}')->group(function () {
                Route::get('/', [PaymentController::class, 'show'])->middleware('permission:'. Permission::PaymentsCanSee)->name('admin.api.show.payment');
                Route::delete('/', [PaymentController::class, 'delete'])->middleware('super_user')->middleware('permission:'. Permission::PaymentsCanDelete)->name('admin.api.delete.payment');
                Route::patch('/', [PaymentController::class, 'update'])->middleware('super_user')->middleware('permission:'. Permission::PaymentsCanUpdate)->name('admin.api.update.payment');
                Route::delete('/refund', [PaymentController::class, 'refund'])->middleware('super_user')->middleware('permission:'. Permission::PaymentsCanUpdate)->name('admin.api.refund.payment');
            });
        });

        // Voucher Codes
        Route::prefix('voucher-codes')->group(function () {
            Route::get('/', [VoucherCodeController::class, 'index'])->middleware('permission:'. Permission::VoucherCodesCanList)->name('admin.api.index.voucher-codes');
            Route::prefix('bulk')->group(function () {
                Route::post('/expire', [VoucherCodeController::class, 'bulkExpire'])->middleware('permission:'. Permission::VoucherCodesCanUpdate)->name('admin.api.voucher-codes.bulk.expire');
                Route::post('/start', [VoucherCodeController::class, 'bulkStart'])->middleware('permission:'. Permission::VoucherCodesCanUpdate)->name('admin.api.voucher-codes.bulk.start');
                Route::post('/activate', [VoucherCodeController::class, 'bulkActivateForPeriod'])->middleware('permission:'. Permission::VoucherCodesCanUpdate)->name('admin.api.voucher-codes.bulk.activate-for-period');
                Route::delete('/', [VoucherCodeController::class, 'bulkDelete'])->middleware('permission:'. Permission::VoucherCodesCanDelete)->name('admin.api.voucher-codes.bulk.delete');
            });
        });
        Route::prefix('voucher-code')->group(function () {
            Route::post('/', [VoucherCodeController::class, 'create'])->middleware('permission:'. Permission::VoucherCodesCanCreate)->name('admin.api.create.voucher-code');
            Route::prefix('{voucherCode}')->group(function () {
                Route::get('/', [VoucherCodeController::class, 'show'])->middleware('permission:'. Permission::VoucherCodesCanSee)->name('admin.api.show.voucher-code');
                Route::delete('/', [VoucherCodeController::class, 'delete'])->middleware('permission:'. Permission::VoucherCodesCanDelete)->name('admin.api.delete.voucher-code');
                Route::patch('/', [VoucherCodeController::class, 'update'])->middleware('permission:'. Permission::VoucherCodesCanUpdate)->name('admin.api.update.voucher-code');
            });
        });

        // Delivery Types
        Route::prefix('delivery-types')->group(function () {
            Route::get('/', [DeliveryTypeController::class, 'index'])->middleware('permission:'. Permission::DeliveryTypesCanList)->name('admin.api.index.delivery-types');
            Route::prefix('bulk')->group(function () {
                Route::post('/availability', [DeliveryTypeController::class, 'bulkUpdateAvailability'])->middleware('permission:'. Permission::DeliveryTypesCanUpdate)->name('admin.api.delivery-types.bulk.update-availability');
                Route::delete('/', [DeliveryTypeController::class, 'bulkDelete'])->middleware('permission:'. Permission::DeliveryTypesCanDelete)->name('admin.api.delivery-types.bulk.delete');
            });
        });
        Route::prefix('delivery-type')->group(function () {
            Route::post('/', [DeliveryTypeController::class, 'create'])->middleware('super_user')->middleware('permission:'. Permission::DeliveryTypesCanCreate)->name('admin.api.create.delivery-type');
            Route::prefix('{deliveryType}')->group(function () {
                Route::get('/', [DeliveryTypeController::class, 'show'])->middleware('permission:'. Permission::DeliveryTypesCanSee)->name('admin.api.show.delivery-type');
                Route::delete('/', [DeliveryTypeController::class, 'delete'])->middleware('permission:'. Permission::DeliveryTypesCanDelete)->middleware('super_user')->name('admin.api.delete.delivery-type');
                Route::patch('/', [DeliveryTypeController::class, 'update'])->middleware('permission:'. Permission::DeliveryTypesCanUpdate)->middleware('super_user')->name('admin.api.update.delivery-type');

                Route::get('delivery-rules', [DeliveryRuleController::class, 'index'])->middleware('permission:'. Permission::DeliveryRulesCanList)->name('admin.api.index.delivery-rules');
                Route::prefix('delivery-rule')->group(function () {
                    Route::post('/', [DeliveryRuleController::class, 'create'])->middleware('super_user')->middleware('permission:'. Permission::DeliveryRulesCanCreate)->name('admin.api.create.delivery-rule');
                    Route::prefix('{deliveryRule}')->group(function () {
                        Route::get('/', [DeliveryRuleController::class, 'show'])->middleware('permission:'. Permission::DeliveryRulesCanSee)->name('admin.api.show.delivery-rule');
                        Route::delete('/', [DeliveryRuleController::class, 'delete'])->middleware('permission:'. Permission::DeliveryRulesCanDelete)->middleware('super_user')->name('admin.api.delete.delivery-rule');
                        Route::patch('/', [DeliveryRuleController::class, 'update'])->middleware('permission:'. Permission::DeliveryRulesCanUpdate)->middleware('super_user')->name('admin.api.update.delivery-rule');
                    });
                });
            });
        });

        // Discount Rules
        Route::prefix('discount-rules')->group(function () {
            Route::get('/', [DiscountRuleController::class, 'index'])->middleware('permission:'. Permission::DiscountRulesCanList)->name('admin.api.index.discount-rules');
            Route::prefix('bulk')->group(function () {
                Route::post('/expire', [DiscountRuleController::class, 'bulkExpire'])->middleware('permission:'. Permission::DiscountRulesCanUpdate)->name('admin.api.discount-rules.bulk.expire');
                Route::post('/start', [DiscountRuleController::class, 'bulkStart'])->middleware('permission:'. Permission::DiscountRulesCanUpdate)->name('admin.api.discount-rules.bulk.start');
                Route::post('/activate', [DiscountRuleController::class, 'bulkActivateForPeriod'])->middleware('permission:'. Permission::DiscountRulesCanUpdate)->name('admin.api.discount-rules.bulk.activate-for-period');
                Route::delete('/', [DiscountRuleController::class, 'bulkDelete'])->middleware('permission:'. Permission::DiscountRulesCanDelete)->name('admin.api.discount-rules.bulk.delete');
            });
        });
        Route::prefix('discount-rule')->group(function () {
            Route::post('/', [DiscountRuleController::class, 'create'])->middleware('super_user')->middleware('permission:'. Permission::DiscountRulesCanCreate)->name('admin.api.create.discount-rule');
            Route::prefix('{discountRule}')->group(function () {
                Route::get('/', [DiscountRuleController::class, 'show'])->middleware('permission:'. Permission::DiscountRulesCanSee)->name('admin.api.show.discount-rule');
                Route::delete('/', [DiscountRuleController::class, 'delete'])->middleware('super_user')->middleware('permission:'. Permission::DiscountRulesCanDelete)->name('admin.api.delete.discount-rule');
                Route::patch('/', [DiscountRuleController::class, 'update'])->middleware('super_user')->middleware('permission:'. Permission::DiscountRulesCanUpdate)->name('admin.api.update.discount-rule');
                Route::get('/available-categories', [DiscountRuleController::class, 'getAvailableCategories'])->middleware('permission:'. Permission::DiscountRulesCanSee)->middleware('super_user')->name('admin.api.show.discount-rule.available-categories');
                Route::get('/available-products', [DiscountRuleController::class, 'getAvailableProducts'])->middleware('permission:'. Permission::DiscountRulesCanSee)->middleware('super_user')->name('admin.api.show.discount-rule.available-products');
                Route::prefix('/products/{product}')->group(function () {
                    Route::post('/', [DiscountRuleController::class, 'addProduct'])->middleware('super_user')->middleware('permission:'. Permission::DiscountRulesCanSee)->name('admin.api.discount-rule.attach-product');
                    Route::delete('/', [DiscountRuleController::class, 'removeProduct'])->middleware('super_user')->middleware('permission:'. Permission::DiscountRulesCanUpdate)->name('admin.api.discount-rule.detach-product');
                });
                Route::prefix('/product-categories/{productCategory}')->group(function () {
                    Route::post('/', [DiscountRuleController::class, 'addProductCategory'])->middleware('super_user')->middleware('permission:'. Permission::DiscountRulesCanSee)->name('admin.api.discount-rule.attach-product-category');
                    Route::delete('/', [DiscountRuleController::class, 'removeProductCategory'])->middleware('super_user')->middleware('permission:'. Permission::DiscountRulesCanUpdate)->name('admin.api.discount-rule.detach-product-category');
                });
            });
        });

        // Users
        Route::get('users', [UserController::class, 'index'])->middleware('permission:'. Permission::UsersCanList)->name('admin.api.index.users');
        Route::prefix('user')->group(function () {
            Route::post('/', [UserController::class, 'create'])->middleware(['role:super_admin'])->middleware('permission:'. Permission::UsersCanInvite)->name('admin.api.create.user');
            Route::patch('/avatar', [UserController::class, 'changeProfileImage'])->name('admin.api.update.avatar');
            Route::prefix('{user}')->group(function () {
                Route::get('/', [UserController::class, 'show'])->middleware('permission:'. Permission::UsersCanSee)->name('admin.api.show.user');
                Route::delete('/', [UserController::class, 'delete'])->middleware(['own_or_super_admin'])->name('admin.api.delete.user');
                Route::patch('/', [UserController::class, 'update'])->middleware(['own_or_super_admin'])->name('admin.api.update.user');
            });
        });

        // Customers
        Route::get('customers', [CustomerController::class, 'index'])->middleware('permission:'. Permission::CustomersCanList)->name('admin.api.index.customers');
        Route::prefix('customer/{customer}')->group(function () {
            Route::get('/', [CustomerController::class, 'show'])->middleware('permission:'. Permission::CustomersCanSee)->name('admin.api.show.customer');
        });

        // Carts
        Route::get('carts', [CartController::class, 'index'])->middleware('permission:'. Permission::CustomersCanSee)->name('admin.api.index.carts');
        Route::prefix('cart')->group(function () {
            Route::post('/', [CartController::class, 'create'])->middleware('permission:'. Permission::CustomersCanUpdate)->name('admin.api.create.cart');
            Route::prefix('{cart}')->group(function () {
                Route::get('/', [CartController::class, 'show'])->middleware('permission:'. Permission::CustomersCanSee)->name('admin.api.show.cart');
                Route::delete('/', [CartController::class, 'delete'])->middleware('permission:'. Permission::CustomersCanUpdate)->name('admin.api.delete.cart');
                Route::patch('/', [CartController::class, 'update'])->middleware('permission:'. Permission::CustomersCanUpdate)->name('admin.api.update.cart');
            });
        });

        // Ratings
        if (Module::enabled('ratings')) {
            Route::prefix('ratings')->group(function () {
                Route::get('/', [RatingController::class, 'index'])->middleware('permission:'. Permission::RatingsCanList)->name('admin.api.index.ratings');
                Route::prefix('bulk')->group(function () {
                    Route::post('/availability', [RatingController::class, 'bulkUpdateAvailability'])->middleware('permission:'. Permission::RatingsCanUpdate)->name('admin.api.ratings.bulk.update-availability');
                    Route::post('/verified-status', [RatingController::class, 'bulkUpdateVerifiedStatus'])->middleware('permission:'. Permission::RatingsCanUpdate)->name('admin.api.ratings.bulk.update-verified-status');
                });
            });
            Route::prefix('rating/{rating}')->group(function () {
                Route::get('/', [RatingController::class, 'show'])->middleware('permission:'. Permission::RatingsCanSee)->name('admin.api.show.rating');
                Route::delete('/', [RatingController::class, 'delete'])->middleware('permission:'. Permission::RatingsCanDelete)->middleware('super_user')->name('admin.api.delete.rating');
            });
        }

        // Carts
        Route::prefix('banners')->group(function () {
            Route::get('/', [BannerController::class, 'index'])->middleware('permission:'. Permission::BannersCanList)->name('admin.api.index.banners');
        });
        Route::prefix('banner')->group(function () {
            Route::prefix('bulk')->group(function () {
                Route::post('/availability', [BannerController::class, 'bulkUpdateAvailability'])->middleware('permission:'. Permission::BannersCanUpdate)->name('admin.api.banners.bulk.update-availability');
                Route::delete('/', [BannerController::class, 'bulkDelete'])->middleware('permission:'. Permission::BannersCanDelete)->name('admin.api.banners.bulk.delete');
            });
            Route::post('/', [BannerController::class, 'create'])->middleware('permission:'. Permission::BannersCanCreate)->name('admin.api.create.banner');
            Route::prefix('{banner}')->group(function () {
                Route::get('/', [BannerController::class, 'show'])->middleware('permission:'. Permission::BannersCanSee)->name('admin.api.show.banner');
                Route::delete('/', [BannerController::class, 'delete'])->middleware('permission:'. Permission::BannersCanDelete)->name('admin.api.delete.banner');
                Route::patch('/', [BannerController::class, 'update'])->middleware('permission:'. Permission::BannersCanUpdate)->name('admin.api.update.banner');
            });
        });

        // Files
        Route::prefix('files')->group(function () {
            Route::get('/', [FileController::class, 'index'])->middleware('permission:'. Permission::FilesCanList)->name('admin.api.index.files');
            Route::prefix('bulk')->group(function () {
                Route::delete('/', [FileController::class, 'bulkDelete'])->middleware('permission:'. Permission::FilesCanDelete)->name('admin.api.files.bulk.delete');
            });
        });
        Route::prefix('file')->group(function () {
            Route::post('/', [FileController::class, 'create'])->middleware('permission:'. Permission::FilesCanCreate)->name('admin.api.create.file');
            Route::prefix('{file}')->group(function () {
                Route::get('/', [FileController::class, 'show'])->middleware('permission:'. Permission::FilesCanSee)->name('admin.api.show.file');
                Route::delete('/', [FileController::class, 'delete'])->middleware('permission:'. Permission::FilesCanDelete)->name('admin.api.delete.file');
                Route::patch('/', [FileController::class, 'update'])->middleware('permission:'. Permission::FilesCanUpdate)->name('admin.api.update.file');
            });
        });

        Route::get('notes', [NoteController::class, 'index'])->middleware('permission:'. Permission::NotesCanList)->name('admin.api.index.notes');
        Route::prefix('note')->group(function () {
            Route::post('/', [NoteController::class, 'create'])->middleware('permission:'. Permission::NotesCanCreate)->name('admin.api.create.note');
            Route::prefix('{note}')->group(function () {
                Route::get('/', [NoteController::class, 'show'])->middleware('permission:'. Permission::NotesCanSee)->name('admin.api.show.note');
                Route::delete('/', [NoteController::class, 'delete'])->middleware('permission:'. Permission::NotesCanDelete)->name('admin.api.delete.note');
                Route::patch('/', [NoteController::class, 'update'])->middleware('permission:'. Permission::NotesCanUpdate)->name('admin.api.update.note');
            });
        });

        // Reports
        Route::prefix('reports')->group(function () {
            Route::post('overview', [ReportController::class, 'getOverview'])->middleware('permission:'. Permission::ReportsCanSee)->name('admin.api.show.report.overview');
            Route::post('sales', [ReportController::class, 'getSales'])->middleware('permission:'. Permission::ReportsCanSee)->name('admin.api.show.report.sales');
            Route::post('product-ratings/{product}', [ReportController::class, 'getProductRatings'])->middleware('permission:'. Permission::ReportsCanSee)->name('admin.api.show.report.product.ratings');
        });

        // Roles
        Route::get('roles', [RoleAndPermissionController::class, 'listRoles'])->middleware('role:super_admin')->name('admin.api.index.roles');
        Route::prefix('role')->middleware('role:super_admin')->group(function () {
            Route::post('/', [RoleAndPermissionController::class, 'createRole'])->name('admin.api.create.role');
            Route::prefix('{role}')->group(function () {
                Route::get('/permissions', [RoleAndPermissionController::class, 'getPermissionsForRole'])->name('admin.api.show.role.permissions');
                Route::get('/users', [RoleAndPermissionController::class, 'getUsersWithRole'])->name('admin.api.show.role.users');
                Route::get('/available-users', [RoleAndPermissionController::class, 'getAvailableUsersForRole'])->name('admin.api.show.available.users.for.role');
                Route::delete('/', [RoleAndPermissionController::class, 'deleteRole'])->name('admin.api.delete.role');
                Route::delete('/permission/{permission}', [RoleAndPermissionController::class, 'removePermissionFromRole'])->name('admin.api.remove.permission.from.role');
                Route::post('/permission/{permission}', [RoleAndPermissionController::class, 'assignPermissionToRole'])->name('admin.api.assign.permission.to.role');
                Route::patch('/', [RoleAndPermissionController::class, 'updateRole'])->name('admin.api.update.role');
                Route::patch('/users/{user}', [RoleAndPermissionController::class, 'assignRoleToUser'])->name('admin.api.assign.role.to.user');
                Route::delete('/users/{user}', [RoleAndPermissionController::class, 'removeRoleFromUser'])->name('admin.api.remove.role.from.user');
            });
        });

        // Permissions
        Route::get('permissions', [RoleAndPermissionController::class, 'listPermissions'])->middleware('role:super_admin')->name('admin.api.index.permissions');

        // Emails
        Route::get('/get-users', [EmailController::class, 'getUserOptions']);
        Route::post('/send-email', [EmailController::class, 'sendEmail'])->middleware('permission:'. Permission::EmailsCanSend)->name('admin.customers.send-email');

        // AI and translations
        Route::post('/ai/translations', [SystemServiceController::class, 'getTranslations']);
        Route::post('/ai/optimise-rewrite', [SystemServiceController::class, 'getOptimiseRewriteTextInMultipleLanguages']);

    });
});
