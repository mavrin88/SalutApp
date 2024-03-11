@extends('shop::customers.account.index')

@section('page_title')
    {{ __('marketplace_warehouse::app.shop.warehouse.title') }}
@endsection

@section('page-detail-wrapper')

    <div class="account-layout">

        <div class="account-head mb-10">
            <span class="account-heading">
                {{ __('marketplace_warehouse::app.shop.warehouse.title') }}
            </span>

            <div class="horizontal-rule"></div>
        </div>

        {!! view_render_event('marketplace_warehouse.warehouse.list.before') !!}

        <div class="account-items-list">
            <div class="account-table-content">

                <datagrid-plus src="{{ route('marketplace_warehouse.user.warehouse.view', $id) }}"></datagrid-plus>

            </div>
        </div>

        {!! view_render_event('marketplace_warehouse.warehouse.list.after') !!}

    </div>

@endsection