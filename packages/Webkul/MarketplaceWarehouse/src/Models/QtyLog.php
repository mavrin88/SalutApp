<?php

namespace Webkul\MarketplaceWarehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MarketplaceWarehouse\Contracts\QtyLog as QtyLogContract;

class QtyLog extends Model implements QtyLogContract
{
    protected $table = 'qty_logs';

    protected $fillable = ['associated_product_id', 'qty', 'receipt_id'];
}
