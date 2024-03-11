<?php

namespace Webkul\MarketplaceWarehouse\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Location
{
    public function getAllowedSellers()
    {
        if (! session()->has('location')) {
            return [];
        }

        $location = Session::get('location');

        $regionIds = [];

        $productIds = [];

        $city = app('Webkul\MarketplaceWarehouse\Repositories\CityRepository')->findOneByField('name', $location);

        if ($city) {
            $cityId = $city->id;

            $regions = app('Webkul\MarketplaceWarehouse\Repositories\AssignCityRepository')->findByField('city_id', $cityId);

            foreach ($regions as $region) {
                $regionId = $region?->warehouse_region_id;

                $regionIds[] = $regionId;

                $Ids = app('Webkul\MarketplaceWarehouse\Repositories\DiscountRepository')->findByField('warehouse_region_id', $regionId)->pluck('product_id');

                $productIds = array_merge($productIds, $Ids->toArray());
            }
        }

        $sellers = app('Webkul\Marketplace\Repositories\SellerRepository')
            ->select('marketplace_sellers.*')
            ->leftJoin('marketplace_products', 'marketplace_products.marketplace_seller_id', '=', 'marketplace_sellers.id')
            ->leftJoin('product_flat', 'product_flat.product_id', '=', 'marketplace_products.product_id')
            ->addSelect(DB::raw("COUNT(marketplace_products.product_id) as marketplace_products_count"))
            ->having('marketplace_products_count', '>', 0)
            ->where('marketplace_products.is_approved', 1)
            ->where('product_flat.status', 1)
            ->leftJoin('warehouse_regions', 'warehouse_regions.marketplace_seller_id', '=', 'marketplace_sellers.id')
            ->whereIn('warehouse_regions.id', $regionIds)
            ->groupBy('warehouse_regions.marketplace_seller_id')
            ->get();

        return $sellers;
    }
}
