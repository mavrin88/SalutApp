<?php

namespace Webkul\MarketplaceWarehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MarketplaceWarehouse\Contracts\Region as RegionContract;

class Region extends Model implements RegionContract
{
    protected $table = 'warehouse_regions';

    protected $fillable = ['region_name', 'delivery_type_id', 'delivery_time_id', 'max_weight', 'warehouse_id', 'price_type_id', 'marketplace_seller_id'];
}
