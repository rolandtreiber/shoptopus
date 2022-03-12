<?php

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
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VoucherCodeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api', 'admin', 'set.locale']], function () {
    Route::group(['prefix' => 'admin'], function () {

        // Products
        Route::group(['prefix' => 'products'], function () {
            Route::get('/', [ProductController::class, 'index'])->name('admin.api.index.products');
            Route::get('summary', [ProductController::class, 'summary'])->name('admin.api.index.products-page-summary');
            Route::group(['prefix' => 'bulk'], function () {
                Route::post('/archive', [ProductController::class, 'bulkArchive'])->name('admin.api.products.bulk.archive');
                Route::delete('/', [ProductController::class, 'bulkDelete'])->name('admin.api.products.bulk.delete');
            });
        });
        Route::group(['prefix' => 'product'], function () {
            Route::post('/', [ProductController::class, 'create'])->name('admin.api.create.product');
            Route::group(['prefix' => '{product}'], function () {
                Route::get('/', [ProductController::class, 'show'])->name('admin.api.show.product');
                Route::delete('/', [ProductController::class, 'delete'])->name('admin.api.delete.product');
                Route::patch('/', [ProductController::class, 'update'])->name('admin.api.update.product');

                // Product Variants
                Route::get('/variants', [ProductVariantController::class, 'index'])->name('admin.api.index.product-variants');
                Route::group(['prefix' => 'variant'], function () {
                    Route::post('/', [ProductVariantController::class, 'create'])->name('admin.api.create.product-variant');
                    Route::group(['prefix' => '{variant}'], function () {
                        Route::get('/', [ProductVariantController::class, 'show'])->name('admin.api.show.product-variant');
                        Route::delete('/', [ProductVariantController::class, 'delete'])->name('admin.api.delete.product-variant');
                        Route::patch('/', [ProductVariantController::class, 'update'])->name('admin.api.update.product-variant');
                    });
                });

            });
        });

        // Product Categories
        Route::group(['prefix' => 'product-categories'], function () {
            Route::get('/', [ProductCategoryController::class, 'index'])->name('admin.api.index.product-categories');
            Route::get('select-data', [ProductCategoryController::class, 'getSelectData'])->name('admin.api.index.product-categories-select');
            Route::group(['prefix' => 'bulk'], function () {
                Route::post('/availability', [ProductCategoryController::class, 'bulkUpdateAvailability'])->name('admin.api.product-categories.bulk.update-availability');
                Route::delete('/', [ProductCategoryController::class, 'bulkDelete'])->name('admin.api.product-categories.bulk.delete');
            });
        });
        Route::group(['prefix' => 'product-category'], function () {
            Route::post('/', [ProductCategoryController::class, 'create'])->name('admin.api.create.product-category');
            Route::group(['prefix' => '{category}'], function () {
                Route::get('/', [ProductCategoryController::class, 'show'])->name('admin.api.show.product-category');
                Route::delete('/', [ProductCategoryController::class, 'delete'])->middleware('super_user')->name('admin.api.delete.product-category');
                Route::patch('/', [ProductCategoryController::class, 'update'])->name('admin.api.update.product-category');
            });
        });

        // Product Tags
        Route::group(['prefix' => 'product-tags'], function () {
            Route::get('/', [ProductTagController::class, 'index'])->name('admin.api.index.product-tags');
            Route::group(['prefix' => 'bulk'], function () {
                Route::post('/availability', [ProductTagController::class, 'bulkUpdateAvailability'])->name('admin.api.product-tags.bulk.update-availability');
                Route::delete('/', [ProductTagController::class, 'bulkDelete'])->name('admin.api.product-tags.bulk.delete');
            });
        });
        Route::group(['prefix' => 'product-tag'], function () {
            Route::post('/', [ProductTagController::class, 'create'])->name('admin.api.create.product-tag');
            Route::group(['prefix' => '{tag}'], function () {
                Route::get('/', [ProductTagController::class, 'show'])->name('admin.api.show.product-tag');
                Route::delete('/', [ProductTagController::class, 'delete'])->name('admin.api.delete.product-tag');
                Route::patch('/', [ProductTagController::class, 'update'])->name('admin.api.update.product-tag');
            });
        });

        // Product Attributes
        Route::group(['prefix' => 'product-attributes'], function () {
            Route::get('/', [ProductAttributeController::class, 'index'])->name('admin.api.index.product-attributes');
            Route::group(['prefix' => 'bulk'], function () {
                Route::post('/availability', [ProductAttributeController::class, 'bulkUpdateAvailability'])->name('admin.api.product-attributes.bulk.update-availability');
                Route::delete('/', [ProductAttributeController::class, 'bulkDelete'])->name('admin.api.product-attributes.bulk.delete');
            });
        });
        Route::group(['prefix' => 'product-attribute'], function () {
            Route::post('/', [ProductAttributeController::class, 'create'])->name('admin.api.create.product-attribute');
            Route::group(['prefix' => '{attribute}'], function () {
                Route::get('/', [ProductAttributeController::class, 'show'])->name('admin.api.show.product-attribute');
                Route::delete('/', [ProductAttributeController::class, 'delete'])->name('admin.api.delete.product-attribute');
                Route::patch('/', [ProductAttributeController::class, 'update'])->name('admin.api.update.product-attribute');

                // Attribute options
                Route::get('/options', [ProductAttributeOptionController::class, 'index'])->name('admin.api.show.product-attribute-options');
                Route::group(['prefix' => 'option'], function () {
                    Route::post('/', [ProductAttributeOptionController::class, 'create'])->name('admin.api.create.product-attribute-option');
                    Route::group(['prefix' => '{option}'], function () {
                        Route::get('/', [ProductAttributeOptionController::class, 'show'])->name('admin.api.show.product-attribute-option');
                        Route::delete('/', [ProductAttributeOptionController::class, 'delete'])->name('admin.api.delete.product-attribute-option');
                        Route::patch('/', [ProductAttributeOptionController::class, 'update'])->name('admin.api.update.product-attribute-option');
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
        Route::get('/report', [ReportController::class, 'chart'])->name('admin.api.show.report');

        // Notifications
        Route::get('/notifications', [UserController::class, 'getNotifications'])->name('admin.api.user.get.notifications');
        Route::post('/notifications', [UserController::class, 'clearNotifications'])->name('admin.api.user.clear-notifications');

        // Orders
        Route::group(['prefix' => 'orders'], function () {
            Route::get('/', [OrderController::class, 'index'])->name('admin.api.index.orders');
            Route::group(['prefix' => 'bulk'], function () {
                Route::post('/status', [OrderController::class, 'bulkStatusUpdate'])->name('admin.api.orders.bulk.status-update');
            });
        });
        Route::group(['prefix' => 'order/{order}'], function () {
            Route::get('/', [OrderController::class, 'show'])->name('admin.api.show.order');
            Route::middleware(['super_user'])->delete('/', [OrderController::class, 'delete'])->name('admin.api.delete.order');
            Route::patch('/', [OrderController::class, 'updateStatus'])->name('admin.api.update-status.order');
        });

        // Payments
        Route::group(['prefix' => 'payments'], function () {
            Route::get('/', [PaymentController::class, 'index'])->name('admin.api.index.payments');
            Route::group(['prefix' => 'bulk'], function () {
                Route::post('/status', [PaymentController::class, 'bulkUpdateStatus'])->name('admin.api.payments.bulk.status-update');
            });
        });
        Route::group(['prefix' => 'payment'], function () {
            Route::post('/', [PaymentController::class, 'create'])->middleware('super_user')->name('admin.api.create.payment');
            Route::group(['prefix' => '{payment}'], function () {
                Route::get('/', [PaymentController::class, 'show'])->name('admin.api.show.payment');
                Route::delete('/', [PaymentController::class, 'delete'])->middleware('super_user')->name('admin.api.delete.payment');
                Route::patch('/', [PaymentController::class, 'update'])->middleware('super_user')->name('admin.api.update.payment');
                Route::delete('/refund', [PaymentController::class, 'refund'])->middleware('super_user')->name('admin.api.refund.payment');
            });
        });

        // Voucher Codes
        Route::group(['prefix' => 'voucher-codes'], function () {
            Route::get('/', [VoucherCodeController::class, 'index'])->name('admin.api.index.voucher-codes');
            Route::group(['prefix' => 'bulk'], function () {
                Route::post('/expire', [VoucherCodeController::class, 'bulkExpire'])->name('admin.api.voucher-codes.bulk.expire');
                Route::post('/start', [VoucherCodeController::class, 'bulkStart'])->name('admin.api.voucher-codes.bulk.start');
                Route::post('/activate', [VoucherCodeController::class, 'bulkActivateForPeriod'])->name('admin.api.voucher-codes.bulk.activate-for-period');
                Route::delete('/', [VoucherCodeController::class, 'bulkDelete'])->name('admin.api.voucher-codes.bulk.delete');
            });
        });
        Route::group(['prefix' => 'voucher-code'], function () {
            Route::post('/', [VoucherCodeController::class, 'create'])->name('admin.api.create.voucher-code');
            Route::group(['prefix' => '{voucherCode}'], function () {
                Route::get('/', [VoucherCodeController::class, 'show'])->name('admin.api.show.voucher-code');
                Route::delete('/', [VoucherCodeController::class, 'delete'])->name('admin.api.delete.voucher-code');
                Route::patch('/', [VoucherCodeController::class, 'update'])->name('admin.api.update.voucher-code');
            });
        });

        // Delivery Types
        Route::group(['prefix' => 'delivery-types'], function () {
            Route::get('/', [DeliveryTypeController::class, 'index'])->name('admin.api.index.delivery-types');
            Route::group(['prefix' => 'bulk'], function () {
                Route::post('/availability', [DeliveryTypeController::class, 'bulkUpdateAvailability'])->name('admin.api.delivery-types.bulk.update-availability');
                Route::delete('/', [DeliveryTypeController::class, 'bulkDelete'])->name('admin.api.delivery-types.bulk.delete');
            });
        });
        Route::group(['prefix' => 'delivery-type'], function () {
            Route::post('/', [DeliveryTypeController::class, 'create'])->middleware('super_user')->name('admin.api.create.delivery-type');
            Route::group(['prefix' => '{deliveryType}'], function () {
                Route::get('/', [DeliveryTypeController::class, 'show'])->name('admin.api.show.delivery-type');
                Route::delete('/', [DeliveryTypeController::class, 'delete'])->middleware('super_user')->name('admin.api.delete.delivery-type');
                Route::patch('/', [DeliveryTypeController::class, 'update'])->middleware('super_user')->name('admin.api.update.delivery-type');

                Route::get('delivery-rules', [DeliveryRuleController::class, 'index'])->name('admin.api.index.delivery-rules');
                Route::group(['prefix' => 'delivery-rule'], function () {
                    Route::post('/', [DeliveryRuleController::class, 'create'])->middleware('super_user')->name('admin.api.create.delivery-rule');
                    Route::group(['prefix' => '{deliveryRule}'], function () {
                        Route::get('/', [DeliveryRuleController::class, 'show'])->name('admin.api.show.delivery-rule');
                        Route::delete('/', [DeliveryRuleController::class, 'delete'])->middleware('super_user')->name('admin.api.delete.delivery-rule');
                        Route::patch('/', [DeliveryRuleController::class, 'update'])->middleware('super_user')->name('admin.api.update.delivery-rule');
                    });
                });

            });
        });

        // Discount Rules
        Route::group(['prefix' => 'discount-rules'], function () {
            Route::get('/', [DiscountRuleController::class, 'index'])->name('admin.api.index.discount-rules');
            Route::group(['prefix' => 'bulk'], function () {
                Route::post('/expire', [DiscountRuleController::class, 'bulkExpire'])->name('admin.api.discount-rules.bulk.expire');
                Route::post('/start', [DiscountRuleController::class, 'bulkStart'])->name('admin.api.discount-rules.bulk.start');
                Route::post('/activate', [DiscountRuleController::class, 'bulkActivateForPeriod'])->name('admin.api.discount-rules.bulk.activate-for-period');
                Route::delete('/', [DiscountRuleController::class, 'bulkDelete'])->name('admin.api.discount-rules.bulk.delete');
            });
        });
        Route::group(['prefix' => 'discount-rule'], function () {
            Route::post('/', [DiscountRuleController::class, 'create'])->middleware('super_user')->name('admin.api.create.discount-rule');
            Route::group(['prefix' => '{discountRule}'], function () {
                Route::get('/', [DiscountRuleController::class, 'show'])->name('admin.api.show.discount-rule');
                Route::delete('/', [DiscountRuleController::class, 'delete'])->middleware('super_user')->name('admin.api.delete.discount-rule');
                Route::patch('/', [DiscountRuleController::class, 'update'])->middleware('super_user')->name('admin.api.update.discount-rule');
            });
        });

        // Users
        Route::get('users', [UserController::class, 'index'])->name('admin.api.index.users');
        Route::group(['prefix' => 'user'], function () {
            Route::post('/', [UserController::class, 'create'])->middleware(['role:super_admin'])->name('admin.api.create.user');
            Route::group(['prefix' => '{user}'], function () {
                Route::get('/', [UserController::class, 'show'])->name('admin.api.show.user');
                Route::delete('/', [UserController::class, 'delete'])->middleware(['own_or_super_admin'])->name('admin.api.delete.user');
                Route::patch('/', [UserController::class, 'update'])->middleware(['own_or_super_admin'])->name('admin.api.update.user');
            });
        });

        // Customers
        Route::get('customers', [CustomerController::class, 'index'])->name('admin.api.index.customers');
        Route::group(['prefix' => 'customer/{customer}'], function () {
            Route::get('/', [CustomerController::class, 'show'])->name('admin.api.show.customer');
        });

        // Carts
        Route::get('carts', [CartController::class, 'index'])->name('admin.api.index.carts');
        Route::group(['prefix' => 'cart'], function () {
            Route::post('/', [CartController::class, 'create'])->name('admin.api.create.cart');
            Route::group(['prefix' => '{cart}'], function () {
                Route::get('/', [CartController::class, 'show'])->name('admin.api.show.cart');
                Route::delete('/', [CartController::class, 'delete'])->name('admin.api.delete.cart');
                Route::patch('/', [CartController::class, 'update'])->name('admin.api.update.cart');
            });
        });

        // Ratings
        if (Module::enabled('ratings')) {
            Route::group(['prefix' => 'ratings'], function () {
                Route::get('/', [RatingController::class, 'index'])->name('admin.api.index.ratings');
                Route::group(['prefix' => 'bulk'], function () {
                    Route::post('/availability', [RatingController::class, 'bulkUpdateAvailability'])->name('admin.api.ratings.bulk.update-availability');
                    Route::post('/verified-status', [RatingController::class, 'bulkUpdateVerifiedStatus'])->name('admin.api.ratings.bulk.update-verified-status');
                });
            });
            Route::group(['prefix' => 'rating/{rating}'], function () {
                Route::get('/', [RatingController::class, 'show'])->name('admin.api.show.rating');
                Route::delete('/', [RatingController::class, 'delete'])->middleware('super_user')->name('admin.api.delete.rating');
            });
        }

        // Carts
        Route::group(['prefix' => 'banners'], function () {
            Route::get('/', [BannerController::class, 'index'])->name('admin.api.index.banners');
            Route::group(['prefix' => 'bulk'], function () {
                Route::post('/availability', [BannerController::class, 'bulkUpdateAvailability'])->name('admin.api.banners.bulk.update-availability');
                Route::delete('/', [BannerController::class, 'bulkDelete'])->name('admin.api.banners.bulk.delete');
            });
        });
        Route::group(['prefix' => 'banner'], function () {
            Route::post('/', [BannerController::class, 'create'])->name('admin.api.create.banner');
            Route::group(['prefix' => '{banner}'], function () {
                Route::get('/', [BannerController::class, 'show'])->name('admin.api.show.banner');
                Route::delete('/', [BannerController::class, 'delete'])->name('admin.api.delete.banner');
                Route::patch('/', [BannerController::class, 'update'])->name('admin.api.update.banner');
            });
        });

        // Files
        Route::group(['prefix' => 'files'], function () {
            Route::get('/', [FileController::class, 'index'])->name('admin.api.index.files');
            Route::group(['prefix' => 'bulk'], function () {
                Route::delete('/', [FileController::class, 'bulkDelete'])->name('admin.api.files.bulk.delete');
            });
        });
        Route::group(['prefix' => 'file'], function () {
            Route::post('/', [FileController::class, 'create'])->name('admin.api.create.file');
            Route::group(['prefix' => '{file}'], function () {
                Route::get('/', [FileController::class, 'show'])->name('admin.api.show.file');
                Route::delete('/', [FileController::class, 'delete'])->name('admin.api.delete.file');
                Route::patch('/', [FileController::class, 'update'])->name('admin.api.update.file');
            });
        });

        // Reports
        Route::group(['prefix' => 'reports'], function () {
            Route::post('overview', [ReportController::class, 'getOverview'])->name('admin.api.show.report.overview');
            Route::post('sales', [ReportController::class, 'getSales'])->name('admin.api.show.report.sales');
        });

        // Emails
        Route::get('/get-users', [EmailController::class, 'getUserOptions']);
        Route::post('/send-email', [EmailController::class, 'sendEmail'])->name('admin.customers.send-email');
    });
});
