@if ($product->product->type == 'bundle')

    <accordian :title="'{{ __('admin::app.catalog.products.bundle-items') }}'" :active="true">
        <div slot="body">
            <bandle-product-header></bandle-product-header>
        </div>
    </accordian>
    @push('scripts')
        <script type="text/x-template" id="bandle-product-header-template">
            <div>
                @{{bundleItemArr}}
                <bandle-product-section
                    v-for='(bundleItem , bundleItemIndex) in bundleItemArr'
                    :bundleitem="bundleItem" 
                    :bundleitemindex="bundleItemIndex"
                    @onRemoveOption="removeOption($event)"
                ></bandle-product-section>
            </div>
        </script>
        
        <script type="text/x-template" id="bandle-product-section-template">
            <div>
                <accordian :active="true">
                    <div slot="header">
                        <i class="icon expand-icon left"></i>
                        <h1 v-if="bundleitem.title">@{{ bundleitem.title }} </h1>
                        <h1 v-else>{{ __('admin::app.catalog.products.new-option') }} </h1>
                        <i class="icon trash-icon"></i>
                    </div>
                    <div slot="body">
                        <div class="control-group">
                            <label class="required">{{ __('marketplace::app.shop.option_title') }}</label>
                            <input type="text" 
                                class="control"
                                v-validate="'required'"
                                :name="bundleitemName(bundleitemindex,'option_title')" 
                                v-model="bundleGroupItemtitle"
                                placeholder="{{ __('marketplace::app.shop.option_title') }}" 
                                autocomplete="off">
                        </div>
                        <div class="control-group">
                            <label class="required">{{ __('marketplace::app.shop.input_type') }}</label>
                            <select id="color" 
                                v-model="bundleitem.input_type"
                                :name="bundleitemName(bundleitemindex,'input_type')" 
                                data-vv-as="&quot;{{ __('marketplace::app.shop.input_type') }}&quot;" 
                                class="control" 
                                aria-required="false" 
                                aria-invalid="false">
                                    <option :value="name"  
                                        v-for="(inputType, name) in inputTypes">@{{ inputType }}</option>
                            </select>
                        </div>
                        <div class="control-group">
                            <label class="required">{{ __('marketplace::app.shop.is_required') }}</label>
                            <select :name="bundleitemName(bundleitemindex,'is_required')"
                                v-model="bundleitem.is_required" 
                                data-vv-as="&quot;{{ __('marketplace::app.shop.is_required') }}&quot;" 
                                class="control" 
                                aria-required="false" 
                                aria-invalid="false">
                                    <option :value="isrequiredName"  
                                        v-for="(isRequired, isrequiredName) in isRequireds">@{{ isRequired }}</option>
                            </select>
                        </div>
                        <div class="control-group">
                            <label class="required">{{ __('marketplace::app.shop.sort_order') }}</label>
                            <input type="text" 
                                class="control"
                                v-model="bundleitem.sort_order" 
                                :name="bundleitemName(bundleitemindex,'sort_order')" 
                                placeholder="{{ __('marketplace::app.shop.sort_order') }}" 
                                autocomplete="off" />
                        </div>
                        <div id="productDiv">
                            <h5>{{ __('marketplace::app.shop.product') }}</h5>
                            <div class="table" style="margin-top: 20px; overflow-x: unset;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="name">{{ __('marketplace::app.shop.is_default') }}</th>
                                            <th class="name">{{ __('marketplace::app.shop.name') }}</th>
                                            <th class="sku">{{ __('marketplace::app.shop.sku') }}</th>
                                            <th class="qty">{{ __('marketplace::app.shop.qty') }}</th>
                                            <th class="sort-order">{{ __('marketplace::app.shop.sort_order') }}</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for='(bundleOption, bundleOptionsIndex) in bundleitem.bundleOptions' 
                                            :bundle-product="bundleOption"
                                            :key="bundleOptionsIndex" 
                                            :index="bundleOptionsIndex" 
                                            >
                                            <td>
                                                <input type="checkbox" 
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
                                                @{{bundleOption.quantity}}
                                            </td>
                                            <td>
                                                @{{bundleOption.sort_order}}
                                            </td>
                                            
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </accordian>
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
                        product_id:"{{$product->product_id}}",
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
                    console.log(this.bundleItemArr)
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
                            'name': 'Radio',
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