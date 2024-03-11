<?php

use Illuminate\Support\Facades\Route;
use Webkul\MarketplaceWarehouse\Http\Controllers\Shop\WarehouseController;
use Webkul\MarketplaceWarehouse\Http\Controllers\Shop\ReceiptWithdrawalController;
use Webkul\MarketplaceWarehouse\Http\Controllers\Shop\PriceController;
use Webkul\MarketplaceWarehouse\Http\Controllers\Shop\RegionController;
use Webkul\MarketplaceWarehouse\Http\Controllers\Shop\LocationController;

/**
 * Shop routes.
 */
Route::group(['middleware' => ['web', 'theme', 'locale', 'currency','marketplace']], function () {

    /**
     * Marketplace Warehouse routes start here.
     */
    Route::prefix('marketplace/warehouse')->group(function () {
        /**
         * Auth Routes.
         */
        Route::group(['middleware' => ['customer']], function () {
            Route::get('/list', [WarehouseController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.index'
            ])->name('marketplace_warehouse.user.warehouse.index');

            Route::get('/create', [WarehouseController::class, 'create'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.create'
            ])->name('marketplace_warehouse.user.warehouse.create');

            Route::post('/create', [WarehouseController::class, 'store'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.index'
            ])->name('marketplace_warehouse.user.warehouse.store');

            Route::get('/edit/{id}', [WarehouseController::class, 'edit'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.edit'
            ])->name('marketplace_warehouse.user.warehouse.edit');

            Route::put('/edit/{id}', [WarehouseController::class, 'update'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.index'
            ])->name('marketplace_warehouse.user.warehouse.update');

            Route::post('/delete/{id}', [WarehouseController::class, 'destroy'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.index'
            ])->name('marketplace_warehouse.user.warehouse.delete');

            Route::post('/massdelete', [WarehouseController::class, 'massDestroy'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.index'
            ])->name('marketplace_warehouse.user.warehouse.mass_delete');

            Route::get('/view/{id}', [WarehouseController::class, 'view'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.view'
            ])->name('marketplace_warehouse.user.warehouse.view');

            /**
             * Warehouse Receipts and Withdrawals Routes
             */
            Route::get('/receipt-and-withdrawal', [ReceiptWithdrawalController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.receipt-and-withdrawal.index'
            ])->name('marketplace_warehouse.user.warehouse.receipt-and-withdrawal.index');

            Route::get('/receipt-and-withdrawal/create', [ReceiptWithdrawalController::class, 'create'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.receipt-and-withdrawal.create'
            ])->name('marketplace_warehouse.user.warehouse.receipt-and-withdrawal.create');

            Route::post('/receipt-and-withdrawal/create', [ReceiptWithdrawalController::class, 'store'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.receipt-and-withdrawal.index'
            ])->name('marketplace_warehouse.user.warehouse.receipt-and-withdrawal.store');

            Route::get('/receipt-and-withdrawal/view/{id}', [ReceiptWithdrawalController::class, 'view'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.receipt-and-withdrawal.view'
            ])->name('marketplace_warehouse.user.warehouse.receipts-and-withdrawals.view');

            /**
             * Getting all seller products
             */
            Route::get('/search-seller-products', [ReceiptWithdrawalController::class, 'getProducts'])->name('shop.marketplace_warehouse.warehouse.search_seller_product');


            /**
             * Price Type Routes
             */
            Route::get('/price', [PriceController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.price.index'
            ])->name('marketplace_warehouse.user.warehouse.price.index');

            Route::get('/price/create', [PriceController::class, 'create'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.price.create'
            ])->name('marketplace_warehouse.user.warehouse.price.create');

            Route::post('/price/create', [PriceController::class, 'store'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.price.index'
            ])->name('marketplace_warehouse.user.warehouse.price.store');

            Route::get('/price/edit/{id}', [PriceController::class, 'edit'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.price.edit'
            ])->name('marketplace_warehouse.user.warehouse.price.edit');

            Route::post('/price/edit/{id}', [PriceController::class, 'update'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.price.index'
            ])->name('marketplace_warehouse.user.warehouse.price.update');

            Route::get('price/view/{id}', [PriceController::class, 'view'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.price.view'
            ])->name('marketplace_warehouse.user.warehouse.price.view');

            Route::post('price-type/delete/{id}', [PriceController::class, 'remove'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.price.index'
            ])->name('marketplace_warehouse.user.warehouse.price.remove');

            Route::post('price/delete/{id}', [PriceController::class, 'destroy'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.price.view'
            ])->name('marketplace_warehouse.user.warehouse.price.delete');

            /**
             * Region Routes
             */
            Route::get('/region', [RegionController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.region.index'
            ])->name('marketplace_warehouse.user.warehouse.region.index');

            Route::get('/region/create', [RegionController::class, 'create'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.region.create'
            ])->name('marketplace_warehouse.user.warehouse.region.create');

            Route::post('/region/create', [RegionController::class, 'store'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.region.view'
            ])->name('marketplace_warehouse.user.warehouse.region.store');

            Route::get('/region/edit/{id}', [RegionController::class, 'edit'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.region.edit'
            ])->name('marketplace_warehouse.user.warehouse.region.edit');

            Route::post('/region/edit/{id}', [RegionController::class, 'update'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.region.index'
            ])->name('marketplace_warehouse.user.warehouse.region.update');

            Route::get('/region/view/{id}', [RegionController::class, 'view'])->defaults('_config', [
                'view' => 'marketplace_warehouse::shop.sellers.account.warehouse.region.view'
            ])->name('marketplace_warehouse.user.warehouse.region.view');

            Route::put('region/edit/{id}/discount', [RegionController::class, 'updateDiscount'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.region.view',
            ])->name('marketplace_warehouse.user.warehouse.region.update_discount');

            Route::post('region/delete/{id}', [RegionController::class, 'remove'])->defaults('_config', [
                'redirect' => 'marketplace_warehouse.user.warehouse.region.index'
            ])->name('marketplace_warehouse.user.warehouse.region.remove');
        });

        Route::post('location', [LocationController::class, 'saveLocation'])
            ->name('marketplace-warehouse.user.location.create');
    });
});
