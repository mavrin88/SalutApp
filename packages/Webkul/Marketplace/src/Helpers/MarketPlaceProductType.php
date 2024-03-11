<?php

namespace Webkul\Marketplace\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MarketPlaceProductType
{
    public static function returnMarketPlaceProductTypes()
    {
        if (Schema::hasTable('mp_seller_allowed_product_type')) {
            $allowedProductType = DB::table('mp_seller_allowed_product_type')->pluck('product_type')->toArray();
        } else {
            $allowedProductType = ['null', 'null'];
        }

        $productTypes = [];

        if (in_array('booking', $allowedProductType)) {
            $productTypes[] = [
                'name'          => 'booking_active_option',
                'title'         => 'marketplace::app.admin.system.allow_marketplace_booking_product',
                'type'          => 'boolean',
                'channel_based' => true,
                'value'         => 'active'
            ];
        }

        if (in_array('bundle', $allowedProductType)) {
            $productTypes[] = [
                'name'          => 'bundle_active_option',
                'title'         => 'marketplace::app.admin.system.allow_marketplace_bundle_product',
                'type'          => 'boolean',
                'channel_based' => true,
                'value'         => 'active'
            ];
        }

        if (in_array('grouped', $allowedProductType)) {
            $productTypes[] = [
                'name'          => 'grouped_active_option',
                'title'         => 'marketplace::app.admin.system.allow_marketplace_grouped_product',
                'type'          => 'boolean',
                'channel_based' => true,
                'value'         => 'active'
            ];
        }

        return $productTypes;
    }
}
