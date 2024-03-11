@if ($product->type == 'bundle')
    <bandle-product-header></bandle-product-header>
    @push('scripts')
        <script type="text/x-template" id="bandle-product-header-template">
            <div>
                <accordian :title="'{{ __('marketplace::app.shop.sellers.account.catalog.products.bundle_product') }}'" :active="true">
                    <div slot="header">
                        {{ __('marketplace::app.shop.sellers.account.catalog.products.bundle_product') }}
                        <i class="icon expand-icon right"></i>
                    </div>
                    <div slot="body">
                   
                        <button type="button"
                            v-on:click="addBunbleOption()"  
                            class="btn btn-md btn-primary" 
                            style="margin-bottom: 20px;">
                            {{ __('marketplace::app.shop.sellers.account.catalog.products.add_option') }}
                        </button>
                        <div class="accordian-main">
                            <div id="bundleProductDiv" v-for="(bundleItem , bundleItemIndex) in  bundleItemArr">
                                <div>
                                    <div class="header"  style="cursor:pointer;">
                                        <h5 @click="openBundleTab(bundleItemIndex,bundleItem.display)">@{{ bundleItem.title }}</h5>
                                        <span @click="removeBundleTab($event)"  
                                            style="float:right; cursor:pointer; margin-top:-25px; margin-right:30px;"><i class="icon remove-icon"></i></span>
                                        
                                    </div>
                                    <hr />
                                </div>
                                <div slot="body" v-bind:style="{display:bundleItem.display}" >
                                    <bandle-product-section 
                                        :bundleitem="bundleItem" 
                                        :bundleitemindex="bundleItemIndex"
                                        ></bandle-product-section>
                                </div>
                            </div>
                        </div>
                    </div>
                </accordian>
            </div>
        </script>
        <script type="text/x-template" id="bandle-product-section-template">
            <div>
                <div class="control-group">
                    <label class="required">{{ __('marketplace::app.shop.sellers.account.catalog.products.option_title') }}</label>
                    <input type="text" 
                        class="control"
                        v-validate="'required'"
                        :name="bundleitemName(bundleitemindex,'option_title')" 
                        v-model="bundleGroupItemtitle"
                        placeholder="{{ __('marketplace::app.shop.sellers.account.catalog.products.option_title') }}" 
                        autocomplete="off">
                </div>
                <div class="control-group">
                    <label class="required">{{ __('marketplace::app.shop.sellers.account.catalog.products.input_type') }}</label>
                    <select id="color" 
                        v-model="bundleitem.input_type"
                        :name="bundleitemName(bundleitemindex,'input_type')" 
                        data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.account.catalog.products.input_type') }}&quot;" 
                        class="control" 
                        aria-required="false" 
                        aria-invalid="false">
                            <option :value="name"  
                                v-for="(inputType, name) in inputTypes">@{{ inputType }}</option>
                        </select>
                </div>
                <div class="control-group">
                    <label class="required">{{ __('marketplace::app.shop.sellers.account.catalog.products.is_required') }}</label>
                    <select :name="bundleitemName(bundleitemindex,'is_required')"
                        v-model="bundleitem.is_required" 
                        data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.account.catalog.products.is_required') }}&quot;" 
                        class="control" 
                        aria-required="false" 
                        aria-invalid="false">
                            <option :value="isrequiredName"  
                                v-for="(isRequired, isrequiredName) in isRequireds">@{{ isRequired }}</option>
                    </select>
                </div>
                <div class="control-group">
                    <label class="required">{{ __('marketplace::app.shop.sellers.account.catalog.products.sort_order') }}</label>
                    <input type="text" 
                        class="control"
                        v-model="bundleitem.sort_order" 
                        :name="bundleitemName(bundleitemindex,'sort_order')" 
                        placeholder="{{ __('marketplace::app.shop.sellers.account.catalog.products.sort_order') }}" 
                        autocomplete="off" />
                </div>
                <div id="productDiv">
                    <h5>{{ __('marketplace::app.shop.sellers.account.catalog.products.title') }}</h5>
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
                                    <th class="name">{{ __('marketplace::app.shop.sellers.account.catalog.products.is_default') }}</th>
                                    <th class="name">{{ __('marketplace::app.shop.sellers.account.catalog.products.name') }}</th>
                                    <th class="sku">{{ __('marketplace::app.shop.sellers.account.catalog.products.sku') }}</th>
                                    <th class="qty">{{ __('marketplace::app.shop.sellers.account.catalog.products.qty') }}</th>
                                    <th class="sort-order">{{ __('marketplace::app.shop.sellers.account.catalog.products.sort_order') }}</th>
                                    <th class="actions"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for='(bundleOption, bundleOptionsIndex) in bundleitem.bundleOptions' 
                                    :bundle-product="bundleOption"
                                    :key="bundleOptionsIndex" 
                                    :index="bundleOptionsIndex" 
                                    >
                                    <td>
                                        <input type="radio" 
                                            :name="[inputName(bundleOption,bundleOptionsIndex,bundleitemindex)+'[is_default]']" 
                                            :checked="bundleOption.is_default == 1"
                                            value="1"
                                            />
                                        <input type="hidden" 
                                            :name="[inputName(bundleOption,bundleOptionsIndex,bundleitemindex) + '[associated_product_id]']"
                                            :value="bundleOption.product_associates_id"
                                            />
                                    </td>
                                    <td>@{{bundleOption.product_name}}</td>
                                    <td> @{{bundleOption.sku}}</td>
                                    <td>
                                        <div class="control-group" :class="[errors.has(inputName(bundleOption) + '[quantity]') ? 'has-error' : '']">
                                            <input type="number" 
                                                v-validate="'required|min_value:0'" 
                                                :name="[inputName(bundleOption,bundleOptionsIndex,bundleitemindex) + '[quantity]']" 
                                                v-model="bundleOption.quantity" 
                                                class="control" 
                                                data-vv-as="&quot;{{ __('admin::app.catalog.products.quantity') }}&quot;"/>
                                            <span class="control-error" 
                                                v-if="errors.has(inputName(bundleOption,bundleOptionsIndex,bundleitemindex) + '[quantity]')">
                                                @{{ errors.first(inputName(bundleOption) + '[quantity]') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="control-group" 
                                            :class="[errors.has(inputName(bundleOption,bundleOptionsIndex,bundleitemindex) + '[sort_order]') ? 'has-error' : '']">
                                            <input type="number" 
                                                    v-validate="'required|min_value:0'" 
                                                    :name="[inputName(bundleOption,bundleOptionsIndex,bundleitemindex) + '[sort_order]']" 
                                                    v-model="bundleOption.sort_order" 
                                                    class="control" 
                                                    data-vv-as="&quot;{{ __('admin::app.catalog.products.sort-order') }}&quot;"/>
                                            <span class="control-error" 
                                                v-if="errors.has(inputName(bundleOption) + '[sort_order]')">
                                                @{{ errors.first(inputName(bundleOption)+'[sort_order]') }}</span>
                                        </div>
                                    </td>
                                    <td class="actions">
                                        <i class="icon remove-icon" @click="removeBundleProductoption(bundleOptionsIndex)"></i>
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </script>

        <script>
            /****Parent component**/
            Vue.component('bandle-product-header', {
                template: '#bandle-product-header-template',
                data: function() {
                    return {
                        heading: "Bundle Group",
                        bundleProductDivStatus: false,
                        collapse1: false,
                        isHidden: true,
                        bundleItemArr: [],
                        product_id:"{{$product->id}}",
                    }
                },
                methods: {
                    removeBundleTab: function(bundleProduct) {
                        let index = this.bundleItemArr.indexOf(bundleProduct)
                        this.bundleItemArr.splice(index, 1)
                    },
                    addBunbleOption: function() {
                        this.bundleItemArr.push({
                            'display': 'none',
                            'title': 'New Option',
                            'input_type':'select',
                            'is_required':"YES",
                            'sort_order': 1,
                            'bundleOptions':[]
                        });
                        if (this.isHidden == false) {
                            this.isHidden = true;
                        }
                    },
                    openBundleTab: function(bundleItemIndex, displayAttribute) {
                        if (displayAttribute == "none") {
                            this.bundleItemArr[bundleItemIndex].display = '';
                        } else {
                            this.bundleItemArr[bundleItemIndex].display = 'none';
                        }
                    },
                    getBundleProductItem:function(){
                        this_this = this;
                        
                        this.$http.get("{{ route('shop.marketplace.get_product_bundle') }}", {
                            params: {
                                query: this.product_id
                            }
                        })
                        .then(function(response) {
                            console.log("Bundle Products")
                            if(response.data.status == 200){
                                response.data.res.forEach(function(item){
                                    var productOptions = [];
                                    item.product_option.forEach(function(productOption){
                                        productOptions.push({
                                                    'is_default':productOption.is_default,
                                                    'sku':productOption.sku,
                                                    'product_name':productOption.name,
                                                    'product_associates_id':productOption.product_id,
                                                    'quantity':productOption.qty,
                                                    'sort_order':productOption.sort_order,
                                                });                             
                                    });
                                    this_this.bundleItemArr.push({
                                        'display': '',
                                        'title': item.label,
                                        'input_type':item.type,
                                        'is_required': (item.is_required == 1) ? "YES" : "NO",
                                        'sort_order': item.sort_order,
                                        'bundleOptions':productOptions
                                    });
                                    
                                });
                            }
                        })
                        .catch(function(error) {
                            this.is_searching = false;
                        })
                    }
                },
                mounted() {
                    this.getBundleProductItem();
                }
            });
            /*End*/
            Vue.component('bandle-product-section', {
                template: '#bandle-product-section-template',
                inject: ['$validator'],
                watch: {
                    search_term(after, before){
                       this.search(); 
                    },
                    bundleGroupItemtitle(after, before) {
                        if(this.bundleGroupItemtitle == ''){
                            this.bundleitem.title = 'New Option';
                        }else{
                            this.bundleitem.title = this.bundleGroupItemtitle;
                        }
                    },
                },
                data: function() {
                    return {
                        bundleGroupItemtitle: this.bundleitem.option_value,
                        search_term:'',
                        is_searching: false,
                        bundleProductDiv: false,
                        inputTypes: {
                            'select': 'Select',
                            'radio': 'Radio',
                            'checkbox': 'Checkbox',
                            'multiselect': 'Multiselect'
                        },
                        isRequireds: {
                            'YES': 'Yes',
                            'NO': 'No',
                        },
                        searched_results: [],
                    }
                },
                methods: {
                    radioInputName:function(bundleOption,bundleitemindex){
                        return 'bundle_item['+bundleitemindex+'][bundle_product_option]';
                    },
                    bundleitemName:function(bundleitemindex,name){
                        //bundleitemindex = bundleitemindex + 1;
                        return 'bundle_item['+bundleitemindex+']['+name+']';
                    },
                    inputName: function(groupedProduct, index,bundleitemindex) {
                        if (groupedProduct.id)
                            return 'bundle_item['+bundleitemindex+'][bundle_product_option][' + groupedProduct.id + ']';
                        else
                            return 'bundle_item['+bundleitemindex+'][bundle_product_option]['+index+']';
                    },
                    addGroupedProduct: function(item, key) {
                        this.bundleitem.bundleOptions.push({
                                                'is_default':0,
                                                'sku':item.sku,
                                                'product_name':item.name,
                                                'product_associates_id':item.id,
                                                'quantity':0,
                                                'sort_order':0,
                                            });
                        this.search_term = '';
                        this.searched_result = []; 
                    },
                    removeBundleProductoption: function(bundleOption) {
                        this.bundleitem.bundleOptions.splice(bundleOption, 1) 
                    },
                    search: function() {
                        this_this = this;
                        if (this.search_term.length > 3) {
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
                        } else {
                            this.searched_results = [];
                            this.is_searching = false;
                        }
                    },
                },
                props: ['bundleitem','bundleitemindex'],
                mounted() {
                    this.bundleGroupItemtitle = this.bundleitem.title;
                },
            });
        </script>
    @endpush
@endif
