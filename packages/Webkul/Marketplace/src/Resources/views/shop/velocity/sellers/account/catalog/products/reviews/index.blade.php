@extends('shop::customers.account.index')

@section('page_title')
    {{ __('marketplace::app.shop.layouts.product-reviews') }}
@endsection

@section('page-detail-wrapper')
    <div class="account-layout">

        <div class="account-head">
            <span class="account-heading">
                {{ __('marketplace::app.shop.layouts.product-reviews') }}
            </span>

            <div class="horizontal-rule"></div>
        </div>

        {!! view_render_event('marketplace.sellers.account.catalog.products.list.before') !!}

        <div class="account-items-list">
            <div class="account-table-content">

                <datagrid-plus src="{{ route('marketplace.account.products.review') }}"></datagrid-plus>

            </div>
        </div>

        {!! view_render_event('marketplace.sellers.account.catalog.products.list.after') !!}

    </div>

@endsection