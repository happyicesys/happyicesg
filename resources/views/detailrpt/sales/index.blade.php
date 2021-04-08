@inject('profiles', 'App\Profile')
@inject('customers', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('custcategoryGroups', 'App\CustcategoryGroup')
@inject('franchisees', 'App\User')
@inject('items', 'App\Item')
@inject('users', 'App\User')
@inject('zones', 'App\Zone')

@extends('template')
@section('title')
{{ $DETAILRPT_TITLE }}
@stop
@section('content')

    <div ng-app="app">
        <div class="row">
            <a class="title_hyper pull-left" href="/detailrpt/account"><h1>Sales - {{ $DETAILRPT_TITLE }} <i class="fa fa-book"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
        </div>
        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician') and !auth()->user()->hasRole('driver-supervisor'))
                        <li class="active"><a href="#cust_detail" role="tab" data-toggle="tab">Customer Detail</a></li>
                        @if(!auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                        <li><a href="#cust_summary" role="tab" data-toggle="tab">Customer Summary</a></li>
                        <li><a href="#cust_summary_group" role="tab" data-toggle="tab">Customer Summary(Group)</a></li>
                        <li><a href="#monthly_report" role="tab" data-toggle="tab">Monthly Report</a></li>
                        <li><a href="#product_detail_month" role="tab" data-toggle="tab">Product Detail (Month)</a></li>
                        @endif
                    @endif
                    @if(!auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                        <li><a href="#product_detail_day" role="tab" data-toggle="tab">Product Detail (Day)</a></li>
                    @endif
                    {{-- <li><a href="#invoice_breakdown" role="tab" data-toggle="tab">Invoice Breakdown</a></li> --}}
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician') and !auth()->user()->hasRole('driver-supervisor'))
                    <div class="tab-pane active" id="cust_detail">
                        @include('detailrpt.sales.cust_detail')
                    </div>
                    <div class="tab-pane" id="cust_summary">
                        @include('detailrpt.sales.cust_summary')
                    </div>
                    <div class="tab-pane" id="cust_summary_group">
                        @include('detailrpt.sales.cust_summary_group')
                    </div>
                    <div class="tab-pane" id="monthly_report">
                        @include('detailrpt.sales.monthly_report')
                    </div>
                    <div class="tab-pane" id="product_detail_month">
                        @include('detailrpt.sales.product_detail_month')
                    </div>
                    @endif
                    <div class="tab-pane {{(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician') or auth()->user()->hasRole('driver-supervisor')) ? 'active' : ''}}" id="product_detail_day">
                        @include('detailrpt.sales.product_detail_day')
                    </div>
{{--                     <div class="tab-pane" id="invoice_breakdown">
                        @include('detailrpt.sales.invoice_breakdown')
                    </div> --}}
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