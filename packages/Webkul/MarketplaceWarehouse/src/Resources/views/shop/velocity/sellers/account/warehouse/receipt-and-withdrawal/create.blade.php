@extends('shop::customers.account.index')

@section('page_title')
    {{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.create') }}
@endsection

@section('page-detail-wrapper')

    <div class="account-layout">

        <form method="POST" action="{{ route('marketplace_warehouse.user.warehouse.receipt-and-withdrawal.store') }}" class="account-table-content">
            <div class="account-head mb-10">
                <span class="account-heading">
                    {{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.title') }}
                </span>

                <div class="account-action">
                    <button type="submit" class="btn btn-primary">
                        {{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.create') }}
                    </button>
                </div>

                <div class="horizontal-rule"></div>
            </div>

            {!! view_render_event('marketplace_warehouse.warehouse.receipt_and_withdrawal.create.before') !!}

            <div class="account-table-content">

                @csrf()

                {!! view_render_event('marketplace_warehouse.warehouse.create_receipt_and_withdrawal_form_accordian.create.before') !!}

                <accordian :title="'{{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.title') }}'" :active="true">
                    <div slot="header">
                        {{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.title') }}
                        <i class="icon expand-icon right"></i>
                    </div> 
                
                    <div slot="body">

                        <div class="control-group" :class="[errors.has('title') ? 'has-error' : '']">
                            <label for="title" class="required">{{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.title') }}</label>
                            <input type="text" class="control" name="title" v-validate="'required|max:200'"  data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.title') }}&quot;"/>
                            <span class="control-error" v-if="errors.has('title')">@{{ errors.first('title') }}</span>
                        </div>

                        <div class="control-group" :class="[errors.has('warehouse_name') ? 'has-error' : '']">
                            <label for="warehouse_name" class="required mandatory">{{ __('marketplace_warehouse::app.shop.warehouse.title') }}</label>
                            <select class="control" v-validate="'required'" id="warehouse_name" name="warehouse_name" data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.title') }}&quot;">
                                <option value="">{{ __('admin::app.select-option') }}</option>
                                
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->warehouse_name }}" >{{ $warehouse->warehouse_name }}</option>
                                @endforeach

                            </select>

                            <span class="control-error" v-if="errors.has('warehouse_name')">@{{ errors.first('warehouse_name') }}</span>
                        </div>

                        <product-search></product-search>
                    </div>
                </accordian>

                {!! view_render_event('marketplace_warehouse.warehouse.create_receipt_and_withdrawal_form_accordian.create.after') !!}

            </div>

            {!! view_render_event('marketplace_warehouse.warehouse.receipt_and_withdrawal.create.after') !!}

        </form>

    </div>

@endsection

@push('scripts')
<script
    type="text/x-template"
    id="product-search-template"
>
<div>
    <div class="control-group">
        <label>
            {{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.search-title') }}
        </label>
        <input
            type="text" 
            class="control"
            placeholder="{{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.enter-search-term') }}" 
            autocomplete="off"
            v-model="search_term"
        >
        <div>
            <ul
                class="autocomplete-results"
                style="list-style-type: none; margin-left:-10px;"
            >
                <li v-for='(product, index) in searched_results' 
                    v-if="(searched_results.length > 0)"
                    style="padding:10px; border-bottom: 1px solid #e8e8e8;
                    cursor: pointer; text-align: left; border-radius: 3px; background-color: #fff;
                    width: 70%; max-height: 200px;"
                    @click="addGroupedProduct(product)">
                    @{{ product.name }}
                </li>
                <li 
                    v-if="(searched_results.length == 0) && (search_term.length > 0)"
                    style="padding:10px; border-bottom: 1px solid #e8e8e8;
                    cursor: pointer; text-align: left; border-radius: 3px; background-color: #fff;
                    width: 70%; max-height: 200px;"
                    >
                    {{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.no-result-found') }}
                </li>
                <li v-if="is_searching"
                    style="padding:10px; border-bottom: 1px solid #e8e8e8;
                    cursor: pointer; text-align: left; border-radius: 3px; background-color: #fff;
                    width: 70%; max-height: 200px;"
                    >
                    {{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.searching') }}
                </li>
            </ul>
        </div>
    </div>
    
    <div class="table-responsive" style="margin-top: 20px;">
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="name">{{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.name') }}</th>
                    <th class="sku">{{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.sku') }}</th>
                    <th class="qty">{{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.qty') }}</th>
                    <th class="actions"></th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for='(product, index) in warehouse_products' 
                    :grouped-product="product" 
                    :key="index" 
                    :index="index" 
                    @onRemoveGroupedProduct="removeGroupedProduct($event)"
                >
                    <td>
                        @{{ product.associated_product.name }}
                        <input
                            type="hidden" 
                            :name="[inputName(product,index) + '[associated_product_id]']" :value="product.associated_product.id"
                        />
                    </td>
                    <td>@{{ product . associated_product . sku }}</td>
                    <td>
                        <div class="control-group" :class="[errors.has(inputName(product) + '[qty]') ? 'has-error' : '']">
                            <input
                                type="number"
                                class="control"
                                :name="[inputName(product,index) + '[qty]']"
                                v-model="product.qty"
                                v-validate="'required|min_value:0'"
                                data-vv-as="&quot;{{ __('admin::app.catalog.products.qty') }}&quot;"
                            />
                            <span
                                class="control-error" 
                                v-if="errors.has(inputName(product) + '[qty]')"
                            >
                                @{{ errors.first(inputName(product) + '[qty]') }}
                            </span>
                        </div>
                    </td>
        
                    <td class="actions text-center">
                        <i class="icon remove-icon" @click="removeGroupedProduct(product)"></i>
                    </td>
                </tr>
            </tbody>

        </table>
    </div>
</div>
</script>
<script>
    Vue.component('product-search', {
        template: '#product-search-template',
        inject: ['$validator'],
        watch: {
            search_term(after, before) {
                this.search();
            }
        },
        data: function() {
            return {
                search_term: '',
                is_searching: false,
                searched_results: [],
                warehouse_products: [],
            }
        },

        methods: {
            inputName: function(product, index) {
                if (product.id) {
                    return 'links[' + product.id + ']';
                }
                
                return 'links[link_' + index + ']';
            },

            addGroupedProduct: function(item, key) {
                var alreadyAdded = false;
                
                this.warehouse_products.forEach(function(product) {
                    if (item.id == product.associated_product.id) {
                        alreadyAdded = true;
                    }
                });

                if (!alreadyAdded) {
                    this.warehouse_products.push({
                        associated_product: item,
                        qty: 0,
                        sort_order: 0
                    });
                }
                
                this.search_term = '';
                this.searched_result = [];
            },

            removeGroupedProduct: function(product) {
                let index = this.warehouse_products.indexOf(product)

                if (index !== -1) {
                    this.warehouse_products.splice(index, 1)
                }
            },

            search: function() {
                this_this = this;
                if (this.search_term.length > 2) {
                    this_this.is_searching = true;
                    this.$http.get("{{ route('shop.marketplace_warehouse.warehouse.search_seller_product') }}", {
                            params: {
                                query: this.search_term
                            }
                        })
                        .then(function(response) {
                            this_this.is_searching = false;
                            this_this.searched_results = response.data.data;
                            
                        })
                        .catch(function(error) {
                            this.is_searching = false;
                        })
                } else {
                    this.searched_results = [];
                    this.is_searching = false;
                }
            }
        }
    });
</script>
@endpush
