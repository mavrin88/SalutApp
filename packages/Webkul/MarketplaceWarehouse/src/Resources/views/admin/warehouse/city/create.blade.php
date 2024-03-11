@extends('marketplace::admin.layouts.content')

@section('page_title')
    {{ __('marketplace_warehouse::app.admin.cities.add-btn-title') }}
@stop

@section('content')

    <div class="content">
        <form method="POST" action="{{ route('admin.warehouse.city.store') }}" @submit.prevent="onSubmit">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ route('admin.dashboard.index') }}';"></i>

                        {{ __('marketplace_warehouse::app.admin.cities.add-btn-title') }}
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

                    {!! view_render_event('marketplace_warehouse.admin.warehouse.city.before') !!}

                        <div slot="body">
                            <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="page_title" class="required">{{ __('marketplace_warehouse::app.admin.cities.title') }}</label>

                                <input type="text" class="control" name="name" v-validate="'required'" value="{{ old('name') }}" data-vv-as="&quot;{{ __('marketplace_warehouse::app.admin.cities.title') }}&quot;">

                                <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>
                            </div>
                        </div>

                    {!! view_render_event('marketplace_warehouse.admin.warehouse.city.after') !!}
                </div>
            </div>
        </form>
    </div>

@stop

