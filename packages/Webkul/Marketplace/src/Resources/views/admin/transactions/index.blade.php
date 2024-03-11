@extends('marketplace::admin.layouts.content')

@section('page_title')
    {{ __('marketplace::app.admin.transactions.title') }}
@stop

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('marketplace::app.admin.transactions.title') }}</h1>
            </div>
            
            <div class="page-action">
            </div>
        </div>

        <div class="page-content">

            <datagrid-plus src="{{ route('admin.marketplace.transactions.index') }}"></datagrid-plus>

        </div>
    </div>

@stop
