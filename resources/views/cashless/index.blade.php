@inject('profiles', 'App\Profile')
@inject('people', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('franchisees', 'App\User')
@inject('cashlessTerminals', 'App\CashlessTerminal')

@extends('template')
@section('title')
Cashless Terminal
@stop
@section('content')

    <div ng-app="app" ng-controller="cashlessController">

    <div class="row">
        <a class="title_hyper pull-left" href="/cashless"><h1>Cashless Terminal <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
    </div>

        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="pull-right">
                            <button class="btn btn-success" data-toggle="modal" data-target="#cashless_modal" ng-click="createCashlessModal()">
                                <i class="fa fa-plus"></i>
                                Add Cashless Terminal
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('provider_id', 'Terminal Provider', ['class'=>'control-label search-title']) !!}
                        {!! Form::select('provider_id',
                            [
                                ''=>'All',
                                '1'=>'Nayax',
                                '2'=>'Castle',
                                '3'=>'XVend',
                                '4'=>'Auresys',
                                '5'=>'Beeptech'
                            ], null, [
                                'class'=>'select2 form-control',
                                'ng-model' => 'search.provider_id',
                                'ng-change'=>'searchDB()'
                                ])
                        !!}
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-8 col-sm-6 col-xs-12">
                        @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                            <button class="btn btn-primary" ng-click="exportData($event)">Export Excel</button>
                        @endif
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12 text-right">
                        <div class="row">
                            <label for="display_num">Display</label>
                            <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='All'" ng-change="pageNumChanged()">
                                <option ng-value="100">100</option>
                                <option ng-value="200">200</option>
                                <option ng-value="All">All</option>
                            </select>
                            <label for="display_num2" style="padding-right: 20px">per Page</label>
                        </div>
                        <div class="row">
                            <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" id="exportableCashless" style="padding-top:20px;">
                    <table class="table table-list-search table-hover table-bordered">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('provider_name')">
                                Terminal Provider
                                <span ng-if="search.sortName == 'provider_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'provider_name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
                                Terminal ID
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('start_date')">
                                Start Date
                                <span ng-if="search.sortName == 'start_date' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'start_date' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                Action
                            </th>
                        </tr>
                        <tbody>
                            <tr dir-paginate="cashless in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">
                                <td class="col-md-1 text-center">@{{ $index + indexFrom }} </td>
                                <td class="col-md-2 text-center">
                                    <a href="#" data-toggle="modal" data-target="#cashless_modal" ng-click="editCashlessModal(cashless)">
                                        @{{ cashless.provider_name }}
                                    </a>
                                </td>
                                <td class="col-md-2 text-center">
                                    @{{ cashless.terminal_id }}
                                </td>
                                <td class="col-md-2 text-center">
                                    @{{ cashless.start_date }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @if(auth()->user()->hasRole('admin'))
                                        <button class="btn btn-danger btn-sm btn-delete" ng-click="removeCashless($event, cashless.id)"><i class="fa fa-times"></i></button>
                                    @endif
                                </td>
                            </tr>
                            <tr ng-if="!alldata || alldata.length == 0">
                                <td colspan="18" class="text-center">No Records Found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
                </div>
        </div>
    </div>

    <div class="modal fade" id="cashless_modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">
                    @{{form.id ? 'Edit Cashless Terminal' : 'Create Cashless Terminal'}}
                </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Terminal Provider
                            </label>
                            <select name="provider_id" class="select form-control" ng-model="form.provider_id">
                                <option value="">None</option>
                                @foreach($cashlessTerminals::PROVIDERS as $index => $provider)
                                    <option value="{{$index}}" ng-selected="form.provider_id == {{$index}}">
                                        {{$provider}}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Terminal ID
                            </label>
                            <input type="text" name="terminal_id" class="form-control" ng-model="form.terminal_id">
                        </div>

                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            {!! Form::label('form.start_date', 'Start Date', ['class'=>'control-label']) !!}
                            <datepicker>
                                <input
                                    name = "start_date"
                                    type = "text"
                                    class = "form-control input-sm"
                                    placeholder = "Start Date"
                                    ng-model = "form.start_date"
                                    ng-change = "onStartDateChanged(form.start_date)"
                                />
                            </datepicker>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" ng-click="createCashless()" data-dismiss="modal" ng-if="!form.id">Create</button>
                    <button type="button" class="btn btn-success" ng-click="editCashless(form.id)" data-dismiss="modal" ng-if="form.id">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/js/cashless_index.js"></script>
@stop