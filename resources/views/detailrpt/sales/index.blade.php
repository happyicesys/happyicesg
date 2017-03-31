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
            <a class="title_hyper pull-left" href="/detailrpt/account"><h1>Sales - {{ $DETAILRPT_TITLE }} <i class="fa fa-book"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#cust_detail" role="tab" data-toggle="tab">Customer Detail</a></li>
                    <li><a href="#cust_summary" role="tab" data-toggle="tab">Customer Summary</a></li>
                    <li><a href="#product_detail_month" role="tab" data-toggle="tab">Product Detail(Month)</a></li>
                    <li><a href="#product_detail_day" role="tab" data-toggle="tab">Product Detail(Day)</a></li>
                    <li><a href="#invoice_breakdown" role="tab" data-toggle="tab">Invoice Breakdown</a></li>
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="cust_detail">
                        @include('detailrpt.sales.cust_detail')
                    </div>
                    <div class="tab-pane" id="cust_summary">
                        @include('detailrpt.sales.cust_summary')
                    </div>
                    <div class="tab-pane" id="product_detail_month">
                        @include('detailrpt.sales.product_detail_month')
                    </div>
                    <div class="tab-pane" id="product_detail_day">
                        @include('detailrpt.sales.product_detail_day')
                    </div>
                    <div class="tab-pane" id="invoice_breakdown">
                        @include('detailrpt.sales.invoice_breakdown')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/sales_detailrpt.js"></script>
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