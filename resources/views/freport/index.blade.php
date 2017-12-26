@extends('template')
@section('title')
{{ $FRANCHISE_RPT }}
@stop
@section('content')

<div class="row">
    <a class="title_hyper pull-left" href="/franrpt"><h1>F-Report</h1></a>
</div>
    <div ng-app="app" ng-cloak>
        <div class="panel panel-default">
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#invoice_breakdown" role="tab" data-toggle="tab">Invoice Breakdown</a></li>
                    <li><a href="#analog_difference" role="tab" data-toggle="tab">Analog Difference</a></li>
                </ul>
            </div>

            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="invoice_breakdown">
                        @include('freport.invbreakdown_detail')
                    </div>
                    <div class="tab-pane" id="analog_difference">
                        @include('freport.analog_difference')
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="/js/freport.js"></script>
@stop