@extends('shop::customers.account.index')

@section('page_title')
    {{ __('marketplace_warehouse::app.shop.warehouse.price.edit-title') }}
@endsection

@section('page-detail-wrapper')

    <div class="account-layout">

        <form method="POST" action="{{ route('marketplace_warehouse.user.warehouse.price.update', $warehousePriceType->id) }}" class="account-table-content">

            <div class="account-head mb-10">
                <span class="account-heading">
                    {{ __('marketplace_warehouse::app.shop.warehouse.price.title') }}
                </span>

                <div class="account-action">
                    <button type="submit" class="btn btn-primary">
                        {{ __('marketplace_warehouse::app.shop.warehouse.price.save-title') }}
                    </button>
                </div>

                <div class="horizontal-rule"></div>
            </div>

            {!! view_render_event('marketplace_warehouse.warehouse.price.edit.before', ['warehousePriceType' => $warehousePriceType]) !!}

            <div class="account-table-content">

                @csrf()

                {!! view_render_event('marketplace_warehouse.warehouse.edit_warehouse_price_form_accordion.general.before', ['warehousePriceType' => $warehousePriceType]) !!}

                <accordion :title="'{{ __('marketplace_warehouse::app.shop.warehouse.general') }}'" :active="true">
                    <div slot="header">
                        {{ __('marketplace_warehouse::app.shop.warehouse.general') }}
                        <i class="icon expand-icon right"></i>
                    </div> 
                
                    <div slot="body">

                        <div class="control-group" :class="[errors.has('price_title') ? 'has-error' : '']">
                            <label for="price_title" class="required">{{ __('marketplace_warehouse::app.shop.warehouse.price.price-title') }}</label>
                            <input type="text" class="control" name="price_title" v-validate="'required|max:200'" data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.price.price-title') }}&quot;" value="{{ $warehousePriceType->title }}"/>
                            <span class="control-error" v-if="errors.has('price_title')">@{{ errors.first('price_title') }}</span>
                        </div>

                        <product-search></product-search>

                    </div>
                </accordion>

                {!! view_render_event('marketplace_warehouse.warehouse.edit_warehouse_price_form_accordion.general.after', ['warehousePriceType' => $warehousePriceType]) !!}

            </div>

            {!! view_render_event('marketplace_warehouse.warehouse.price.edit.after', ['warehousePriceType' => $warehousePriceType]) !!}

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
                    @click="addProduct(product)">
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
                    <th class="Price">{{ __('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.price') }}</th>
                    <th class="actions"></th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for='(product, index) in warehouse_products' 
                    :grouped-product="product" 
                    :key="index" 
                    :index="index" 
                    @onRemoveProduct="removeProduct($event)"
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
                                :name="[inputName(product,index) + '[price]']"
                                :value="product.associated_product.price"
                                />
                        </div>
                    </td>
        
                    <td class="actions text-center">
                        <i class="icon remove-icon" @click="removeProduct()"></i>
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
                warehouse_products: []
            }
        },

        created() {
            console.log(@json($productGroupedProduct));
            this.warehouse_products = this.transformData(@json($productGroupedProduct));// Log the value of warehouse_products to the console
        },

        methods: {
            inputName: function(product, index) {
                if (product.id) {
                    return 'links[' + product.id + ']';
                }
                
                return 'links[link_' + index + ']';
            },

            transformData(data) {
                return data.map(product => {
                    return {
                        associated_product: {
                            id: product.id,
                            name: product.name,
                            sku: product.sku,
                            price: product.productPrice,
                        }
                    };
                });
            },

            addProduct: function(item, key) {
                var alreadyAdded = false;
                
                this.warehouse_products.forEach(function(product) {
                    if (item.id == product.associated_product.id) {
                        alreadyAdded = true;
                    }
                });

                if (! alreadyAdded) {
                    this.warehouse_products.push({
                        associated_product: item,
                        qty: 0,
                        sort_order: 0
                    });
                }
                
                this.search_term = '';
                this.searched_result = [];
            },

            removeProduct: function(product) {
                let index = this.warehouse_products.indexOf(product)
                this.warehouse_products.splice(index, 1)
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