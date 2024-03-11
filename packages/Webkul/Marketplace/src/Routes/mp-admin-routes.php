<?php

use Illuminate\Support\Facades\Route;
use Webkul\Marketplace\Http\Controllers\Admin\SellerController;
use Webkul\Marketplace\Http\Controllers\Admin\OrderController;
use Webkul\Marketplace\Http\Controllers\Admin\TransactionController;
use Webkul\Marketplace\Http\Controllers\Admin\ProductController;
use Webkul\Marketplace\Http\Controllers\Admin\ReviewController;
use Webkul\Marketplace\Http\Controllers\Admin\CustomerController;
use Webkul\Marketplace\Http\Controllers\Admin\ProductFlagReasonController;
use Webkul\Marketplace\Http\Controllers\Admin\SellerFlagReasonController;
use Webkul\Marketplace\Http\Controllers\Admin\SellerCategoryController;
use Webkul\Marketplace\Http\Controllers\Admin\PaymentRequestController;

/**
 * Seller routes.
 */
Route::group(['middleware' => ['web', 'marketplace']], function () {

    Route::prefix('admin/marketplace')->group(function () {

        Route::group(['middleware' => ['admin']], function () {

            /**
             * Seller routes.
             */
            Route::get('sellers', [SellerController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace::admin.sellers.index'
            ])->name('admin.marketplace.sellers.index');

            Route::get('sellers/create', [SellerController::class, 'create'])->defaults('_config', [
                'view' => 'marketplace::admin.sellers.create'
            ])->name('admin.marketplace.sellers.create');

            Route::post('sellers/delete/{id}', [SellerController::class, 'destroy'])
                ->name('admin.marketplace.sellers.delete');

            Route::post('sellers/massdelete', [SellerController::class, 'massDestroy'])->defaults('_config', [
                'redirect' => 'admin.marketplace.sellers.index'
            ])->name('admin.marketplace.sellers.massdelete');

            Route::post('sellers/massupdate', [SellerController::class, 'massUpdate'])->defaults('_config', [
                'redirect' => 'admin.marketplace.sellers.index'
            ])->name('admin.marketplace.sellers.massupdate');

            Route::post('seller/status/update/{id}', [SellerController::class, 'sellerApprovedUnapproved'])->defaults('_config', [
                'redirect' => 'admin.marketplace.sellers.index'
            ])->name('admin.marketplace.seller.status.update');

            Route::get('seller/product/search/{id}', [SellerController::class, 'search'])->defaults('_config', [
                'view' => 'marketplace::admin.sellers.products.search'
            ])->name('admin.marketplace.seller.product.search');

            Route::get('seller/product/assign/{seller_id}/{product_id?}', [SellerController::class, 'assignProduct'])->defaults('_config', [
                'view' => 'marketplace::admin.sellers.products.assign'
            ])->name('admin.marketplace.seller.product.create');

            Route::post('seller/product/assign/{seller_id}/{product_id?}', [SellerController::class, 'saveAssignProduct'])->defaults('_config', [
                'redirect' => 'admin.marketplace.sellers.index'
            ])->name('admin.marketplace.seller.product.store');

            Route::get('/products/edit-assign/{id}', [SellerController::class, 'edit'])->defaults('_config', [
                'view' => 'marketplace::admin.sellers.products.edit-assign'
            ])->name('admin.marketplace.products.edit-assign');

            Route::put('/products/edit-assign/{id}', [SellerController::class, 'update'])->defaults('_config', [
                'redirect' => 'admin.marketplace.products.index'
            ])->name('admin.marketplace.products.assign.update');

            Route::get('/products/edit/{id}', [SellerController::class, 'edit'])->defaults('_config', [
                'view' => 'marketplace::admin.sellers.products.edit'
            ])->name('admin.marketplace.products.edit');

            Route::put('/products/edit/{id}', [SellerController::class, 'update'])->defaults('_config', [
                'redirect' => 'admin.marketplace.products.index'
            ])->name('admin.marketplace.products.update');

            /**
             * seller profile routes start here.
             */
            Route::get('sellers/profile/edit/{id}', [SellerController::class, 'editProfile'])->defaults('_config', [
                'view' => 'marketplace::admin.sellers.edit'
            ])->name('admin.marketplace.seller.edit');

            Route::put('sellers/profile/edit/{id}', [SellerController::class, 'updateProfile'])->defaults('_config', [
                'redirect' => 'admin.marketplace.sellers.index'
            ])->name('marketplace.admin.seller.update');

            /**
             * Order routes.
             */
            Route::get('sellers/{id}/orders', [OrderController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace::admin.orders.index'
            ])->name('admin.marketplace.sellers.orders.index');

            Route::get('orders', [OrderController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace::admin.orders.index'
            ])->name('admin.marketplace.orders.index');

            Route::post('orders', [OrderController::class, 'pay'])->defaults('_config', [
                'redirect' => 'admin.marketplace.orders.index'
            ])->name('admin.marketplace.orders.pay');

            /**
             * transactions routes.
             */
            Route::get('transactions', [TransactionController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace::admin.transactions.index'
            ])->name('admin.marketplace.transactions.index');

            Route::get('transactions/view/{id}', [TransactionController::class, 'view'])->defaults('_config', [
                'view' => 'marketplace::admin.transactions.view'
            ])->name('marketplace.admin.account.transactions.view');

            /**
             * products routes.
             */
            Route::get('products', [ProductController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace::admin.products.index'
            ])->name('admin.marketplace.products.index');

            Route::post('products/delete/{id}', [ProductController::class, 'destroy'])
                ->name('admin.marketplace.products.delete');

            Route::post('products/massdelete', [ProductController::class, 'massDestroy'])->defaults('_config', [
                'redirect' => 'admin.marketplace.products.index'
            ])->name('admin.marketplace.products.massdelete');

            Route::post('products/massupdate', [ProductController::class, 'massUpdate'])->defaults('_config', [
                'redirect' => 'admin.marketplace.products.index'
            ])->name('admin.marketplace.products.massupdate');

            Route::get('product-reviews', [ProductController::class, 'reviews'])->defaults('_config', [
                'view' => 'marketplace::admin.sellers.products.reviews.index'
            ])->name('admin.marketplace.products.review');

            /**
             * review routes.
             */
            Route::get('reviews', [ReviewController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace::admin.reviews.index'
            ])->name('admin.marketplace.reviews.index');

            Route::post('reviews/massupdate', [ReviewController::class, 'massUpdate'])->defaults('_config', [
                'redirect' => 'admin.marketplace.reviews.index'
            ])->name('admin.marketplace.reviews.massupdate');

            /**
             * customer edit routes.
             */
            Route::post('customers/create', [CustomerController::class, 'store'])->defaults('_config', [
                'redirect' => 'admin.marketplace.sellers.index',
            ])->name('marketplace.admin.customer.store');

            /**
             * product Flag routes.
             */
            Route::prefix('product-flag')->group(function () {
                Route::get('/', [ProductFlagReasonController::class, 'index'])->defaults('_config', [
                    'view' => 'marketplace::admin.productFlagReason.index',
                ])->name('marketplace.admin.product.flag.reason.index');

                Route::get('create', [ProductFlagReasonController::class, 'create'])->defaults('_config', [
                    'view' => 'marketplace::admin.productFlagReason.create',
                ])->name('marketplace.admin.product.flag.reason.create');

                Route::post('create', [ProductFlagReasonController::class, 'store'])->defaults('_config', [
                    'redirect' => 'marketplace.admin.product.flag.reason.index',
                ])->name('marketplace.admin.product.flag.reason.store');

                Route::get('edit/{id}', [ProductFlagReasonController::class, 'edit'])->defaults('_config', [
                    'view' => 'marketplace::admin.productFlagReason.edit',
                ])->name('marketplace.admin.product.flag.reason.edit');

                Route::post('edit/{id}', [ProductFlagReasonController::class, 'update'])->defaults('_config', [
                    'redirect' => 'marketplace.admin.product.flag.reason.index',
                ])->name('marketplace.admin.product.flag.reason.update');

                Route::get('/delete/{id}', [ProductFlagReasonController::class, 'delete'])->defaults('_config', [
                    'redirect' => 'marketplace.admin.product.flag.reason.index',
                ])->name('marketplace.admin.product.flag.reason.delete');

                Route::post('/massdelete', [ProductFlagReasonController::class, 'massDelete'])->defaults('_config', [
                    'redirect' => 'marketplace.admin.product.flag.reason.index',
                ])->name('marketplace.admin.product.flag.reason.mass-delete');
            });

            /**
             * seller Flag routes.
             */
            Route::prefix('seller-flag')->group(function () {
                Route::get('/', [SellerFlagReasonController::class, 'index'])->defaults('_config', [
                    'view' => 'marketplace::admin.sellerFlagReason.index',
                ])->name('marketplace.admin.seller.flag.reason.index');

                Route::get('create', [SellerFlagReasonController::class, 'create'])->defaults('_config', [
                    'view' => 'marketplace::admin.sellerFlagReason.create',
                ])->name('marketplace.admin.seller.flag.reason.create');

                Route::post('create', [SellerFlagReasonController::class, 'store'])->defaults('_config', [
                    'redirect' => 'marketplace.admin.seller.flag.reason.index',
                ])->name('marketplace.admin.seller.flag.reason.store');

                Route::get('edit/{id}', [SellerFlagReasonController::class, 'edit'])->defaults('_config', [
                    'view' => 'marketplace::admin.sellerFlagReason.edit',
                ])->name('marketplace.admin.seller.flag.reason.edit');

                Route::post('edit/{id}', [SellerFlagReasonController::class, 'update'])->defaults('_config', [
                    'redirect' => 'marketplace.admin.seller.flag.reason.index',
                ])->name('marketplace.admin.seller.flag.reason.update');

                Route::get('/delete/{id}', [SellerFlagReasonController::class, 'delete'])->defaults('_config', [
                    'redirect' => 'marketplace.admin.seller.flag.reason.index',
                ])->name('marketplace.admin.seller.flag.reason.delete');

                Route::post('/massdelete', [SellerFlagReasonController::class, 'massDelete'])->defaults('_config', [
                    'redirect' => 'marketplace.admin.seller.flag.reason.index',
                ])->name('marketplace.admin.seller.flag.reason.mass-delete');
            });

        });

        /**
         * sellers category routes start here.
         */
        Route::prefix('seller-categories')->group(function () {
            Route::get('/', [SellerCategoryController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace::admin.sellers.category.index'
            ])->name('admin.marketplace.seller.category.index');

            Route::get('create', [SellerCategoryController::class, 'create'])->defaults('_config', [
             'view' => 'marketplace::admin.sellers.category.create',
            ])->name('admin.marketplace.seller.category.create');

            Route::post('create', [SellerCategoryController::class, 'store'])->defaults('_config', [
                'redirect' => 'admin.marketplace.seller.category.index',
            ])->name('admin.marketplace.seller.category.store');

            Route::get('edit/{id}', [SellerCategoryController::class, 'edit'])->defaults('_config', [
             'view' => 'marketplace::admin.sellers.category.edit',
            ])->name('admin.marketplace.seller.category.edit');

            Route::post('edit/{id}', [SellerCategoryController::class, 'update'])->defaults('_config', [
                'redirect' => 'admin.marketplace.seller.category.index',
            ])->name('admin.marketplace.seller.category.update');

            Route::post('/delete/{id}', [SellerCategoryController::class, 'destroy'])->defaults('_config', [
                'redirect' => 'admin.marketplace.seller.category.index',
            ])->name('admin.marketplace.seller.category.delete');

            Route::post('/massdelete', [SellerCategoryController::class, 'massDestroy'])->defaults('_config', [
                'redirect' => 'admin.marketplace.seller.category.index',
            ])->name('admin.marketplace.seller.category.mass-delete');

        });

        /**
         * payment request routes start here.
         */
        Route::get('payment-request', [PaymentRequestController::class, 'index'])->defaults('_config', [
            'view' => 'marketplace::admin.paymentRequest.index'
        ])->name('marketplace.admin.payment.request.index');
    });

});