@inject('profiles', 'App\Profile')
@inject('customers', 'App\Person')
@inject('custcategories', 'App\Custcategory')

@extends('template')
@section('title')
{{ $DETAILRPT_TITLE }}
@stop
@section('content')

    <div ng-app="app">
        <div class="row">
            <a class="title_hyper pull-left" href="/detailrpt/vending"><h1>Vending - {{ $DETAILRPT_TITLE }} <i class="fa fa-book"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
        </div>
        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                Operation Worksheet
            </div>
            <div class="panel-body">
                @include('detailrpt.operation.operation_worksheet')
            </div>
        </div>
    </div>
    <script src="/js/operation_detailrpt.js"></script>
@stop