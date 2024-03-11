<?php

use Illuminate\Support\Facades\Route;
use Webkul\MarketplaceWarehouse\Http\Controllers\Admin\CityController;
use Webkul\MarketplaceWarehouse\Http\Controllers\Admin\DeliveryTypeController;
use Webkul\MarketplaceWarehouse\Http\Controllers\Admin\DeliveryTimeController;

/**
 * Seller routes.
 */
Route::group(['middleware' => ['web', 'marketplace']], function () {

    Route::prefix('admin/warehouse')->group(function () {

        Route::group(['middleware' => ['admin']], function () {

            /*
             * Marketplace Warehouse City Routes
             */
            Route::get('/city', [CityController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace_warehouse::admin.warehouse.city.index'
            ])->name('admin.warehouse.cities.index');

            Route::get('/city/create', [CityController::class, 'create'])->defaults('_config', [
                'view' => 'marketplace_warehouse::admin.warehouse.city.create',
            ])->name('admin.warehouse.city.create');

            Route::post('/city/create', [CityController::class, 'store'])->defaults('_config', [
                'redirect' => 'admin.warehouse.cities.index',
            ])->name('admin.warehouse.city.store');

            Route::get('/city/edit/{id}', [CityController::class, 'edit'])->defaults('_config', [
                'view' => 'marketplace_warehouse::admin.warehouse.city.edit',
            ])->name('admin.warehouse.city.edit');

            Route::post('/city/edit/{id}', [CityController::class, 'update'])->defaults('_config', [
                'redirect' => 'admin.warehouse.cities.index',
            ])->name('admin.warehouse.city.update');

            Route::get('/city/delete/{id}', [CityController::class, 'delete'])->defaults('_config', [
                'redirect' => 'admin.warehouse.cities.index',
            ])->name('admin.warehouse.city.delete');

            Route::post('/city/massdelete', [CityController::class, 'massDelete'])->defaults('_config', [
                'redirect' => 'admin.warehouse.cities.index',
            ])->name('admin.warehouse.city.mass-delete');

            /** 
             * Marketplace Warehouse Delivery Type Routes
             */
            Route::get('/delivery-type', [DeliveryTypeController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace_warehouse::admin.warehouse.delivery-type.index'
            ])->name('admin.warehouse.delivery_type.index');

            Route::get('/delivery-type/create', [DeliveryTypeController::class, 'create'])->defaults('_config', [
                'view' => 'marketplace_warehouse::admin.warehouse.delivery-type.create',
            ])->name('admin.warehouse.delivery_type.create');

            Route::post('/delivery-type/create', [DeliveryTypeController::class, 'store'])->defaults('_config', [
                'redirect' => 'admin.warehouse.delivery_type.index',
            ])->name('admin.warehouse.delivery_type.store');

            Route::get('/delivery-type/edit/{id}', [DeliveryTypeController::class, 'edit'])->defaults('_config', [
                'view' => 'marketplace_warehouse::admin.warehouse.delivery-type.edit',
            ])->name('admin.warehouse.delivery_type.edit');

            Route::post('/delivery-type/edit/{id}', [DeliveryTypeController::class, 'update'])->defaults('_config', [
                'redirect' => 'admin.warehouse.delivery_type.index',
            ])->name('admin.warehouse.delivery_type.update');

            Route::get('/delivery-type/delete/{id}', [DeliveryTypeController::class, 'delete'])->defaults('_config', [
                'redirect' => 'admin.warehouse.delivery_type.index',
            ])->name('admin.warehouse.delivery_type.delete');

            Route::post('/delivery-type/massdelete', [DeliveryTypeController::class, 'massDelete'])->defaults('_config', [
                'redirect' => 'admin.warehouse.delivery_type.index',
            ])->name('admin.warehouse.delivery_type.mass-delete');

            /** 
             * Marketplace Warehouse Delivery Time Routes
             */
            Route::get('/delivery-time', [DeliveryTimeController::class, 'index'])->defaults('_config', [
                'view' => 'marketplace_warehouse::admin.warehouse.delivery-time.index'
            ])->name('admin.warehouse.delivery_time.index');

            Route::get('/delivery-time/create', [DeliveryTimeController::class, 'create'])->defaults('_config', [
                'view' => 'marketplace_warehouse::admin.warehouse.delivery-time.create',
            ])->name('admin.warehouse.delivery_time.create');

            Route::post('/delivery-time/create', [DeliveryTimeController::class, 'store'])->defaults('_config', [
                'redirect' => 'admin.warehouse.delivery_time.index',
            ])->name('admin.warehouse.delivery_time.store');

            Route::get('/delivery-time/edit/{id}', [DeliveryTimeController::class, 'edit'])->defaults('_config', [
                'view' => 'marketplace_warehouse::admin.warehouse.delivery-time.edit',
            ])->name('admin.warehouse.delivery_time.edit');

            Route::post('/delivery-time/edit/{id}', [DeliveryTimeController::class, 'update'])->defaults('_config', [
                'redirect' => 'admin.warehouse.delivery_time.index',
            ])->name('admin.warehouse.delivery_time.update');

            Route::get('/delivery-time/delete/{id}', [DeliveryTimeController::class, 'delete'])->defaults('_config', [
                'redirect' => 'admin.warehouse.delivery_time.index',
            ])->name('admin.warehouse.delivery_time.delete');

            Route::post('/delivery-time/massdelete', [DeliveryTimeController::class, 'massDelete'])->defaults('_config', [
                'redirect' => 'admin.warehouse.delivery_time.index',
            ])->name('admin.warehouse.delivery_time.mass-delete');
        });
    });
});