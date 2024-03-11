@inject ('toolbarHelper', 'Webkul\Product\Helpers\Toolbar')

@inject('sellerProduct', 'Webkul\Marketplace\Repositories\ProductRepository')

@extends('shop::customers.account.index')

@section('page_title')
    {{ __('shop::app.customer.account.wishlist.page-title') }}
@endsection

@section('page-detail-wrapper')
    <div class="account-head">
        <span class="account-heading">{{ __('shop::app.customer.account.wishlist.title') }}</span>

        @if (count($items))
            <div class="account-action float-right">
                <form id="remove-all-wishlist" class="d-none" action="{{ route('shop.customer.wishlist.remove_all') }}" method="POST">
                    @method('DELETE')

                    @csrf
                </form>

                <a
                    class="remove-decoration theme-btn light"
                    href="javascript:void(0);"
                    onclick="document.getElementById('remove-all-wishlist').submit();">
                    {{ __('shop::app.customer.account.wishlist.deleteall') }}
                </a>
            </div>
        @endif
    </div>

    {!! view_render_event('bagisto.shop.customers.account.wishlist.list.before', ['wishlist' => $items]) !!}

    <div class="wishlist-container">

        @if ($items->count())
            @foreach ($items as $item)
                @php
                    if(isset($item->additional['seller_info'])){
                        $sellerProduct = $sellerProduct->find($item->additional['seller_info']['product_id']);
                    }
                    $currentMode = $toolbarHelper->getCurrentMode();
                    $moveToCartText = __('shop::app.customer.account.wishlist.move-to-cart');
                @endphp

                @include ('shop::products.list.card', [
                    'list'                 => true,
                    'checkmode'            => true,
                    'moveToCart'           => true,
                    'wishlistMoveRoute'    => route('shop.customer.wishlist.move', $item->id),
                    'addToCartForm'        => true,
                    'removeWishlist'       => true,
                    'reloadPage'           => true,
                    'itemId'               => $item->id,
                    'item'                 => $item,
                    'product'              => $item->product,
                    'additionalAttributes' => true,
                    'btnText'              => $moveToCartText,
                    'addToCartBtnClass'    => 'small-padding',
                ])
            @endforeach

            <div>
                {{ $items->links()  }}
            </div>
        @else
            <div class="empty">
                {{ __('customer::app.wishlist.empty') }}
            </div>
        @endif
    </div>

    {!! view_render_event('bagisto.shop.customers.account.wishlist.list.after', ['wishlist' => $items]) !!}
@endsection
