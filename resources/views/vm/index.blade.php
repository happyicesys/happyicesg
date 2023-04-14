@inject('profiles', 'App\Profile')
@inject('people', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('franchisees', 'App\User')
@inject('rackingConfigs', 'App\RackingConfig')

@extends('template')
@section('title')
Vending Machine
@stop
@section('content')

    <div ng-app="app" ng-controller="vmController">

    <div class="row">
        <a class="title_hyper pull-left" href="/vm"><h1>Vending Machine <i class="fa fa-plug"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
    </div>

        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="pull-right">
                            <a href="/vm/create" class="btn btn-success">
                                <i class="fa fa-plus"></i>
                                <span class="hidden-xs"> New Vending Machine </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('type', 'Type', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('type', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.type',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Vend ID',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ])
                        !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('id', 'Cust ID', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('id', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.cust_id',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Cust ID',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ])
                        !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('company', 'ID Name', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('company', null,
                                                        [
                                                            'class'=>'form-control input-sm',
                                                            'ng-model'=>'search.company',
                                                            'ng-change'=>'searchDB()',
                                                            'placeholder'=>'ID Name',
                                                            'ng-model-options'=>'{ debounce: 500 }'
                                                        ])
                        !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('custcategory', 'Category', ['class'=>'control-label search-title']) !!}
                        {!! Form::select('custcategory', [''=>'All']+$custcategories::orderBy('name')->pluck('name', 'id')->all(), null,
                            [
                            'class'=>'select form-control',
                            'ng-model'=>'search.custcategory',
                            'ng-change'=>'searchDB()'
                            ])
                        !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('desc', 'Desc', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('desc', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.desc',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Vend ID',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ])
                        !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('serial_no', 'Serial No', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('serial_no', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.serial_no',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Vend ID',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ])
                        !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('racking_config_id', 'Racking Config', ['class'=>'control-label search-title']) !!}
                        {!! Form::select('racking_config_id', [''=>'All']+$rackingConfigs::orderBy('name')->pluck('name', 'id')->all(), null,
                            [
                            'class'=>'select form-control',
                            'ng-model'=>'search.racking_config_id',
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
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('serial_no')">
                                Serial No
                                <span ng-if="search.sortName == 'serial_no' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'serial_no' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('type')">
                                Type
                                <span ng-if="search.sortName == 'type' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'type' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('racking_config_id')">
                                Racking Config
                                <span ng-if="search.sortName == 'racking_config_id' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'racking_config_id' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('telco_name')">
                                Sim Card
                                <span ng-if="search.sortName == 'telco_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'telco_name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('provider_name')">
                                Cashless
                                <span ng-if="search.sortName == 'provider_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'provider_name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('desc')">
                                Desc
                                <span ng-if="search.sortName == 'desc' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'desc' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('cust_id')">
                                Current Cust
                                <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('custcategory')">
                                CustCat
                                <span ng-if="search.sortName == 'custcategory' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'custcategory' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                Action
                            </th>
                        </tr>
                        <tbody>
                            <tr dir-paginate="vm in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">
                                <td class="col-md-1 text-center">
                                    <a href="/vm/@{{ vm.id }}/edit">
                                        @{{ $index + indexFrom }}
                                    </a>
                                </td>
                                <td class="col-md-1 text-center">
                                    <a href="/vm/@{{ vm.id }}/edit">
                                        @{{ vm.serial_no }}
                                    </a>
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ vm.type }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ vm.racking_config_name }} <br>
                                    @{{ vm.racking_config_desc }}
                                </td>
                                <td class="col-md-2 text-left">
                                    <a href="/simcard/@{{vm.simcard_id}}/edit" ng-if="vm.simcard_id">
                                        @{{ vm.simcard_no }}
                                            @{{ vm.simcard_no && vm.telco_name ? '-' : '' }}
                                        @{{vm.telco_name}}
                                            @{{ vm.telco_name && vm.phone_no ? '-' : '' }}
                                        @{{vm.phone_no}}
                                        </a>
                                </td>
                                <td class="col-md-2 text-left">
                                    @{{ vm.provider_name }}
                                        @{{vm.terminal_id ? '-' : ''}}
                                    @{{vm.terminal_id}}
                                </td>
                                <td class="col-md-2 text-left">
                                    @{{ vm.desc }}
                                </td>
                                <td class="col-md-2 text-left">
                                    <a href="/person/@{{ vm.person_id }}" ng-if="vm.person_id">
                                        @{{ vm.cust_id}} - @{{vm.company}}
                                    </a>
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ vm.custcategory}}
                                </td>
                                <td class="col-md-1 text-center">
                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete($event, vm.id)"><i class="fa fa-times"></i></button>
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

    <script src="/js/vm_index.js"></script>
@stop