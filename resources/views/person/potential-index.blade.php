@inject('custcategories', 'App\Custcategory')
@inject('custcategoryGroups', 'App\CustcategoryGroup')
@inject('freezers', 'App\Freezer')
@inject('profiles', 'App\Profile')
@inject('franchisees', 'App\User')
@inject('persontags', 'App\Persontag')
@inject('users', 'App\User')
@inject('zones', 'App\Zone')

@extends('template')
<style>
    .person-color {
        color:  #6D3A9C;
    }
</style>

@section('title')
{{ $PERSON_TITLE }}
@stop
@section('content')
    <div class="row">
        <a class="title_hyper pull-left person-color" href="/person"><h1>Potential Customer <i class="fa fa-users"></i></h1></a>
    </div>
    <div ng-app="app">
        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="active"><a href="#list" role="tab" data-toggle="tab">Customer List</a></li>
                    {{-- <li><a href="#creation" role="tab" data-toggle="tab">Acc Holder</a></li> --}}
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="list" ng-controller="personController">
                        @include('person.potential-index-list')
                    </div>
                    {{-- <div class="tab-pane" id="creation" ng-controller="creationController">
                        @include('person.creation')
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <script src="/js/person-index.js"></script>
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