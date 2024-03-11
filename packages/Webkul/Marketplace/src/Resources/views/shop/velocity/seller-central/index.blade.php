@extends('marketplace::shop.layouts.master')

@section('page_title')
    {{ __('marketplace::app.shop.marketplace.title') }}
@stop

@section('content-wrapper')
    <div class="main seller-central-container">
        @include('marketplace::shop.seller-central.layout')
    </div>

@endsection
