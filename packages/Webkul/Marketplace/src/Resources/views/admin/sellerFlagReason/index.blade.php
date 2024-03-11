@extends('marketplace::admin.layouts.content')

@section('page_title')
    {{ __('marketplace::app.admin.sellers.flag.title') }}
@stop

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('marketplace::app.admin.sellers.flag.title') }}</h1>
            </div>

            <div class="page-action">
                <a href="{{route('marketplace.admin.seller.flag.reason.create')}}" class="btn btn-lg btn-primary" > {{ __('marketplace::app.admin.sellers.flag.add-btn-title') }}</a>
            </div>
        </div>

        <div class="page-content">

            <datagrid-plus src="{{ route('marketplace.admin.seller.flag.reason.index') }}"></datagrid-plus>

        </div>
    </div>

@stop