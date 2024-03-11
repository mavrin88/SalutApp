<?php

use Illuminate\Support\Facades\Route;
use Webkul\Marketplace\Http\Controllers\Shop\MarketplaceController;
use Webkul\Marketplace\Http\Controllers\Shop\ProductController;
use Webkul\Marketplace\Http\Controllers\Shop\CartController;
use Webkul\Marketplace\Http\Controllers\Shop\SellerController;
use Webkul\Customer\Http\Controllers\RegistrationController;
use Webkul\Marketplace\Http\Controllers\Shop\ReviewController;
use Webkul\Marketplace\Http\Controllers\Shop\MinimumOrderController;
use Webkul\Marketplace\Http\Controllers\Shop\FlagController;
use Webkul\Marketplace\Http\Controllers\Shop\CategoryController;

/**
 * Shop routes.
 */
Route::group(['middleware' => ['web', 'theme', 'locale', 'currency','marketplace']], function () {

    /**
     * Marketplace routes starts here.
     */
    Route::prefix('marketplace')->group(function () {
        Route::get('/', [MarketplaceController::class, 'index'])->defaults('_config', [
            'view' => 'marketplace::shop.seller-central.index'
        ])->name('marketplace.seller_central.index');

        /**
         * download sample route.
         */
        Route::get('/downloadable/download-sample/{type}/{id}', [ProductController::class, 'downloadSample'])->name('marketplace.downloadable.download_sample');

        /**
         * Add cart items.
         */
        Route::post('checkout/cart/add/{id}', [CartController::class, 'add'])->defaults('_config', [
            'redirect' => 'marketplace.product.offers.index'
        ])->name('marketplace.cart.add');

        /**
         * Seller Products routes.
         */
        Route::get('seller/{url}/products', [ProductController::class, 'index'])->defaults('_config', [
            'view' => 'marketplace::shop.sellers.products.index'
        ])->name('marketplace.products.index');

        /**
         * Seller related routes.
         */
        Route::get('seller/profile/{url}', [SellerController::class, 'show'])->defaults('_config', [
            'view' => 'marketplace::shop.sellers.profile'
        ])->name('marketplace.seller.show');

        Route::post('seller/url', [SellerController::class, 'checkShopUrl'])->name('marketplace.seller.url');

        Route::post('seller/{url}/contact', [SellerController::class, 'contact'])->name('marketplace.seller.contact');

        Route::post('seller/query', [SellerController::class, 'sellerQuery'])->name('marketplace.seller.query');

        Route::post('seller-info', [SellerController::class, 'getSellerInfo']);

        /**
         * Seller register routes.
         */
        Route::get('seller/register', [RegistrationController::class, 'show'])->defaults('_config', [
            'view' => 'marketplace::shop.sellers.signup.index'
        ])->name('marketplace.seller.create');

        /**
         * Seller Review routes.
         */
        Route::get('seller/{url}/reviews', [ReviewController::class, 'index'])->defaults('_config', [
            'view' => 'marketplace::shop.sellers.reviews.index'
        ])->name('marketplace.reviews.index');

        /**
         * Flag routes.
         */
        Route::post('flag/product/create', [FlagController::class, 'productFlagstore'])->name('marketplace.flag.product.store');

        Route::post('flag/seller/create', [FlagController::class, 'sellerFlagstore'])->name('marketplace.flag.seller.store');

    });

    /**
     * Minimum order routes.
     */
    Route::get('checkout/onepage/', [MinimumOrderController::class, 'index'])->defaults('_config', [
        'view' => 'shop::checkout.onepage'
    ])->name('shop.checkout.onepage.index');

    /**
     * Seller review routes.
     */
    Route::get('products/{id}/offers', [ProductController::class, 'offers'])->defaults('_config', [
        'view' => 'marketplace::shop.products.offers'
    ])->name('marketplace.product.offers.index');

    /**
     * Product view routes.
     */
    Route::fallback(Webkul\Marketplace\Http\Controllers\Shop\ProductsCategoriesProxyController::class . '@index')
            ->defaults('_config', [
                'product_view'  => 'shop::products.view',
                'category_view' => 'shop::products.index',
            ])
            ->name('shop.productOrCategory.index');
});
