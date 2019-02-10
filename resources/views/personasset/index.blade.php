@inject('people', 'App\Person')

@extends('template')
@section('title')
{{ $PERSONASSET_TITLE }}
@stop
@section('content')

    <div ng-app="app">
        <div class="row">
            <a class="title_hyper pull-left" href="/personasset"><h1>{{ $PERSONASSET_TITLE }}  <i class="fa fa-fw fa-cubes"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
        </div>
        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#movement" role="tab" data-toggle="tab">Asset Movement</a></li>
                    <li><a href="#category" role="tab" data-toggle="tab">Asset Category</a></li>
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="movement">
                        @include('personasset.movement')
                    </div>
                    <div class="tab-pane" id="category">
                        @include('personasset.category')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/personasset.js"></script>
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