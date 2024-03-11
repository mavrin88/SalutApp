@extends('marketplace::shop.layouts.master')

@php
$productFlags = app('Webkul\Marketplace\Repositories\ProductFlagReasonRepository')->findWhere(['status' => 1]);
@endphp

@section('page_title')
    {{ trim($product->meta_title) != "" ? $product->meta_title : $product->name }}
@stop

@section('seo')
    <meta name="description" content="{{ trim($product->meta_description) != "" ? $product->meta_description : Illuminate\Support\Str::limit(strip_tags($product->description), 120, '') }}"/>
    <meta name="keywords" content="{{ $product->meta_keywords }}"/>
@stop

@section('content-wrapper')

    <?php
        $baseProduct = $product->parent_id ? $product->parent : $product;
        $productRepository = app('Webkul\Marketplace\Repositories\ProductRepository');
    ?>

    {!! view_render_event('bagisto.shop.sellers.products.offers.before', ['product' => $product]) !!}

    {!! view_render_event('bagisto.shop.products.view.before', ['product' => $product]) !!}

    <div class="product-offer-container">

        <div class="product">
            <div class="product-information">

                <?php $productBaseImage = product_image()->getProductBaseImage($product); ?>

                <div class="product-logo-block">
                    <a href="{{ route('shop.productOrCategory.index', $baseProduct->url_key) }}" title="{{ $baseProduct->name }}">
                        <img src="{{ $productBaseImage['medium_image_url'] }}" />
                    </a>
                </div>

                <div class="product-information-block">
                    <a href="{{ route('shop.productOrCategory.index', $baseProduct->url_key) }}" class="product-title">
                        {{ $baseProduct->name }}
                    </a>

                    <div class="price">
                        @include ('shop::products.price', ['product' => $product])
                    </div>

                    @include ('shop::products.view.stock', ['product' => $product])

                    <?php $attributes = []; ?>

                    @if ($baseProduct->type == 'configurable')

                        <div class="options">
                            <?php $options = []; ?>

                            @foreach ($baseProduct->super_attributes as $attribute)

                                @foreach ($attribute->options as $option)

                                    @if ($product->{$attribute->code} == $option->id)

                                        <?php $attributes[$attribute->id] = $option->id; ?>

                                        <?php array_push($options, $attribute->name . ' : ' . $option->label); ?>

                                    @endif

                                @endforeach

                            @endforeach

                            {{ implode(', ', $options) }}

                        </div>

                    @endif

                </div>
            </div>

            <div class="review-information">

                @include ('shop::products.review', ['product' => $baseProduct])

            </div>
        </div>

        <div class="seller-product-list padding-15">
            <h2 class="heading">{{ __('marketplace::app.shop.products.more-sellers') }}</h2>

            <div class="content">

                @foreach ($productRepository->getSellerProducts($product) as $sellerProduct)
                    <form action="{{ route('marketplace.cart.add', $baseProduct->id) }}" method="POST" >
                        @csrf()
                        <input type="hidden" name="product_id" value="{{ $sellerProduct->id }}">
                        <input type="hidden" name="seller_info[product_id]" value="{{ $sellerProduct->id }}">
                        <input type="hidden" name="seller_info[seller_id]" value="{{ $sellerProduct->seller->id }}">
                        <input type="hidden" name="seller_info[is_owner]" value="0">

                        @if ($baseProduct->type == 'configurable')
                            <input type="hidden" name="selected_configurable_option" value="{{ $product->id }}">

                            @foreach ($attributes as $attributeId => $optionId)
                                <input type="hidden" name="super_attribute[{{$attributeId}}]" value="{{$optionId}}"/>
                            @endforeach
                        @endif

                        <div class="seller-product-item">

                            <div class="product-info-top table">

                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="profile-logo-block">
                                                    @if ($logo = $sellerProduct->seller->logo_url)
                                                        <img src="{{ $logo }}" />
                                                    @else
                                                        <img src="{{ bagisto_asset('vendor/webkul/marketplace/assets/images/default-logo.svg') }}" />
                                                    @endif
                                                </div>

                                                <div class="profile-information-block">
                                                    <div class="seller-store-name">
                                                        <a href="{{route('marketplace.seller.show', $sellerProduct->seller->url)}}">{{$sellerProduct->seller->shop_title}}</a>
                                                    </div>
                                                    <div class="report-flag">
                                                        <a href="javascript:void(0);"  @click="showModal('reportFlag')">
                                                           
                                                            {{core()->getConfigData('marketplace.settings.product_flag.text')}}
                                                        </a>
                                                    </div>

                                                    <div class="review-information">

                                                        <?php $reviewRepository = app('Webkul\Marketplace\Repositories\ReviewRepository') ?>

                                                        <span class="stars">
                                                            <span class="icon star-icon"></span>

                                                            {{
                                                                __('marketplace::app.shop.products.seller-total-rating', [
                                                                        'avg_rating' => $reviewRepository->getAverageRating($sellerProduct->seller),
                                                                        'total_rating' => $reviewRepository->getTotalRating($sellerProduct->seller),
                                                                    ])
                                                            }}
                                                        </span>

                                                    </div>
                                                    
                                                </div>
                                            </td>

                                            <td>
                                                @if ($sellerProduct->condition == 'new')
                                                    {{ __('marketplace::app.shop.products.new') }}
                                                @else
                                                    {{ __('marketplace::app.shop.products.used') }}
                                                @endif
                                            </td>

                                            <td>
                                                <div class="product-price">

                                                @if ($sellerProduct->is_owner)
                                                    @if ($product->getTypeInstance()->haveSpecialPrice($sellerProduct))
                                                        <div class="sticker sale">
                                                            {{ __('shop::app.products.sale') }}
                                                        </div>

                                                        <span class="regular-price">{{ core()->currency($sellerProduct->price) }}</span>

                                                        <span class="special-price">{{ core()->currency($product->getTypeInstance()->getSpecialPrice($sellerProduct)) }}</span>
                                                    @else
                                                        <span>{{ core()->currency($sellerProduct->price) }}</span>
                                                    @endif
                                                @else
                                                    <span>{{ core()->currency($sellerProduct->price) }}</span>
                                                @endif
                                                </div>
                                            </td>

                                            <td>
                                                <div class="control-group">
                                                    <input type="text" name="quantity" value="1" class="control">
                                                </div>

                                                @if ($sellerProduct->product->type == 'simple' || $sellerProduct->product->type == 'configurable')
                                                    @if ($sellerProduct->haveSufficientQuantity(1))

                                                        <button type="submit" class="btn btn-black btn-md">
                                                            {{ __('marketplace::app.shop.products.add-to-cart') }}
                                                        </button>
                                                    @else

                                                        <div class="stock-status">
                                                            {{ __('marketplace::app.shop.products.out-of-stock') }}
                                                        </div>

                                                    @endif
                                                @else
                                                    @if ($sellerProduct->isSaleable())
                                                        <button type="submit" class="btn btn-black btn-md">
                                                            {{ __('marketplace::app.shop.products.add-to-cart') }}
                                                        </button>
                                                    @else
                                                        <div class="stock-status">
                                                            {{ __('marketplace::app.shop.products.out-of-stock') }}
                                                        </div>
                                                    @endif

                                                @endif

                                            </td>
                                        </tr>
                                        <tr>
                                            @if ($sellerProduct->product->type == 'downloadable')
                                                @include('marketplace::shop.default.products.downloadable', ['product' => $sellerProduct, 'baseProduct' => $sellerProduct->product])
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                            <div class="product-info-bottom">
                            <?php $baseSellerProduct = $sellerProduct->parent_id ? $sellerProduct->parent : $sellerProduct; ?>
                                <div class="product">
                                    <div class="product-information">

                                    @php
                                        $productImages = product_image()->getGalleryImages($baseSellerProduct); 
                                    @endphp
                                        <div class="row col-12">
                                        <ul class="thumb-list col-12 row ltr" type="none">
                                            @if (sizeof($productImages) > 4)
                                                <li class="arrow left" @click="scroll('prev')">
                                                    <i class="rango-arrow-left fs24"></i>
                                                </li>
                                            @endif
                                            <carousel-component
                                                slides-per-page="4"
                                                pagination-enabled="hide"
                                                navigation-enabled="hide"
                                                add-class="product-gallary"
                                                :slides-count="{{ sizeof($productImages) }}">
                                                @foreach ($productImages as $index => $thumb)
                                                    <slide :slot="`slide-0`">
                                                        <img style="padding: 0px 67px 0px 10px;" src="{{$thumb['small_image_url']}}" />
                                                    </slide>
                                                @endforeach
                                            </carousel-component>

                                            @if (sizeof($productImages) > 4)
                                                <li class="arrow right" @click="scroll('next')">
                                                    <i class="rango-arrow-right fs24"></i>
                                                </li>
                                            @endif
                                        </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="product-info-bottom">
                                <?php $baseSellerProduct = $sellerProduct->parent_id ? $sellerProduct->parent : $sellerProduct; ?>
                                <div class="product">
                                    <div class="product-information">

                                        @php
                                            $productVideos = $baseSellerProduct->assignVideos;
                                            $videoData = [];
                                            foreach ($productVideos as $key => $video) {
                                                $videoData[$key]['type'] = $video->type;
                                                $videoData[$key]['large_image_url'] = $videoData[$key]['small_image_url']= $videoData[$key]['medium_image_url']= $videoData[$key]['original_image_url'] = $video->path;
                                            }
                                        @endphp

                                        <div class="row col-12">
                                            <product-video></product-video>
                                        </div>

                                        <div class="product-information-block">

                                            {{ $baseSellerProduct->description }}

                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </form>
                @endforeach

            </div>

        </div>

    </div>

    <modal id="reportFlag" :is-open="modalIds.reportFlag">
        <h3 slot="header">
            {{ __('marketplace::app.shop.flag.title') }}
        </h3>

        <div slot="body">
            <product-flag-form></product-flag-form>
        </div>
    </modal>

    {!! view_render_event('bagisto.shop.sellers.products.offers.after', ['product' => $product]) !!}

@endsection

<script type="text/x-template" id="product-gallery-template">
    <ul class="thumb-list col-12 row ltr" type="none">
        @if (sizeof($productImages) > 4)
            <li class="arrow left" @click="scroll('prev')">
                <i class="rango-arrow-left fs24"></i>
            </li>
        @endif
        <carousel-component
            slides-per-page="4"
            :id="galleryCarouselId"
            pagination-enabled="hide"
            navigation-enabled="hide"
            add-class="product-gallary"
            :slides-count="{{ sizeof($productImages) }}">
            @foreach ($productImages as $index => $thumb)
                <slide :slot="`slide-0`">
                    <img style="padding: 0px 67px 0px 10px;" src="{{$thumb['small_image_url']}}" />
                </slide>
            @endforeach
        </carousel-component>

        @if (sizeof($productImages) > 4)
            <li class="arrow right" @click="scroll('next')">
                <i class="rango-arrow-right fs24"></i>
            </li>
        @endif
    </ul>
</script>

<script type="text/x-template" id="product-video-template">
    <ul class="thumb-list col-12 row ltr" type="none">
        <carousel-component
            slides-per-page="4"
            :id="galleryCarouselId"
            pagination-enabled="hide"
            navigation-enabled="hide"
            add-class="product-gallary"
            :slides-count="{{ sizeof($videoData) }}">

            @foreach ($videoData as $index => $thumb)
                <slide :slot="`slide-0`">
                    <video v-if="{{ $thumb['type'] == 'video'}}" width='200' height='112'
                    style = "border: 1px solid #c4c;" controls>
                        <source src="{{bagisto_asset('storage/' . $thumb['small_image_url'])}}" type="video/mp4">
                        {{ __('admin::app.catalog.products.not-support-video') }}
                    </video>
                </slide>
            @endforeach
        </carousel-component>
    </ul>
</script>

@push('scripts')

    <script type="text/x-template" id="flag-form-template">
        <form method="POST"  action="{{route('marketplace.flag.product.store')}}"   v-on:submit.prevent="onSubmit">
            @csrf()

            <input type="hidden" name="product_id" value="{{ $product->product->id }}">
            <input type="hidden" name="seller_id" class="append_seller_id" value="">

            <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']">
                <label for="name" class="required label-style">{{ __('marketplace::app.shop.flag.name') }}</label>
                <input v-validate="'required'" type="text" class="form-style" id="name" name="name" data-vv-as="&quot;{{ __('marketplace::app.shop.flag.name') }}&quot;" value="{{ old('name') }}"/>
                <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>
            </div>

            <div class="control-group" :class="[errors.has('email') ? 'has-error' : '']">
                <label for="type" class="required label-style">{{ __('marketplace::app.shop.flag.email') }}</label>
               <input type="email" v-validate="'required'" class="form-style" id="email" name="email" data-vv-as="&quot;{{ __('marketplace::app.shop.flag.email') }}&quot;" value="{{ old('email') }}" />
                <span class="control-error" v-if="errors.has('email')">@{{ errors.first('email') }}</span>
            </div>

            <div class="control-group" :class="[errors.has('reason') ? 'has-error' : '']">
                <label for="reason" class="label-style">{{ __('marketplace::app.shop.flag.reason') }}</label>

                <select name="reason" id="reason" v-model="reason" class="form-style" >
                    @foreach ($productFlags as $flag)
                        <option value="{{$flag->reason}}">{{$flag->reason}}</option>
                    @endforeach
                    <option value="other">Other</option>
                </select>
                @if (core()->getConfigData('marketplace.settings.product_flag.other_reason'))
                <textarea class="form-style mt-3" v-validate="'required'" id="other-reason" v-if="reason == 'other'" placeholder="{{core()->getConfigData('marketplace.settings.product_flag.other_placeholder')}}" name="reason" data-vv-as="&quot;{{ __('marketplace::app.shop.flag.reason') }}&quot;" value="{{ old('reason') }}"
                ></textarea>
                @endif
                <span class="control-error" v-if="errors.has('reason')">@{{ errors.first('reason') }}</span>
            </div>

            <div class="mt-5">
                @if (core()->getConfigData('marketplace.settings.product_flag.guest_can'))
                <button type="submit" class="btn btn-lg btn-primary theme-btn">
                    {{ __('marketplace::app.shop.flag.submit') }}
                </button>
                @else
                    <a href="{{route('shop.customer.session.index')}}" class="btn btn-lg btn-primary theme-btn"> {{ __('marketplace::app.shop.flag.submit') }}</a>
                @endif
            </div>

        </form>
    </script>
    
    <script type="text/javascript">
        (() => {
            var galleryImages = @json($productImages);
            var galleryVideo = @json($videoData);
            
            $('body').on('click','.seller_report',function(){
                var seller_id = $(this).attr('seller_id');
                $('.append_seller_id').val(seller_id);
            });

            Vue.component('product-gallery', {
                template: '#product-gallery-template',
                data: function() {
                    return {
                        images: galleryImages,
                        thumbs: [],
                        galleryCarouselId: 'product-gallery-carousel',
                        currentLargeImageUrl: '',
                        currentOriginalImageUrl: '',
                        counter: {
                            up: 0,
                            down: 0,
                        }
                    }
                },

                watch: {
                    'images': function(newVal, oldVal) {
                        this.changeImage({
                            largeImageUrl: this.images[0]['large_image_url'],
                            originalImageUrl: this.images[0]['original_image_url'],
                        })

                        this.prepareThumbs()
                    }
                },

                created: function() {
                    this.changeImage({
                        largeImageUrl: this.images[0]['large_image_url'],
                        originalImageUrl: this.images[0]['original_image_url'],
                    })

                    this.prepareThumbs()
                },

                methods: {
                    prepareThumbs: function() {
                        this.thumbs = [];

                        this.images.forEach(image => {
                            this.thumbs.push(image);
                        });
                    },

                    changeImage: function({largeImageUrl, originalImageUrl}) {
                        this.currentLargeImageUrl = largeImageUrl;

                        this.currentOriginalImageUrl = originalImageUrl;

                        this.$root.$emit('changeMagnifiedImage', {
                            smallImageUrl: this.currentOriginalImageUrl
                        });

                        let productImage = $('.vc-small-product-image');
                        if (productImage && productImage[0]) {
                            productImage = productImage[0];

                            productImage.src = this.currentOriginalImageUrl;
                        }
                    },

                    scroll: function (navigateTo) {
                        let navigation = $(`#${this.galleryCarouselId} .VueCarousel-navigation .VueCarousel-navigation-${navigateTo}`);

                        if (navigation && (navigation = navigation[0])) {
                            navigation.click();
                        }
                    },
                }
            });

            Vue.component('product-video', {
                template: '#product-video-template',
                data: function() {
                    return {
                        images: galleryImages,
                        thumbs: [],
                        galleryCarouselId: 'product-gallery-carousel',
                        currentLargeImageUrl: '',
                        currentOriginalImageUrl: '',
                        counter: {
                            up: 0,
                            down: 0,
                        }
                    }
                },

                watch: {
                    'images': function(newVal, oldVal) {
                        this.changeImage({
                            largeImageUrl: this.images[0]['large_image_url'],
                            originalImageUrl: this.images[0]['original_image_url'],
                        })

                        this.prepareThumbs()
                    }
                },

                created: function() {
                    this.changeImage({
                        largeImageUrl: this.images[0]['large_image_url'],
                        originalImageUrl: this.images[0]['original_image_url'],
                    })

                    this.prepareThumbs()
                },

                methods: {
                    prepareThumbs: function() {
                        this.thumbs = [];

                        this.images.forEach(image => {
                            this.thumbs.push(image);
                        });
                    },

                    changeImage: function({largeImageUrl, originalImageUrl}) {
                        this.currentLargeImageUrl = largeImageUrl;

                        this.currentOriginalImageUrl = originalImageUrl;

                        this.$root.$emit('changeMagnifiedImage', {
                            smallImageUrl: this.currentOriginalImageUrl
                        });

                        let productImage = $('.vc-small-product-image');
                        if (productImage && productImage[0]) {
                            productImage = productImage[0];

                            productImage.src = this.currentOriginalImageUrl;
                        }
                    },

                    scroll: function (navigateTo) {
                        let navigation = $(`#${this.galleryCarouselId} .VueCarousel-navigation .VueCarousel-navigation-${navigateTo}`);

                        if (navigation && (navigation = navigation[0])) {
                            navigation.click();
                        }
                    },
                }
            });
        })()
    </script>
    <script>

        Vue.component('product-flag-form', {

        data: () => ({
            reason: ''
        }),

        template: '#flag-form-template',
        });
    </script>
@endpush
