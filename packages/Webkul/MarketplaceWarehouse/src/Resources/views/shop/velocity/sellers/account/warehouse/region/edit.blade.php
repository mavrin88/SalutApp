@extends('shop::customers.account.index')

@section('page_title')
    {{ __('marketplace_warehouse::app.shop.warehouse.region.edit-title') }}
@endsection

@section('page-detail-wrapper')

    <div class="account-layout">

        <form method="POST" action="{{ route('marketplace_warehouse.user.warehouse.region.update', $region->id) }}" class="account-table-content">
            <div class="account-head mb-10">
                <span class="account-heading">
                    {{ __('marketplace_warehouse::app.shop.warehouse.region.region-title') }}
                </span>

                <div class="account-action">
                    <button type="submit" class="btn btn-primary">
                        {{ __('marketplace_warehouse::app.shop.warehouse.region.save') }}
                    </button>
                </div>

                <div class="horizontal-rule"></div>
            </div>

            {!! view_render_event('marketplace_warehouse.warehouse.create.region.before') !!}

            <div class="account-table-content">

                @csrf()

                {!! view_render_event('marketplace_warehouse.warehouse.create_warehouse_region_form_accordian.general.before') !!}

                <accordian :title="'{{ __('marketplace_warehouse::app.shop.warehouse.general') }}'" :active="true">
                    <div slot="header">
                        {{ __('marketplace_warehouse::app.shop.warehouse.general') }}
                        <i class="icon expand-icon right"></i>
                    </div> 
                
                    <div slot="body">

                        <div class="control-group" :class="[errors.has('region__name') ? 'has-error' : '']">
                            <label for="region_name" class="required">{{ __('marketplace_warehouse::app.shop.warehouse.region.region-name') }}</label>
                            <input type="text" class="control" name="region_name" v-validate="'required|max:200'" value="{{ $region->region_name }}"  data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.region.region-name') }}&quot;" disabled/>
                            <span class="control-error" v-if="errors.has('region_name')">@{{ errors.first('region_name') }}</span>
                        </div>

                        <div class="control-group" :class="[errors.has('warehouse_name') ? 'has-error' : '']">
                            <label for="warehouse_name" class="required mandatory">{{ __('marketplace_warehouse::app.shop.warehouse.title') }}</label>
                            <select class="control" v-validate="'required'" id="warehouse_name" name="warehouse_name" data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.title') }}&quot;" disabled>
                                <option value="">{{ $region->region_name }}</option>

                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->warehouse_name }}"  @if($warehouse->warehouse_name  == $regionWarehouse->warehouse_name) selected @endif>{{ $warehouse->warehouse_name }}</option>
                                @endforeach
                            </select>

                            <span class="control-error" v-if="errors.has('warehouse_name')">@{{ errors.first('warehouse_name') }}</span>
                        </div>

                        <div class="control-group" :class="[errors.has('price_type') ? 'has-error' : '']">
                            <label for="price_type" class="required mandatory">{{ __('marketplace_warehouse::app.shop.warehouse.price.title') }}</label>
                            <select class="control" v-validate="'required'" id="price_type" name="price_type" data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.price.title') }}&quot;">
                                <option value="">{{ __('admin::app.select-option') }}</option>

                                @foreach($priceTypes as $priceType)
                                    <option value="{{ $priceType->title }}" @if($priceType->title == $regionPriceType->title ) selected @endif>{{ $priceType->title }}</option>
                                @endforeach
                            </select>

                            <span class="control-error" v-if="errors.has('price_type')">@{{ errors.first('price_type') }}</span>
                        </div>

                        <div class="control-group" :class="[errors.has('delivery_type') ? 'has-error' : '']">
                            <label for="delivery_type">{{ __('marketplace_warehouse::app.admin.delivery-type.title') }}</label>
                            <select class="control" v-validate="'required'" id="delivery_type" name="delivery_type" data-vv-as="&quot;{{ __('marketplace_warehouse::app.admin.delivery-type.title') }}&quot;">
                                <option value="">{{ __('admin::app.select-option') }}</option>

                                @foreach($deliveryTypes as $deliveryType)
                                    <option value="{{ $deliveryType->title }}" @if($regionDeliveryType && $deliveryType->title  == $regionDeliveryType->title ) selected @endif>{{ $deliveryType->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="control-group" :class="[errors.has('delivery_time') ? 'has-error' : '']">
                            <label for="delivery_time">{{ __('marketplace_warehouse::app.admin.delivery-time.title') }}</label>
                            <select class="control" v-validate="'required'" id="delivery_time" name="delivery_time" data-vv-as="&quot;{{ __('marketplace_warehouse::app.admin.delivery-time.title') }}&quot;">
                                <option value="">{{ __('admin::app.select-option') }}</option>

                                @foreach($deliveryTimes as $deliveryTime)
                                    <option value="{{ $deliveryTime->title }}" @if($regionDeliveryTime &&  $deliveryTime->title  == $regionDeliveryTime->title ) selected @endif>{{ $deliveryTime->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="control-group" :class="[errors.has('max_weight') ? 'has-error' : '']">
                            <label for="max_weight" class="required">{{ __('marketplace_warehouse::app.shop.warehouse.max-weight') }}</label>
                            <input type="text" class="control" name="max_weight" v-validate="'required|max:200'" value="{{ $region->max_weight }}"  data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.max-weight') }}&quot;"/>
                            <span class="control-error" v-if="errors.has('max_weight')">@{{ errors.first('max_weight') }}</span>
                        </div>

                    </div>
                </accordian>

                {!! view_render_event('marketplace_warehouse.warehouse.create_warehouse_region_form_accordian.general.after') !!}

                {!! view_render_event('marketplace_warehouse.warehouse.create_warehouse_region_form_accordian.delivery.before') !!}

                <accordian :title="'{{ __('marketplace_warehouse::app.shop.warehouse.delivery') }}'" :active="true">
                    <div slot="header">
                        {{ __('marketplace_warehouse::app.shop.warehouse.delivery') }}
                        <i class="icon expand-icon right"></i>
                    </div> 
                
                    <div slot="body">

                        {!! view_render_event('marketplace_warehouse.warehouse.create_warehouse_region_form_accordian.delivery.controls.before') !!}

                        <delivery-wrapper></delivery-wrapper>

                        {!! view_render_event('marketplace_warehouse.warehouse..create_warehouse_region_form_accordian.delivery.controls.after') !!}

                    </div>
                </accordian>

                {!! view_render_event('marketplace_warehouse.warehouse.create_warehouse_region_form_accordian.delivery.after') !!}

                {!! view_render_event('marketplace_warehouse.warehouse.create_warehouse_region_form_accordian.cities.before') !!}

                <accordian :title="'{{ __('marketplace_warehouse::app.shop.warehouse.cities') }}'" :active="true">
                    <div slot="header">
                        {{ __('marketplace_warehouse::app.shop.warehouse.cities') }}
                        <i class="icon expand-icon right"></i>
                    </div> 
                
                    <div slot="body">

                        <div class="control-group" :class="[errors.has('assigned_cities') ? 'has-error' : '']">
                            <label for="assigned_cities" class="required mandatory">{{ __('marketplace_warehouse::app.shop.warehouse.select-cities') }}</label>
                            <select class="control" v-validate="'required'" id="assigned_cities" name="assigned_cities[]" data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.select-cities') }}&quot;" multiple="multiple">

                                @foreach($cities as $city)
                                    <option value="{{ $city->name }}" @if(in_array($city->id, $regionCityIds)) selected @endif>{{ $city->name }}</option>
                                @endforeach

                            </select>

                            <span class="control-error" v-if="errors.has('assigned_cities')">@{{ errors.first('assigned_cities') }}</span>
                        </div>

                    </div>
                </accordian>

                {!! view_render_event('marketplace_warehouse.warehouse.create_warehouse_region_form_accordian.cities.after') !!}
            
            </div>

            {!! view_render_event('marketplace_warehouse.warehouse.create.region.after') !!}

        </form>

    </div>

@endsection

@push('scripts')
    <script type="text/x-template" id="delivery-template">
        <div>
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('marketplace_warehouse::app.shop.warehouse.from') }}</th>
                            <th>{{ __('marketplace_warehouse::app.shop.warehouse.to') }}</th>
                            <th>{{ __('marketplace_warehouse::app.shop.warehouse.cost') }}</th>
                            <th class="actions"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="row in deliveryRows">
                            <td>
                                <div class="control-group">
                                    <input
                                        type="number"
                                        :name="[inputName(row)+'[from]']"
                                        class="control"
                                        v-model="row.from"
                                        data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.from') }}&quot;"
                                    />

                                    <span class="control-error" v-if="errors.has(inputName(row)+'[from]')">
                                        @{{ errors.first(inputName(row)+'[from]') }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                <div class="control-group">
                                    <input
                                        type="number"
                                        :name="[inputName(row)+'[to]']"
                                        class="control"
                                        v-model="row.to"
                                        data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.from') }}&quot;"
                                    />

                                    <span class="control-error" v-if="errors.has(inputName(row)+'[to]')">
                                        @{{ errors.first(inputName(row)+'[to]') }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                <div class="control-group">
                                    <input
                                        type="number"
                                        :name="[inputName(row)+'[cost]']"
                                        class="control"
                                        v-model="row.cost"
                                        data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.from') }}&quot;"
                                    />

                                    <span class="control-error" v-if="errors.has(inputName(row)+'[cost]')">
                                        @{{ errors.first(inputName(row)+'[cost]') }}
                                    </span>
                                </div>
                            </td>

                            <td class="action text-center">
                                <i class="icon remove-icon" @click="removeRow(row)"></i>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>

            <button
                type="button"
                class="btn btn-lg btn-primary mt-20"
                id="add-option-btn"
                @click="addDeliveryRow()"
            >
                {{ __('admin::app.catalog.attributes.add-option-btn-title') }}
            </button>
        </div>
    </script>

    <script>
        Vue.component('delivery-wrapper', {

            template: '#delivery-template',

            inject: ['$validator'],

            data: function() {
                return {
                    optionRowCount: 1,

                    deliveryRows: @json($deliveryData),
                }
            },

            methods: {
                addDeliveryRow: function () {
                    const rowCount = this.optionRowCount++;
                    const id = 'delivery_' + rowCount;
                    let row = {'id': id};

                    this.deliveryRows.push(row);
                },

                removeRow: function (row) {
                    const index = this.deliveryRows.findIndex(item => item.id === row.id);
                    console.log(index);
                    if (index !== -1) {
                        this.deliveryRows.splice(index, 1);
                    }
                },

                inputName: function (row) {
                    return 'delivery[' + row.id + ']';
                },       
            },

        })
    </script>
@endpush

