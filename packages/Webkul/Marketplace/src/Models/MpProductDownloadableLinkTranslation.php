<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Contracts\MpProductDownloadableLinkTranslation as ProductDownloadableLinkTranslationContract;

class MpProductDownloadableLinkTranslation extends Model implements ProductDownloadableLinkTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['title'];

    protected $table = 'mp_product_downloadable_link_translations';
}