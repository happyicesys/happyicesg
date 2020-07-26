@inject('profiles', 'App\Profile')
@inject('customers', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('outletVisits', 'App\OutletVisit')
@inject('persontags', 'App\Persontag')
@inject('users', 'App\User')
@inject('zones', 'App\Zone')


@extends('template')
@section('title')
{{ $DETAILRPT_TITLE }}
@stop
@section('content')

    <div class="row">
        <a class="title_hyper pull-left" href="/operation/worksheet"><h1>Merchandiser - {{ $DETAILRPT_TITLE }} <i class="fa fa-book"></i></h1></a>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Merchandiser
        </div>
        <div class="panel-body" style="font-size: 13px;">
        <div ng-app="app" ng-controller="merchandiserController" ng-cloak>
            {!! Form::open(['id'=>'export_excel', 'method'=>'POST', 'action'=>['OperationWorksheetController@exportOperationExcel']]) !!}
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('profile_id', [''=>'All']+
                                $profiles::filterUserProfile()
                                    ->pluck('name', 'id')
                                    ->all(),
                                null,
                                [
                                    'class'=>'select form-control',
                                    'ng-model'=>'search.profile_id'
                                ])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('id_prefix', 'ID Group', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('id_prefix',
                                [
                                    '' => 'All',
                                    'C' => 'C',
                                    'D' => 'D',
                                    'E' => 'E',
                                    'F' => 'F',
                                    'G' => 'G',
                                    'S' => 'S',
                                    'R' => 'R',
                                    'H' => 'H',
                                    'V' => 'V',
                                    'W' => 'W',
                                ],
                                null,
                                [
                                    'class'=>'selectmultiple form-control',
                                    'ng-model'=>'search.id_prefix',
                                    'multiple' => 'multiple'
                                ])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                            <label class="pull-right">
                                <input type="checkbox" name="exclude_custcategory" ng-model="search.exclude_custcategory" ng-true-value="'1'" ng-false-value="'0'">
                                <span style="margin-top: 5px;">
                                    Exclude
                                </span>
                            </label>
                            {!! Form::select('custcategory', [''=>'All'] + $custcategories::orderBy('name')->pluck('name', 'id')->all(),
                                null,
                                [
                                    'class'=>'selectmultiple form-control',
                                    'ng-model'=>'search.custcategory',
                                    'multiple'=>'multiple'
                                ])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('cust_id',
                                null,
                                [
                                    'class'=>'form-control',
                                    'ng-model'=>'search.cust_id',
                                    'placeholder'=>'Cust ID'
                                ])
                            !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('company', 'ID Name', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('company',
                                null,
                                [
                                    'class'=>'form-control',
                                    'ng-model'=>'search.company',
                                    'placeholder'=>'ID Name'
                                ])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('del_postcode', 'Postcode', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('del_postcode',
                                null,
                                [
                                    'class'=>'form-control',
                                    'ng-model'=>'search.del_postcode',
                                    'placeholder'=>'Postcode'
                                ])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('chosen_date', 'Today Date', ['class'=>'control-label search-title']) !!}
                            <datepicker selector="form-control">
                                <input
                                    type = "text"
                                    name="chosen_date"
                                    class = "form-control input-sm"
                                    placeholder = "Today Date"
                                    ng-model = "search.chosen_date"
                                    ng-change = "onChosenDateChanged(search.chosen_date)"
                                />
                            </datepicker>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('preferred_days', 'Preferred Day(s)', ['class'=>'control-label search-title']) !!}
                            <select name="preferred_days" class="select form-control" ng-model="search.preferred_days">
                                <option value="">All</option>
                                <option value="1">Mon</option>
                                <option value="2">Tues</option>
                                <option value="3">Wed</option>
                                <option value="4">Thu</option>
                                <option value="5">Fri</option>
                                <option value="6">Sat</option>
                                <option value="7">Sun</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('zones', 'Zone', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('zones',
                                $zones::lists('name', 'id')->all(),
                                null,
                                [
                                    'class'=>'selectmultiple form-control',
                                    'ng-model'=>'search.zones',
                                    'multiple' => 'multiple'
                                ])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('previous', 'Previous', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('previous',
                                [
                                    'Last 7 days' => 'Last 7 days',
                                    '' => 'Nil',
                                    'Last 14 days' => 'Last 14 days',
                                ],
                                null,
                                [
                                    'class'=>'select form-control',
                                    'ng-model'=>'search.previous'
                                ])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('future', 'Future', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('future',
                                [
                                    '' => 'Nil',
                                    '2 days' => '2 days',
                                    '5 days' => '5 days',
                                ],
                                null,
                                [
                                    'class'=>'select form-control',
                                    'ng-model'=>'search.future'
                                ])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('color', 'Show Color', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('color',
                                [
                                    '' => 'All',
                                    'Yellow' => 'Yellow',
                                    'Orange' => 'Orange (Pending or Confimed)',
                                    'Green' => 'Green (Delivered)',
                                    'Red' => 'Red (Cancelled)',
                                ],
                                null,
                                [
                                    'class'=>'select form-control',
                                    'ng-model'=>'search.color'
                                ])
                            !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('tags', 'Customer Tags', ['class'=>'control-label search-title']) !!}
                        <select name="tags" id="tags" class="selectmultiple form-control" ng-model="search.tags" ng-change="searchDB()" multiple>
                            <option value="">All</option>
                            @foreach($persontags::orderBy('name')->get() as $persontag)
                                <option value="{{$persontag->id}}">
                                    {{$persontag->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('account_manager', 'Account Manager', ['class'=>'control-label']) !!}
                        {!! Form::select('account_manager',
                                [''=>'All']+$users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->lists('name', 'id')->all(),
                                null,
                                [
                                    'class'=>'select form-control',
                                    'ng-model'=>'search.account_manager',
                                    'ng-change'=>'searchDB()'
                                ])
                        !!}
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('last_transac_color', 'Last Transac Color', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('last_transac_color',
                                [
                                    '' => 'All',
                                    'Red' => 'Red',
                                    'Blue' => 'Blue'
                                ],
                                null,
                                [
                                    'class'=>'select form-control',
                                    'ng-model'=>'search.last_transac_color'
                                ])
                            !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" style="padding-left: 15px;">
                <div class="col-md-8 col-sm-12 col-xs-12" style="padding-top: 20px;">
                    <button type="submit" class="btn btn-info" ng-click="searchDB($event)"><i class="fa fa-search"></i><span class="hidden-xs"></span> Search</button>
                    <button type="submit" class="btn btn-default" form="export_excel" name="excel_all" value="excel_all"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export All Excel</button>
                    <button type="submit" form="export_excel" class="btn btn-default" name="excel_single" value="excel_single"><i class="fa fa-file-excel-o"></i> Export Filtered Excel</button>

                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked()" ng-if="people.length > 0"><i class="fa fa-map-o"></i> Generate Map</button>
                    <span ng-show="spinner"> <i class="fa fa-spinner fa-2x fa-spin"></i></span>
                </div>

                <div class="col-md-4 col-sm-4 col-xs-12 text-right">
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
                        <label class="" style="padding-right:18px;" for="totalnum">Showing @{{people.length}} of @{{totalCount}} entries</label>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

                <div class="table-responsive hidden-xs" id="exportable" style="padding-top: 20px;">
                    <table id="datatable" class="table table-list-search table-bordered table-fixedheader">
                        <thead style="font-size: 12px;">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('del_postcode')">
                                Postcode
                                <span ng-if="search.sortName == 'del_postcode' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'del_postcode' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('cust_id')">
                                Cust ID
                                <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('company')">
                                ID Name
                                <span ng-if="search.sortName == 'company' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'company' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('custcategory')">
                                Cat
                                <span ng-if="search.sortName == 'custcategory' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'custcategory' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('account_manager')">
                                Acc Manager
                                <span ng-if="search.sortName == 'account_manager' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'account_manager' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
                                Outlet Visit
                            </th>
                            <th class="col-md-1 text-center">
                                Zone
                            </th>
                            <th class="col-md-1 text-center">
                                Last 2 Transac
                            </th>
                            <th class="col-md-1 text-center">
                                Last Transac
                            </th>
                            <th class="col-md-1 text-center" ng-repeat="date in dates" ng-class="todayDateChecker(date)">
                                @{{date | date : "yy-MM-dd"}}
                                <br>
                                @{{date | date : "EEE"}}
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                            <tr dir-paginate="person in people | itemsPerPage:itemsPerPage" pagination-id="operation_worksheet" total-items="totalCount" current-page="currentPage">
                                <td class="col-md-1 text-center">
                                    @{{$index + indexFrom}}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{person.del_postcode}}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{person.cust_id}}
                                </td>
                                <td class="col-md-1 text-left">
                                    <a href="/person/@{{ person.person_id }}">
                                        @{{ person.cust_id[0] == 'D' || person.cust_id[0] == 'H' ? person.name : person.company }}
                                    </a>
                                    <br>
                                    <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(person, $index)"><i class="fa fa-map-o"></i></button>
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ person.custcategory }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ person.account_manager_name }}
                                </td>
                                <td class="col-md-1 text-center" style="min-width: 75px; font-size: 12px;">
                                    <span ng-if="person.outletvisit_date">
                                        Last Visit<br>
                                        <span ng-style="{'color': person.outletvisit_date_color}">
                                            @{{person.outletvisit_date | date : "yy-MM-dd"}}<br>
                                            @{{person.outletvisit_date | date : "EEE"}}<br>
                                        </span>
                                    </span>
                                    <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#outletVisitModal" ng-click="onOutletVisitClicked($event, person)"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
                                </td>
                                <td class="col-md-1" style="max-width: 50px;">
                                    <div class="checkbox" style="margin-top: 0px;">
                                        <span class="col-md-4 pull-left" style="padding-left: 0px;">
                                            @{{person.zone_name}}
                                            <ui-select ng-model="person.zone_id" on-select="onPersonZoneChanged(person, $index)">
                                                <ui-select-match allow-clear="true">
                                                    <span ng-bind="$select.name"></span>
                                                </ui-select-match>
                                                <ui-select-choices null-option="removeDriver" repeat="zone in zones | filter: $select.search">
                                                    <div ng-bind-html="zone.name | highlight: $select.search"></div>
                                                </ui-select-choices>
                                            </ui-select>
            {{--
                                            <select name="" class="form-control select" ng-model="person.zone_id">
                                                <option value=""></option>
                                                @foreach($zones::all() as $zone)
                                                    <option value="{{$zone->id}}">
                                                        {{$zone->name}}
                                                    </option>
                                                @endforeach
                                            </select> --}}
                                        </span>
                                    </div>
                                </td>
                                <td class="col-md-1 text-left" style="min-width: 75px; font-size: 12px;">
                                    <span class="row">
                                        @{{person.ops2_deldate | date : "yy-MM-dd"}}
                                        <br>
                                        @{{person.ops2_day}}
                                        <br>
                                        @{{person.ops2_total_qty}}
                                        <br>
                                        @{{person.ops2_total}}
                                    </span>
                                </td>
                                <td class="col-md-1 text-left" style="min-width: 75px; font-size: 12px;">
                                    <span class="row" ng-style="{'color': person.last_date_color}">
                                        @{{person.ops_deldate | date : "yy-MM-dd"}}
                                        <br>
                                        @{{person.ops_day}}
                                        <br>
                                        @{{person.ops_total_qty}}
                                        <br>
                                        @{{person.ops_total}}
                                    </span>
                                </td>
                                <td class="col-md-1 text-center td_edit" style="min-width: 70px; font-size: 12px;" ng-repeat="alldata in alldata[$index]" ng-click="changeColor(alldata, $parent.$index, $index)" ng-style="{'background-color': getBackgroundColor(alldata, $parent.$index, $index), 'color': alldata.font_color}">
                                    <span ng-if="alldata.items">
                                        <span ng-repeat="item in alldata.items">
                                            @{{item.product_id}} (@{{item.qty}})
                                            <br>
                                        </span>
                                    </span>
                                    <span class="text-right">
                                        <strong>
                                            <span ng-if="alldata.qty[0]['qty']">
                                                <br> Qty @{{alldata.qty[0]['qty']}} <br>
                                            </span>
                                            <span ng-if="alldata.total[0]['total']">
                                                Amt @{{alldata.total[0]['total']}}
                                            </span>
                                        </strong>
                                    </span>
                                </td>
                            </tr>

                            <tr ng-if="!people.length > 0">
                                <td colspan="18" class="text-center">No Records Found</td>
                            </tr>
                        </tbody>
                    </table>

                    <div>
                          <dir-pagination-controls max-size="5" pagination-id="operation_worksheet" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
                    </div>
                </div>

                {{-- mobile view --}}
                <div class="hidden-lg hidden-md hidden-sm" style="padding-top: 20px;">
                    <table id="datatable" class="table table-list-search table-bordered table-fixedheader" dir-paginate="person in people | itemsPerPage:itemsPerPage" pagination-id="operation_worksheet" total-items="totalCount" current-page="currentPage">
                        <thead style="font-size: 12px;">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('custcategory')">
                                Cat
                                <span ng-if="search.sortName == 'custcategory' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'custcategory' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('del_postcode')">
                                Postcode
                                <span ng-if="search.sortName == 'del_postcode' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'del_postcode' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('cust_id')">
                                Cust ID
                                <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td class="col-md-1 text-center">
                                    @{{$index + indexFrom}}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ person.custcategory }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{person.del_postcode}}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{person.cust_id}}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1"></td>
                                <th class="col-md-1 text-left" style="background-color: #DDFDF8">
                                    ID Name
                                </th>
                                <th class="col-md-1 text-left" colspan="2">
                                    @{{person.company}}
                                    <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(person, $index)"><i class="fa fa-map-o"></i></button>
                                </th>
                            </tr>
                            <tr>
                                <td colspan="1"></td>
                                <th class="col-md-1 text-left" style="background-color: #DDFDF8">
                                    Acc Manager
                                </th>
                                <th class="col-md-1 text-left" colspan="2">
                                    @{{person.account_manager_name}}
                                </th>
                            </tr>
                            <tr>
                                <td colspan="1"></td>
                                <th class="col-md-1 text-left" style="background-color: #DDFDF8">
                                    Last Visit
                                </th>
                                <th class="col-md-1 text-left" ng-style="{'color': person.outletvisit_date_color}" colspan="2">
                                    @{{person.outletvisit_date | date : "yy-MM-dd"}}<br>
                                    @{{person.outletvisit_date | date : "EEE"}}<br>
                                </th>
                                <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#outletVisitModal" ng-click="onOutletVisitClicked($event, person)"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
                            </tr>
                            <tr>
                                <td colspan="1"></td>
                                <th class="col-md-1 text-left" style="background-color: #DDFDF8">
                                    Last Transac
                                </th>
                                <td class="col-md-1" colspan="2">
                                    @{{person.ops_deldate | date : "yy-MM-dd"}} (@{{person.ops_day}})
                                    <br>
                                    <span ng-if="person.last">
                                        <span ng-repeat="item in person.last">
                                            @{{item.product_id}} (@{{item.qty}})
                                            <br>
                                        </span>
                                    </span>
                                    <span class="text-right">
                                        <strong>
                                            <span ng-if="person.ops_total_qty">
                                                <br> Qty @{{person.ops_total_qty}} <br>
                                            </span>
                                            <span ng-if="person.ops_total">
                                                Amt @{{person.ops_total}}
                                            </span>
                                        </strong>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1"></td>
                                <th class="col-md-1 text-left" style="background-color: #DDFDF8">
                                    Last 2 Transac
                                </th>
                                <td class="col-md-1" colspan="2">
                                    @{{person.ops2_deldate | date : "yy-MM-dd"}} (@{{person.ops2_day}})
                                    <br>
                                    <span ng-if="person.last2">
                                        <span ng-repeat="item in person.last2">
                                            @{{item.product_id}} (@{{item.qty}})
                                            <br>
                                        </span>
                                    </span>
                                    <span class="text-right">
                                        <strong>
                                            <span ng-if="person.ops2_total_qty">
                                                <br> Qty @{{person.ops2_total_qty}} <br>
                                            </span>
                                            <span ng-if="person.ops2_total">
                                                Amt @{{person.ops2_total}}
                                            </span>
                                        </strong>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1"></td>
                                <th class="col-md-1 text-left" style="background-color: #DDFDF8">
                                    Last 3 Transac
                                </th>
                                <td class="col-md-1" colspan="2">
                                    @{{person.ops3_deldate | date : "yy-MM-dd"}} (@{{person.ops3_day}})
                                    <br>
                                    <span ng-if="person.last3">
                                        <span ng-repeat="item in person.last3">
                                            @{{item.product_id}} (@{{item.qty}})
                                            <br>
                                        </span>
                                    </span>
                                    <span class="text-right">
                                        <strong>
                                            <span ng-if="person.ops3_total_qty">
                                                <br> Qty @{{person.ops3_total_qty}} <br>
                                            </span>
                                            <span ng-if="person.ops3_total">
                                                Amt @{{person.ops3_total}}
                                            </span>
                                        </strong>
                                    </span>
                                </td>
                            </tr>

                            <tr ng-if="!people.length > 0">
                                <td colspan="18" class="text-center">No Records Found</td>
                            </tr>
                        </tbody>
                    </table>

                    <div>
                          <dir-pagination-controls max-size="5" pagination-id="operation_worksheet" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
                    </div>
                </div>

            <div id="mapModal" class="modal fade" role="dialog">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Plotted Map</h4>
                  </div>
                  <div class="modal-body">
                    <div id="map"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>

              </div>
            </div>

            <div class="modal fade" id="outletVisitModal" role="dialog">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title">
                            Outlet Visit "@{{form.person.cust_id}} - @{{form.person.company}}"
                        </h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Date
                                    </label>
                                    <datepicker selector="form-control">
                                        <input
                                            type = "text"
                                            name="chosen_date"
                                            class = "form-control input-sm"
                                            placeholder = "Visit Date"
                                            ng-model = "form.date"
                                            ng-change = "onOutletVisitDateChanged(form.date)"
                                        />
                                    </datepicker>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Day
                                    </label>
                                    <input type="text" name="name" class="form-control" ng-model="form.day" readonly>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Outcome
                                    </label>
                                    <select name="outcome" class="form-control select" ng-model="form.outcome">
                                        @foreach($outletVisits::OUTCOMES as $index => $outcome)
                                            <option value="{{$index}}" ng-init='form.outcome = "1"'>
                                                {{$outcome}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Remarks
                                    </label>
                                    <textarea name="remarks" class="form-control" ng-model="form.remarks" rows="3"></textarea>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <button class="btn btn-success" ng-click="saveOutletVisitForm(form.person)"><i class="fa fa-upload"></i> Save</button>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-list-search table-hover table-bordered table-condensed">
                                    <thead>
                                        <tr style="background-color: #DDFDF8">
                                            <th class="col-md-1 text-center">
                                                #
                                            </th>
                                            <th class="col-md-1 text-center">
                                                Date
                                            </th>
                                            <th class="col-md-1 text-center">
                                                Day
                                            </th>
                                            <th class="col-md-1 text-center">
                                                Outcome
                                            </th>
                                            <th class="col-md-3 text-center">
                                                Remarks
                                            </th>
                                            <th class="col-md-2 text-center">
                                                Created By
                                            </th>
                                            <th class="col-md-1 text-center">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="visit in form.person.outlet_visits">
                                            <td class="col-md-1 text-center">
                                                @{{ $index + 1 }}
                                            </td>
                                            <td class="col-md-1 text-center">
                                                @{{ visit.date }}
                                            </td>
                                            <td class="col-md-1 text-center">
                                                @{{ visit.day }}
                                            </td>
                                            <td class="col-md-1 text-center">
                                                @{{outcomes[visit.outcome]}}
                                            </td>
                                            <td class="col-md-2 text-left">
                                                @{{ visit.remarks }}
                                            </td>
                                            <td class="col-md-1 text-center">
                                                @{{ visit.creator.name }}
                                            </td>
                                            <td class="col-md-1 text-center">
                                                <button class="btn btn-xs btn-danger btn-delete" ng-click="deleteOutletVisitEntry(visit.id, form.person)">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr ng-if="!form.person.outlet_visits || form.person.outlet_visits.length == 0">
                                            <td class="text-center" colspan="18">
                                                No Results Found
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="/js/operation_worksheet.js"></script>
        </div>
        </div>
    </div>
@stop