@php
    $cart = cart()->getCart();
    $cartItemsCount = $cart ? $cart->items->count() : trans('shop::app.minicart.zero');
    $customer = auth('customer')->user();
@endphp

<mobile-header
    is-customer="{{ auth()->guard('customer')->check() ? 'true' : 'false' }}"
    heading= "{{ __('velocity::app.menu-navbar.text-category') }}"
    :header-content="{{ json_encode(app('Webkul\Velocity\Repositories\ContentRepository')->getAllContents()) }}"
    category-count="{{ $velocityMetaData ? $velocityMetaData->sidebar_category_count : 10 }}"
    cart-items-count="{{ $cartItemsCount }}"
    cart-route="{{ route('shop.checkout.cart.index') }}"
    :locale="{{ json_encode(core()->getCurrentLocale()) }}"
    :all-locales="{{ json_encode(core()->getCurrentChannel()->locales) }}"
    :currency="{{ json_encode(core()->getCurrentCurrency()) }}"
    :all-currencies="{{ json_encode(core()->getCurrentChannel()->currencies) }}"
>
    {{-- this is default content if js is not loaded --}}
    <div class="row">
        <div class="col-6">
            <div class="hamburger-wrapper">
                <i class="rango-toggle hamburger"></i>
            </div>

            <a class="left" href="{{ route('shop.home.index') }}" aria-label="Logo">
                <img class="logo" src="{{ core()->getCurrentChannel()->logo_url ?? asset('themes/velocity/assets/images/logo-text.png') }}" alt="" />
            </a>
        </div>

        <div class="right-vc-header col-6">
            <a href="{{ auth()->guard('customer')->check() ? route('velocity.customer.product.compare') : route('velocity.product.compare') }}" class="compare-btn unset">
                <i class="material-icons">compare_arrows</i>
            </a>
            <a href="{{ route('shop.customer.wishlist.index') }}" class="wishlist-btn unset">
                <i class="material-icons">favorite_border</i>
            </a>
            <a class="unset cursor-pointer">
                <i class="material-icons">search</i>
            </a>
            <a href="{{ route('shop.checkout.cart.index') }}" class="unset">
                <i class="material-icons text-down-3">shopping_cart</i>
                <div class="badge-wrapper">
                    <span class="badge">{{ $cartItemsCount }}</span>
                </div>
            </a>
        </div>
    </div>

    <template v-slot:greetings>
        @guest('customer')
            <a class="unset" href="{{ route('shop.customer.session.index') }}">
                {{ __('velocity::app.responsive.header.greeting', ['customer' => 'Guest']) }}
            </a>
        @endguest

        @auth('customer')
            <a class="unset" href="{{ route('shop.customer.profile.index') }}">
                {{ __('velocity::app.responsive.header.greeting', ['customer' => auth()->guard('customer')->user()->first_name]) }}
            </a>
        @endauth
    </template>

    <template v-slot:customer-navigation>
        @auth('customer')
            <ul type="none" class="vc-customer-options">
                <li>
                    <a href="{{ route('shop.customer.profile.index') }}" class="unset">
                        <i class="icon profile text-down-3"></i>
                        <span>{{ __('shop::app.header.profile') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('shop.customer.addresses.index') }}" class="unset">
                        <i class="icon address text-down-3"></i>
                        <span>{{ __('velocity::app.shop.general.addresses') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('shop.customer.reviews.index') }}" class="unset">
                        <i class="icon reviews text-down-3"></i>
                        <span>{{ __('velocity::app.shop.general.reviews') }}</span>
                    </a>
                </li>

                @if (core()->getConfigData('general.content.shop.wishlist_option'))
                    <li>
                        <a href="{{ route('shop.customer.wishlist.index') }}" class="unset">
                            <i class="icon wishlist text-down-3"></i>
                            <span>{{ __('shop::app.header.wishlist') }}</span>
                        </a>
                    </li>
                @endif

                @if (core()->getConfigData('general.content.shop.compare_option'))
                    <li>
                        <a href="{{ route('velocity.customer.product.compare') }}" class="unset">
                            <i class="icon compare text-down-3"></i>
                            <span>{{ __('shop::app.customer.compare.text') }}</span>
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('shop.customer.orders.index') }}" class="unset">
                        <i class="icon orders text-down-3"></i>
                        <span>{{ __('velocity::app.shop.general.orders') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('shop.customer.downloadable_products.index') }}" class="unset">
                        <i class="icon downloadables text-down-3"></i>
                        <span>{{ __('velocity::app.shop.general.downloadables') }}</span>
                    </a>
                </li>
            </ul>
            
        @endauth
    </template>

    <template v-slot:extra-navigation>
        <li>
            @auth('customer')
            <form id="customerLogout" action="{{ route('shop.customer.session.destroy') }}" method="POST">
                            @csrf

                            @method('DELETE')
                        </form>

                        <a
                            class="unset"
                            href="{{ route('shop.customer.session.destroy') }}"
                            onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                            {{ __('shop::app.header.logout') }}
                        </a>
            @endauth

            @guest('customer')
                <a
                    class="unset"
                    href="{{ route('shop.customer.session.create') }}">
                    <span>{{ __('shop::app.customer.login-form.title') }}</span>
                </a>
            @endguest
        </li>

        
        @guest('customer')
        <li>
            <a
                class="unset"
                href="{{ route('shop.customer.register.index') }}">
                <span>{{ __('shop::app.header.sign-up') }}</span>
            </a>
        </li>
        @endguest
        
        @if (core()->getConfigData('marketplace.settings.general.status'))
            <li> 
                <a  
                    class="unset"
                    href="{{ route('marketplace.seller_central.index') }}">
                    <span>{{ __('marketplace::app.shop.layouts.sell') }}</span>
                </a>
            </li>
            </ul>
            @if ( isset($customer->id))
            @if (app('Webkul\Marketplace\Repositories\SellerRepository')->isSeller(auth()->guard('customer')->user()->id))
                <ul type="none" class="meta-wrapper">
                <li>
                    {{  __('marketplace::app.shop.layouts.marketplace') }}
                </li>
                </ul>
                <ul type="none" class="vc-customer-options"> 
                <li>
                    <a href="{{ route('marketplace.account.seller.edit') }}" class="unset">
                        <i class="icon seller text-down-3"></i>
                        <span>{{ __('marketplace::app.shop.layouts.profile') }}</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('marketplace.account.dashboard.index') }}" class="unset">
                        <i class="icon dashboard text-down-3"></i>
                        <span>{{ __('marketplace::app.shop.layouts.dashboard') }}</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('marketplace.account.earning.index') }}" class="unset">
                        <i class="icon earnings text-down-3"></i>
                        <span>{{ __('marketplace::app.shop.layouts.earnings') }}</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('marketplace.account.products.index') }}" class="unset">
                        <i class="icon products text-down-3"></i>
                        <span>{{ __('marketplace::app.shop.layouts.products') }}</span>
                    </a>
                </li> 

                <li>
                    <a href="{{ route('marketplace.account.products.review') }}" class="unset">
                        <i class="icon product-reviews text-down-3"></i>
                        <span>{{ __('marketplace::app.shop.layouts.product-reviews') }}</span>
                    </a>
                </li> 

                <li>
                    <a href="{{ route('marketplace.account.orders.index') }}" class="unset">
                        <i class="icon orders text-down-3"></i>
                        <span>{{ __('marketplace::app.shop.layouts.orders') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('marketplace.account.transactions.index') }}" class="unset">
                        <i class="icon transactions text-down-3"></i>
                        <span>{{ __('marketplace::app.shop.layouts.transactions') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('marketplace.account.reviews.index') }}" class="unset">
                        <i class="icon reviews text-down-3"></i>
                        <span>{{ __('marketplace::app.shop.layouts.reviews') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('marketplace.account.customers.index') }}" class="unset">
                        <i class="icon seller text-down-3"></i>
                        <span>{{ __('marketplace::app.shop.layouts.customers') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('marketplace.account.payment.request.index') }}" class="unset">
                        <i class="icon paymentRequest text-down-3"></i>
                        <span>{{ __('marketplace::app.shop.layouts.paymentRequest') }}</span>
                    </a>
                </li>

            @else
                <li class="menu-item {{ request()->route()->getName() == 'marketplace.account.seller.create' ? 'active' : '' }}">
                    <a class="unset fw6 full-width" href="{{ route('marketplace.account.seller.create') }}">
                        <i class="icon become_parther text-down-3"></i>
                        {{ __('marketplace::app.shop.layouts.become-seller') }}
                    </a>
                </li>
            @endif
        </ul>
        @endif
        @endif

    </template>

    <template v-slot:logo>
        <a class="left" href="{{ route('shop.home.index') }}" aria-label="Logo">
            <img class="logo" src="{{ core()->getCurrentChannel()->logo_url ?? asset('themes/velocity/assets/images/logo-text.png') }}" alt="" />
        </a>
    </template>

    <template v-slot:top-header>
        @include('velocity::shop.layouts.particals.compare', ['isText' => false])

        @include('velocity::shop.layouts.particals.wishlist', ['isText' => false])
    </template>

    <template v-slot:search-bar>
        <div class="row">
            <div class="col-md-12">
                @include('velocity::shop.layouts.particals.search-bar')
            </div>
        </div>
    </template>

</mobile-header>