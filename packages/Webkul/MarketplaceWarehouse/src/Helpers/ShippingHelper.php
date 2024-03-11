<?php

namespace Webkul\MarketplaceWarehouse\Helpers;

use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Session;
use Webkul\MarketplaceWarehouse\Helpers\SellerRate;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductFlatRepository;
use Webkul\Checkout\Repositories\CartAddressRepository;

class ShippingHelper
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
     * @param 
     * @return void
     */
    public function __construct(
        protected SellerRepository $sellerRepository,
        protected ProductRepository $productRepository,
        protected CartAddressRepository $cartAddressRepository,
        protected ProductFlatRepository $productFlatRepository,
        protected SellerRate $sellerRate,
    )
    {
        $this->_config = request('_config');
    }

    /**
     * Find Appropriate TableRate Methods
     *
     * @return $shippingData
     */
    public function findAppropriateTableRateMethods()
    {
        $shippingMethods = [];
        $cart = Cart::getCart();

        //Get The Sellers Admin and Product
        foreach ($cart->items as $item) {
            $cartItem = $this->getSellerAdminItem($item);
            
            if ( $cartItem['marketplace_seller_id'] == null ) {
                $shippingRates = [];
                $cartItem['marketplace_seller_id'] = 0;
                $cartItem['warehouse_region_id'] = 0;
            } else {
                $shippingRates = $this->sellerRate->getAvailableSellerRate($cartItem);
            }
           
            if ( count($shippingRates) > 0 ) {
                $shippingMethods[$cartItem['marketplace_seller_id']][$item->product_id] = $shippingRates;
            } 
        }
        
        return $shippingMethods;
    }

    /**
     * Get The Seller or Admin Item
     *
     * @param $item
     * @return $cartItem
     */
    public function getSellerAdminItem($item)
    {
        $cartItem = null;

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

        if (isset($item->additional['seller_info']) && ! $item->additional['seller_info']['is_owner']) {
            $seller = $this->sellerRepository->find($item->additional['seller_info']['seller_id']);
        } else {
            $seller = $this->productRepository->getSellerByProductId($item->product_id);
        }

        if ( $seller && $seller->is_approved ) {

            $sellerProduct = $this->productRepository->findOneWhere([
                'product_id'            => $item->product->id,
                'marketplace_seller_id' => $seller->id,
            ]);

            $update_cart_item = [
                'product_id'            => $item->product_id,
                'weight'                => $item->weight,
                'price'                 => $item->price,
                'base_price'            => $item->base_price,
                'total'                 => $item->total,
                'quantity'              => $item->quantity,
                'parent_id'             => $item->parent_id,
                'cart_id'               => $item->cart_id,
                'sku'                   => $item->sku,
                'marketplace_seller_id' => $seller->id,
                'additional'            => [
                    'product'           => $item->additional['product_id'],
                    'quantity'          => $item->additional['quantity'],
                    'seller_info'       => [
                        'seller_id'         => $seller->id
                    ],
                ]
            ];
            
            if (in_array($item->product_id, $productIds)) {
                $region  = app('Webkul\MarketplaceWarehouse\Repositories\DiscountRepository')
                    ->findByField('product_id', $item->product_id)
                    ->WhereIn('warehouse_region_id', $regionIds)
                    ->first();
                
                $update_cart_item['warehouse_region_id'] = $region->warehouse_region_id;
            } 

            if ( $sellerProduct['is_owner'] == 0 ) {
                $update_cart_item['additional'] = $item->additional;
            }

            $cartItem = collect($update_cart_item);
        } else {
            $cartItem = collect([
                'product_id'            => $item->product_id,
                'weight'                => $item->weight,
                'price'                 => $item->price,
                'base_price'            => $item->base_price,
                'total'                 => $item->total,
                'quantity'              => $item->quantity,
                'parent_id'             => $item->parent_id,
                'cart_id'               => $item->cart_id,
                'sku'                   => $item->sku,
                'marketplace_seller_id' => null,
                'additional'            => $item->additional,
                'warehouse_region_id'   => null
            ]);
        }

        return $cartItem;
    }

    /**
     * Get the Method Wise Shipping Data
     *
     * @param $shippingMethods
     * @return $data
     */
    public function getMethodWiseShippingData($shippingMethods)
    {
        if ($shippingMethods != null) {
            foreach ($shippingMethods as $sellerId => $shippingMethod) {
                
                foreach($shippingMethod as $productId => $methods) {
                    
                    if ($methods != null) {
                        foreach($methods as $methodCode => $method) {
                            
                            $codeWiseMethods[0][] = $method;
                        }
                    }
                }
            }
            
            foreach ($codeWiseMethods as $methodCode => $commonMethods) {
                $data[$methodCode] = $commonMethods;
            }

            return $data;
        }
    }

    public function findWeight() {
        $shippingMethods = [];
        $cart = Cart::getCart();

        //Get The Sellers Admin and Product
        foreach ($cart->items as $item) {
            $cartItem = $this->getSellerAdminItem($item);
            
            if ( $cartItem['marketplace_seller_id'] == null ) {
                $shippingRates = [];
                $cartItem['marketplace_seller_id'] = 0;
                $cartItem['warehouse_region_id'] = 0;
            } else {
                $shippingMethods[$cartItem['marketplace_seller_id']][$item->product_id] = $cartItem;
            }
        }
        
        return $shippingMethods;
    }
}