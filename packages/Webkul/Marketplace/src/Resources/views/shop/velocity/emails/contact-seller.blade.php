@component('shop::emails.layouts.master')
    <div style="text-align: center;">
        <a href="{{ config('app.url') }}">
            <img src="{{ bagisto_asset('images/logo.svg') }}">
        </a>
    </div>

    <div style="padding: 30px;">
        <div style="font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 34px;">
            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('marketplace::app.shop.sellers.mails.contact-seller.dear', ['name' => $mail_to == "admin" ? __('admin::app.catalog.attributes.admin') : $seller_name ]) }},
            </p>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('marketplace::app.shop.sellers.mails.contact-seller.info') }}
            </p>

            <br/>

            @if ($mail_to == "admin")
            <div style="font-weight: bold; font-size: 16px; color: #242424;">
                {{ __('marketplace::app.admin.transactions.seller-name') }}
            </div>
            
            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ $seller_name }}
            </p>

            <br/>
            @endif

            <div style="font-weight: bold; font-size: 16px; color: #242424;">
                {{ __('marketplace::app.shop.sellers.mails.contact-seller.issue') }}
            </div>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ $query }}
            </p>

            <br/>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('marketplace::app.shop.sellers.mails.contact-seller.thanks') }}
            <br>
                {{ $customer_name }}
            </p>

        </div>
    </div>
@endcomponent