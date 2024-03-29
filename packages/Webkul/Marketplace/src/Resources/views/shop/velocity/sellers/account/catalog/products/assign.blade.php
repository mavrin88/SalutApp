@extends('marketplace::shop.layouts.account')

@section('page_title')
    {{ __('marketplace::app.shop.sellers.account.catalog.products.assing-title') }}
@endsection

@section('css')

    <style>
        .variant-image .image-item {
                height: 100px !important;
                width: 100px !important;
                background-size: 100px 100px !important;
        }

        .variant-image .trash-icon {
            position: absolute;
            cursor: pointer;
            margin-top: 25%;
        }

    </style>
@stop

@php
    $allowInventory = ['configurable', 'bundle', 'downloadable', 'booking', 'grouped'];
    $allowPrice = ['configurable', 'bundle', 'grouped'];

    $variantImages = [];

    foreach ($product->variants as $variant) {
        foreach ($variant->images as $image) {
            $variantImages[$variant->id] = $image;
        }
    }
@endphp

@section('content')
    <div class="account-layout right m10">

        <form method="POST" action="" enctype="multipart/form-data" @submit.prevent="onSubmit" class="account-table-content">

            <div class="account-head mb-10">
                <span class="account-heading">
                    {{ __('marketplace::app.shop.sellers.account.catalog.products.assing-title') }}
                </span>

                <div class="account-action">
                    <button type="submit" class="btn btn-primary">
                        {{ __('marketplace::app.shop.sellers.account.catalog.products.save-btn-title') }}
                    </button>
                </div>

                <div class="horizontal-rule"></div>
            </div>

            {!! view_render_event('marketplace.sellers.account.catalog.products.assign.before') !!}

            <div class="account-table-content">

                @csrf()

                <div class="product-information">

                    <div class="product-image">
                        <img src="{{ $baseProduct->base_image_url ?: bagisto_asset('images/product/meduim-product-placeholder.png') }}"/>
                    </div>

                    <div class="product-details">
                        <div class="product-name">
                            <a href="{{ url()->to('/').'/products/'.$baseProduct->url_key }}" title="{{ $baseProduct->name }}">
                                <span>
                                    {{ $baseProduct->name }}
                                </span>
                            </a>
                        </div>

                        @include ('shop::products.price', ['product' => $baseProduct])
                    </div>

                </div>

                <accordian :title="'{{ __('marketplace::app.shop.sellers.account.catalog.products.general') }}'" :active="true">
                    <div slot="header">
                        {{ __('marketplace::app.shop.sellers.account.catalog.products.general') }}
                        <i class="icon expand-icon right"></i>
                    </div>
                    <div slot="body">

                        <div class="control-group" :class="[errors.has('condition') ? 'has-error' : '']">
                            <label for="condition" class="required mandatory">{{ __('marketplace::app.shop.sellers.account.catalog.products.product-condition') }}</label>
                            <select class="control" v-validate="'required'" id="condition" name="condition" data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.account.catalog.products.product-condition') }}&quot;">
                                <option value="new">{{ __('marketplace::app.shop.sellers.account.catalog.products.new') }}</option>
                                <option value="old">{{ __('marketplace::app.shop.sellers.account.catalog.products.old') }}</option>
                            </select>
                            <span class="control-error" v-if="errors.has('type')">@{{ errors.first('type') }}</span>
                        </div>

                        <div class="control-group" :class="[errors.has('price') ? 'has-error' : '']">
                            <label for="price" class="required mandatory">{{ __('marketplace::app.shop.sellers.account.catalog.products.price') }}</label>
                            <input type="text" v-validate="'required|decimal'" class="control" id="price" name="price" value="{{ old('price') }}" data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.account.catalog.products.price') }}&quot;" {{ in_array( $baseProduct->type, $allowPrice ) ? 'disabled' : '' }}/>
                            <span class="control-error" v-if="errors.has('price')">@{{ errors.first('price') }}</span>
                        </div>

                        <div class="control-group form-group" :class="[errors.has('description') ? 'has-error' : '']">
                            <label for="description" class="required mandatory">{{ __('marketplace::app.shop.sellers.account.catalog.products.description') }}</label>
                            <textarea v-validate="'required'" class="control " id="description" name="description" data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.account.catalog.products.description') }}&quot;">{{ old('description') }}</textarea>
                            <span class="control-error" v-if="errors.has('description')">@{{ errors.first('description') }}</span>
                        </div>

                    </div>
                </accordian>

                <accordian :title="'{{ __('marketplace::app.shop.sellers.account.catalog.products.images') }}'" :active="true">
                    <div slot="header">
                        {{ __('marketplace::app.shop.sellers.account.catalog.products.images') }}
                        <i class="icon expand-icon right"></i>
                    </div>
                    <div slot="body">

                        <image-wrapper :button-label="'{{ __('admin::app.catalog.products.add-image-btn-title') }}'" input-name="images"></image-wrapper>

                        <span class="control-error" v-if="{!! $errors->has('images.*') !!}">
                            @php $count=1 @endphp
                            @foreach ($errors->get('images.*') as $key => $message)
                                @php echo $message[0]; @endphp
                            @endforeach
                        </span>

                    </div>
                </accordian>

                @include ('marketplace::shop.sellers.account.catalog.products.accordians.assign-videos')

                @if ( !in_array( $baseProduct->type, $allowInventory ) )
                    <accordian :title="'{{ __('marketplace::app.shop.sellers.account.catalog.products.inventory') }}'" :active="true">
                    <div slot="header">
                        {{ __('marketplace::app.shop.sellers.account.catalog.products.inventory') }}
                        <i class="icon expand-icon right"></i>
                    </div>
                    <div slot="body">

                            @foreach ($inventorySources as $inventorySource)

                                <div class="control-group" :class="[errors.has('inventories[{{ $inventorySource->id }}]') ? 'has-error' : '']">
                                    <label>{{ $inventorySource->name }}</label>

                                    <input type="text" v-validate="'numeric|min:0'" name="inventories[{{ $inventorySource->id }}]" class="control" value="{{ old('inventories[' . $inventorySource->id . ']') }}" data-vv-as="&quot;{{ $inventorySource->name }}&quot;"/>

                                    <span class="control-error" v-if="errors.has('inventories[{{ $inventorySource->id }}]')">@{{ errors.first('inventories[{!! $inventorySource->id !!}]') }}</span>
                                </div>

                            @endforeach

                        </div>
                    </accordian>
                @endif

            </div>

            @if ($baseProduct->type == 'downloadable')
                @include('marketplace::shop.velocity.sellers.account.catalog.products.accordians.downloadable', ['product' => $baseProduct])
            @endif

            @if ($baseProduct->type == 'configurable')
                <accordian :title="'{{ __('marketplace::app.shop.sellers.account.catalog.products.variations') }}'" :active="true">
                    <div slot="header">
                        {{ __('marketplace::app.shop.sellers.account.catalog.products.variations') }}
                        <i class="icon expand-icon right"></i>
                    </div>
                    <div slot="body">

                        <variant-list></variant-list>

                    </div>
                </accordian>
            @endif

            @include ('marketplace::shop.sellers.account.catalog.products.accordians.assign_booking_product_section')

            {!! view_render_event('shop.marketplace.sellers.account.catalog.products.assign.after', ['product' => $product]) !!}

        </form>

    </div>

@endsection

@if ($baseProduct->type == 'configurable')
    @push('scripts')

        <script type="text/x-template" id="variant-list-template">
            <div class="table" style="margin-top: 20px; overflow-x: unset;">
                <table>

                    <thead>
                        <tr>
                            <th class=""></th>

                            <th>{{ __('admin::app.catalog.products.name') }}</th>

                            <th>{{ __('admin::app.catalog.products.images') }}</th>

                            <th class="qty">{{ __('admin::app.catalog.products.qty') }}</th>

                            @foreach ($baseProduct->super_attributes as $attribute)
                                <th class="{{ $attribute->code }}" style="width: 150px">{{ $attribute->admin_name }}</th>
                            @endforeach

                            <th class="price" style="width: 100px;">{{ __('admin::app.catalog.products.price') }}</th>
                        </tr>
                    </thead>

                    <tbody>

                        <variant-item v-for='(variant, index) in variants' :variant="variant" :key="index" :index="index"></variant-item>

                    </tbody>

                </table>
            </div>
        </script>

        <script type="text/x-template" id="variant-item-template">
            <tr>
                <td>
                    <span class="checkbox">
                        <input type="checkbox" :id="variant.id" name="selected_variants[]" :value="variant.id" v-model="selected_variants[variant.id]">
                        <label :for="variant.id" class="checkbox-view"></label>
                    </span>
                </td>

                <td>
                    @{{ variant.name }}
                </td>

                <td>
                    <div class="control-group" :class="[errors.has(variantInputName + '[images][files][' + index + ']') ? 'has-error' : '']">
                        <div v-for='(image, index) in items' class="image-wrapper variant-image">
                            <label class="image-item" v-bind:class="{ 'has-image': imageData[index] }">
                                <input
                                    type="hidden"
                                    :name="[variantInputName + '[images][files][' + image.id + ']']"
                                    v-if="! new_image[index]"/>

                                <input
                                    :ref="'imageInput' + index"
                                    :id="image.id"
                                    type="file"
                                    :name="[variantInputName + '[images][files][' + index + ']']"
                                    accept="image/*"
                                    multiple="multiple"
                                    v-validate="'mimes:image/*'"
                                    @change="addImageView($event, index)"/>

                                <img
                                    class="preview"
                                    :src="imageData[index]"
                                    v-if="imageData[index]">
                            </label>

                            <span class="icon trash-icon" @click="removeImage(image)"></span>
                        </div>

                        <label class="btn btn-lg btn-primary add-image" @click="createFileType">
                            {{ __('admin::app.catalog.products.add-image-btn-title') }}
                        </label>
                    </div>
                </td>

                <td>
                    <button style="width: 100%;" type="button" class="dropdown-btn dropdown-toggle" :disabled="!selected_variants[variant.id]">
                        @{{ totalQty }}
                        <i class="icon arrow-down-icon"></i>
                    </button>

                    <div class="dropdown-list">
                        <div class="dropdown-container">
                            <ul>
                                <li v-for='(inventorySource, index) in inventorySources'>
                                    <div class="control-group" :class="[errors.has(variantInputName + '[inventories][' + inventorySource.id + ']') ? 'has-error' : '']">
                                        <label>@{{ inventorySource.name }}</label>
                                        <input type="text" v-validate="'numeric|min:0'" :name="[variantInputName + '[inventories][' + inventorySource.id + ']']" v-model="inventories[inventorySource.id]" class="control" v-on:keyup="updateTotalQty()" :data-vv-as="'&quot;' + inventorySource.name  + '&quot;'"/>
                                        <span class="control-error" v-if="errors.has(variantInputName + '[inventories][' + inventorySource.id + ']')">@{{ errors.first(variantInputName + '[inventories][' + inventorySource.id + ']') }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </td>

                <td v-for='(attribute, index) in superAttributes'>
                    @{{ optionName(variant[attribute.code]) }}
                </td>

                <td>
                    <div class="control-group" :class="[errors.has(variantInputName + '[price]') ? 'has-error' : '']">
                        <input type="text" v-validate="'required|decimal'" :name="[variantInputName + '[price]']" class="control" data-vv-as="&quot;{{ __('admin::app.catalog.products.price') }}&quot;" value="0" :disabled="!selected_variants[variant.id]"/>
                        <span class="control-error" v-if="errors.has(variantInputName + '[price]')">@{{ errors.first(variantInputName + '[price]') }}</span>
                    </div>
                </td>
            </tr>
        </script>

        <script>
            var super_attributes = @json(app('\Webkul\Marketplace\Repositories\MpProductRepository')->getSuperAttributes($baseProduct));
            var variants = @json($baseProduct->variants);

            Vue.component('variant-list', {

                template: '#variant-list-template',

                inject: ['$validator'],

                data: () => ({
                    variants: variants,
                    superAttributes: super_attributes
                })
            });

            Vue.component('variant-item', {

                template: '#variant-item-template',

                props: ['index', 'variant'],

                inject: ['$validator'],

                data: () => ({
                    inventorySources: @json($inventorySources),
                    inventories: {},
                    totalQty: 0,
                    superAttributes: super_attributes,
                    selected_variants: {},
                    items: [],
                    imageCount: 0,
                    images: {},
                    imageData: [],
                    new_image: [],
                }),

                mounted () {
                    let self = this;

                    self.variant.images.forEach(function(image) {
                        self.items.push(image)
                        self.imageCount++;

                        if (image.id && image.url) {
                            self.imageData.push(image.url);
                        } else if (image.id && image.file) {
                            self.readFile(image.file);
                        }
                    });
                },

                computed: {
                    variantInputName () {
                        return "variants[" + this.variant.id + "]";
                    }
                },

                methods: {
                    optionName (optionId) {
                        var optionName = '';

                        this.superAttributes.forEach (function(attribute) {
                            attribute.options.forEach (function(option) {
                                if (optionId == option.id) {
                                    optionName = option.admin_name;
                                }
                            });
                        })

                        return optionName;
                    },

                    updateTotalQty () {
                        this.totalQty = 0;
                        for (var key in this.inventories) {
                            this.totalQty += parseInt(this.inventories[key]);
                        }
                    },

                    createFileType: function() {
                        let self = this;

                        this.imageCount++;

                        this.items.push({'id': 'image_' + this.imageCount});

                        this.imageData[this.imageData.length] = '';
                    },

                    removeImage (image) {
                        let index = this.items.indexOf(image);

                        Vue.delete(this.items, index);

                        Vue.delete(this.imageData, index);
                    },

                    addImageView: function($event, index) {
                        let ref = "imageInput" + index;
                        let imageInput = this.$refs[ref][0];

                        if (imageInput.files && imageInput.files[0]) {
                            if (imageInput.files[0].type.includes('image/')) {
                                this.readFile(imageInput.files[0], index);

                            } else {
                                imageInput.value = "";

                                alert('Only images (.jpeg, .jpg, .png, ..) are allowed.');
                            }
                        }
                    },

                    readFile: function(image, index) {
                        let reader = new FileReader();

                        reader.onload = (e) => {
                            this.imageData.splice(index, 1, e.target.result);
                        }

                        reader.readAsDataURL(image);

                        this.new_image[index] = 1;
                    },
                }

            });
        </script>
    @endpush
@endif
