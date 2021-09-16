<?php

use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Admin\ProductAttributeOptionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DeliveryRuleController;
use App\Http\Controllers\DiscountRuleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductTagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherCodeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api', 'admin', 'set.locale']], function () {
    Route::group(['prefix' => 'admin'], function () {

        // Products
        Route::get('products', [ProductController::class, 'index'])->name('admin.api.index.products');
        Route::group(['prefix' => 'product'], function () {
            Route::post('/', [ProductController::class, 'create'])->name('admin.api.create.product');
            Route::group(['prefix' => '{id}'], function () {
                Route::get('/', [ProductController::class, 'show'])->name('admin.api.show.product');
                Route::delete('/', [ProductController::class, 'delete'])->name('admin.api.delete.product');
                Route::patch('/', [ProductController::class, 'update'])->name('admin.api.update.product');

                // Product Variants
                Route::get('/variants', [ProductController::class, 'show'])->name('admin.api.show.product-variants');
                Route::group(['prefix' => 'variant'], function () {
                    Route::post('/', [ProductVariantController::class, 'create'])->name('admin.api.create.product-variant');
                    Route::group(['prefix' => '{id}'], function () {
                        Route::get('/', [ProductVariantController::class, 'show'])->name('admin.api.show.product-variant');
                        Route::delete('/', [ProductVariantController::class, 'delete'])->name('admin.api.delete.product-variant');
                        Route::patch('/', [ProductVariantController::class, 'update'])->name('admin.api.update.product-variant');
                    });
                });

            });
        });

        // Product Categories
        Route::get('product-categories', [ProductCategoryController::class, 'index'])->name('admin.api.index.product-categories');
        Route::group(['prefix' => 'product-category'], function () {
            Route::post('/', [ProductCategoryController::class, 'create'])->name('admin.api.create.product-category');
            Route::group(['prefix' => '{id}'], function () {
                Route::get('/', [ProductCategoryController::class, 'show'])->name('admin.api.show.product-category');
                Route::delete('/', [ProductCategoryController::class, 'delete'])->name('admin.api.delete.product-category');
                Route::patch('/', [ProductCategoryController::class, 'update'])->name('admin.api.update.product-category');
            });
        });

        // Product Tags
        Route::get('product-tags', [ProductTagController::class, 'index'])->name('admin.api.index.product-tags');
        Route::group(['prefix' => 'product-tag'], function () {
            Route::post('/', [ProductTagController::class, 'create'])->name('admin.api.create.product-tag');
            Route::group(['prefix' => '{id}'], function () {
                Route::get('/', [ProductTagController::class, 'show'])->name('admin.api.show.product-tag');
                Route::delete('/', [ProductTagController::class, 'delete'])->name('admin.api.delete.product-tag');
                Route::patch('/', [ProductTagController::class, 'update'])->name('admin.api.update.product-tag');
            });
        });

        // Product Attributes
        Route::get('product-attributes', [ProductAttributeController::class, 'index'])->name('admin.api.index.product-attributes');
        Route::group(['prefix' => 'product-attribute'], function () {
            Route::post('/', [ProductAttributeController::class, 'create'])->name('admin.api.create.product-attribute');
            Route::group(['prefix' => '{id}'], function () {
                Route::get('/', [ProductAttributeController::class, 'show'])->name('admin.api.show.product-attribute');
                Route::delete('/', [ProductAttributeController::class, 'delete'])->name('admin.api.delete.product-attribute');
                Route::patch('/', [ProductAttributeController::class, 'update'])->name('admin.api.update.product-attribute');

                // Attribute options
                Route::get('/options', [ProductAttributeOptionController::class, 'show'])->name('admin.api.show.product-attribute-options');
                Route::group(['prefix' => 'option'], function () {
                    Route::post('/', [ProductAttributeOptionController::class, 'create'])->name('admin.api.create.product-attribute-option');
                    Route::group(['prefix' => '{id}'], function () {
                        Route::get('/', [ProductAttributeOptionController::class, 'show'])->name('admin.api.show.product-attribute-option');
                        Route::delete('/', [ProductAttributeOptionController::class, 'delete'])->name('admin.api.delete.product-attribute-option');
                        Route::patch('/', [ProductAttributeOptionController::class, 'update'])->name('admin.api.update.product-attribute-option');
                    });
                });
            });
        });

        // Audits
        Route::get('/audits', [AuditController::class, 'index'])->name('admin.api.index.audits');
        Route::get('audit/{id}', [AuditController::class, 'show'])->name('admin.api.show.audit');

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.api.show.dashboard');

        // Reports
        Route::get('/report', [ReportController::class, 'chart'])->name('admin.api.show.report');

        // Notifications
        Route::get('/notifications', [UserController::class, 'getNotifications'])->name('admin.api.user.get.notifications');
        Route::post('/notifications', [UserController::class, 'clearNotifications'])->name('admin.api.user.clear-notifications');

        // Orders
        Route::get('orders', [OrderController::class, 'index'])->name('admin.api.index.orders');
        Route::group(['prefix' => 'order'], function () {
            Route::post('/', [OrderController::class, 'create'])->name('admin.api.create.order');
            Route::group(['prefix' => '{id}'], function () {
                Route::get('/', [OrderController::class, 'show'])->name('admin.api.show.order');
                Route::delete('/', [OrderController::class, 'delete'])->name('admin.api.delete.order');
                Route::patch('/', [OrderController::class, 'update'])->name('admin.api.update.order');
            });
        });

        // Payments
        Route::get('payments', [PaymentController::class, 'index'])->name('admin.api.index.payments');
        Route::group(['prefix' => 'payment'], function () {
            Route::post('/', [PaymentController::class, 'create'])->name('admin.api.create.payment');
            Route::group(['prefix' => '{id}'], function () {
                Route::get('/', [PaymentController::class, 'show'])->name('admin.api.show.payment');
                Route::delete('/', [PaymentController::class, 'delete'])->name('admin.api.delete.payment');
                Route::patch('/', [PaymentController::class, 'update'])->name('admin.api.update.payment');
                Route::delete('/refund', [PaymentController::class, 'refund'])->name('admin.api.refund.payment');
            });
        });

        // Voucher Codes
        Route::get('voucher-codes', [VoucherCodeController::class, 'index'])->name('admin.api.index.voucher-codes');
        Route::group(['prefix' => 'voucher-code'], function () {
            Route::post('/', [VoucherCodeController::class, 'create'])->name('admin.api.create.voucher-code');
            Route::group(['prefix' => '{id}'], function () {
                Route::get('/', [VoucherCodeController::class, 'show'])->name('admin.api.show.voucher-code');
                Route::delete('/', [VoucherCodeController::class, 'delete'])->name('admin.api.delete.voucher-code');
                Route::patch('/', [VoucherCodeController::class, 'update'])->name('admin.api.update.voucher-code');
            });
        });

        // Delivery Rules
        Route::get('delivery-rules', [DeliveryRuleController::class, 'index'])->name('admin.api.index.delivery-rules');
        Route::group(['prefix' => 'delivery-rule'], function () {
            Route::post('/', [DeliveryRuleController::class, 'create'])->name('admin.api.create.delivery-rule');
            Route::group(['prefix' => '{id}'], function () {
                Route::get('/', [DeliveryRuleController::class, 'show'])->name('admin.api.show.delivery-rule');
                Route::delete('/', [DeliveryRuleController::class, 'delete'])->name('admin.api.delete.delivery-rule');
                Route::patch('/', [DeliveryRuleController::class, 'update'])->name('admin.api.update.delivery-rule');
            });
        });

        // Discount Rules
        Route::get('discount-rules', [DiscountRuleController::class, 'index'])->name('admin.api.index.discount-rules');
        Route::group(['prefix' => 'discount-rule'], function () {
            Route::post('/', [DiscountRuleController::class, 'create'])->name('admin.api.create.discount-rule');
            Route::group(['prefix' => '{id}'], function () {
                Route::get('/', [DiscountRuleController::class, 'show'])->name('admin.api.show.discount-rule');
                Route::delete('/', [DiscountRuleController::class, 'delete'])->name('admin.api.delete.discount-rule');
                Route::patch('/', [DiscountRuleController::class, 'update'])->name('admin.api.update.discount-rule');
            });
        });

        // Carts
        Route::get('carts', [CartController::class, 'index'])->name('admin.api.index.carts');
        Route::group(['prefix' => 'cart'], function () {
            Route::post('/', [CartController::class, 'create'])->name('admin.api.create.cart');
            Route::group(['prefix' => '{id}'], function () {
                Route::get('/', [CartController::class, 'show'])->name('admin.api.show.cart');
                Route::delete('/', [CartController::class, 'delete'])->name('admin.api.delete.cart');
                Route::patch('/', [CartController::class, 'update'])->name('admin.api.update.cart');
            });
        });

    });
});
