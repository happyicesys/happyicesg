@extends('template')
@section('title')
{{ $FRANCHISE_RPT }}
@stop
@section('content')

<div class="row">
    <a class="title_hyper pull-left" href="/franrpt"><h1>F-Report <i class="fa fa-area-chart"></i></h1></a>
</div>
    <div ng-app="app" ng-cloak>
        <div class="panel panel-default">
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#analog_difference" role="tab" data-toggle="tab">Analog Difference</a></li>
                    <li><a href="#invoice_breakdown" role="tab" data-toggle="tab">Invoice Breakdown</a></li>
                    {{-- <li><a href="#variance_management" role="tab" data-toggle="tab">Variance Management</a></li> --}}
                </ul>
            </div>

            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="analog_difference">
                        @include('freport.analog_difference')
                    </div>
                    <div class="tab-pane" id="invoice_breakdown">
                        @include('freport.invbreakdown_detail')
                    </div>
                    <div class="tab-pane" id="variance_management">
                        @include('freport.variance_management')
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="/js/freport.js"></script>
@stop