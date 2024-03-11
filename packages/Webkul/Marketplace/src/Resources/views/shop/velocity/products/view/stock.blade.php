{!! view_render_event('bagisto.shop.products.view.stock.before', ['product' => $product]) !!}

@inject('InventoryHelper', 'Webkul\Marketplace\Helpers\Helper')

<div class="col-12 availability">
    @if($product->type == 'simple' || $product->type == 'configurable')
        @php
            $inStock = $InventoryHelper->stockHaveSufficientQuantity($product);
        @endphp
        <button
            type="button"
            class="{{ $inStock ? 'active' : '' }} disable-box-shadow">
            @if ($inStock)
                {{ __('shop::app.products.in-stock') }}
            @else
                {{ __('shop::app.products.out-of-stock') }}
            @endif
        </button>
    @else
        @php
            $inStock = $product->haveSufficientQuantity(1);
        @endphp
        <button
            type="button"
            class="{{ $inStock ? 'active' : '' }} disable-box-shadow">
            @if ($inStock)
                {{ __('shop::app.products.in-stock') }}
            @else
                {{ __('shop::app.products.out-of-stock') }}
            @endif
        </button>
    @endif
</div>

{!! view_render_event('bagisto.shop.products.view.stock.after', ['product' => $product]) !!}
