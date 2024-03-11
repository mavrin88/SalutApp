<?php

namespace Webkul\MarketplaceWarehouse\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class MarketplaceWarehouseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'marketplace_warehouse');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'marketplace_warehouse');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->app->register(ModuleServiceProvider::class);

        $this->app->register(EventServiceProvider::class);

        $this->publishes([
            __DIR__ . '/../../publishable/assets' => public_path('vendor/webkul/marketplaceWarehouse/assets'),
        ], 'public');

        if (core()->getConfigData('marketplace.settings.general.status')) {
            $this->app->bind(
                \Webkul\Marketplace\Repositories\ProductRepository::class, \Webkul\MarketplaceWarehouse\Repositories\MpProduct\ProductRepository::class
            );

            $this->app->bind(
                \Webkul\Product\Repositories\ProductRepository::class, \Webkul\MarketplaceWarehouse\Repositories\Product\ProductRepository::class
            );

            $this->app->bind(
                \Webkul\Marketplace\Http\Controllers\Shop\ShopController::class, \Webkul\MarketplaceWarehouse\Http\Controllers\Shop\MpShop\ShopController::class
            );

            $this->app->bind(
                \Webkul\Marketplace\Listeners\Cart::class, \Webkul\MarketplaceWarehouse\Listeners\Cart::class
            );

            $this->app->bind(
                \Webkul\Marketplace\Http\Controllers\Shop\Account\ProductController::class, \Webkul\MarketplaceWarehouse\Http\Controllers\Shop\Account\ProductController::class
            );
        }

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/sellers/products/price.blade.php' => resource_path('themes/velocity/views/products/price.blade.php'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/admin-menu.php', 'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php', 'menu.customer'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/carriers.php', 'carriers'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );
    }
}
