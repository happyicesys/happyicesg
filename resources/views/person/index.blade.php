@inject('custcategories', 'App\Custcategory')
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
        <a class="title_hyper pull-left person-color" href="/person"><h1>{{ $PERSON_TITLE }} <i class="fa fa-users"></i></h1></a>
    </div>
    <div ng-app="app" ng-controller="personController">

        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <div class="panel-title">
                    <div class="pull-right">
                        @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user')and !auth()->user()->hasRole('driver-supervisor'))
                        @cannot('transaction_view')
                            <a href="/person/create" class="btn btn-sm btn-success">+ New {{ $PERSON_TITLE }}</a>
                            @if(!auth()->user()->hasRole('franchisee'))
                            <a href="/onlineprice/create" class="btn btn-sm btn-default">+ Ecommerce Price Setup</a>
                            @endif
                            @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('operation') or auth()->user()->hasRole('supervisor'))
                                <a href="/pricematrix" class="btn btn-sm btn-default"><i class="fa fa-list"></i> Price Matrix</a>
                            @endif
                        @endcannot
                        @endif
                    </div>
                </div>
            </div>

            <div class="panel-body">

                    <div class="row">
                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                            {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                            <label class="pull-right">
                                <input type="checkbox" name="strictCustId" ng-model="search.strictCustId" ng-change="searchDB()">
                                <span style="margin-top: 5px; margin-right: 5px;">
                                    Strict
                                </span>
                            </label>
                            {!! Form::text('cust_id', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.cust_id',
                                                                'placeholder'=>'ID',
                                                                'ng-change'=>'searchDB()',
                                                                'ng-model-options'=>'{ debounce: 500 }'
                                                            ])
                            !!}
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                            {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                            <select name="custcategory" class="selectmultiple form-control" ng-model="search.custcategory" ng-change="searchDB()" multiple>
                                <option value="">All</option>
                                @foreach($custcategories::orderBy('name')->get() as $custcategory)
                                <option value="{{$custcategory->id}}">{{$custcategory->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                            {!! Form::label('company', 'ID Name', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('company', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.company',
                                                                'placeholder'=>'ID Name',
                                                                'ng-change'=>'searchDB()',
                                                                'ng-model-options'=>'{ debounce: 500 }'
                                                            ])
                            !!}
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                            {!! Form::label('contact', 'Contact', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('contact', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.contact',
                                                                'placeholder'=>'Contact',
                                                                'ng-change'=>'searchDB()',
                                                                'ng-model-options'=>'{ debounce: 500 }'
                                                            ])
                            !!}
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                            {!! Form::label('active', 'Status', ['class'=>'control-label search-title']) !!}
                            <select name="active" id="active" class="selectmultiple form-control" ng-model="search.active" ng-change="searchDB()" multiple>
                                <option value="">All</option>
                                <option value="Yes">Active</option>
                                <option value="New">New</option>
                                @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                                    <option value="No">Inactive</option>
                                    <option value="Pending">Pending</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                            {!! Form::label('tags', 'Tags', ['class'=>'control-label search-title']) !!}
                            <select name="tags" id="tags" class="selectmultiple form-control" ng-model="search.tags" ng-change="searchDB()" multiple>
                                <option value="">All</option>
                                @foreach($persontags::orderBy('name')->get() as $persontag)
                                    <option value="{{$persontag->id}}">
                                        {{$persontag->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user'))
                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                            {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('profile_id', [''=>'All']+$profiles::filterUserProfile()->pluck('name', 'id')->all(), null, ['id'=>'profile_id',
                                'class'=>'select form-control',
                                'ng-model'=>'search.profile_id',
                                'ng-change' => 'searchDB()'
                                ])
                            !!}
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                            {!! Form::label('franchisee_id', 'Franchisee', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('franchisee_id', [''=>'All', '0' => 'Own']+$franchisees::filterUserFranchise()->select(DB::raw("CONCAT(user_code,' (',name,')') AS full, id"))->orderBy('user_code')->pluck('full', 'id')->all(), null, ['id'=>'franchisee_id',
                                'class'=>'select form-control',
                                'ng-model'=>'search.franchisee_id',
                                'ng-change' => 'searchDB()'
                                ])
                            !!}
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                            {!! Form::label('account_manager', 'Account Manager', ['class'=>'control-label']) !!}
                            @if(auth()->user()->hasRole('merchandiser'))
                                <select name="account_manager" class="select form-control" ng-model="search.account_manager" ng-change="searchDB()" ng-init="merchandiserInit('{{auth()->user()->id}}')" disabled>
                                    <option value="">All</option>
                                    @foreach($users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->orderBy('name')->get() as $user)
                                    <option value="{{$user->id}}">
                                        {{$user->name}}
                                    </option>
                                    @endforeach
                                </select>
                            @else
                                {!! Form::select('account_manager',
                                        [''=>'All']+$users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->lists('name', 'id')->all(),
                                        null,
                                        [
                                            'class'=>'select form-control',
                                            'ng-model'=>'search.account_manager',
                                            'ng-change'=>'searchDB()'
                                        ])
                                !!}
                            @endif
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                            {!! Form::label('zone_id', 'Zone', ['class'=>'control-label']) !!}
                            {!! Form::select('zone_id',
                                    [''=>'All']+ $zones::orderBy('priority')->lists('name', 'id')->all(),
                                    null,
                                    [
                                        'class'=>'select form-control',
                                        'ng-model'=>'search.zone_id',
                                        'ng-change'=>'searchDB()'
                                    ])
                            !!}
                        </div>
                        @endif
                    </div>

                    <div class="row" style="padding-top: 20px;">
                        <div class="col-md-4 col-xs-12">
                            <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked()" ng-if="alldata.length > 0"><i class="fa fa-map-o"></i> Generate Map</button>
                        </div>
                        <div class="col-md-4 col-md-offset-4 col-xs-12 text-right">
                            <div class="row" style="padding-right:18px;">
                                <label>Display</label>
                                <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='All'" ng-change="pageNumChanged()">
                                    <option ng-value="100">100</option>
                                    <option ng-value="200">200</option>
                                    <option ng-value="All">All</option>
                                </select>
                                <label>per Page</label>
                            </div>
                            <div class="row">
                                <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" id="exportable" style="padding-top: 20px;">
                    <table class="table table-list-search table-hover table-bordered">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('cust_id')">
                                ID
                                <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
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
                                <a href="" ng-click="sortTable('account_managers.name')">
                                Acc Manager
                                <span ng-if="search.sortName == 'account_managers.name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'account_managers.name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('name')">
                                Att. To
                                <span ng-if="search.sortName == 'name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('contact')">
                                Contact
                                <span ng-if="search.sortName == 'contact' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'contact' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-3 text-center">
                                Delivery Add
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('del_postcode')">
                                Postcode
                                <span ng-if="search.sortName == 'del_postcode' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'del_postcode' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('zone_id')">
                                Zone
                                <span ng-if="search.sortName == 'zone_id' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'zone_id' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('payterm')">
                                Payterm
                                <span ng-if="search.sortName == 'payterm' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'payterm' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('active')">
                                Status
                                <span ng-if="search.sortName == 'active' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'active' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                        </tr>

                        <tbody>
                            <tr dir-paginate="person in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage">
                                <td class="col-md-1 text-center">@{{ $index + indexFrom }} </td>
                                <td class="col-md-1">
                                    <a href="/person/@{{ person.id }}/edit">
                                    @{{ person.cust_id }}
                                    </a>
                                </td>
                                <td class="col-md-2">
                                    @{{ person.company }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ person.custcategory }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ person.account_manager_name }}
                                </td>
                                <td class="col-md-1">@{{ person.name }}</td>
                                <td class="col-md-1">
                                    @{{ person.contact }}
                                    <span ng-show="person.alt_contact.length > 0">
                                    / @{{ person.alt_contact }}
                                    </span>
                                </td>
                                <td class="col-md-3">@{{ person.del_address }}</td>
                                <td class="col-md-1 text-center">@{{ person.del_postcode }}</td>
                                <td class="col-md-1 text-center">
                                    @{{ person.zone_name }}
                                </td>
                                <td class="col-md-1 text-center">@{{person.payterm}}</td>
                                <td class="col-md-1 text-center">@{{ person.active }}</td>
                            </tr>
                            <tr ng-if="!alldata || alldata.length == 0">
                                <td colspan="14" class="text-center">No Records Found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
                <div class="panel-footer">
                    <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
                </div>
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

    <script src="/js/person.js"></script>
@stop