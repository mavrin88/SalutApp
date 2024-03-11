<span id="seller-{{ $seller->id }}-status">
    <a id="seller-{{ $seller->id }}-status-anchor" href="javascript:void(0);" onclick="showSellerStatusForm('{{ $seller->id }}')">

        @if ($seller->is_approved == 1)
            <span class="badge badge-md badge-success"> {{ __('marketplace::app.admin.sellers.approved') }} </span>
        @else
            <span class="badge badge-md badge-danger"> {{ __('marketplace::app.admin.sellers.un-approved') }} </span>
        @endif
    </a>
</span>

<span id="edit-seller-{{ $seller->id }}-status-form-block" style="display: none;">
    <form id="edit-seller-{{ $seller->id }}-status-form" action="javascript:void(0);">
        @csrf

        @method('POST')

        @if(!$seller->is_approved)
            <div class="control-group" :class="[errors.has('is_approved') ? 'has-error' : '']">
                <label for="is_approved" class="required">{{ __('admin::app.customers.customers.status') }}</label>
                <select name="is_approved" class="control" v-validate="'required'" data-vv-as="&quot;{{ __('marketplace::app.admin.sellers.status') }}&quot;">
                    <option value="1">{{ __('marketplace::app.admin.sellers.approve') }}</option>
                </select>
                <span class="control-error" v-if="errors.has('is_approved')">@{{ errors.first('is_approved') }}</span>
            </div>
        @endif

        @if($seller->is_approved)
            <div class="control-group" :class="[errors.has('is_approved') ? 'has-error' : '']">
                <label for="is_approved" class="required">{{ __('admin::app.customers.customers.status') }}</label>
                <select name="is_approved" class="control" v-validate="'required'" data-vv-as="&quot;{{ __('admin::app.customers.customers.status') }}&quot;">
                    <option value="0">{{ __('marketplace::app.admin.sellers.unapprove') }}</option>
                </select>
                <span class="control-error" v-if="errors.has('is_approved')">@{{ errors.first('is_approved') }}</span>
            </div>
        @endif

        <button class="btn btn-primary" onclick="saveSellerStatusForm('{{ route('admin.marketplace.seller.status.update', $seller->id) }}', '{{ $seller->id }}')">{{ __('admin::app.catalog.products.save') }}</button>

        <button class="btn btn-danger" onclick="cancelSellerStatusForm('{{ $seller->id }}')">{{ __('admin::app.catalog.products.cancel') }}</button>
    </form>
</span>
