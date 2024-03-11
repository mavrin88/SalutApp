@component('shop::emails.layouts.master')
    <div style="text-align: center;">
        <a href="{{ config('app.url') }}">
            <img src="{{ asset('themes/default/assets/images/logo.svg') }}">
        </a>
    </div>

    <div style="padding: 30px;">

        <div style="font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 34px;">
            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('marketplace::app.mail.seller.flag.title', ['name' => $seller->customer->name]) }},
            </p>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">

                {{ $reason }}

            </p>

            <p style="text-align: center;padding: 20px 0;">

                {{ __('marketplace::app.mail.seller.flag.by-customer') }}

            </p>
            <p><b>{{$name}}</b></p>
            <p>{{$email}}</p>
        </div>

        <div style="font-size: 16px;color: #5E5E5E;line-height: 24px;display: inline-block">
            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                <a href="{{route('marketplace.seller.show', $seller->url)}}">{{__('marketplace::app.mail.seller.flag.view-seller-profile')}}</a>
            </p>
        </div>

    </div>

@endcomponent