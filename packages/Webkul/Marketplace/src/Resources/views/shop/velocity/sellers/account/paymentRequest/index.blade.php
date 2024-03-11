@extends('shop::customers.account.index')

@section('page_title')
    {{ __('marketplace::app.shop.layouts.paymentRequest') }}
@endsection

@section('page-detail-wrapper')

    <div class="account-layout dashboard">

        <div class="account-head mb-10">
            <span class="account-heading">
                {{ __('marketplace::app.shop.layouts.paymentRequest') }}
            </span>

            <div class="account-action">
            </div>

            <div class="horizontal-rule"></div>
        </div>

        {!! view_render_event('marketplace.sellers.account.sales.transactions.list.before') !!}

        <div class="account-items-list">

            <div class="dashboard-stats" style="margin-top: 40px; margin-bottom: 30px;">

                <div class="dashboard-card">
                    <div class="title">
                        {{ __('marketplace::app.shop.sellers.account.sales.transactions.total-sale') }}
                    </div>

                    <div class="data">
                        {{ core()->formatBasePrice($statistics['total_sale']) }}
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="title">
                        {{ __('marketplace::app.shop.sellers.account.sales.transactions.total-payout') }}
                    </div>

                    <div class="data">
                        {{ core()->formatBasePrice($statistics['total_payout']) }}
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="title">
                        {{ __('marketplace::app.shop.sellers.account.sales.transactions.remaining-payout') }}
                    </div>

                    <div class="data">
                        {{ core()->formatBasePrice($statistics['remaining_payout']) }}
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="title">
                        {{ __('marketplace::app.shop.sellers.account.sales.transactions.total-refunded') }}
                    </div>

                    <div class="data">
                        {{ core()->formatBasePrice($statistics['total_refunded']) }}
                    </div>
                </div>

            </div>

            <div class="account-items-list">
                <div class="account-table-content">
                
                    <datagrid-plus src="{{ route('marketplace.account.payment.request.index') }}"></datagrid-plus>

                </div>
            </div>

        </div>

        {!! view_render_event('marketplace.sellers.account.sales.transactions.list.after') !!}

    </div>

@endsection