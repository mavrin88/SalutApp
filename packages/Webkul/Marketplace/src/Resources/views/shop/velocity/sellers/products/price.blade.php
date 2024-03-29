{!! view_render_event('bagisto.shop.products.price.before', ['product' => $product]) !!}

@inject ('priceHelper', 'Webkul\Marketplace\Helpers\Price')
<?php
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
    @if ($product->type == 'configurable')
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