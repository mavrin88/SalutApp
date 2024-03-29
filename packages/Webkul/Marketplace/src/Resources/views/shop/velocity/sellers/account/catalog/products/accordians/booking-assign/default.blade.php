{!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.booking.table.before', ['product' => $product]) !!}

<default-booking></default-booking>

{!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.booking.table.after', ['product' => $product]) !!}

@push('css')
    <style>
        .has-form-group .form-group {
            width: 50%;
            float: left;
        }

        .has-form-group .form-group:first-child {
            padding-right: 10px;
        }

        .has-form-group .form-group:last-child {
            padding-left: 10px;
        }

    </style>
@endpush

@push('scripts')

    <script type="text/x-template" id="default-booking-template">
        <div>
            <div class="form-group" :class="[errors.has('booking[booking_type]') ? 'has-error' : '']">
                <label class="required">{{ __('bookingproduct::app.admin.catalog.products.type') }}</label>

                <select v-validate="'required'" name="booking[booking_type]" v-model="default_booking.booking_type" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.type') }}&quot;" disabled>
                    <option value="many">{{ __('bookingproduct::app.admin.catalog.products.many-bookings-for-one-day') }}</option>
                    <option value="one">{{ __('bookingproduct::app.admin.catalog.products.one-booking-for-many-days') }}</option>
                </select>

                <span class="control-error" v-if="errors.has('booking[booking_type]')">@{{ errors.first('booking[booking_type]') }}</span>
            </div>

            <div v-if="default_booking.booking_type == 'many'">
                <div class="form-group" :class="[errors.has('booking[duration]') ? 'has-error' : '']">
                    <label class="required">{{ __('bookingproduct::app.admin.catalog.products.slot-duration') }}</label>

                    <input type="text" v-validate="'required|min_value:1'" name="booking[duration]" v-model="default_booking.duration" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.slot-duration') }}&quot;" disabled/>

                    <span class="control-error" v-if="errors.has('booking[duration]')">@{{ errors.first('booking[duration]') }}</span>
                </div>

                <div class="form-group" :class="[errors.has('booking[break_time]') ? 'has-error' : '']">
                    <label class="required">{{ __('bookingproduct::app.admin.catalog.products.break-time') }}</label>

                    <input type="text" v-validate="'required|min_value:1'" name="booking[break_time]" v-model="default_booking.break_time" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.break-time') }}&quot;" disabled/>

                    <span class="control-error" v-if="errors.has('booking[break_time]')">@{{ errors.first('booking[break_time]') }}</span>
                </div>
            </div>

            <div class="section">
                <div class="secton-title">
                    <span>{{ __('bookingproduct::app.admin.catalog.products.slots') }}</span>
                </div>

                <div class="section-content">

                    <div class="slot-list table" v-if="default_booking.booking_type == 'many'">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ __('bookingproduct::app.admin.catalog.products.day') }}</th>
                                    <th>{{ __('bookingproduct::app.admin.catalog.products.from') }}</th>
                                    <th>{{ __('bookingproduct::app.admin.catalog.products.to') }}</th>
                                    <th>{{ __('bookingproduct::app.admin.catalog.products.status') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr v-for="(day, index) in days">
                                    <td>@{{ day }}</td>

                                    <td>
                                        <div class="form-group" :class="[errors.has('booking[slots][' + index + '][from]') ? 'has-error' : '']">
                                            <time-component>
                                                <input type="text" v-validate="parseInt(slots.many[index].status) ? 'required': ''" :name="'booking[slots][' + index + '][from]'" class="form-style" v-model="slots.many[index].from" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.from') }}&quot;" disabled>
                                            </time-component>

                                            <span class="control-error" v-if="errors.has('booking[slots][' + index + '][from]')">
                                                @{{ errors.first('booking[slots][' + index + '][from]') }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group" :class="[errors.has('booking[slots][' + index + '][to]') ? 'has-error' : '']">
                                            <time-component>
                                                <input type="text" v-validate="parseInt(slots.many[index].status) ? 'required': ''" :name="'booking[slots][' + index + '][to]'" class="form-style" v-model="slots.many[index].to" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.to') }}&quot;" disabled>
                                            </time-component>

                                            <span class="control-error" v-if="errors.has('booking[slots][' + index + '][to]')">
                                                @{{ errors.first('booking[slots][' + index + '][to]') }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group" :class="[errors.has('booking[slots][' + index + '][status]') ? 'has-error' : '']">
                                            <select v-validate="'required'" :name="'booking[slots][' + index + '][status]'" class="form-style" v-model="slots.many[index].status" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.status') }}&quot;" disabled>
                                                <option value="1">{{ __('bookingproduct::app.admin.catalog.products.open') }}</option>
                                                <option value="0">{{ __('bookingproduct::app.admin.catalog.products.close') }}</option>
                                            </select>

                                            <span class="control-error" v-if="errors.has('booking[slots][' + index + '][status]')">@{{ errors.first('booking[slots][' + index + '][status]') }}</span>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="slot-list table" v-if="default_booking.booking_type == 'one'">

                        <table>
                            <thead>
                                <tr>
                                    <th>{{ __('bookingproduct::app.admin.catalog.products.from') }}</th>
                                    <th>{{ __('bookingproduct::app.admin.catalog.products.to') }}</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>

                                <default-slot-item
                                    v-for="(slot, index) in slots.one"
                                    :key="index"
                                    :slot-item="slot"
                                    :control-name="'booking[slots][' + index + ']'"
                                    @onRemoveSlot="removeSlot($event)">
                                </default-slot-item>

                            </tbody>
                        </table>

                        <button type="button" class="btn btn-md btn-primary" style="margin-top: 20px" @click="addSlot()" disabled>
                            {{ __('bookingproduct::app.admin.catalog.products.add-slot') }}
                        </button>

                    </div>

                </div>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="default-slot-item-template">
        <tr>
            <td class="has-form-group">
                <div class="form-group" :class="[errors.has(controlName + '[from_day]') ? 'has-error' : '']">
                    <select v-validate="'required'" :name="controlName + '[from_day]'" v-model="slotItem.from_day" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.day') }}&quot;" disabled>
                        <option v-for="(day, index) in $parent.days" :value="index">@{{ day }}</option>
                    </select>

                    <span class="control-error" v-if="errors.has(controlName + '[from_day]')">@{{ errors.first(controlName + '[from_day]') }}</span>
                </div>

                <div class="form-group date" :class="[errors.has(controlName + '[from]') ? 'has-error' : '']">
                    <time-component>
                        <input type="text" v-validate="'required'" :name="controlName + '[from]'" v-model="slotItem.from" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.from') }}&quot;" disabled>
                    </time-component>

                    <span class="control-error" v-if="errors.has(controlName + '[from]')">
                        @{{ errors.first(controlName + '[from]') }}
                    </span>
                </div>
            </td>

            <td class="has-form-group">
                <div class="form-group" :class="[errors.has(controlName + '[to_day]') ? 'has-error' : '']">
                    <select v-validate="'required'" :name="controlName + '[to_day]'" v-model="slotItem.to_day" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.day') }}&quot;" disabled>
                        <option v-for="(day, index) in $parent.days" :value="index">@{{ day }}</option>
                    </select>

                    <span class="control-error" v-if="errors.has(controlName + '[to_day]')">@{{ errors.first(controlName + '[to_day]') }}</span>
                </div>

                <div class="form-group date" :class="[errors.has(controlName + '[to]') ? 'has-error' : '']">
                    <time-component>
                        <input type="text" v-validate="'required'" :name="controlName + '[to]'" v-model="slotItem.to" class="form-style" data-vv-as="&quot;{{ __('bookingproduct::app.admin.catalog.products.to') }}&quot;" disabled>
                    </time-component>

                    <span class="control-error" v-if="errors.has(controlName + '[to]')">
                        @{{ errors.first(controlName + '[to]') }}
                    </span>
                </div>
            </td>

            <td>
                <i class="icon remove-icon" @click="removeSlot()"></i>
            </td>
        </tr>
    </script>

    <script>
        Vue.component('default-booking', {

            template: '#default-booking-template',

            inject: ['$validator'],

            data: function() {
                return {
                    default_booking: bookingProduct && bookingProduct.default_slot ? bookingProduct.default_slot  : {
                        booking_type: 'one',

                        duration: 45,

                        break_time: 15,

                        slots: []
                    },

                    slots: {
                        'one': [],

                        'many': [
                            {'from': '', 'to': '', 'status': 0},
                            {'from': '', 'to': '', 'status': 1},
                            {'from': '', 'to': '', 'status': 1},
                            {'from': '', 'to': '', 'status': 1},
                            {'from': '', 'to': '', 'status': 1},
                            {'from': '', 'to': '', 'status': 1},
                            {'from': '', 'to': '', 'status': 1}
                        ]
                    },

                    days: [
                        "{{ __('bookingproduct::app.admin.catalog.products.sunday') }}",
                        "{{ __('bookingproduct::app.admin.catalog.products.monday') }}",
                        "{{ __('bookingproduct::app.admin.catalog.products.tuesday') }}",
                        "{{ __('bookingproduct::app.admin.catalog.products.wednesday') }}",
                        "{{ __('bookingproduct::app.admin.catalog.products.thursday') }}",
                        "{{ __('bookingproduct::app.admin.catalog.products.friday') }}",
                        "{{ __('bookingproduct::app.admin.catalog.products.saturday') }}"
                    ]
                }
            },

            created: function() {
                if (this.default_booking.booking_type == 'one') {
                    this.slots['one'] = this.default_booking.slots ? this.default_booking.slots : [];
                } else {
                    if (this.default_booking.slots)
                        this.slots['many'] = this.default_booking.slots;
                }
            },

            methods: {
                addSlot: function () {
                    this.slots.one.push({ 'from_day': 0, 'from': '', 'to_day': 0, 'to': '' });
                },

                removeSlot: function(slot) {
                    let index = this.slots.one.indexOf(slot)

                    this.slots.one.splice(index, 1)
                },
            }
        });

        Vue.component('default-slot-item', {

            template: '#default-slot-item-template',

            props: ['slotItem', 'controlName'],

            inject: ['$validator'],

            methods: {
                removeSlot: function() {
                    this.$emit('onRemoveSlot', this.slotItem)
                },
            }
        });
    </script>
@endpush