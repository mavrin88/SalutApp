<?php

namespace Webkul\MarketplaceWarehouse\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\MarketplaceWarehouse\Models\Warehouse::class,
        \Webkul\MarketplaceWarehouse\Models\Receipt::class,
        \Webkul\MarketplaceWarehouse\Models\QtyLog::class,
        \Webkul\MarketplaceWarehouse\Models\Price::class,
        \Webkul\MarketplaceWarehouse\Models\PriceType::class,
        \Webkul\MarketplaceWarehouse\Models\City::class,
        \Webkul\MarketplaceWarehouse\Models\DeliveryTime::class,
        \Webkul\MarketplaceWarehouse\Models\DeliveryType::class,
        \Webkul\MarketplaceWarehouse\Models\DeliveryCharge::class,
        \Webkul\MarketplaceWarehouse\Models\Region::class,
        \Webkul\MarketplaceWarehouse\Models\AssignCity::class,
        \Webkul\MarketplaceWarehouse\Models\Discount::class,
    ];
}