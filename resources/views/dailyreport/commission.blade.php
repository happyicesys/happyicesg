@inject('profiles', 'App\Profile')
@inject('customers', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('users', 'App\User')

@extends('template')
@section('title')
Daily Report
@stop
@section('content')

    <div ng-app="app">
        <div class="row">
            <a class="title_hyper pull-left" href="/dailyreport/index"><h1> Performance <i class="fa fa-flag"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
        </div>
        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#daily_report" role="tab" data-toggle="tab"> Driver Commission</a></li>
                    {{-- <li><a href="#monthly_summary" role="tab" data-toggle="tab">Monthly Summary</a></li> --}}
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="daily_report">
                        @include('dailyreport.dailyreport')
                    </div>
                    <div class="tab-pane" id="monthly_summary">
                        {{-- @include('detailrpt.vending.monthly_summary') --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/dailyreport.js"></script>
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