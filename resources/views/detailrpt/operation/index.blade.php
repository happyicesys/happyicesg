@inject('profiles', 'App\Profile')
@inject('customers', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('custcategoryGroups', 'App\CustcategoryGroup')

@extends('template')
@section('title')
{{ $DETAILRPT_TITLE }}
@stop
@section('content')

    <div class="row">
        <a class="title_hyper pull-left" href="/operation/worksheet"><h1>Operation Worksheet - {{ $DETAILRPT_TITLE }} <i class="fa fa-book"></i></h1></a>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Operation Worksheet
        </div>
        <div class="panel-body" style="font-size: 13px;">
            @include('detailrpt.operation.operation_worksheet')
        </div>
    </div>
@stop