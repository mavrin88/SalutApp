{!! view_render_event('bagisto.shop.products.price.before', ['product' => $product]) !!}

@inject ('priceHelper', 'Webkul\Marketplace\Helpers\Price')
<?php        
    $status = core()->getConfigData('marketplace.settings.general.status');

    if ($status) {
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
    }

    if (isset($seller)) {
        if ($product->type == 'configurable') {

            $marketPlaceProduct = app('Webkul\Marketplace\Repositories\ProductRepository')->findOneWhere([
                'marketplace_seller_id' => $seller->id,
                'product_id' => $product->id
            ]);

            if (isset($marketPlaceProduct)) {
                $childProduct = app('Webkul\Marketplace\Repositories\ProductRepository')->findWhere([
                    'marketplace_seller_id' => $seller->id,
                    'parent_id' => $marketPlaceProduct->id
                ])->pluck('price');

                if (count($childProduct)) {
                    $minPrice = min($childProduct->toArray());
                    $variantMinPrice = core()->currency($minPrice);
                }
            }
        }
    }
?>

<div class="product-price">
    @if ($status && in_array($product->id, $productIds))
        @php
            $minPrice = app('Webkul\MarketplaceWarehouse\Repositories\DiscountRepository')
                ->findByField('product_id', $product->id)
                ->whereIn('warehouse_region_id', $regionIds)
                ->first();
        @endphp
       
        @if ($minPrice && $minPrice->discount > 0)
            <div class="sticker sale">
                {{ __('shop::app.products.sale') }}
            </div>

            <span class="regular-price">{{ core()->currency($minPrice->base_selling_price) }}</span>

            <span class="special-price">{{ core()->currency($minPrice->real_selling_price) }}</span>
        @else
            <span>{{ core()->currency($minPrice->base_selling_price) }}</span>
        @endif

    @elseif ($product->type == 'configurable')
        <span class="price-label">{{ __('shop::app.products.price-label') }}</span>

        @if (isset($seller) && isset($childProduct) && isset($variantMinPrice))
            <span class="final-price">{{ $variantMinPrice }}</span>
        @else
            <span class="final-price">{{ core()->currency($product->product->getTypeInstance()->getMinimalPrice()) }}</span>
        @endif
    @else
        @if ($priceHelper->haveSpecialPrice($product))
            <div class="sticker sale">
                {{ __('shop::app.products.sale') }}
            </div>

            <span class="regular-price">{{ core()->currency($product->price) }}</span>

            <span class="special-price">{{ core()->currency($priceHelper->getSpecialPrice($product)) }}</span>
        @else
            @if($product->type == 'bundle' || $product->type == 'grouped')
                {!! $product->product->getTypeInstance()->getPriceHtml() !!}
            @else
           
                <span>{{ core()->currency($product->price) }}</span>
            @endif
        @endif
    @endif
</div>

{!! view_render_event('bagisto.shop.products.price.after', ['product' => $product]) !!}