<span id="product-{{ $discount->id }}-discount">
    <a id="product-{{ $discount->id }}-discount-anchor" href="javascript:void(0);" onclick="showEditDiscountForm('{{ $discount->id }}')">{{ $totalDiscount }}</a>
</span>

<span id="edit-product-{{ $discount->id }}-discount-form-block" style="display: none;">
    <form id="edit-product-{{ $discount->id }}-discount-form" action="javascript:void(0);">
        @csrf

        @method('PUT')

        <div class="control-group" :class="[errors.has('discount') ? 'has-error' : '']"> 
            <label for="discount">{{ __('marketplace_warehouse::app.shop.warehouse.region.discount') }}</label>
            <input 
                type="number" 
                name="discount"
                class="control" 
                value="{{ $totalDiscount }}"
                data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.region.discount') }}&quot;"/>
        </div>

        <button class="btn btn-primary" onclick="saveEditDiscountForm('{{ route('marketplace_warehouse.user.warehouse.region.update_discount', $discount->id) }}', '{{ $discount->id }}')">{{ __('admin::app.catalog.products.save') }}</button>

        <button class="btn btn-danger" onclick="cancelEditDiscountForm('{{ $discount->id }}')">{{ __('admin::app.catalog.products.cancel') }}</button>
    </form>
</span>
