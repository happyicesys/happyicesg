@inject('profiles', 'App\Profile')
@inject('people', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('franchisees', 'App\User')

@extends('template')
@section('title')
SIM Card
@stop
@section('content')

    <div ng-app="app" ng-controller="simcardController">

    <div class="row">
        <a class="title_hyper pull-left" href="/simcard"><h1>SIM Card <i class="fa fa-phone-square"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
    </div>

        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="pull-right">
                            <a href="/simcard/create" class="btn btn-success">
                                <i class="fa fa-plus"></i>
                                Add SIM Card
                            </button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('phone_no', 'Phone Num', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('phone_no', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.phone_no',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Phone Num',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ])
                        !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('telco_name', 'Telco Name', ['class'=>'control-label search-title']) !!}
                        {!! Form::select('telco_name', [''=>'All', 'Singtel_IMSI'=>'Singtel (IMSI)', 'Starhub_ICCID'=>'Starhub (ICCID)', 'M1'=>'M1', 'Redone'=>'Redone'], null,
                            [
                            'class'=>'select2 form-control',
                            'ng-model'=>'search.telco_name',
                            'ng-change'=>'searchDB()'
                            ])
                        !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('simcard_no', 'SimCard No', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('simcard_no', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.simcard_no',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'SimCard No',
                                                        'ng-model-options'=>'{ debounce: 500 }'
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
                            <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
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

                <div class="table-responsive" id="exportable" style="padding-top:20px;">
                    <table class="table table-list-search table-hover table-bordered">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('phone_no')">
                                Phone Number
                                <span ng-if="search.sortName == 'phone_no' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'phone_no' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('telco_name')">
                                Telco Name
                                <span ng-if="search.sortName == 'telco_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'telco_name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('simcard_no')">
                                SIM Card No
                                <span ng-if="search.sortName == 'simcard_no' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'simcard_no' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('updated_by')">
                                Last Modified By
                                <span ng-if="search.sortName == 'updated_by' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'updated_by' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('simcards.updated_at')">
                                Last Modified Time
                                <span ng-if="search.sortName == 'simcards.updated_at' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'simcards.updated_at' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                Action
                            </th>
                        </tr>
                        <tbody>
                            <tr dir-paginate="simcard in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">
                                <td class="col-md-1 text-center">@{{ $index + indexFrom }} </td>
                                <td class="col-md-2 text-center">
                                    @{{ simcard.phone_no }}
                                </td>
                                <td class="col-md-2 text-center">
                                    <a href="/simcard/@{{simcard.id}}/edit" ng-if="simcard.id">
                                        @{{ simcard.telco_name }}
                                    </a>
                                </td>
                                <td class="col-md-2 text-center">
                                    @{{ simcard.simcard_no }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ simcard.updater }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ simcard.updated_at }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @if(auth()->user()->hasRole('admin'))
                                        <button class="btn btn-danger btn-sm btn-delete" ng-click="removeSimcard($event, simcard.id)"><i class="fa fa-times"></i></button>
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

    <script src="/js/simcard_index.js"></script>
@stop