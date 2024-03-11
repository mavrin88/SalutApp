<?php

namespace Webkul\MarketplaceWarehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MarketplaceWarehouse\Contracts\AssignCity as AssignCityContract;

class AssignCity extends Model implements AssignCityContract
{
    protected $table = 'warehouse_assigned_cities';

    protected $fillable = ['warehouse_region_id', 'city_id'];
}
