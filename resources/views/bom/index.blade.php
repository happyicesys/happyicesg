@extends('template')
@section('title')
{{ $BOM_TITLE }}
@stop
@section('content')

    <div class="row">
        <a class="title_hyper pull-left" href="/bom">
            <h1>B<i class="fa fa-cogs"></i>M</h1>
        </a>
    </div>
    <div ng-app="app" ng-cloak>
        <div class="panel panel-default">
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#component" role="tab" data-toggle="tab">BOM Template</a></li>
                    {{-- <li class="active"><a href="#part" role="tab" data-toggle="tab">Part</a></li> --}}
                    {{-- <li><a href="#template" role="tab" data-toggle="tab">Template Definition</a></li> --}}
                    <li><a href="#vending" role="tab" data-toggle="tab">BOM Comparison</a></li>
                    <li><a href="#maintenance" role="tab" data-toggle="tab">Maintenance Record</a></li>
                    <li><a href="#category" role="tab" data-toggle="tab">Settings</a></li>
                </ul>
            </div>

            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="component">
                        @include('bom.component')
                    </div>
{{--                     <div class="tab-pane active" id="part">
                        @include('bom.part')
                    </div>
                    <div class="tab-pane" id="template">
                        @include('bom.template')
                    </div> --}}
                    <div class="tab-pane" id="vending">
                        @include('bom.vending')
                    </div>
                    <div class="tab-pane" id="maintenance">
                        @include('bom.maintenance')
                    </div>
                    <div class="tab-pane" id="category">
                        @include('bom.category')
                    </div>
                </div>
            </div>
        </div>


    <script src="/js/bom_index.js"></script>
@stop