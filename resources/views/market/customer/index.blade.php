@extends('template')
@section('title')
Customers
@stop
@section('content')

    <div class="row">
    <a class="title_hyper pull-left" href="/market/customer"><h1>Customers <i class="fa fa-male"></i></h1></a>
    </div>
    <div ng-app="app" ng-controller="customerController">
        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#cust_profile" role="tab" data-toggle="tab"> Customer Profile</a></li>
                    @if(Auth::user()->hasRole('admin'))
                        <li><a href="#cust_trans" role="tab" data-toggle="tab"> Customer Transaction</a></li>
                    @endif
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="cust_profile">
                        @include('market.customer.custprofile_template')
                    </div>
                    <div class="tab-pane" id="cust_trans">
                        @include('market.customer.custtrans_template')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/js/customer.js"></script>
@stop