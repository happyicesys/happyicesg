@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

<div ng-app="app" ng-controller="locateController" class="row" style="padding: 100px 0px 30px 0px;">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="" id="fvm">
            <div id="fvm-map"></div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#fvm" role="tab" data-toggle="tab" style="color: black;">Fun Vending Machine</a></li>
                    <li><a href="#dvm" role="tab" data-toggle="tab" style="color: black;">Direct Vending Machine</a></li>
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="fvm">
                        <div id="fvmMap"></div>
                    </div>
                    <div class="tab-pane" id="dvm">
                        <div id="dvmMap"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/client_locate.js"></script>

@stop