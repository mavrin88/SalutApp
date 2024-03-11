@if($product->type == "grouped")

<accordian :title="'{{ __('marketplace::app.shop.sellers.account.catalog.products.grouped_product') }}'" :active="false">
    <div slot="header">
        {{ __('marketplace::app.shop.sellers.account.catalog.products.grouped_product') }}
        <i class="icon expand-icon right"></i>
    </div>
    <div slot="body">
        <grouped-product-section></grouped-product-section>
    </div>
</accordian>
    <style>
        .table th.price, .table th.weight {
            width: 100px;
        }
        .table th.actions {
            width: 85px;
        }
        .table td.actions .icon {
            margin-top: 8px;
        }
        .table td.actions .icon.pencil-lg-icon {
            margin-right: 10px;
        }
    </style>

@push('scripts')
    <script type="text/x-template" id="grouped-product-section-template">
        <div>
                <div class="control-group">
                    <label>{{ __('marketplace::app.shop.sellers.account.catalog.products.search-title') }}</label>
                    <input type="text" 
                        class="control" 
                        v-model="search_term"
                        placeholder="{{ __('marketplace::app.shop.sellers.account.catalog.products.enter-search-term') }}" 
                        autocomplete="off">
                    <div>
                        <ul class="autocomplete-results" style="list-style-type: none; margin-left:-10px;">
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
                                {{ __('marketplace::app.shop.sellers.account.catalog.products.no-result-found') }}
                            </li>
                            <li v-if="is_searching"
                                style="padding:10px; border-bottom: 1px solid #e8e8e8;
                                cursor: pointer; text-align: left; border-radius: 3px; background-color: #fff;
                                width: 70%; max-height: 200px;"
                                >
                                {{ __('marketplace::app.shop.sellers.account.catalog.products.searching') }}
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="table" style="margin-top: 20px; overflow-x: unset;">
                    <table>
                        <thead>
                            <tr>
                                <th class="name">{{ __('marketplace::app.shop.sellers.account.catalog.products.name') }}</th>
                                <th class="sku">{{ __('marketplace::app.shop.sellers.account.catalog.products.sku') }}</th>
                                <th class="qty">{{ __('marketplace::app.shop.sellers.account.catalog.products.qty') }}</th>
                                <th class="sort-order">{{ __('marketplace::app.shop.sellers.account.catalog.products.sort_order') }}</th>
                                <th class="actions"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for='(groupedProduct, index) in grouped_products' 
                                :grouped-product="groupedProduct" 
                                :key="index" 
                                :index="index" 
                                @onRemoveGroupedProduct="removeGroupedProduct($event)">
                                <td>
                                    @{{ groupedProduct.associated_product.name }}
                                    <input type="hidden" 
                                        :name="[inputName(groupedProduct,index) + '[associated_product_id]']" :value="groupedProduct.associated_product.id"/>
                                </td>
                                <td>@{{ groupedProduct . associated_product . sku }}</td>
                                <td>
                                    <div class="control-group" :class="[errors.has(inputName(groupedProduct) + '[qty]') ? 'has-error' : '']">
                                        <input type="number" 
                                            v-validate="'required|min_value:0'" 
                                            :name="[inputName(groupedProduct,index) + '[qty]']" 
                                            v-model="groupedProduct.qty" 
                                            class="control" 
                                            data-vv-as="&quot;{{ __('admin::app.catalog.products.qty') }}&quot;"/>
                                        <span class="control-error" 
                                            v-if="errors.has(inputName(groupedProduct) + '[qty]')">
                                            @{{ errors.first(inputName(groupedProduct) + '[qty]') }}</span>
                                    </div>
                                </td>
                    
                                <td>
                                    <div class="control-group" 
                                        :class="[errors.has(inputName(groupedProduct,index) + '[sort_order]') ? 'has-error' : '']">
                                        <input type="number" 
                                                v-validate="'required|min_value:0'" 
                                                :name="[inputName(groupedProduct,index) + '[sort_order]']" 
                                                v-model="groupedProduct.sort_order" 
                                                class="control" 
                                                data-vv-as="&quot;{{ __('admin::app.catalog.products.sort-order') }}&quot;"/>
                                        <span class="control-error" 
                                            v-if="errors.has(inputName(groupedProduct) + '[sort_order]')">
                                            @{{ errors.first(inputName(groupedProduct) + '[sort_order]') }}</span>
                                    </div>
                                </td>
                                <td class="actions">
                                    <i class="icon remove-icon" @click="removeGroupedProduct()"></i>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </script>
    <script>
        Vue.component('grouped-product-section', {
            template: '#grouped-product-section-template',
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
                    //searched_results: [{'name':'product 1'}, {'name':'product 1'}],
                    searched_results: [],
                    grouped_products: @json($product->grouped_products()->with('associated_product')->get())
                }
            },
            
            methods: {
                inputName: function (groupedProduct,index) {
                    if (groupedProduct.id)
                        return 'links[' + groupedProduct.id + ']';
                    else
                    return 'links[link_'+index+']';
                },

                addGroupedProduct: function(item, key) {
                    var alreadyAdded = false;
                    this.grouped_products.forEach(function(groupProduct) {
                        if (item.id == groupProduct.associated_product.id) {
                            alreadyAdded = true;
                        }
                    });
                    if (!alreadyAdded) {
                        this.grouped_products.push({
                            associated_product: item,
                            qty: 0,
                            sort_order: 0
                        });
                    }
                    this.search_term = '';
                    this.searched_result = [];
                },

                removeGroupedProduct: function(groupedProduct) {
                    let index = this.grouped_products.indexOf(groupedProduct)
                    this.grouped_products.splice(index, 1)
                },

                search: function() {
                    this_this = this;
                    if (this.search_term.length > 2) {
                        this_this.is_searching = true;
                        this.$http.get("{{ route('shop.marketplace.search_product') }}", {
                            params: {
                                query: this.search_term
                            }
                        })
                        .then(function(response) {
                            
                            this_this.is_searching = false; 
                            this_this.searched_results = response.data;
                        })
                        .catch(function(error) {
                            this.is_searching = false;
                        })
                    }else{
                        this.searched_results = [];
                        this.is_searching = false;
                    }
                }
            }
        });
    </script>
@endpush
@endif
