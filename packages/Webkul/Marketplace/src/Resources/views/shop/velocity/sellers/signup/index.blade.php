@extends('shop::layouts.master')
@section('page_title')
    {{ __('marketplace::app.shop.sellers.account.signup.signup-page-title') }}
@endsection

@push('css')
<style>
    .text-success {
        color: #4CAF50 !important;
    }

    .text-danger {
        color: #FC6868 !important;
    }

</style>
@endpush

@section('content-wrapper')
    <div class="auth-content form-container">
        <div class="container">
            <div class="col-lg-10 col-md-12 offset-lg-1">
                <div class="heading">
                    <h2 class="fs24 fw6">
                        {{ __('marketplace::app.shop.sellers.account.signup.seller-registration') }}
                    </h2>

                    <a href="{{ route('shop.customer.session.index') }}" class="btn-new-customer">
                        <button type="button" class="theme-btn light">
                            {{ __('velocity::app.customer.signup-form.login')}}
                        </button>
                    </a>
                </div>

                <div class="body col-12">
                    <h3 class="fw6">
                        {{ __('marketplace::app.shop.sellers.account.signup.become-seller') }}
                    </h3>

                    <p class="fs16">
                        {{ __('velocity::app.customer.signup-form.form-signup-text')}}
                    </p>

                    {!! view_render_event('bagisto.shop.customers.signup.before') !!}

                    <form
                        method="post"
                        action="{{ route('shop.customer.register.create') }}"
                        @submit.prevent="onSubmit">

                        {{ csrf_field() }}

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.before') !!}

                        <div class="control-group" :class="[errors.has('first_name') ? 'has-error' : '']">
                            <label for="first_name" class="required label-style">
                                {{ __('shop::app.customer.signup-form.firstname') }}
                            </label>

                            <input
                                type="text"
                                class="form-style"
                                name="first_name"
                                v-validate="'required'"
                                value="{{ old('first_name') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.signup-form.firstname') }}&quot;" />

                            <span class="control-error" v-if="errors.has('first_name')" v-text="errors.first('first_name')"></span>
                        </div>

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.firstname.after') !!}

                        <div class="control-group" :class="[errors.has('last_name') ? 'has-error' : '']">
                            <label for="last_name" class="required label-style">
                                {{ __('shop::app.customer.signup-form.lastname') }}
                            </label>

                            <input
                                type="text"
                                class="form-style"
                                name="last_name"
                                v-validate="'required'"
                                value="{{ old('last_name') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.signup-form.lastname') }}&quot;" />

                            <span class="control-error" v-if="errors.has('last_name')" v-text="errors.first('last_name')"></span>
                        </div>

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.lastname.after') !!}

                        <div class="control-group" :class="[errors.has('email') ? 'has-error' : '']">
                            <label for="email" class="required label-style">
                                {{ __('shop::app.customer.signup-form.email') }}
                            </label>

                            <input
                                type="email"
                                class="form-style"
                                name="email"
                                v-validate="'required|email'"
                                value="{{ old('email') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.signup-form.email') }}&quot;" />

                            <span class="control-error" v-if="errors.has('email')" v-text="errors.first('email')"></span>
                        </div>

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.email.after') !!}

                        <div class="control-group" :class="[errors.has('password') ? 'has-error' : '']">
                            <label for="password" class="required label-style">
                                {{ __('shop::app.customer.signup-form.password') }}
                            </label>

                            <input
                                type="password"
                                class="form-style"
                                name="password"
                                v-validate="'required|min:6'"
                                ref="password"
                                value="{{ old('password') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.signup-form.password') }}&quot;" />

                            <span class="control-error" v-if="errors.has('password')" v-text="errors.first('password')"></span>
                        </div>

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.password.after') !!}

                        <div class="control-group" :class="[errors.has('password_confirmation') ? 'has-error' : '']">
                            <label for="password_confirmation" class="required label-style">
                                {{ __('shop::app.customer.signup-form.confirm_pass') }}
                            </label>

                            <input
                                type="password"
                                class="form-style"
                                name="password_confirmation"
                                v-validate="'required|min:6|confirmed:password'"
                                data-vv-as="&quot;{{ __('shop::app.customer.signup-form.confirm_pass') }}&quot;" />

                            <span class="control-error" v-if="errors.has('password_confirmation')" v-text="errors.first('password_confirmation')"></span>
                        </div>

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.password_confirmation.after') !!}

                        <seller-controls></seller-controls>
                        
                        <div class="control-group">

                            {!! Captcha::render() !!}

                        </div>

                        @if (core()->getConfigData('customer.settings.newsletter.subscription'))
                            <div class="control-group">
                                <input type="checkbox" id="checkbox2" name="is_subscribed">
                                <span>{{ __('shop::app.customer.signup-form.subscribe-to-newsletter') }}</span>
                            </div>
                        @endif

                        <div class="become-seller-link">
                            <a href="{{ route('shop.customer.register.index') }}" class="float-right">{{ __('marketplace::app.shop.sellers.account.signup.as-customer') }}</a>
                        </div>

                        {!! view_render_event('bagisto.shop.sellers.signup_form_controls.after') !!}

                        <button class="theme-btn" type="submit">
                            {{ __('shop::app.customer.signup-form.title') }}
                        </button>
                    </form>

                    {!! view_render_event('bagisto.shop.customers.signup.after') !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script type="text/x-template" id="seller-controls-template">

        <div class="seller-form-controls">
            <div class="form-group" :class="[errors.has('url') ? 'has-error' : '']">
                <label for="url" class="required">
                    {{ __('marketplace::app.shop.sellers.account.signup.shop_url') }}
                </label>

                <input 
                    type="text" 
                    class="form-style" 
                    name="url" 
                    v-validate="'required'" 
                    value="{{ old('url') }}" 
                    data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.account.signup.shop_url') }}&quot;" 
                    v-on:keyup="checkShopUrl($event.target.value)">

                <span class="control-info text-success" v-if="isShopUrlAvailable != null && isShopUrlAvailable">{{ __('marketplace::app.shop.sellers.account.signup.shop_url_available') }}</span>

                <span class="control-info text-danger" v-if="isShopUrlAvailable != null && !isShopUrlAvailable">{{ __('marketplace::app.shop.sellers.account.signup.shop_url_not_available') }}</span>

                <span class="control-error" v-if="errors.has('url')">@{{ errors.first('url') }}</span>
            </div>
        </div>

    </script>

    <script>

        Vue.component('seller-controls', {
            template: "#seller-controls-template",

            data: () => ({
                isShopUrlAvailable: null
            }),

            watch: {
                
            },

            methods: {
                checkShopUrl (shopUrl) {
                    this_this = this;

                    this.$http.post("{{ route('marketplace.seller.url') }}", {'url': shopUrl})
                        .then(function(response) {
                            if (response.data.available) {
                                this_this.isShopUrlAvailable = true;

                                document.querySelectorAll("form button.btn")[0].disabled = false;
                            } else {
                                this_this.isShopUrlAvailable = false;
                                document.querySelectorAll("form button.btn")[0].disabled = true;
                            }
                        })
                        .catch(function (error) {
                            document.querySelectorAll("form button.btn")[0].disabled = true;
                        })
                },
            }
        });

    </script>

{!! Captcha::renderJS() !!}

@endpush