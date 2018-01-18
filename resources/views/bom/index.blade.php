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
                    <li><a href="#category" role="tab" data-toggle="tab">Category</a></li>
                    <li><a href="#component" role="tab" data-toggle="tab">Component</a></li>
                    <li class="active"><a href="#part" role="tab" data-toggle="tab">Part</a></li>
                </ul>
            </div>

            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane" id="category">
                        @include('bom.category')
                    </div>
                    <div class="tab-pane" id="component">
                        @include('bom.component')
                    </div>
                    <div class="tab-pane active" id="part">
                        @include('bom.part')
                    </div>
                </div>
            </div>
        </div>


    <script src="/js/bom_index.js"></script>
@stop