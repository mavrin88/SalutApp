<?php

namespace Webkul\MarketplaceWarehouse\Repositories\MpProduct;

use Illuminate\Support\Facades\Session;
use Webkul\Marketplace\Repositories\ProductRepository as MpProductRepository;

/**
 * Seller Product Repository
 */
class ProductRepository extends MpProductRepository
{
    /**
     * Returns seller product Ids based on the given attribute value (new or featured).
     *
     * @param string $attribute
     * @return array
     */
    public function getSellersProductIds($attribute, $isSellerAllowCity, $isOnlySeller = false)
    {
        $productIds = [];

        $configKey = 'marketplace.settings.general.' . $attribute;

        $location = Session::get('location');

        $regionIds = [];

        $city = app('Webkul\MarketplaceWarehouse\Repositories\CityRepository')->findOneByField('name', $location);

        if ($city) {
            $cityId = $city->id;

            $regions = app('Webkul\MarketplaceWarehouse\Repositories\AssignCityRepository')->findByField('city_id', $cityId);

            foreach ($regions as $region) {
                $regionIds[] = $region?->warehouse_region_id;
            }
        }

        if (! $isOnlySeller) {
            if (! core()->getConfigData($configKey)) {
                $productIds = $this->with('product_flats')
                    ->whereHas('product_flats', function ($q) use ($attribute) {
                        $q->where($attribute, 1);
                    })
                    ->where('is_owner', 1)
                    ->pluck('product_id')
                    ->toArray();
            } else {
                if (empty($regionIds)) {
                    $productIds = $this->with('product_flats')
                        ->whereHas('product_flats', function ($q) use ($attribute) {
                            $q->where($attribute, 1);
                        })
                        ->whereNotIn('marketplace_seller_id', $isSellerAllowCity)
                        ->where('is_owner', 1)
                        ->pluck('marketplace_products.product_id')
                        ->toArray();
                } else {
                    $sellerProductIds = $this->with('product_flats')
                        ->join('warehouse_discount', 'marketplace_products.product_id', '=', 'warehouse_discount.product_id')
                        ->whereIn('marketplace_products.product_id', function ($query) use ($regionIds) {
                            $query->select('product_id')
                                ->from('marketplace_products')
                                ->whereIn('warehouse_region_id', $regionIds)
                                ->where('base_selling_price', '>', 0)
                                ->where('is_approved', 1)
                                ->where('is_owner', 1);

                        })
                        ->pluck('marketplace_products.product_id')
                        ->toArray();

                    $productIds = app('Webkul\Marketplace\Repositories\ProductRepository')
                        ->whereNotIn('marketplace_products.product_id', $sellerProductIds)
                        ->pluck('marketplace_products.product_id')
                        ->toArray();
                }
            }
        } else {
            $productIds = $this->with('product_flats')
                ->whereHas('product_flats', function ($q) use ($attribute) {
                    $q->where($attribute, 1);
                })
                ->leftjoin('warehouse_discount', 'marketplace_products.product_id', '=', 'warehouse_discount.product_id')
                ->where('base_selling_price', '>', 0)
                ->whereIn('warehouse_region_id', $regionIds)
                ->where('is_approved', 1)
                ->where('is_owner', 1)
                ->pluck('marketplace_products.product_id')
                ->toArray();
        }

        return $productIds;
    }

    public function getShopProducts($slug, $limit)
    {
        $isNewOrIsFeatured = ($slug == 'new-products') ? 'new' : 'featured';

        $adminAllowCity = core()->getConfigData('sales.shipping.origin.city');

        $city = str_replace(' ', '+', session()->get('location'));

        $isAdminAllowCity = ! empty($city) && (strtolower($city) == strtolower($adminAllowCity));

        $sellers = app('Webkul\MarketplaceWarehouse\Helpers\Location')->getAllowedSellers();

        $isSellerAllowCity = empty($sellers) ? [] : array_column($sellers->toArray(), 'id');

        return $this->productRepository
            ->with('product_flats')
            ->whereHas('product_flats', function ($query) use ($isNewOrIsFeatured, $isAdminAllowCity, $isSellerAllowCity) {
                $query->where($isNewOrIsFeatured, 1)
                        ->where('status', 1)
                        ->where('visible_individually', 1);

                $query->where(function ($subQuery) use ($isNewOrIsFeatured, $isSellerAllowCity, $isAdminAllowCity) {
                    if ($isAdminAllowCity) {
                        $subQuery->whereNotIn('product_id', $this->getSellersProductIds($isNewOrIsFeatured, $isSellerAllowCity));
                    } else if (! empty($isSellerAllowCity)) {
                        $subQuery->whereIn('product_id', $this->getSellersProductIds($isNewOrIsFeatured, $isSellerAllowCity, true));
                    } else {
                        $subQuery->where('product_id', 0);
                    }
                });
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Returns seller product Ids based on the given attribute value (new or featured).
     *
     * @param string $attribute
     * @return array
     */
    public function getSerachProductIds($isSellerAllowCity, $isOnlySeller = false)
    {
        $productIds = [];

        $location = Session::get('location');

        $regionIds = [];

        $city = app('Webkul\MarketplaceWarehouse\Repositories\CityRepository')->findOneByField('name', $location);

        if ($city) {
            $cityId = $city->id;

            $regions = app('Webkul\MarketplaceWarehouse\Repositories\AssignCityRepository')->findByField('city_id', $cityId);

            foreach ($regions as $region) {
                $regionIds[] = $region?->warehouse_region_id;
            }
        }

        if (! $isOnlySeller) {
            if (empty($regionIds)) {
                $productIds = $this->with('product_flats')
                    ->whereNotIn('marketplace_seller_id', $isSellerAllowCity)
                    ->where('is_owner', 1)
                    ->pluck('marketplace_products.product_id')
                    ->toArray();
            } else {
                $sellerProductIds = $this->with('product_flats')
                    ->join('warehouse_discount', 'marketplace_products.product_id', '=', 'warehouse_discount.product_id')
                    ->whereIn('marketplace_products.product_id', function ($query) use ($regionIds) {
                        $query->select('product_id')
                            ->from('marketplace_products')
                            ->whereIn('warehouse_region_id', $regionIds)
                            ->where('base_selling_price', '>', 0)
                            ->where('is_approved', 1)
                            ->where('is_owner', 1);

                    })
                    ->pluck('marketplace_products.product_id')
                    ->toArray();

                $productIds = app('Webkul\Marketplace\Repositories\ProductRepository')
                    ->whereNotIn('marketplace_products.product_id', $sellerProductIds)
                    ->pluck('marketplace_products.product_id')
                    ->toArray();

            }
        } else {
            $productIds = $this->with('product_flats')
                ->leftjoin('warehouse_discount', 'marketplace_products.product_id', '=', 'warehouse_discount.product_id')
                ->where('base_selling_price', '>', 0)
                ->whereIn('warehouse_region_id', $regionIds)
                ->where('is_approved', 1)
                ->where('is_owner', 1)
                ->pluck('marketplace_products.product_id')
                ->toArray();
        }

        return $productIds;
    }
}
