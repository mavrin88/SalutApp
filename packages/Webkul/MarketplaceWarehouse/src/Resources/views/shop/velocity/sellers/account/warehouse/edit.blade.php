@extends('marketplace::shop.layouts.account')

@section('page_title')
    {{ __('marketplace_warehouse::app.shop.warehouse.edit-title') }}
@endsection

@section('content')
    <div class="account-layout right m10">

        {!! view_render_event('marketplace_warehouse.warehouse.edit.before', ['warehouse' => $warehouse]) !!}

        <form method="POST" action={{ route('marketplace_warehouse.user.warehouse.update', $warehouse->id) }} enctype="multipart/form-data"
            class="account-table-content">

            <div class="account-head mb-10">

                <span class="account-heading">
                    {{ __('marketplace_warehouse::app.shop.warehouse.edit-title') }}
                </span>

                <div class="account-action">
                    <button type="submit" class="btn btn-primary">
                        {{ __('marketplace_warehouse::app.shop.warehouse.save-title') }}
                    </button>
                </div>

                <div class="horizontal-rule"></div>

            </div>

            <div class="account-table-content">

                @csrf()

                <input type="hidden" name="_method" value="PUT">

                {!! view_render_event(
                    'marketplace_warehouse.warehouse.edit_warehouse_form_accordian.general.before',
                    ['warehouse' => $warehouse],
                ) !!}

                <accordian :title="'{{ __('marketplace_warehouse::app.shop.warehouse.general') }}'" :active="true">
                    <div slot="header">
                        {{ __('marketplace_warehouse::app.shop.warehouse.general') }}
                        <i class="icon expand-icon right"></i>
                    </div> 
                
                    <div slot="body">

                        <div class="control-group" :class="[errors.has('warehouse_name') ? 'has-error' : '']">
                            <label for="warehouse_name" class="required">{{ __('marketplace_warehouse::app.shop.warehouse.warehouse-name') }}</label>
                            <input type="text" class="control" name="warehouse_name" v-validate="'required|max:200'" value="{{ $warehouse->warehouse_name }}" data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.warehouse-name') }}&quot;"/>
                            <span class="control-error" v-if="errors.has('warehouse_name')">@{{ errors.first('warehouse_name') }}</span>
                        </div>

                        <div class="control-group" :class="[errors.has('warehouse_description') ? 'has-error' : '']">
                            <label for="warehouse_description" class="required">{{ __('marketplace_warehouse::app.shop.warehouse.warehouse-desc') }}</label>
                            <textarea type="text" class="control" name="warehouse_description" v-validate="'required|max:500'" data-vv-as="&quot;{{ __('marketplace_warehouse::app.shop.warehouse.warehouse-desc') }}&quot;" style="height: 100px;">{{ $warehouse->warehouse_description }}
                            </textarea>
                            <span class="control-error" v-if="errors.has('warehouse_description')">@{{ errors.first('warehouse_description') }}</span>
                        </div>

                    </div>
                </accordian>

                {!! view_render_event(
                    'marketplace_warehouse.warehouse.edit_warehouse_form_accordian.general.after',
                    ['warehouse' => $warehouse],
                ) !!}
               
            </div>

        </form>

        {!! view_render_event('marketplace_warehouse.warehouse.edit.after', ['warehouse' => $warehouse]) !!}

    </div>

@stop


