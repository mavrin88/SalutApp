<?php

namespace Webkul\Marketplace\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Webkul\Marketplace\Http\Middleware\MarketplaceMiddleware;

class MarketplaceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        $router->aliasMiddleware('marketplace', MarketplaceMiddleware::class);

        $this->app->register(ModuleServiceProvider::class);

        $this->app->register(EventServiceProvider::class);

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'marketplace');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'marketplace');

        $this->publishes([
            __DIR__ . '/../../publishable/assets' => public_path('vendor/webkul/marketplace/assets')
        ], 'public');

        $this->publishesVelocity();

        $this->publishesDefault();

        $this->publishesAdminFile();

        $this->publishesFiles();

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Webkul\Marketplace\Console\Commands\InstallMarketplace::class,
            ]);
        }

        $this->app->bind('cart', \Webkul\Marketplace\Cart::class);

        $this->app->bind(
            \Webkul\Velocity\Http\Controllers\Shop\ShopController::class, \Webkul\Marketplace\Http\Controllers\Shop\ShopController::class
        );

        $this->app->bind(
            \Webkul\Velocity\Http\Controllers\Shop\CartController::class, \Webkul\Marketplace\Http\Controllers\Shop\Velocity\CartController::class
        );
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php', 'menu.customer'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/admin-menu.php', 'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php', 'acl'
        );
    }

    /**
     * Publish all Velocity theme page.
     *
     * @return void
     */
    protected function publishesVelocity()
    {
        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/customers/account/wishlist/wishlist.blade.php' => resource_path('themes/velocity/views/customers/account/wishlist/wishlist.blade.php')
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/products/add-to-cart.blade.php' => resource_path('themes/velocity/views/products/add-to-cart.blade.php')
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/products/configurable-options.blade.php' => resource_path('themes/velocity/views/products/view/configurable-options.blade.php')
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/products/buy-now.blade.php' => resource_path('themes/velocity/views/products/buy-now.blade.php')
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/products/view/stock.blade.php' => resource_path('themes/velocity/views/products/view/stock.blade.php')
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/products/wishlist.blade.php' => resource_path('themes/velocity/views/products/wishlist.blade.php')
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/customers/account/partials' => resource_path('themes/velocity/views/customers/account/partials'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/sellers/products/add-buttons.blade.php' => resource_path('themes/velocity/views/products/add-buttons.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/sellers/products/price.blade.php' => resource_path('themes/velocity/views/products/price.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/checkout/cart/mini-cart.blade.php' => resource_path('themes/velocity/views/checkout/cart/mini-cart.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/customers/account/orders/view.blade.php' => resource_path('themes/velocity/views/customers/account/orders/view.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/checkout/onepage/review.blade.php' => resource_path('themes/velocity/views/checkout/onepage/review.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/checkout/cart/index.blade.php' => resource_path('themes/velocity/views/checkout/cart/index.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/velocity/layouts/header/mobile.blade.php' => resource_path('themes/velocity/views/layouts/header/mobile.blade.php'),
        ]);
    }

    /**
     * Publish all Default theme page.
     *
     * @return void
     */
    protected function publishesDefault()
    {
        $this->publishes([
            __DIR__ . '/../Resources/views/shop/default/customers/account/partials' => resource_path('themes/default/views/customers/account/partials'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/default/products/add-buttons.blade.php' => resource_path('themes/default/views/products/add-buttons.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/default/products/add-to-cart.blade.php' => resource_path('themes/default/views/products/add-to-cart.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/default/products/buy-now.blade.php' => resource_path('themes/default/views/products/buy-now.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/default/products/view/stock.blade.php' => resource_path('themes/default/views/products/view/stock.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/default/sellers/products/price.blade.php' => resource_path('themes/default/views/products/price.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/default/checkout/cart/index.blade.php' => resource_path('themes/default/views/checkout/cart/index.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/default/checkout/cart/mini-cart.blade.php' => resource_path('themes/default/views/checkout/cart/mini-cart.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/default/customers/account/orders/view.blade.php' => resource_path('themes/default/views/customers/account/orders/view.blade.php'),
        ]);
    }

    /**
     * Publish all Admin page.
     *
     * @return void
     */
    protected function publishesAdminFile()
    {
        $this->publishes([
            __DIR__ . '/../Resources/views/admin/layouts/nav-left.blade.php' => resource_path('views/vendor/admin/layouts/nav-left.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/admin/dashboard/index.blade.php' => resource_path('views/vendor/admin/dashboard/index.blade.php'),
        ]);
    }

    /**
     * Publish InvoiceRepository Repository.
     *
     * @return void
     */
    protected function publishesFiles()
    {
        $this->publishes([
            __DIR__ . '/../Repositories/Admin/InvoiceRepository.php' => __DIR__ .'/../../../Sales/src/Repositories/InvoiceRepository.php',
        ]);

        $this->publishes([
            __DIR__ . '/../Type/AbstractType.php' => __DIR__ . '/../../../Product/src/Type/AbstractType.php',
        ]);

        $this->publishes([
            __DIR__ . '/../Type/Downloadable.php' => __DIR__ . '/../../../Product/src/Type/Downloadable.php',
        ]);

        $this->publishes([
            __DIR__ . '/../Type/Grouped.php' =>  __DIR__ . '/../../../Product/src/Type/Grouped.php',
        ]);
    }
}
