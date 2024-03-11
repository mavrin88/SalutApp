@if (core()->getConfigData('marketplace.settings.general.status'))
@if (session()->has('location'))
    <div class="default cursor-pointer d-inline-block align-top">
        <a @click="showModal('location')">
            {{ session()->get('location') }}
        </a>
    </div>
@else 

@php
    $locationPlaceHolder = trans('marketplace_warehouse::app.shop.location.enter-location');
@endphp

    <div class="default cursor-pointer d-inline-block align-top">
        <form action="{{ route('marketplace-warehouse.user.location.create') }}" method="POST" @submit.prevent="onSubmit($event)">
            @csrf
            <div class="form-container">
                <div class="control-group" :class="[errors.has('location') ? 'has-error' : '']">
                    <input
                        type="text"
                        id="location"
                        name="location"
                        class="input-text loc-input pac-target-input"
                        maxlength="40"
                        v-validate="'alpha_num'"
                        value="{{ session()->has('location') ? session()->get('location') : '' }}"
                        placeholder="<?php echo $locationPlaceHolder; ?>"
                    />

                    <button type="submit" id="header-search-icon" aria-label="Search" class="btn">
                        <i class="fs16 fw6 rango-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endif

@else
@push('scripts')
<script>
       $(".navigation li[title='Shops']").css('display', 'none');
    </script>
    @endpush
@endif
