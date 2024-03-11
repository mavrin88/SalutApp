<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Contracts\GroupProductSellerPrice as GroupProductSellerPriceContract;

class GroupProductSellerPrice extends Model implements GroupProductSellerPriceContract
{
    protected $table = "mp_grouped_product_price";

    protected $fillable = [
        'id',
        'product_grouped_product_id',
        'marketplace_seller_id',
        'seller_sell_price',
        'created_at',
        'updated_at',
    ];
}
