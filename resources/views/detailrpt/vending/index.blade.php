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
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#generate_invoice" role="tab" data-toggle="tab">Generate Invoice</a></li>
                    {{-- <li><a href="#monthly_summary" role="tab" data-toggle="tab">Monthly Summary</a></li> --}}
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="generate_invoice">
                        @include('detailrpt.vending.generate_invoice')
                    </div>
                    <div class="tab-pane" id="monthly_summary">
                        {{-- @include('detailrpt.vending.monthly_summary') --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/vending_detailrpt.js"></script>
    <script>
        $(function() {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                localStorage.setItem('lastTab', $(this).attr('href'));
            });
            var lastTab = localStorage.getItem('lastTab');
            if (lastTab) {
                $('[href="' + lastTab + '"]').tab('show');
            }
        });
    </script>
@stop