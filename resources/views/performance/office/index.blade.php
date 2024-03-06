@inject('tasks', 'App\Task')

@extends('template')
@section('title')
Task Planner
@stop
@section('content')

    <div ng-app="app" ng-controller="performanceOfficeIndexController">

    <div class="row">
        <a class="title_hyper pull-left" href="/vm"><h1>Task Planner (Office) <i class="fa fa-list"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
    </div>

        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="pull-right">
                            <a href="/performance/office/create" class="btn btn-success">
                                <i class="fa fa-plus"></i>
                                <span class="hidden-xs"> New Task </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                  <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('date', 'Date', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                        <datepicker>
                            <input
                                type = "text"
                                class = "form-control input-sm"
                                placeholder = "Date"
                                ng-model = "search.date"
                                ng-change = "dateChange(search.date)"
                            />
                        </datepicker>
                        <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('date', search.date)"></span>
                        <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('date', search.date)"></span>
                    </div>
                  </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('name', 'Name', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('name', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.name',
                                                                'placeholder'=>'Name',
                                                                'ng-change'=>'searchDB()',
                                                                'ng-model-options'=>'{ debounce: 500 }'
                                                            ])
                            !!}
                        </div>
                    </div>
                    {{-- <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
                            <select name="status" id="status" class="select form-control" ng-model="search.status" ng-change="searchDB()">
                                <option value="">All</option>
                                @foreach($tasks::STATUSES as $indexStatus => $status)
                                    <option value="{{$indexStatus}}">
                                        {{$status}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}
                </div>


                <div class="row">
                    <div class="col-md-8 col-sm-6 col-xs-12">
                        @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                            <button class="btn btn-primary" ng-click="exportData($event)">Export Excel</button>
                        @endif
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12 text-right">
                        {{-- <div class="row">
                            <label for="display_num">Display</label>
                            <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
                                <option ng-value="100">100</option>
                                <option ng-value="200">200</option>
                                <option ng-value="All">All</option>
                            </select>
                            <label for="display_num2" style="padding-right: 20px">per Page</label>
                        </div> --}}
                        <div class="row">
                            <label class="" style="padding-right:18px;" for="totalnum">Showing  entries</label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" id="exportable" style="padding-top:20px;">
                    <table class="table table-list-search table-hover table-bordered">
                        <tr>
                            <th class="col-md-1 text-center" ng-repeat="header in headers" ng-style="{'background-color':'@{{header.color}}'}">
                                @{{header.shortDate}}
                            </th>
                        </tr>
                        <tr>
                            <th class="col-md-1 text-center" ng-repeat="header in headers" ng-style="{'background-color':'@{{header.color}}'}">
                                @{{header.day}}
                            </th>
                        </tr>
                        <tbody>
                            <tr ng-repeat="">
                                <td class="col-md-1 text-center" ng-repeat="(index, task) in contents" ng-if="index == header.date">
                                    @{{task.name}}
                                    {{-- <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete($event, vm.id)"><i class="fa fa-times"></i></button> --}}
                                </td>
                            </tr>
                            <tr ng-if="!contents || contents.length == 0">
                                <td colspan="18" class="text-center">No Records Found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>

    <script src="/js/performance_office_index.js"></script>
@stop