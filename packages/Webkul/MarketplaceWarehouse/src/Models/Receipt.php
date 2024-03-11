<?php

namespace Webkul\MarketplaceWarehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MarketplaceWarehouse\Contracts\Receipt as ReceiptContract;

class Receipt extends Model implements ReceiptContract
{
    protected $table = 'receipts';

    protected $fillable = ['title', 'warehouse_id'];
}
