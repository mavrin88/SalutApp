@if (core()->getConfigData('marketplace.settings.general.status'))
@push('css')
    <style>
        .become-seller-link{
            margin-bottom: 20px;
        }
    </style>
@endpush

    <div class="become-seller-link">
        <a href="{{ route('marketplace.seller.create') }}" class="float-right">{{ __('marketplace::app.shop.sellers.account.signup.wanna-be-seller') }}</a>
    </div>

@endif