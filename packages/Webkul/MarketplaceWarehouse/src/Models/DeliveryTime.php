<?php

namespace Webkul\MarketplaceWarehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MarketplaceWarehouse\Contracts\DeliveryTime as DeliveryTimeContract;

class DeliveryTime extends Model implements DeliveryTimeContract
{
    protected $table = 'warehouse_delivery_time';

    protected $fillable = ['title'];
}
