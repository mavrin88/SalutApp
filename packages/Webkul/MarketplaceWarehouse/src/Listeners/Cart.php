<?php

namespace Webkul\MarketplaceWarehouse\Listeners;

use Illuminate\Support\Facades\Session;
use Webkul\Marketplace\Listeners\Cart as MpCart;
use Webkul\Product\Repositories\ProductRepository as CoreProductRepository;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Checkout\Repositories\CartItemRepository;

class Cart extends MpCart
{
    /**
     * Create a new customer event listener instance.
     *
     * @param  Webkul\Product\Repositories\ProductRepository $coreProductRepository
     * @param  Webkul\Marketplace\Repositories\ProductRepository $productRepository
     * @param  Webkul\Checkout\Repositories\CartItemRepository   $cartItemRepository
     * @return void
     */
    public function __construct(
        protected ProductRepository $productRepository,
        protected CoreProductRepository $coreProductRepository,
        protected CartItemRepository $cartItemRepository
    ) {
    }

    /**
     * Product added to the cart
     *
     * @param mixed $cartItem
     */
    public function cartItemAddAfter()
    {
        $cartItems = $this->cartItemRepository->get();

        foreach ($cartItems as $items) {
            if (
                ! empty($items->additional['seller_info'])
            ) {
                $product = $this->productRepository->findOneWhere([
                    'marketplace_seller_id' => $items->additional['seller_info']['seller_id'],
                    'product_id' => $items->additional['product_id']
                ]);

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

                if (in_array($product->product_id, $productIds)) {
                    $minPrice = app('Webkul\MarketplaceWarehouse\Repositories\DiscountRepository')
                        ->findByField('product_id', $product->product_id)
                        ->whereIn('warehouse_region_id', $regionIds)
                        ->first();

                    $minPrice = $minPrice->discount > 0 ? $minPrice->real_selling_price : $minPrice->base_selling_price;
                }

                if ($product && $minPrice) {
                    $items->price = core()->convertPrice($minPrice);
                    $items->base_price = $minPrice;
                    $items->custom_price = $minPrice;
                    $items->total = core()->convertPrice($minPrice * $items->quantity);
                    $items->base_total = $minPrice * $items->quantity;

                    $items->save();
                }
            } elseif (
                isset($items->additional['seller_info']) &&
                ! $items->additional['seller_info']['is_owner']
            ) {
                $product = $this->productRepository->findOneWhere([
                    'marketplace_seller_id' => $items->additional['seller_info']['seller_id'],
                    'id' => $items->additional['product_id']
                ]);

                if ($product) {
                    $items->price = core()->convertPrice($product->price);
                    $items->base_price = $product->price;
                    $items->custom_price = $product->price;
                    $items->total = core()->convertPrice($product->price * $items->quantity);
                    $items->base_total = $product->price * $items->quantity;

                    if ($items->product->type == 'downloadable') {

                        $data = request()->all();

                        foreach ($product->downloadable_links as $link) {
                            if (! in_array($link->id, $data['links'])) {
                                continue;
                            }

                            $items->price  += core()->convertPrice($link->price);
                            $items->base_price  += $link->price;
                            $items->custom_price += $link->price;
                            $items->total += (core()->convertPrice($link->price) * $items->quantity);
                            $items->base_total += ($link->price * $items->quantity);
                        }
                    }

                    $items->save();
                }

                $items->save();
            } else {
                $items->save();
            }
        }
    }
}
