@extends('admin::layouts.master')

@section('page_title')
    {{ __('marketplace::app.shop.sellers.account.sales.transactions.view-title', ['transaction_id' => $transaction->transaction_id]) }}
@stop


@section('content-wrapper')
    <div class="content full-page">
        <div class="page-header">
            <div class="page-title">
               <h1>{{ __('marketplace::app.shop.sellers.account.sales.transactions.view-title', ['transaction_id' => $transaction->transaction_id]) }}</h1>
            </div>
        </div>

        <div class="page-content">
            <div class="sale-container">
                <accordian title="Transaction Information" :active="true">
                    <div slot="body">
                        <div class="sale">
                            <div class="sale-section">
                                <div class="section-content">
                                    <div class="row">
                                        <span class="title">
                                            {{ __('marketplace::app.shop.sellers.account.sales.transactions.created-at') }}
                                        </span>

                                        <span class="value">
                                            {{ core()->formatDate($transaction->created_at, 'd M Y') }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title">
                                            {{ __('marketplace::app.shop.sellers.account.sales.transactions.payment-method') }}
                                        </span>

                                        <span class="value">
                                            {{ ucfirst($transaction->method) }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title">
                                            {{ __('marketplace::app.shop.sellers.account.sales.transactions.total') }}
                                        </span>

                                        <span class="value">
                                            {{ core()->formatBasePrice($transaction->base_total) }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title">
                                            {{ __('marketplace::app.shop.sellers.account.sales.transactions.comment') }}
                                        </span>

                                        <span class="value">
                                            {{ $transaction->comment }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </accordian>
            </div>

            <div  class="sale-container">
                <?php $sellerOrder = $transaction->order; ?>
                <div class="sale-section">
                    <div class="secton-title">
                        <span>Order <a href="{{ route('admin.sales.orders.view', $transaction->order->order_id) }}">#{{ $transaction->order->order->increment_id}}</a></span>
                    </div>
                    <div class="section-content">
                        <div class="table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>{{ __('shop::app.customer.account.order.view.product-name') }}</th>
                                        <th>{{ __('shop::app.customer.account.order.view.price') }}</th>
                                        <th>{{ __('shop::app.customer.account.order.view.qty') }}</th>
                                        <th>{{ __('shop::app.customer.account.order.view.total') }}</th>
                                        <th>{{ __('marketplace::app.shop.sellers.account.sales.transactions.commission') }}</th>
                                        <th>{{ __('marketplace::app.shop.sellers.account.sales.transactions.seller-total') }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($sellerOrder->items as $sellerOrderItem)

                                        <tr>
                                            <td data-value="{{ __('shop::app.customer.account.order.view.product-name') }}">
                                                {{ $sellerOrderItem->item->name }}

                                                @if (isset($sellerOrderItem->additional['attributes']))
                                                    <div class="item-options">

                                                        @foreach ($sellerOrderItem->additional['attributes'] as $attribute)
                                                            <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}</br>
                                                        @endforeach

                                                    </div>
                                                @endif
                                            </td>

                                            <td data-value="{{ __('shop::app.customer.account.order.view.price') }}">{{ core()->formatPrice($sellerOrderItem->item->price, $sellerOrder->order->order_currency_code) }}</td>

                                            <td data-value="{{ __('shop::app.customer.account.order.view.qty') }}">{{ $sellerOrderItem->item->qty_ordered }}</td>

                                            <td data-value="{{ __('shop::app.customer.account.order.view.total') }}">{{ core()->formatPrice($sellerOrderItem->item->total, $sellerOrder->order->order_currency_code) }}</td>

                                            <td data-value="{{ __('marketplace::app.shop.sellers.account.sales.transactions.commission') }}">{{ core()->formatPrice($sellerOrderItem->commission, $sellerOrder->order->order_currency_code) }}</td>

                                            <td data-value="{{ __('marketplace::app.shop.sellers.account.sales.transactions.seller-total') }}">{{ core()->formatPrice($sellerOrderItem->seller_total, $sellerOrder->order->order_currency_code) }}</td>
                                        </tr>

                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                        <div style="float: right;" class="totals">
                            <table class="table sale-summary">
                                <tbody>
                                    <tr>
                                        <td>{{ __('marketplace::app.shop.sellers.account.sales.transactions.sub-total') }}</td>
                                        <td>-</td>
                                        <td>{{ core()->formatPrice($sellerOrder->sub_total, $sellerOrder->order->order_currency_code) }}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ __('shop::app.customer.account.order.view.shipping-handling') }}</td>
                                        <td>-</td>
                                        <td>{{ core()->formatPrice($sellerOrder->base_shipping_amount, $sellerOrder->order->order_currency_code) }}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ __('marketplace::app.shop.sellers.account.sales.transactions.tax') }}</td>
                                        <td>-</td>
                                        <td>{{ core()->formatPrice($sellerOrder->tax_amount, $sellerOrder->order->order_currency_code) }}</td>
                                    </tr>

                                    <tr class="bold">
                                        <td>{{ __('marketplace::app.shop.sellers.account.sales.transactions.commission') }}</td>
                                        <td>-</td>
                                        <td>-{{ core()->formatPrice($sellerOrder->commission, $sellerOrder->order->order_currency_code) }}</td>
                                    </tr>

                                    <tr class="bold">
                                        <td>{{ __('marketplace::app.shop.sellers.account.sales.transactions.seller-total') }}</td>
                                        <td>-</td>
                                        <td>{{ core()->formatPrice($sellerOrder->seller_total, $sellerOrder->order->order_currency_code) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop