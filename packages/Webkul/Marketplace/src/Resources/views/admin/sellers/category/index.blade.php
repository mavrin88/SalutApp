@extends('marketplace::admin.layouts.content')

@section('page_title')
    {{ __('marketplace::app.admin.sellers.category.title') }}
@stop

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('marketplace::app.admin.sellers.category.title') }}</h1>
            </div>

            <div class="page-action">
                <a href="{{route('admin.marketplace.seller.category.create')}}" class="btn btn-lg btn-primary">{{ __('marketplace::app.admin.sellers.category.create') }}</a>
            </div>
        </div>

        <div class="page-content">

            <datagrid-plus src="{{ route('admin.marketplace.seller.category.index') }}"></datagrid-plus>

        </div>
    </div>

@stop
