<?php

namespace Webkul\MarketplaceWarehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MarketplaceWarehouse\Contracts\Price as PriceContract;

class Price extends Model implements PriceContract
{
    protected $table = 'warehouse_prices';

    protected $fillable = ['price', 'price_type_id', 'product_id'];
}
