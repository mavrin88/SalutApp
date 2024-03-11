@extends('shop::customers.account.index')

@section('page_title')
    {{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.view-title', ['receipt_id' => $receipt->id]) }}
@endsection

@section('page-detail-wrapper')
    <div class="account-layout">

        <div class="account-head">
            <span class="account-heading">
                {{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.view-title', ['receipt_id' => $receipt->id]) }}
            </span>

            <span></span>
        </div>

        {!! view_render_event('marketplace_warehouse.warehouse.receipt_and_withdrawal.list.view.before') !!}

        <div class="sale-container">

            <tabs>
                <tab name="{{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.info') }}" :selected="true">

                    <div class="account-table-content profile-page-content">
                        <div class="table">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            {{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.created-on') }}
                                        </td>

                                        <td>
                                            {{ core()->formatDate($receipt->created_at, 'd M Y') }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            {{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.warehouse-name') }}
                                        </td>

                                        <td>
                                            {{ $warehouse }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            {{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.title') }}
                                        </td>

                                        <td>
                                            {{ $receipt->title }}
                                        </td>
                                    </tr>


                                    <tr>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="account-items-list">
                        <div class="account-table-content">
            
                            <datagrid-plus src="{{ route('marketplace_warehouse.user.warehouse.receipts-and-withdrawals.view', $receipt->id) }}"></datagrid-plus>
            
                        </div>
                    </div>

                </tab>
            </tabs>

        </div>

        {!! view_render_event('marketplace_warehouse.warehouse.receipt_and_withdrawal.list.view.after') !!}

    </div>

@endsection