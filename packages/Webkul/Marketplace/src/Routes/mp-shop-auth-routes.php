<?php

use Illuminate\Support\Facades\Route;
use Webkul\Marketplace\Http\Controllers\Shop\Account\SellerController as SellerAccountController;
use Webkul\Marketplace\Http\Controllers\Shop\Account\DashboardController;
use Webkul\Marketplace\Http\Controllers\Shop\Account\EarningController;
use Webkul\Marketplace\Http\Controllers\Shop\Account\ProductController as ProductAccountController;
use Webkul\Marketplace\Http\Controllers\Shop\Account\AssignProductController;
use Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\OrderController;
use Webkul\Marketplace\Http\Controllers\Shop\Account\CustomerController;
use Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\InvoiceController;
use Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\ShipmentController;
use Webkul\Marketplace\Http\Controllers\Shop\Account\ReviewController as ReviewAccountController;
use Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\TransactionController;
use Webkul\Marketplace\Http\Controllers\Shop\ReviewController as ReviewController;
use Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\PaymentRequestController;

/**
 * Shop routes.
 */
Route::group(['middleware' => ['web', 'theme', 'locale', 'currency','marketplace']], function () {

    /**
     * Marketplace routes starts here.
     */
    Route::prefix('marketplace')->group(function () {
        /**
         * Auth Routes.
         */
        Route::group(['middleware' => ['customer']], function () {

            /**
             * Seller Review routes.
             */
            Route::get('seller/{url}/reviews/create', [ReviewController::class, 'create'])->defaults('_config', [
                'view' => 'marketplace::shop.sellers.reviews.create'
            ])->name('marketplace.reviews.create');

            Route::post('seller/{url}/reviews/create', [ReviewController::class, 'store'])->defaults('_config', [
                'redirect' => 'marketplace.seller.show'
            ])->name('marketplace.reviews.store');

            /**
             * Marketplace seller accounts.
             */
            Route::prefix('account')->group(function () {

                Route::get('create', [SellerAccountController::class, 'create'])->defaults('_config', [
                    'view' => 'marketplace::shop.sellers.account.profile.create'
                ])->name('marketplace.account.seller.create');

                Route::post('create', [SellerAccountController::class, 'store'])->defaults('_config', [
                    'redirect' => 'marketplace.account.seller.create'
                ])->name('marketplace.account.seller.store');

                Route::get('edit', [SellerAccountController::class, 'edit'])->defaults('_config', [
                    'view' => 'marketplace::shop.sellers.account.profile.edit'
                ])->name('marketplace.account.seller.edit');

                Route::put('edit/{id}', [SellerAccountController::class, 'update'])->defaults('_config', [
                    'redirect' => 'marketplace.account.seller.edit'
                ])->name('marketplace.account.seller.update');

                Route::post('revoke', [SellerAccountController::class, 'revoke'])->name('marketplace.account.seller.revoke');

                /**
                 * Dashboard route.
                 */
                Route::get('dashboard', [DashboardController::class, 'index'])->defaults('_config', [
                    'view' => 'marketplace::shop.sellers.account.dashboard.index'
                ])->name('marketplace.account.dashboard.index');

                /**
                 * Earnings route.
                 */
                Route::get('earning', [EarningController::class, 'index'])->defaults('_config', [
                    'view' => 'marketplace::shop.sellers.account.earning.index'
                ])->name('marketplace.account.earning.index');

                /**
                 * Catalog Routes.
                 */
                Route::prefix('catalog')->group(function () {

                    /**
                     * Catalog Product Routes.
                     */
                    Route::get('/products', [ProductAccountController::class, 'index'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.catalog.products.index'
                    ])->name('marketplace.account.products.index');

                    /**
                     * Upload file if seller is owner.
                     */
                    Route::post('/products/owner-upload-file/{id}', [ProductAccountController::class, 'uploadLink'])->name('marketplace.catalog.products.owner-upload_link');

                    Route::post('/products/owner-upload-sample/{id}', [ProductAccountController::class, 'uploadSample'])->name('marketplace.catalog.products.owner-upload_sample');

                    Route::get('/products/create', [ProductAccountController::class, 'create'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.catalog.products.create'
                    ])->name('marketplace.account.products.create');

                    Route::post('/products/create', [ProductAccountController::class, 'store'])->defaults('_config', [
                        'redirect' => 'marketplace.account.products.edit'
                    ])->name('marketplace.account.products.store');

                    Route::get('/products/edit/{id}', [ProductAccountController::class, 'edit'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.catalog.products.edit'
                    ])->name('marketplace.account.products.edit');

                    Route::put('/products/edit/{id}', [ProductAccountController::class, 'update'])->defaults('_config', [
                        'redirect' => 'marketplace.account.products.index'
                    ])->name('marketplace.account.products.update');

                    Route::post('/products/delete/{id}', [ProductAccountController::class, 'destroy'])->name('marketplace.account.products.delete');

                    Route::post('products/massdelete', [ProductAccountController::class, 'massDestroy'])->defaults('_config', [
                        'redirect' => 'marketplace.account.products.index'
                    ])->name('marketplace.account.products.massdelete');

                    Route::get('products/copy/{id}', [ProductAccountController::class, 'copy'])->defaults('_config', [
                        'view' => 'marketplace.account.products.edit',
                    ])->name('seller.catalog.products.copy');

                    Route::get('/seller-product/{sellerId}/{urlKey}', [ProductAccountController::class, 'sellerProducts'])->defaults('_config', [
                        'view' => 'shop::seller-group-products.view',
                    ])->name('sellers.products');

                    Route::get('seller-product/search-simple-products', [ProductAccountController::class, 'searchSimpleProducts'])->name('shop.marketplace.search_simple_product');

                    Route::get('/get_product_bundle', [ProductAccountController::class, 'bundleProductItem'])->name('shop.marketplace.get_product_bundle');

                    Route::get('/search-product', [AssignProductController::class, 'searchProduct'])->name('shop.marketplace.search_product');

                    /**
                     * Catalog Product Routes.
                     */
                    Route::get('/products/search', [AssignProductController::class, 'index'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.catalog.products.search'
                    ])->name('marketplace.account.products.search');

                    Route::get('/products/assign/{id?}', [AssignProductController::class, 'create'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.catalog.products.assign'
                    ])->name('marketplace.account.products.assign');

                    Route::post('/products/assign/{id?}', [AssignProductController::class, 'store'])->defaults('_config', [
                        'redirect' => 'marketplace.account.products.index'
                    ])->name('marketplace.account.products.assign-store');

                    Route::get('/products/edit-assign/{id}', [AssignProductController::class, 'edit'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.catalog.products.edit-assign'
                    ])->name('marketplace.account.products.edit-assign');

                    Route::put('/products/edit-assign/{id}', [AssignProductController::class, 'update'])->defaults('_config', [
                        'redirect' => 'marketplace.account.products.index'
                    ])->name('marketplace.account.products.assign.update');

                    Route::post('/products/upload-file/{id}', [AssignProductController::class, 'uploadLink'])->name('marketplace.catalog.products.upload_link');

                    Route::post('/products/upload-sample/{id}', [AssignProductController::class, 'uploadSample'])->name('marketplace.catalog.products.upload_sample');

                });

                /**
                 * Product reviews.
                 */
                Route::get('/product-reviews', [ProductAccountController::class, 'reviews'])->defaults('_config', [
                    'view' => 'marketplace::shop.sellers.account.catalog.products.reviews.index'
                ])->name('marketplace.account.products.review');

                /**
                 * Sales routes.
                 */
                Route::prefix('sales')->group(function () {
                    Route::get('orders', [OrderController::class, 'index'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.sales.orders.index'
                    ])->name('marketplace.account.orders.index');

                    Route::get('orders/view/{id}', [OrderController::class, 'view'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.sales.orders.view'
                    ])->name('marketplace.account.orders.view');

                    Route::get('/orders/cancel/{id}', [OrderController::class, 'cancel'])->name('marketplace.account.orders.cancel');

                    Route::get('customer/orders/{customer_id}', [CustomerController::class, 'orders'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.customers.orders'
                    ])->name('marketplace.account.customers.order.index');

                    /**
                     * Sales Invoices Routes.
                     */
                    Route::get('invoices/create/{order_id}', [InvoiceController::class, 'create'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.sales.invoices.create'
                    ])->name('marketplace.account.invoices.create');

                    Route::post('invoices/create/{id}', [InvoiceController::class, 'store'])->defaults('_config', [
                        'redirect' => 'marketplace.account.orders.view'
                    ])->name('marketplace.account.invoices.store');

                    /**
                     * Prints invoice.
                     */
                    Route::get('invoices/print/{id}', [InvoiceController::class, 'print'])
                        ->name('marketplace.account.invoices.print');

                    /**
                     * Sales Shipments Routes.
                     */
                    Route::get('shipments/create/{order_id}', [ShipmentController::class, 'create'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.sales.shipments.create'
                    ])->name('marketplace.account.shipments.create');

                    Route::post('shipments/create/{id}', [ShipmentController::class, 'store'])->defaults('_config', [
                        'redirect' => 'marketplace.account.orders.view'
                    ])->name('marketplace.account.shipments.store');

                    /**
                     * Sales Transactions Routes.
                     */
                    Route::get('transactions', [TransactionController::class, 'index'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.sales.transactions.index'
                    ])->name('marketplace.account.transactions.index');

                    Route::get('transactions/view/{id}', [TransactionController::class, 'view'])->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.sales.transactions.view'
                    ])->name('marketplace.account.transactions.view');
                });

                /**
                 * Seller review routes.
                 */
                Route::get('reviews', [ReviewAccountController::class, 'index'])->defaults('_config', [
                    'view' => 'marketplace::shop.sellers.account.reviews.index'
                ])->name('marketplace.account.reviews.index');

                Route::get('customers', [CustomerController::class, 'index'])->defaults('_config', [
                    'view' => 'marketplace::shop.sellers.account.customers.index'
                ])->name('marketplace.account.customers.index');

                /**
                 * Seller Payment Request routes.
                 */
                Route::get('payment-request', [PaymentRequestController::class, 'index'])->defaults('_config', [
                    'view' => 'marketplace::shop.sellers.account.paymentRequest.index'
                ])->name('marketplace.account.payment.request.index');

                Route::get('payment/request/{orderId}', [PaymentRequestController::class, 'requestPayment'])->name('marketplace.account.payment.request');

                Route::post('payment/request/massrequest', [PaymentRequestController::class, 'massRequestPayment'])->defaults('_config', [
                    'redirect' => 'marketplace.account.payment.request.index'
                ])->name('marketplace.account.payment.massrequest');

            });
        });
    });
});
