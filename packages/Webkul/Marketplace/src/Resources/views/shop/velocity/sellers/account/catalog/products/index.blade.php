@extends('shop::customers.account.index')

@section('page_title')
    {{ __('marketplace::app.shop.sellers.account.catalog.products.title') }}
@endsection

@push('css')
    <style>
        .btn-sm {
            max-height: 35px;
            margin-right: 5px;
        }
    </style>
@endpush

@section('page-detail-wrapper')
    <div class="account-layout">

        <div class="account-head">
            <span class="account-heading">
                {{ __('marketplace::app.shop.sellers.account.catalog.products.title') }}
            </span>

            <div class="account-action">
                <a href="{{ route('marketplace.account.products.search') }}" class="btn btn-primary">
                    {{ __('marketplace::app.shop.sellers.account.catalog.products.create') }}
                </a>
            </div>

            <div class="horizontal-rule"></div>
        </div>

        {!! view_render_event('marketplace.sellers.account.catalog.products.list.before') !!}

        <div class="account-items-list">
            <div class="account-table-content">

                <datagrid-plus src="{{ route('marketplace.account.products.index') }}"></datagrid-plus>

            </div>
        </div>

        {!! view_render_event('marketplace.sellers.account.catalog.products.list.after') !!}

    </div>

@endsection