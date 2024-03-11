@extends('shop::customers.account.index')

@section('page_title')
    {{ __('marketplace_warehouse::app.shop.warehouse.region.title') }}
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

        <div class="account-head mb-10">
            <span class="account-heading">
                {{ __('marketplace_warehouse::app.shop.warehouse.region.title') }}
            </span>

            <div class="account-action">
                <a href="{{ route('marketplace_warehouse.user.warehouse.region.create') }}" class="btn btn-primary">
                    {{ __('marketplace_warehouse::app.shop.warehouse.create') }}
                </a>
            </div>

            <div class="horizontal-rule"></div>
        </div>

        {!! view_render_event('marketplace_warehouse.warehouse.region.before') !!}

        <div class="account-items-list">
            <div class="account-table-content">

                <datagrid-plus src="{{ route('marketplace_warehouse.user.warehouse.region.index') }}"></datagrid-plus>

            </div>
        </div>

        {!! view_render_event('marketplace_warehouse.warehouse.region.after') !!}

    </div>

@endsection
