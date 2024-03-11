<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Contracts\SellerCategory as SellerCategoryContract;

class SellerCategory extends Model implements SellerCategoryContract
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
