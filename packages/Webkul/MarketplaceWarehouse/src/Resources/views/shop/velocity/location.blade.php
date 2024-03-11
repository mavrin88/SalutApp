@php
    $marketplaceStatus = core()->getConfigData('marketplace.settings.general.status');

    $location = false;

    // session()->forget('location');

    if (session()->has('location')) {
        $location = true;
    }

    $cart = cart()->getCart();

    $cartItems = false;

    if (
        $cart
        && count($cart->items)
    ) {
        $cartItems = true;
    }
@endphp

@if ($marketplaceStatus)
    <location></location>
@endif

@push('scripts')
    <style>
        .modal-container.theme-btn {
            font-size: 14px;
            padding: 3px 12px;
        }
        .modal-container input {
            width: 100%;
            resize: none;
            font-size: 16px;
            padding: 5px 16px;
            border-radius: 1px;
            background: $btn-text-color;
            border: 1px solid $border-common;
        }
        .modal-container input:active,
        .modal-container input:focus {
            border-color: $theme-color;
        }
        .custom-input {
            width: 100%;
            resize: none;
            font-size: 16px;
            padding: 5px 16px;
            border-radius: 1px;
            background: #fff;
            border: 1px solid #ccc;
        }
        .custom-input:focus,
        .custom-input:active {
            border-color: #26a37c;
        }
        /* .shop:before {
            content: "\E971";
        } */
        .remove-icon {
            background-image: url({{ asset ('/themes/velocity/assets/images/Icon-remove.svg') }}) !important;
            margin-right: 0px !important;
        }
    </style>

    <script type="text/x-template" id="location-template">
        <div>
            <modal-component id="location" :is-open="$root.$root.modalIds.location">
                <h3 slot="header">
                    {{ __('marketplace_warehouse::app.shop.location.add-location') }}
                </h3>

                <div slot="body">
                    <form action="{{ route('marketplace-warehouse.user.location.create') }}" method="POST" @submit.prevent="onSubmit($event)">
                        @csrf

                        <div class="form-container">
                            <div class="control-group" :class="[errors.has('location') ? 'has-error' : '']">
                                <label for="location" class="required">
                                    {{ __('marketplace_warehouse::app.shop.location.enter-location') }}
                                </label>

                                <input
                                    type="text"
                                    id="location"
                                    name="location"
                                    class="control custom-input"
                                    maxlength="40"
                                    v-validate="'required|alpha_num'"
                                    value="{{ session()->has('location') ? session()->get('location') : '' }}"
                                />

                                <span class="control-error" v-if="errors.has('location')">
                                    @{{ errors.first('location') }}
                                </span>
                            </div>

                            <button type="submit" class="theme-btn mt15">
                                {{ __('marketplace_warehouse::app.shop.location.go-to-shop') }}
                            </button>
                        </div>

                    </form>
                </div>
            </modal-component>

            <form action="{{ route('marketplace-warehouse.user.location.create') }}" method="POST" id="location-form">
                @csrf
                <input type="hidden" id="user-location" name="location"/>
            </form>
        </div>
    </script>

    <script>
        Vue.component('location', {
            template: '#location-template',

            data: function () {
                return {
                    location : @json($location),
                    hyperLocal: @json($marketplaceStatus),

                }
            },

            mounted: function () {
                if (this.hyperLocal == 1) {console.log(this.location);
                    if (this.location == 0 || this.location == 'false') {
                        this.show();
                    }
                }
            },

            methods: {
                showPosition (position) {
                    var location = position.coords.latitude + ',' + position.coords.longitude;

                    try {
                        var point = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

                        new google.maps.Geocoder().geocode(
                            {'latLng': point},
                            (res, status) => {
                                var zip = res[0].address_components.filter(component => {
                                    return (component.types.indexOf("postal_code") > -1);
                                });

                                $("#user-location").val(zip[0].long_name);

                                $("#location-form").submit();
                            }
                        );
                    } catch (exception) {
                        $("#user-location").val(location);

                        $("#location-form").submit();
                    };
                },

                show() {
                    this.$root.$root.showModal('location');
                },

                onSubmit (e) {
                    this.$validator.validateAll().then(result => {
                        if (result) {
                            if ((('{{ $cartItems }}' == 1) || (this.$root.miniCartKey > 0)) && ! confirm('{{ __('marketplace_warehouse::app.shop.cart.empty') }}')) {
                                event.preventDefault();
                            } else {
                                e.target.submit();
                            }
                        }
                    });
                }
            }
        });
    </script>
@endpush
