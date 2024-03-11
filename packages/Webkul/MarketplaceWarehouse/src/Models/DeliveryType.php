<?php

namespace Webkul\MarketplaceWarehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MarketplaceWarehouse\Contracts\DeliveryType as DeliveryTypeContract;

class DeliveryType extends Model implements DeliveryTypeContract
{
    protected $table = 'warehouse_delivery_type';

    protected $fillable = ['title'];
}
