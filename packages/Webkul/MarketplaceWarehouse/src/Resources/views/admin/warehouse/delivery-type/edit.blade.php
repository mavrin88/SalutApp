@extends('marketplace::admin.layouts.content')

@section('page_title')
    {{ __('marketplace_warehouse::app.admin.delivery-type.edit-title') }}
@stop

@section('content')

    <div class="content">
        <form method="POST" action="{{ route('admin.warehouse.delivery_type.update', $deliveryType->id) }}" @submit.prevent="onSubmit">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ route('admin.dashboard.index') }}';"></i>

                        {{ __('marketplace_warehouse::app.admin.delivery-type.edit-title') }}
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('marketplace_warehouse::app.admin.layouts.create-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">

                <div class="form-container">
                    @csrf()

                    {!! view_render_event('marketplace_warehouse.admin.delivery_type.edit.before') !!}

                        <div slot="body">
                            <div class="control-group" :class="[errors.has('title') ? 'has-error' : '']">
                                <label for="page_title" class="required">{{ __('marketplace_warehouse::app.admin.cities.title') }}</label>

                                <input type="text" class="control" name="title" v-validate="'required'" value="{{ old('title')?: $deliveryType->title }}" data-vv-as="&quot;{{ __('marketplace_warehouse::app.admin.delivery-type.title')  }}&quot;">

                                <span class="control-error" v-if="errors.has('title')">@{{ errors.first('title') }}</span>
                            </div>
                        </div>

                    {!! view_render_event('marketplace_warehouse.admin.delivery_type.edit.after') !!}
                </div>
            </div>
        </form>
    </div>

@stop

