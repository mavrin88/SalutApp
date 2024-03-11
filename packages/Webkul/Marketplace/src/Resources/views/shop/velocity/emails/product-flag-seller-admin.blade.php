@component('shop::emails.layouts.master')
    <div style="text-align: center;">
        <a href="{{ config('app.url') }}">
            <img src="{{ bagisto_asset('images/logo.svg') }}">
        </a>
    </div>

    <div style="padding: 30px;">
        <div style="font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 34px;">
            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('marketplace::app.shop.sellers.mails.report-product-toadmin.dear', ['name' => $name]) }},
            </p>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {!! __('marketplace::app.shop.sellers.mails.report-product-toadmin.info', [
                    'product_name' => '<a href="' . route('admin.catalog.products.edit', $data['product']->id) . '" style="color: #0041FF; font-weight: bold;">' . $data['product']->product->name . '</a>'
                    ,'reason'=>$data['reason'],'name' => $data['name']])
                !!}
            </p>

            <br/>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('marketplace::app.shop.sellers.mails.report-product-toadmin.thanks') }}
            </p>

        </div>
    </div>
@endcomponent