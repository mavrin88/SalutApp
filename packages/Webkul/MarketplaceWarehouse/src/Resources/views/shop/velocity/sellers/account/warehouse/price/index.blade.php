@extends('shop::customers.account.index')

@section('page_title')
    {{ __('marketplace_warehouse::app.shop.warehouse.price.title') }}
@endsection

@section('page-detail-wrapper')

    <div class="account-layout">

        <div class="account-head mb-10">
            <span class="account-heading">
                {{ __('marketplace_warehouse::app.shop.warehouse.price.title') }}
            </span>

            <div class="account-action">
                <a href="{{ route('marketplace_warehouse.user.warehouse.price.create') }}" class="btn btn-primary">
                    {{ __('marketplace_warehouse::app.shop.warehouse.create') }}
                </a>
            </div>

            <div class="horizontal-rule"></div>
        </div>

        {!! view_render_event('marketplace_warehouse.warehouse.price_list.before') !!}

        <div class="account-items-list">
            <div class="account-table-content">

                <datagrid-plus src="{{ route('marketplace_warehouse.user.warehouse.price.index') }}"></datagrid-plus>

            </div>
        </div>

        {!! view_render_event('marketplace_warehouse.warehouse.price_list.after') !!}

    </div>

@endsection