<?php

namespace Webkul\MarketplaceWarehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MarketplaceWarehouse\Contracts\City as CityContract;

class City extends Model implements CityContract
{
    protected $table = 'warehouse_delivery_city';

    protected $fillable = ['name'];
}
