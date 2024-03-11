<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Contracts\MpProductDownloadableSampleTranslation as ProductDownloadableSampleTranslationContract;

class MpProductDownloadableSampleTranslation extends Model implements ProductDownloadableSampleTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['title'];

    protected $table = 'mp_product_downloadable_sample_translations';
}