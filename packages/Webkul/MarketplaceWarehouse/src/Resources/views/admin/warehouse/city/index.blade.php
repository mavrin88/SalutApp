@extends('marketplace::admin.layouts.content')

@section('page_title')
    {{ __('marketplace_warehouse::app.admin.cities.title') }}
@stop

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('marketplace_warehouse::app.admin.cities.title') }}</h1>
            </div>

            <div class="page-action">
                <a href="{{route('admin.warehouse.city.create')}}" class="btn btn-lg btn-primary" > {{ __('marketplace_warehouse::app.admin.cities.add-btn-title') }}</a>
            </div>
        </div>

        <div class="page-content">

            <datagrid-plus src="{{ route('admin.warehouse.cities.index') }}"></datagrid-plus>

        </div>
    </div>

@stop