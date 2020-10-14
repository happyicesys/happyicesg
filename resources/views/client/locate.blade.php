@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

<div class="row" style="padding: 50px 0px 30px 0px;">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#fvm" role="tab" data-toggle="tab" style="color: black;">Fun Vending Machine</a></li>
                    <li><a href="#dvm" role="tab" data-toggle="tab" style="color: black;">Direct Vending Machine</a></li>
                </ul>
            </div>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane active" id="fvm">
                    <div id="map-fvm"></div>
                </div>
                <div class="tab-pane active" id="dvm">
                    <div id="map-dvm"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/client_locate.js"></script>

@stop