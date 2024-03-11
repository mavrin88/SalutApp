<?php

namespace Webkul\MarketplaceWarehouse\Helpers;

use Illuminate\Support\Facades\Session;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Checkout\Facades\Cart;

class SellerRate
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository $productRepostiory
     * @return void
     */
    public function __construct(
        protected productRepository $productRepository
    )  {
        $this->_config = request('_config');
    }

    /**
     * Get the Selle Shipping Rate
     *
     * @param $cartItem
     * @return $sellerRate
     */
    public function getAvailableSellerRate($cartItem)
    {
        $price = core()->convertToBasePrice($cartItem['price']);

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

        $sellerSupesetRates = app('Webkul\MarketplaceWarehouse\Repositories\DeliveryChargeRepository')
            ->where('warehouse_delivery_charges.warehouse_region_id', $cartItem['warehouse_region_id'])
            ->where('warehouse_delivery_charges.from', '<=', $price)
            ->where('warehouse_delivery_charges.to', '>=', $price)
            ->orderBy('warehouse_delivery_charges.created_at')
            ->get();
       
        if ( count($sellerSupesetRates) > 0 ) {
            foreach ($sellerSupesetRates as $sellerSupesetRate) {
                $sellerRate[$sellerSupesetRate->code] = $this->getDataInArrayFormate($cartItem, $sellerSupesetRate);   
            }
        } else {
            return $sellerSupesetRates;
        }

        return $sellerRate;
    }

    /**
     * Get Data in Array
     *
     * @param $cartItem, $superSet
     * @return $adminRate
     */
    public function getDataInArrayFormate($cartItem, $superSet)
    {
        $product = $this->productRepository->find($cartItem['product_id']);

        if (! $product->getTypeInstance()->isStockable() && core()->getConfigData('sales.carriers.mpwarehouse.type') == 'per_unit' ) {
            $superSet->price = 0;
        }

        $sellerRate = [
            'price'                 => $cartItem['price'],
            'base_price'            => $cartItem['base_price'],
            'weight'                => $cartItem['weight'],
            'shipping_cost'         => $superSet->cost,
            'marketplace_seller_id' => $cartItem['marketplace_seller_id'],
            'warehouse_region_id'   => $superSet->warehouse_region_id,
            'quantity'              => $cartItem['quantity']
        ];

        return $sellerRate;
    }
}