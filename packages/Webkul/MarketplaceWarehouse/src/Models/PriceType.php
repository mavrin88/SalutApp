<?php

namespace Webkul\MarketplaceWarehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MarketplaceWarehouse\Contracts\PriceType as PriceTypeContract;

class PriceType extends Model implements PriceTypeContract
{
    public $timestamps = false;
    
    protected $table = 'warehouse_price_type';

    protected $fillable = ['title', 'marketplace_seller_id'];
}
