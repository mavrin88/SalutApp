<?php

namespace Webkul\MarketplaceWarehouse\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('bagisto.shop.layout.header.before', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('marketplace_warehouse::shop.location');
        });

        Event::listen('bagisto.shop.layout.header.currency-item.before', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('marketplace_warehouse::shop.layouts.header.index');
        });

        if ( (core()->getCurrentChannel() && core()->getCurrentChannel()->theme == "velocity")) {
            Event::listen('bagisto.shop.layout.head', function($viewRenderEventManager) {
                $viewRenderEventManager->addTemplate('marketplace_warehouse::shop.velocity.layouts.style');
            });
        } else {
            Event::listen('bagisto.shop.layout.head', function($viewRenderEventManager) {
                $viewRenderEventManager->addTemplate('marketplace_warehouse::shop.default.layouts.style');
            });
        }
    }
}
