{!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.booking.table.before', ['product' => $product]) !!}

<table-booking></table-booking>

{!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.booking.table.after', ['product' => $product]) !!}

@push('scripts')

    <script type="text/x-template" id="table-booking-template">
        <div>
            <div class="form-group" :class="[errors.has('booking[price_type]') ? 'has-error' : '']">
                <label class="required">{{ __('bookingproduct::app.admin.catalog.products.charged-per') }}</label>

                <select v-validate="'required'" name="booking[price_type]" v-model="table_booking.price_type" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.charged-per') }}&quot;" disabled>
                    <option value="guest">{{ __('bookingproduct::app.admin.catalog.products.guest') }}</option>
                    <option value="table">{{ __('bookingproduct::app.admin.catalog.products.table') }}</option>
                </select>
                
                <span class="control-error" v-if="errors.has('booking[price_type]')">@{{ errors.first('booking[price_type]') }}</span>
            </div>

            <div v-if="table_booking.price_type == 'table'" class="form-group" :class="[errors.has('booking[guest_limit]') ? 'has-error' : '']">
                <label class="required">{{ __('bookingproduct::app.admin.catalog.products.guest-limit') }}</label>

                <input type="text" v-validate="'required|min_value:1'" name="booking[guest_limit]" v-model="table_booking.guest_limit" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.guest-limit') }}&quot;" disabled/>
                
                <span class="control-error" v-if="errors.has('booking[guest_limit]')">@{{ errors.first('booking[guest_limit]') }}</span>
            </div>

            <div class="form-group" :class="[errors.has('booking[qty]') ? 'has-error' : '']">
                <label class="required">{{ __('bookingproduct::app.admin.catalog.products.guest-capacity') }}</label>

                <input type="text" v-validate="'required|min_value:1'" name="booking[qty]" value="{{ $bookingProduct ? $bookingProduct->qty : 0 }}" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.guest-limit') }}&quot;" disabled/>
                
                <span class="control-error" v-if="errors.has('booking[qty]')">@{{ errors.first('booking[qty]') }}</span>
            </div>

            <div class="form-group" :class="[errors.has('booking[duration]') ? 'has-error' : '']">
                <label class="required">{{ __('bookingproduct::app.admin.catalog.products.slot-duration') }}</label>

                <input type="text" v-validate="'required|min_value:1'" name="booking[duration]" v-model="table_booking.duration" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.slot-duration') }}&quot;" disabled/>
                
                <span class="control-error" v-if="errors.has('booking[duration]')">@{{ errors.first('booking[duration]') }}</span>
            </div>

            <div class="form-group" :class="[errors.has('booking[break_time]') ? 'has-error' : '']">
                <label class="required">{{ __('bookingproduct::app.admin.catalog.products.break-time') }}</label>

                <input type="text" v-validate="'required|min_value:1'" name="booking[break_time]" v-model="table_booking.break_time" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.break-time') }}&quot;" disabled/>
                
                <span class="control-error" v-if="errors.has('booking[break_time]')">@{{ errors.first('booking[break_time]') }}</span>
            </div>

            <div class="form-group" :class="[errors.has('booking[prevent_scheduling_before]') ? 'has-error' : '']">
                <label class="required">{{ __('bookingproduct::app.admin.catalog.products.prevent-scheduling-before') }}</label>

                <input type="text" v-validate="'required|min_value:1'" name="booking[prevent_scheduling_before]" v-model="table_booking.prevent_scheduling_before" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.prevent-scheduling-before') }}&quot;" disabled/>
                
                <span class="control-error" v-if="errors.has('booking[prevent_scheduling_before]')">@{{ errors.first('booking[prevent_scheduling_before]') }}</span>
            </div>

            <div class="form-group" :class="[errors.has('booking[same_slot_all_days]') ? 'has-error' : '']">
                <label class="required">{{ __('bookingproduct::app.admin.catalog.products.same-slot-all-days') }}</label>

                <select v-validate="'required'" name="booking[same_slot_all_days]" v-model="table_booking.same_slot_all_days" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.same-slot-all-days') }}&quot;" disabled>
                    <option value="1">{{ __('bookingproduct::app.admin.catalog.products.yes') }}</option>
                    <option value="0">{{ __('bookingproduct::app.admin.catalog.products.no') }}</option>
                </select>
                
                <span class="control-error" v-if="errors.has('booking[same_slot_all_days]')">@{{ errors.first('booking[same_slot_all_days]') }}</span>
            </div>

            <div class="section">
                <div class="secton-title">
                    <span>{{ __('bookingproduct::app.admin.catalog.products.slots') }}</span>
                </div>

                <div class="section-content">

                    <slot-list
                        booking-type="table_slot"
                        :same-slot-all-days="table_booking.same_slot_all_days">
                    </slot-list>
                
                </div>
            </div>
        </div>
    </script>

    <script>
        Vue.component('table-booking', {

            template: '#table-booking-template',

            inject: ['$validator'],

            data: function() {
                return {
                    table_booking: bookingProduct && bookingProduct.table_slot ? bookingProduct.table_slot : {
                        price_type: 'guest',

                        guest_limit: 2,

                        duration: 45,

                        break_time: 15,

                        prevent_scheduling_before: 0,

                        same_slot_all_days: 1,

                        slots: []
                    }
                }
            }
        });
    </script>
@endpush