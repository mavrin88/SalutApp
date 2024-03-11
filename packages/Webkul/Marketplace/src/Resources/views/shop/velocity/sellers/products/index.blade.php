@extends('marketplace::shop.layouts.master')

@inject ('categoryRepository', 'Webkul\Category\Repositories\CategoryRepository')

@php
    $category = $categoryRepository->first();
@endphp

@section('page_title')
    {{ __('marketplace::app.shop.sellers.products.title', ['shop_title' => $seller->shop_title]) }} - {{ $seller->name }}
@endsection

@section('content-wrapper')

    <div class="main">

        {!! view_render_event('marketplace.shop.sellers.products.index.before', ['seller' => $seller]) !!}

        <div class="profile-container">
            @include('marketplace::shop.sellers.top-profile')

            <div class="profile-right-block">

                @if ($banner = $seller->banner_url)
                    <img src="{{ $banner }}" />
                @else
                    <img src="{{ bagisto_asset('vendor/webkul/marketplace/assets/images/mp-velocity-banner.png') }}" />
                @endif

            </div>
        </div>

        <section class="category-container seller-products">

            <div class="row col-12 velocity-divide-page category-page-wrapper">

                <div class="category-container right">

                    <div class="category-block">

                        @if ($product->count())

                            <div class="filters-container">

                                @include ('shop::products.list.toolbar')

                                @inject ('toolbarHelper', 'Webkul\Product\Helpers\Toolbar')
                            </div>

                            @if ($toolbarHelper->getCurrentMode() == 'grid')
                                <div class="row col-12 remove-padding-margin pt15" style="padding:15px 0px 0px !important">
                                    @foreach ($product as $productFlat)
                                        @include ('shop::products.list.card', ['product' => $productFlat])
                                    @endforeach
                                </div>
                            @else
                                <div class="product-list pt15">
                                    @foreach ($product as $productFlat)
                                        @include ('shop::products.list.card', [
                                            'product' => $productFlat,
                                            'checkmode' => true,
                                        ])
                                    @endforeach
                                </div>
                            @endif

                            {!! view_render_event('marketplace.shop.sellers.products.index.pagination.before') !!}

                            <div class="bottom-toolbar">
                                {{ $product->links() }}
                            </div>

                            {!! view_render_event('marketplace.shop.sellers.products.index.pagination.after') !!}
                        @else
                            <div class="product-list empty">
                                <h2>{{ __('shop::app.products.whoops') }}</h2>

                                <p>
                                    {{ __('shop::app.products.empty') }}
                                </p>
                            </div>

                        @endif

                    </div>

                </div>
            </div>

        </section>

        {!! view_render_event('marketplace.shop.sellers.products.index.after', ['seller' => $seller]) !!}

    </div>

@endsection
