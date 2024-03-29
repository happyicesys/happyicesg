@inject('custcategories', 'App\Custcategory')
@inject('profiles', 'App\Profile')
@inject('users', 'App\User')

@extends('template')

@section('title')
GT Sales Lead
@stop
@section('content')

    <div class="row">
        <a class="title_hyper pull-left" href="/potential-customer"><h1>GT Sales Lead <i class="fa fa-address-card-o"></i></h1></a>
    </div>
    <div ng-app="app">
        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#potential_customer" role="tab" data-toggle="tab">GT Sales Lead</a></li>
                    <li><a href="#performance" role="tab" data-toggle="tab">Performance</a></li>
                    <li><a href="#meeting_minute" role="tab" data-toggle="tab">Meeting Minutes</a></li>
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="potential_customer" ng-controller="potentialCustomerController">
                        @include('potential-customer.potential')
                    </div>
                    <div class="tab-pane" id="performance" ng-controller="performanceController">
                        @include('potential-customer.performance')
                    </div>
                    <div class="tab-pane" id="meeting_minute" ng-controller="meetingMinuteController">
                        @include('potential-customer.meeting-minute')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/js/potential-customer.js"></script>
    {{-- <script src="/js/meeting-minutes.js"></script> --}}
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