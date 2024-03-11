@extends('admin::layouts.content')

@section('page_title')
    {{ __('marketplace::app.admin.sellers.add-title') }}
@stop

@section('content')
    <div class="content">
        <form method="post" action="{{ route('marketplace.admin.seller.update', $seller->id) }}" @submit.prevent="onSubmit" enctype="multipart/form-data">
            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ route('admin.dashboard.index') }}';"></i>

                        {{ __('marketplace::app.admin.sellers.add-title') }}

                        {{ Config::get('carrier.social.facebook.url') }}
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('marketplace::app.admin.sellers.save-btn-title') }}
                    </button>
                </div>
            </div>

            {!! view_render_event('marketplace.sellers.account.profile.edit.before', ['seller' => $seller]) !!}

            <div class="account-table-content">

                @csrf()

                <input type="hidden" name="_method" value="PUT">

                <accordian :title="'{{ __('marketplace::app.shop.sellers.account.profile.general') }}'" :active="true">
                    <div slot="body">

                        <div class="control-group" :class="[errors.has('shop_title') ? 'has-error' : '']">
                            <label for="shop_title" class="required">{{ __('marketplace::app.shop.sellers.account.profile.shop_title') }}</label>
                            <input type="text" class="control" name="shop_title" v-validate="'required'" data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.account.profile.shop_title') }}&quot;" value="{{ old('shop_title') ?: $seller->shop_title }}">
                            <span class="control-error" v-if="errors.has('shop_title')">@{{ errors.first('shop_title') }}</span>
                        </div>

                        <div class="control-group" :class="[errors.has('url') ? 'has-error' : '']">
                            <label for="url" class="required">{{ __('marketplace::app.shop.sellers.account.profile.url') }}</label>
                            <input type="text" class="control" name="url" v-validate="'required'" data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.account.profile.url') }}&quot;" value="{{ old('url') ?: $seller->url }}">
                            <span class="control-error" v-if="errors.has('url')">@{{ errors.first('url') }}</span>
                        </div>

                        <div class="control-group">
                            <label for="tax_vat">{{ __('marketplace::app.shop.sellers.account.profile.tax_vat') }}</label>
                            <input type="text" class="control" name="tax_vat" value="{{ old('tax_vat') ?: $seller->tax_vat }}">
                        </div>

                        <div class="control-group" :class="[errors.has('phone') ? 'has-error' : '']">
                            <label for="phone" class="required">{{ __('marketplace::app.shop.sellers.account.profile.phone') }}</label>
                            <input type="text"  class="control" name="phone" v-validate="'required|integer'" data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.account.profile.phone') }}&quot;" value="{{ old('phone') ?: $seller->phone }}">
                            <span class="control-error" v-if="errors.has('phone')">@{{ errors.first('phone') }}</span>
                        </div>

                        <div class="control-group" :class="[errors.has('address1') ? 'has-error' : '']">
                            <label for="address1" class="required">{{ __('marketplace::app.shop.sellers.account.profile.address1') }}</label>
                            <input type="text" class="control" name="address1" v-validate="'required'" data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.account.profile.address1') }}&quot;" value="{{ old('address1') ?: $seller->address1 }}">
                            <span class="control-error" v-if="errors.has('address1')">@{{ errors.first('address1') }}</span>
                        </div>

                        <div class="control-group">
                            <label for="address2">{{ __('marketplace::app.shop.sellers.account.profile.address2') }}</label>
                            <input type="text" class="control" name="address2" value="{{ old('address2') ?: $seller->address2 }}">
                        </div>

                        <div class="control-group" :class="[errors.has('city') ? 'has-error' : '']">
                            <label for="city" class="required">{{ __('marketplace::app.shop.sellers.account.profile.city') }}</label>
                            <input type="text" class="control" name="city" v-validate="'required'" data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.account.profile.city') }}&quot;" value="{{ old('city') ?: $seller->city }}">
                            <span class="control-error" v-if="errors.has('city')">@{{ errors.first('city') }}</span>
                        </div>

                        @include ('admin::customers.country-state', ['countryCode' => old('country') ?? $seller->country, 'stateCode' => old('state') ?? $seller->state])

                        <div class="control-group" :class="[errors.has('postcode') ? 'has-error' : '']">
                            <label for="postcode" class="required">{{ __('marketplace::app.shop.sellers.account.profile.postcode') }}</label>
                            <input type="text" class="control" name="postcode" v-validate="'required'" data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.account.profile.postcode') }}&quot;" value="{{ old('postcode') ?: $seller->postcode }}">
                            <span class="control-error" v-if="errors.has('postcode')">@{{ errors.first('postcode') }}</span>
                        </div>

                    </div>
                </accordian>

                <accordian :title="'{{ __('marketplace::app.shop.sellers.account.profile.media') }}'" :active="false">
                    <div slot="body">

                        <div class="control-group">
                            <label>{{ __('marketplace::app.shop.sellers.account.profile.logo') }}

                            <image-wrapper :button-label="'{{ __('marketplace::app.shop.sellers.account.profile.add-image-btn-title') }}'" input-name="logo" :multiple="false" :images='"{{ $seller->logo_url }}"'></image-wrapper>
                        </div>

                        <div class="control-group">
                            <label>{{ __('marketplace::app.shop.sellers.account.profile.banner') }}

                            <image-wrapper :button-label="'{{ __('marketplace::app.shop.sellers.account.profile.add-image-btn-title') }}'" input-name="banner" :multiple="false" :images='"{{ $seller->banner_url }}"'></image-wrapper>
                        </div>

                    </div>
                </accordian>

                <accordian :title="'{{ __('marketplace::app.shop.sellers.account.profile.about') }}'" :active="false">
                    <div slot="body">

                        <div class="control-group">
                            <label for="description">{{ __('marketplace::app.shop.sellers.account.profile.about') }}</label>
                            <textarea class="control" id="description" name="description">{{ old('description') ?: $seller->description }}</textarea>
                        </div>

                    </div>
                </accordian>

                <accordian :title="'{{ __('marketplace::app.shop.sellers.account.profile.allow_product_type') }}'"
                    :active="false">
                    <div slot="body">

                        <div class="control-group">
                            <label for="seller_product_type">{{ __('marketplace::app.shop.sellers.account.profile.allow_product_type') }}</label>
                            <select class="control"
                                    multiple
                                    id="seller_product_type"
                                    name="seller_product_type[]">
                                @foreach ($allProductTypes as $allProductType)
                                    <option
                                        {{ ($allProductType->selected == 1) ? 'selected': '' }}
                                        value="{{$allProductType->product_type}}">
                                            {{$allProductType->product_type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </accordian>

                <accordian :title="'{{ __('marketplace::app.shop.sellers.account.profile.social_links') }}'" :active="false">
                    <div slot="body">

                        <div class="control-group">
                            <label for="twitter">{{ __('marketplace::app.shop.sellers.account.profile.twitter') }}</label>
                            <input type="text" class="control" name="twitter" value="{{ old('twitter') ?: $seller->twitter }}">
                        </div>

                        <div class="control-group">
                            <label for="facebook">{{ __('marketplace::app.shop.sellers.account.profile.facebook') }}</label>
                            <input type="text" class="control" name="facebook" value="{{ old('facebook') ?: $seller->facebook }}">
                        </div>

                        <div class="control-group">
                            <label for="youtube">{{ __('marketplace::app.shop.sellers.account.profile.youtube') }}</label>
                            <input type="text" class="control" name="youtube" value="{{ old('youtube') ?: $seller->youtube }}">
                        </div>

                        <div class="control-group">
                            <label for="instagram">{{ __('marketplace::app.shop.sellers.account.profile.instagram') }}</label>
                            <input type="text" class="control" name="instagram" value="{{ old('instagram') ?: $seller->instagram }}">
                        </div>

                        <div class="control-group">
                            <label for="skype">{{ __('marketplace::app.shop.sellers.account.profile.skype') }}</label>
                            <input type="text" class="control" name="skype" value="{{ old('skype') ?: $seller->skype }}">
                        </div>

                        <div class="control-group">
                            <label for="linked_in">{{ __('marketplace::app.shop.sellers.account.profile.linked_in') }}</label>
                            <input type="text" class="control" name="linked_in" value="{{ old('linked_in') ?: $seller->linked_in }}">
                        </div>

                        <div class="control-group">
                            <label for="pinterest">{{ __('marketplace::app.shop.sellers.account.profile.pinterest') }}</label>
                            <input type="text" class="control" name="pinterest" value="{{ old('pinterest') ?: $seller->pinterest }}">
                        </div>

                    </div>
                </accordian>

                <accordian :title="'{{ __('marketplace::app.shop.sellers.account.profile.policies') }}'" :active="false">
                    <div slot="body">

                        <div class="control-group">
                            <label for="return_policy">{{ __('marketplace::app.shop.sellers.account.profile.return_policy') }}</label>
                            <textarea class="control" id="return_policy" name="return_policy">{{ old('return_policy') ?: $seller->return_policy }}</textarea>
                        </div>

                        <div class="control-group">
                            <label for="shipping_policy">{{ __('marketplace::app.shop.sellers.account.profile.shipping_policy') }}</label>
                            <textarea class="control" id="shipping_policy" name="shipping_policy">{{ old('shipping_policy') ?: $seller->shipping_policy }}</textarea>
                        </div>

                        <div class="control-group">
                            <label for="privacy_policy">{{ __('marketplace::app.shop.sellers.account.profile.privacy_policy') }}</label>
                            <textarea class="control" id="privacy_policy" name="privacy_policy">{{ old('privacy_policy') ?: $seller->privacy_policy }}</textarea>
                        </div>

                    </div>
                </accordian>

                <accordian :title="'{{ __('marketplace::app.shop.sellers.account.profile.seo') }}'" :active="false">
                    <div slot="body">

                        <div class="control-group">
                            <label for="meta_description">{{ __('marketplace::app.shop.sellers.account.profile.meta_description') }}</label>
                            <textarea class="control" id="meta_description" name="meta_description">{{ old('meta_description') ?: $seller->meta_description }}</textarea>
                        </div>

                        <div class="control-group">
                            <label for="meta_keywords">{{ __('marketplace::app.shop.sellers.account.profile.meta_keywords') }}</label>
                            <textarea class="control" id="meta_keywords" name="meta_keywords">{{ old('meta_keywords') ?: $seller->meta_keywords }}</textarea>
                        </div>

                    </div>
                </accordian>

                @if (core()->getConfigData('marketplace.settings.minimum_order_amount.enable'))
                    <accordian :title="'{{ __('marketplace::app.shop.sellers.account.profile.minimum_amount') }}'" :active="false">
                        <div slot="body">

                            <div class="control-group">
                                <label for="min_order_amount">{{ __('marketplace::app.shop.sellers.account.profile.min_order_amount') }}</label>
                                <input class="control" v-validate="'decimal:3'" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount') ?: $seller->min_order_amount }}"/>
                                <span class="control-error" v-if="errors.has('min_order_amount')">@{{ errors.first('min_order_amount') }}</span>
                            </div>

                        </div>
                    </accordian>
                @endif

                @if (core()->getConfigData('marketplace.settings.google_analytics.enable'))
                <accordian :title="'{{ __('marketplace::app.shop.sellers.account.profile.google_analytics') }}'" :active="false">
                    <div slot="body">

                        <div class="control-group">
                            <label for="analytics_id">{{ __('marketplace::app.shop.sellers.account.profile.google_analytics_id') }}</label>
                            <input class="control"  id="google_analytics_id" name="google_analytics_id" value="{{ old('google_analytics_id') ?: $seller->google_analytics_id }}"/>
                            <span class="control-error" v-if="errors.has('google_analytics_id')">@{{ errors.first('google_analytics_id') }}</span>
                        </div>

                    </div>
                </accordian>
                @endif

                <?php
                    $seller = app('Webkul\Marketplace\Repositories\SellerRepository')->findOneWhere([
                        'customer_id' => $seller->customer_id
                    ]);
                ?>

                @if ($seller)
                    <accordian :title="'{{ __('marketplace::app.shop.sellers.account.profile.admin-commission') }}'" :active="false">
                        <div slot="body">

                            <seller-commission
                            :percentage = "'{{ $seller->commission_percentage }}'"
                            :enable = "'{{ $seller->commission_enable }}'"
                            >
                            </seller-commission>

                        </div>
                    </accordian>
                @endif

            </div>

            {!! view_render_event('marketplace.sellers.account.profile.edit.after', ['seller' => $seller]) !!}

        </form>

        @if ( core()->getConfigData('marketplace.settings.seller_flag.enable') && $seller)
        <accordian :title="'{{ __('marketplace::app.admin.products.flag.flag-title') }}'" :active="false">

            <div slot="body">
                {!! app('Webkul\Marketplace\DataGrids\Admin\SellerFlagDataGrid')->render() !!}
            </div>
        </accordian>
        @endif

    </div>
@endsection

@push('scripts')

<script type="text/x-template" id="seller-commission-template">

    <div>
        <div class="control-group">
            <span class="checkbox">
                <input type="checkbox" id="status" name="commission_enable" @change="check"
                v-model="commission_enable" value="1">

                <label class="checkbox-view" for="status"></label>
                {{ __('marketplace::app.admin.sellers.change-commission') }}
            </span>
        </div>

        <div class="control-group" :class="[errors.has('commission_percentage') ? 'has-error' : '']">
            <label for="commission_percentage" :class="isRequired ? 'required' : ''">
                {{ __('marketplace::app.admin.sellers.commission-percentage') }}
            </label>

            <input type="text"  class="control" name="commission_percentage" v-validate="isRequired ? 'required|between:.5,99' : ''" v-model="commission_percentage" data-vv-as="&quot;{{ __('marketplace::app.admin.sellers.commission-percentage') }}&quot;"
            :disabled="isActive == false ? true : false"/>

            <span class="control-error" v-if="errors.has('commission_percentage')">@{{ errors.first('commission_percentage') }}</span>
        </div>
    </div>

</script>

<script>

    Vue.component('seller-commission', {

        template: '#seller-commission-template',

        inject: ['$validator'],

        props: ['percentage', 'enable'],

        data: function() {
            return {
                commission_enable : 0,

                commission_percentage: '',

                isActive : false,

                isRequired: false,
            }
        },

        created: function() {
            if (this.enable == 1) {
                this.commission_percentage = this.percentage;
                this.commission_enable = 1;
                this.isActive = true;
                this.isRequired = true;
            }
        },

        methods: {
            check: function() {
                this.isActive = !this.isActive;
                this.isRequired = !this.isRequired;
            }
        }
    });

</script>

@endpush
