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
            <a class="title_hyper pull-left" href="/detailrpt/account"><h1>Account - {{ $DETAILRPT_TITLE }} <i class="fa fa-book"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#cust_detail" role="tab" data-toggle="tab">Customer Detail</a></li>
                    <li><a href="#cust_outstanding" role="tab" data-toggle="tab">Customer Outstanding Summary</a></li>
                    <li><a href="#payment_detail" role="tab" data-toggle="tab">Payment Detail</a></li>
                    <li><a href="#payment_summary" role="tab" data-toggle="tab">Payment Summary</a></li>
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="cust_detail">
                        @include('detailrpt.account.cust_detail')
                    </div>
                    <div class="tab-pane" id="cust_outstanding">
                        @include('detailrpt.account.cust_outstanding')
                    </div>
                    <div class="tab-pane" id="payment_detail">
                        @include('detailrpt.account.payment_detail')
                    </div>
                    <div class="tab-pane" id="payment_summary">
                        @include('detailrpt.account.payment_summary')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/account_detailrpt.js"></script>
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