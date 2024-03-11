<?php

namespace Webkul\MarketplaceWarehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MarketplaceWarehouse\Contracts\DeliveryCharge as DeliveryChargeContract;

class DeliveryCharge extends Model implements DeliveryChargeContract
{
    protected $table = 'warehouse_delivery_charges';

    protected $fillable = ['from', 'to', 'cost', 'warehouse_region_id'];
}
