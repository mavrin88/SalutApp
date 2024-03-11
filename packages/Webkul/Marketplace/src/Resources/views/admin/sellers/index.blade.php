@extends('marketplace::admin.layouts.content')

@section('page_title')
    {{ __('marketplace::app.admin.sellers.title') }}
@stop

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('marketplace::app.admin.sellers.title') }}</h1>
            </div>

            <div class="page-action">
                <a href="{{ route('admin.marketplace.sellers.create') }}" class="btn btn-lg btn-primary">{{ __('marketplace::app.admin.sellers.create') }}</a>
            </div>
        </div>

        <div class="page-content">

            <div class="page-content">
                <datagrid-plus src="{{ route('admin.marketplace.sellers.index') }}"></datagrid-plus>
            </div>
        </div>
    </div>

@stop

@push('scripts')
    @include('admin::export.export', ['gridName' => app('Webkul\Admin\DataGrids\ProductDataGrid')])

    <script>

        function showSellerStatusForm(sellerId) {
            $(`#seller-${sellerId}-status`).hide();

            $(`#edit-seller-${sellerId}-status-form-block`).show();
        }

        function cancelSellerStatusForm(sellerId) {

            $(`#edit-seller-${sellerId}-status-form-block`).hide();

            $(`#seller-${sellerId}-status`).show();
        }

        function saveSellerStatusForm(updateSource, sellerId) {
            let sellerStatusFormData = $(`#edit-seller-${sellerId}-status-form`).serialize();

            axios
                .post(updateSource, sellerStatusFormData)
                .then(function (response) {
                    let data = response.data;

                    $(`#inventoryErrors${sellerId}`).text('');

                    $(`#edit-seller-${sellerId}-status-form-block`).hide();

                    location.reload()

                    $(`#seller-${sellerId}-status-anchor`).text(data.getStatus);

                    $(`#seller-${sellerId}-status`).show();
                })
                .catch(function ({ response }) {
                    let { data } = response;

                    $(`#statusErrors${sellerId}`).text(data.message);
                });
        }
    </script>
@endpush