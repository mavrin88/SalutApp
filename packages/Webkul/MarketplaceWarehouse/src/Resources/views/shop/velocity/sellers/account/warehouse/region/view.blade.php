@extends('shop::customers.account.index')

@section('page_title')
    {{ __('marketplace_warehouse::app.shop.warehouse.region.title') }}
@endsection

@section('page-detail-wrapper')

    <div class="account-layout">

        <div class="account-head mb-10">
            <span class="account-heading" id='hello'>
                {{ __('marketplace_warehouse::app.shop.warehouse.region.title') }}
            </span>

            <div class="horizontal-rule"></div>
        </div>

        {!! view_render_event('marketplace_warehouse.warehouse.region.list.view.before') !!}

        <div class="account-items-list">
            <div class="account-table-content" >

                <datagrid-plus src="{{ route('marketplace_warehouse.user.warehouse.region.view', $id) }}"></datagrid-plus>

            </div>
        </div>

        {!! view_render_event('marketplace_warehouse.warehouse.region.list.view.after') !!}

    </div>
@endsection

@push('scripts')
    @include('admin::export.export', ['gridName' => app('Webkul\MarketplaceWarehouse\DataGrids\Shop\RegionRecordDataGrid')])

    <script>
        function reloadPage(getVar, getVal) {
            let url = new URL(window.location.href);

            url.searchParams.set(getVar, getVal);

            window.location.href = url.href;
        }

        function showEditDiscountForm(productId) {
            $(`#product-${productId}-discount`).hide();

            $(`#edit-product-${productId}-discount-form-block`).show();
        }

        function cancelEditDiscountForm(productId) {
            $(`#edit-product-${productId}-discount-form-block`).hide();

            $(`#product-${productId}-discount`).show();
        }

        function saveEditDiscountForm(updateSource, productId) {
            let discountFormData = $(`#edit-product-${productId}-discount-form`).serialize();

            axios
                .post(updateSource, discountFormData)
                .then(function (response) {
                    let data = response.data;

                    $(`#edit-product-${productId}-discount-form-block`).hide();

                    $(`#product-${productId}-discount-anchor`).text(data.updatedTotal);

                    $(`#product-${productId}-discount`).show();

                    let parentTd = $(`#product-${productId}-discount`).parent();

                    let nextSibling = parentTd.next();

                    if (nextSibling.length > 0) {
                        nextSibling.text(data.realSellingPrice); 
                    }
                })
                .catch(function ({ response }) {
                    let { data } = response;

                    $(`#inventoryErrors${productId}`).text(data.message);
                });
        }
    </script>
@endpush