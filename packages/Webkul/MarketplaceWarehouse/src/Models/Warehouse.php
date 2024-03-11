<?php

namespace Webkul\MarketplaceWarehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MarketplaceWarehouse\Contracts\Warehouse as WarehouseContract;

class Warehouse extends Model implements WarehouseContract
{
    protected $table = 'warehouses';

    protected $fillable = ['warehouse_name', 'warehouse_description', 'marketplace_seller_id'];
}
