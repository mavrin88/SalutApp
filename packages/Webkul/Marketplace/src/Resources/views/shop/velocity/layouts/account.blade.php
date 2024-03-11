@extends('marketplace::shop.layouts.master')

@section('content-wrapper')
    <div class="account-content no-margin velocity-divide-page">
        <div class="sidebar left">
            @include('shop::customers.account.partials.sidemenu')
        </div>

        <div class="account-layout">
            @yield('content')
        </div>

    </div>
@stop

@push('scripts')
    <script type="text/javascript">
        window.addEventListener("load", function() {
            @if(app('Webkul\Marketplace\Repositories\SellerRepository')->isSeller(auth()->guard('customer')->user()->id) && core()->getConfigData('marketplace.settings.general.status'))
                let sidebarHeight = $('.account-layout').css('height');
                customersidebarHeight = parseInt(sidebarHeight.substring(0, sidebarHeight.length - 2)) + 42;
                height = customersidebarHeight + "px";
                $('.sidebar').css('min-height', parseInt(sidebarHeight.substring(0, sidebarHeight.length - 2)) + "px");
                $('.main-content-wrapper').css('min-height', height);
            @endif
        });
    </script>
@endpush