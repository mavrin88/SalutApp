<?php

namespace Webkul\MarketplaceWarehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MarketplaceWarehouse\Contracts\Discount as DiscountContract;

class Discount extends Model implements DiscountContract
{
    protected $table = 'warehouse_discount';

    protected $fillable = ['discount', 'product_id', 'warehouse_region_id', 'base_selling_price', 'real_selling_price'];
}
