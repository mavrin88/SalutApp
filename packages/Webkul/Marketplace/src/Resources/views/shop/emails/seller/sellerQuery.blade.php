@component('shop::emails.layouts.master')

    <div style="text-align: center;">
        <a href="{{ config('app.url') }}">
            <img src="{{ asset('themes/default/assets/images/logo.svg') }}">
        </a>
    </div>

    <div style="padding: 30px;">

        <div style="font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 34px;">
           <p>A seller has raised the below query</p><br>

           <p>{{$query}}</p>
        </div>

        <div style="font-size: 16px;color: #5E5E5E;line-height: 24px;display: inline-block">
            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                Seller Details
            </p>
            <p>{{$name}}</p>
            <p>{{$email}}</p>

        </div>

    </div>

@endcomponent