@inject('profiles', 'App\Profile')
@inject('people', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('franchisees', 'App\User')
@inject('persontags', 'App\Persontag')
@inject('users', 'App\User')

@extends('template')
@section('title')
{{ $TRANS_TITLE }}
@stop
@section('content')

    <div ng-app="app" ng-controller="transController">

    <div class="row">
        <a class="title_hyper pull-left" href="/transaction"><h1>{{ $TRANS_TITLE }} <i class="fa fa-briefcase"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
    </div>

        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="pull-right">
                            @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee'))
                                <a href="/transaction/create" class="btn btn-success">
                                    <i class="fa fa-plus"></i>
                                    <span class="hidden-xs"> New {{ $TRANS_TITLE }} </span>
                                </a>
                            @endif
                            @if(Auth::user()->hasRole('admin'))
                            <a href="/transaction/freeze/date" class="btn btn-primary">
                                <i class="fa fa-clock-o"></i>
                                <span class="hidden-xs">Freeze Transaction Invoice </span>
                            </a>
                            <a href="/transaction/email/subscription/" class="btn btn-default">
                                <i class="fa fa-calendar"></i>
                                <span class="hidden-xs">Weekly Email Subscription </span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                {!! Form::open(['id'=>'transaction_rpt', 'method'=>'POST','action'=>['TransactionController@exportAccConsolidatePdf']]) !!}
                    <div class="row">
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('invoice', 'Invoice', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('invoice', null,
                                                        [
                                                            'class'=>'form-control input-sm',
                                                            'ng-model'=>'search.transaction_id',
                                                            'ng-change'=>'searchDB()',
                                                            'placeholder'=>'Inv Num',
                                                            'ng-model-options'=>'{ debounce: 500 }'
                                                        ]) !!}
                    </div>

                    @if(!auth()->user()->hasRole('hd_user'))
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('id', 'ID', ['class'=>'control-label search-title']) !!}
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
                    @else
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('do_po', 'PO Num', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('do_po', [''=>'All', '4505160978_(FSI)'=>'4505160978 (FSI)', '4505160966_(Retail)'=>'4505160966 (Retail)'], null,
                                [
                                'class'=>'select form-control',
                                'ng-model'=>'search.do_po',
                                'ng-change'=>'searchDB()'
                                ])
                            !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('requester_name', 'Requester Name', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('requester_name', null,
                                                        [
                                                            'class'=>'form-control input-sm',
                                                            'ng-model'=>'search.requester_name',
                                                            'ng-change'=>'searchDB()',
                                                            'placeholder'=>'Requester Name',
                                                            'ng-model-options'=>'{ debounce: 500 }'
                                                        ])
                        !!}
                    </div>
                    @endif

                    @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('statuses', 'Status', ['class'=>'control-label search-title']) !!}
                        <select name="statuses" class="selectmultiple form-control" ng-model="search.statuses" ng-change="searchDB()" multiple>
                            <option value="">All</option>
                            <option value="Pending">Pending</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    @endif
                    </div>
                    <div class="row">
                        @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('pay_status', 'Payment', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('pay_status', [''=>'All', 'Owe'=>'Owe', 'Paid'=>'Paid'], null,
                                [
                                'class'=>'select form-control',
                                'ng-model'=>'search.pay_status',
                                'ng-change'=>'searchDB()'
                                ])
                            !!}
                        </div>

                        @if(!auth()->user()->hasRole('hd_user'))
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('updated_by', 'Last Modify By', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('updated_by', null,
                                                                [
                                                                    'class'=>'form-control input-sm',
                                                                    'ng-model'=>'search.updated_by',
                                                                    'ng-change'=>'searchDB()',
                                                                    'placeholder'=>'Last Modified By',
                                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                                ])
                            !!}
                        </div>
                        @else
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('pickup_location_name', 'Pickup Loc Name', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('pickup_location_name', null,
                                                                [
                                                                    'class'=>'form-control input-sm',
                                                                    'ng-model'=>'search.pickup_location_name',
                                                                    'ng-change'=>'searchDB()',
                                                                    'placeholder'=>'Pickup Loc Name',
                                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                                ])
                            !!}
                        </div>
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('delivery_location_name', 'Delivery Loc Name', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('delivery_location_name', null,
                                                                [
                                                                    'class'=>'form-control input-sm',
                                                                    'ng-model'=>'search.delivery_location_name',
                                                                    'ng-change'=>'searchDB()',
                                                                    'placeholder'=>'Delivery Loc Name',
                                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                                ])
                            !!}
                        </div>
                        @endif

                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('updated_at', 'Last Modify Dt', ['class'=>'control-label search-title']) !!}
                            <div class="input-group">
                                <datepicker>
                                    <input
                                        type = "text"
                                        class = "form-control input-sm"
                                        placeholder = "Last Modify Date"
                                        ng-model = "search.updated_at"
                                        ng-change = "dateChange2(search.updated_at)"
                                    />
                                </datepicker>
                                <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('updated_at', search.updated_at)"></span>
                                <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('updated_at', search.updated_at)"></span>
                            </div>
                        </div>
                        @endif
                        @if(!auth()->user()->hasRole('hd_user'))
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('driver', 'Assigned Driver', ['class'=>'control-label search-title']) !!}
                            @if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician'))
                                <select name="driver" class="form-control select" ng-model="search.driver" ng-change="searchDB()">
                                    <option value="">All</option>
                                    <option value="{{auth()->user()->name}}">
                                        {{auth()->user()->name}}
                                    </option>
                                </select>
                            @else
                                <select name="driver" class="form-control select" ng-model="search.driver" ng-change="searchDB()">
                                    <option value="">All</option>
                                    <option value="-1">-- Unassigned --</option>
                                    @foreach($users::where('is_active', 1)->orderBy('name')->get() as $user)
                                        @if(($user->hasRole('driver') or $user->hasRole('technician')) and count($user->profiles) > 0)
                                            <option value="{{$user->name}}">
                                                {{$user->name}}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        @endif
                    </div>
                    @if(!auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                    <div class="row">
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                                <label class="pull-right">
                                    <input type="checkbox" name="exclude_custcategory" ng-model="search.exclude_custcategory" ng-true-value="'1'" ng-false-value="'0'" ng-change="searchDB()">
                                    <span style="margin-top: 5px;">
                                        Exclude
                                    </span>
                                </label>
                                {!! Form::select('custcategory', [''=>'All'] + $custcategories::orderBy('name')->pluck('name', 'id')->all(),
                                    null,
                                    [
                                        'class'=>'selectmultiple form-control',
                                        'ng-model'=>'search.custcategory',
                                        'multiple'=>'multiple',
                                        'ng-change' => "searchDB()"
                                    ])
                                !!}
                            </div>
                        </div>
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('profile_id', [''=>'All']+$profiles::filterUserProfile()->pluck('name', 'id')->all(), null, ['id'=>'profile_id',
                                'class'=>'select form-control',
                                'ng-model'=>'search.profile_id',
                                'ng-change' => 'searchDB()'
                                ])
                            !!}
                        </div>
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('franchisee_id', 'Franchisee', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('franchisee_id', [''=>'All', '0' => 'Own']+$franchisees::filterUserFranchise()->select(DB::raw("CONCAT(user_code,' (',name,')') AS full, id"))->orderBy('user_code')->pluck('full', 'id')->all(), null, ['id'=>'franchisee_id',
                                'class'=>'select form-control',
                                'ng-model'=>'search.franchisee_id',
                                'ng-change' => 'searchDB()'
                                ])
                            !!}
                        </div>
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('person_active', 'Customer Status', ['class'=>'control-label search-title']) !!}
                            <select name="person_active" id="person_active" class="selectmultiple form-control" ng-model="search.person_active" ng-change="searchDB()" multiple>
                                <option value="">All</option>
                                <option value="Yes">Active</option>
                                <option value="New">New</option>
                                @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                                    <option value="No">Inactive</option>
                                    <option value="Pending">Pending</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif
                </div>
                @if(auth()->user()->hasRole('hd_user'))
                <div class="row">
                    <div class="row">
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('requested_from', 'Requested Date (Start)', ['class'=>'control-label search-title']) !!}
                        <div class="input-group">
                            <datepicker>
                                <input
                                    name = "requested_from"
                                    type = "text"
                                    class = "form-control input-sm"
                                    placeholder = "Requested Date (Start)"
                                    ng-model = "search.requested_from"
                                    ng-change = "dateChange('requested_from', search.requested_from)"
                                />
                            </datepicker>
                            <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('requested_from', search.requested_from)"></span>
                            <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('requested_from', search.requested_from)"></span>
                        </div>
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('requested_to', 'Requested Date (End)', ['class'=>'control-label search-title']) !!}
                        <div class="input-group">
                            <datepicker>
                                <input
                                    name = "requested_to"
                                    type = "text"
                                    class = "form-control input-sm"
                                    placeholder = "Requested Date (End)"
                                    ng-model = "search.requested_to"
                                    ng-change = "dateChange('requested_to', search.requested_to)"
                                />
                            </datepicker>
                            <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('requested_to', search.requested_to)"></span>
                            <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('requested_to', search.requested_to)"></span>
                        </div>
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        <div class="row col-md-12 col-sm-12 col-xs-12">
                            {!! Form::label('delivery_shortcut', 'Date Shortcut', ['class'=>'control-label search-title']) !!}
                        </div>
                        <div class="btn-group">
                            <a href="" ng-click="onPrevDateClicked('requested_from', 'requested_to')" class="btn btn-default"><i class="fa fa-backward"></i></a>
                            <a href="" ng-click="onTodayDateClicked('requested_from', 'requested_to')" class="btn btn-default"><i class="fa fa-circle"></i></a>
                            <a href="" ng-click="onNextDateClicked('requested_from', 'requested_to')" class="btn btn-default"><i class="fa fa-forward"></i></a>
                        </div>
                    </div>
                    </div>
                </div>
                @else
                <div class="row">
                    <div class="row">
                        @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('area_groups', 'Zone', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('area_groups',
                            [
                                '1' => 'West',
                                '2' => 'East',
                                '6' => 'North',
                                '3' => 'Others',
                                '4' => 'Sup',
                                '5' => 'Ops'
                            ],
                            null,
                            [
                                'class'=>'selectmultiple form-control',
                                'ng-model'=>'search.area_groups',
                                'multiple' => 'multiple',
                                'ng-change' => 'searchDB()'
                            ])
                        !!}
                        </div>
                        @endif
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('po_no', 'PO Num', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('po_no', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.po_no',
                                                                'ng-change'=>'searchDB()',
                                                                'placeholder'=>'PO Num',
                                                                'ng-model-options'=>'{ debounce: 500 }'
                                                            ]) !!}
                        </div>
                        @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('contact', 'Attn Contact', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('contact', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.contact',
                                                                'ng-change'=>'searchDB()',
                                                                'placeholder'=>'Attn Contact',
                                                                'ng-model-options'=>'{ debounce: 500 }'
                                                            ]) !!}
                        </div>

                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('is_gst_inclusive', 'GST', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('is_gst_inclusive',
                            [
                                '' => 'All',
                                'true' => 'Already added GST',
                                'false' => 'To add GST'
                            ],
                            null,
                            [
                                'class'=>'select form-control',
                                'ng-model'=>'search.is_gst_inclusive',
                                'ng-change' => 'searchDB()'
                            ])
                        !!}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="row">
                        @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('gst_rate', 'GST Rate (%)', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('gst_rate', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.gst_rate',
                                                                'ng-change'=>'searchDB()',
                                                                'placeholder'=>'GST Rate',
                                                                'ng-model-options'=>'{ debounce: 500 }'
                                                            ]) !!}
                        </div>
                        @endif
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
                    </div>
                </div>
                <div class="row">
                    <div class="row">
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('delivery_from', 'Delivery From', ['class'=>'control-label search-title']) !!}
                            <div class="input-group">
                                <datepicker>
                                    <input
                                        name = "delivery_from"
                                        type = "text"
                                        class = "form-control input-sm"
                                        placeholder = "Delivery From"
                                        ng-model = "search.delivery_from"
                                        ng-change = "dateChange('delivery_from', search.delivery_from)"
                                    />
                                </datepicker>
                                <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_from', search.delivery_from)"></span>
                                <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_from', search.delivery_from)"></span>
                            </div>
                        </div>
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('delivery_to', 'Delivery To', ['class'=>'control-label search-title']) !!}
                            <div class="input-group">
                                <datepicker>
                                    <input
                                        name = "delivery_to"
                                        type = "text"
                                        class = "form-control input-sm"
                                        placeholder = "Delivery To"
                                        ng-model = "search.delivery_to"
                                        ng-change = "dateChange('delivery_to', search.delivery_to)"
                                    />
                                </datepicker>
                                <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_to', search.delivery_to)"></span>
                                <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_to', search.delivery_to)"></span>
                            </div>
                        </div>
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            <div class="row col-md-12 col-sm-12 col-xs-12">
                                {!! Form::label('delivery_shortcut', 'Date Shortcut', ['class'=>'control-label search-title']) !!}
                            </div>
                            <div class="btn-group">
                                <a href="" ng-click="onPrevDateClicked('delivery_from', 'delivery_to')" class="btn btn-default"><i class="fa fa-backward"></i></a>
                                <a href="" ng-click="onTodayDateClicked('delivery_from', 'delivery_to')" class="btn btn-default"><i class="fa fa-circle"></i></a>
                                <a href="" ng-click="onNextDateClicked('delivery_from', 'delivery_to')" class="btn btn-default"><i class="fa fa-forward"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <button class="btn btn-sm btn-primary" ng-click="exportData($event)">Export Excel</button>
                        @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('operation'))
                            <button class="btn btn-sm btn-default" ng-click="enableAccConsolidate($event)">
                                Export Acc Consolidate
                                <span ng-if="!show_acc_consolidate_div" class="fa fa-caret-down"></span>
                                <span ng-if="show_acc_consolidate_div" class="fa fa-caret-up"></span>
                            </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked()" ng-if="alldata.length > 0"><i class="fa fa-map-o"></i> Generate Map</button>
                        @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                        <button class="btn btn-sm btn-default" ng-click="onDriverAssignToggleClicked($event)">
                            <span ng-if="driverOptionShowing === true">
                                Hide Map & Driver Assign
                            </span>
                            <span ng-if="driverOptionShowing === false">
                                Show Map & Driver Assign
                            </span>
                        </button>

                        <button class="btn btn-sm btn-primary" ng-click="onBatchFunctionClicked($event)">
                            Batch Function
                            <span ng-if="!showBatchFunctionPanel" class="fa fa-caret-down"></span>
                            <span ng-if="showBatchFunctionPanel" class="fa fa-caret-up"></span>
                        </button>
                        @endif
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12" style="padding-top:5px;">
                        <div class="col-md-5 col-xs-5">
                            Total
                        </div>
                        <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                            <strong>@{{total_amount ? total_amount : 0.00 | currency: "": 2}}</strong>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12 text-right">
                        <div class="row">
                            <label for="display_num">Display</label>
                            <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='200'" ng-change="pageNumChanged()">
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

                <div ng-show="show_acc_consolidate_div">
                <hr class="row">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('person_account', 'Customer to bill ', ['class'=>'control-label search-title']) !!}
                                    {!! Form::select('person_account',
                                        $people::whereHas('profile', function($q){
                                            $q->filterUserProfile();
                                        })->select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id')->whereActive('Yes')->where('cust_id', 'NOT LIKE', 'H%')->lists('full', 'id')->all(),
                                        null,
                                        [
                                            'class'=>'select form-control',
                                            'ng-model'=>'form.person_account',
                                        ])
                                    !!}
                                    <p class="text-muted">*For Acc Consolidate Rpt, must select "Customer to bill"</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                <label class="control-label"></label>
                                <div class="btn-group-control">
                                    <button type="submit" class="btn btn-sm btn-default" form="transaction_rpt" name="exportpdf" value="do" ng-disabled="!form.person_account"><i class="fa fa-compress"></i> Export DO</button>
                                    <button type="submit" class="btn btn-sm btn-default" form="transaction_rpt" name="exportpdf" value="invoice" ng-disabled="!form.person_account"><i class="fa fa-compress"></i> Export Tax Invoice</button>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <hr class="row">
                </div>
                <div ng-show="showBatchFunctionPanel">
                    <hr class="row">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('batch_assign_driver', 'Batch Assign Driver', ['class'=>'control-label search-title']) !!}
                                    <select name="driver" class="form-control select" ng-model="form.driver">
                                        <option value="-1">
                                            -- Clear --
                                        </option>
                                        @foreach($users::where('is_active', 1)->orderBy('name')->get() as $user)
                                            @if(($user->hasRole('driver') or $user->hasRole('technician')) and count($user->profiles) > 0)
                                                <option value="{{$user->name}}">
                                                    {{$user->name}}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                <label class="control-label"></label>
                                <div class="btn-group-control">
                                    <button type="submit" class="btn btn-sm btn-warning" name="batch_assign" value="invoice" ng-click="onBatchAssignDriverClicked($event)"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Batch Assign</button>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="row">
                </div>
                {!! Form::close() !!}
                    <div class="table-responsive" id="exportable" style="padding-top:20px;">
                        <table class="table table-list-search table-hover table-bordered">
                            {{-- hidden table for excel export --}}
                            <tr class="hidden">
                                <td></td>
                                <td data-tableexport-display="always">Total Amount</td>
                                <td data-tableexport-display="always" class="text-right">@{{total_amount | currency: "": 2}}</td>
                            </tr>
                            <tr class="hidden" data-tableexport-display="always">
                                <td></td>
                            </tr>
                            <tr style="background-color: #DDFDF8">

                                <th class="col-md-1 text-center">
                                    {{-- <input type="checkbox" id="checkAll" /> --}}
                                    <input type="checkbox" id="check_all" ng-model="form.checkall" ng-change="onCheckAllChecked()"/>
                                </th>
                                <th class="col-md-1 text-center">
                                    #
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('transactions.id')">
                                    INV #
                                    <span ng-if="search.sortName == 'transactions.id' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'transactions.id' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                @if(!auth()->user()->hasRole('hd_user'))

                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('cust_id')">
                                    ID
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
                                    Action
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('custcategories.name')">
                                    Cust Cat
                                    <span ng-if="search.sortName == 'custcategories.name' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'custcategories.name' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('transactions.del_postcode')">
                                    Del Postcode
                                    <span ng-if="search.sortName == 'transactions.del_postcode' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'transactions.del_postcode' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    Zone
                                </th>
                                @else

                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('do_po')">
                                    PO Num
                                    <span ng-if="search.sortName == 'do_po' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'do_po' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('requester_name')">
                                    Requester Name
                                    <span ng-if="search.sortName == 'requester_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'requester_name' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('pickup_location_name')">
                                    Pickup Loc Name
                                    <span ng-if="search.sortName == 'pickup_location_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'pickup_location_name' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('delivery_location_name')">
                                    Delivery Loc Name
                                    <span ng-if="search.sortName == 'delivery_location_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'delivery_location_name' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                @endif

                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('status')">
                                    Status
                                    <span ng-if="search.sortName == 'status' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'status' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('transactions.po_no')">
                                    PO Num
                                    <span ng-if="search.sortName == 'transactions.po_no' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'transactions.po_no' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('transactions.name')">
                                    Attn Name
                                    <span ng-if="search.sortName == 'transactions.name' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'transactions.name' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('transactions.contact')">
                                    Contact
                                    <span ng-if="search.sortName == 'transactions.contact' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'transactions.contact' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                @if(!auth()->user()->hasRole('hd_user'))
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('delivery_date')">
                                    Delivery Date
                                    <span ng-if="search.sortName == 'delivery_date' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'delivery_date' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('driver')">
                                    Assigned Driver
                                    <span ng-if="search.sortName == 'driver' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'driver' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                @else
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('delivery_date1')">
                                    Requested Delivery Date
                                    <span ng-if="search.sortName == 'delivery_date1' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'delivery_date1' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                @endif
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('total')">
                                    Total Amount
                                    <span ng-if="search.sortName == 'total' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'total' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                @if(!auth()->user()->hasRole('hd_user'))
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('total_qty')">
                                    Total Qty
                                    <span ng-if="search.sortName == 'total_qty' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'total_qty' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                @endif
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('pay_status')">
                                    Payment
                                    <span ng-if="search.sortName == 'pay_status' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'pay_status' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('del_address')">
                                    Del Address
                                    <span ng-if="search.sortName == 'del_address' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'del_address' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('updated_by')">
                                    Last Modified By
                                    <span ng-if="search.sortName == 'updated_by' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'updated_by' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('transactions.updated_at')">
                                    Last Modified Time
                                    <span ng-if="search.sortName == 'transactions.updated_at' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'transactions.updated_at' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                            </tr>
                            <tbody>
                                <tr dir-paginate="transaction in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">

                                    <td class="col-md-1 text-center">
                                        <input type="checkbox" name="checkbox" ng-model="transaction.check">
                                    </td>
                                    <td class="col-md-1 text-center">@{{ $index + indexFrom }} </td>
                                    <td class="col-md-1 text-center">
                                        <a href="/transaction/@{{ transaction.id }}/edit">
                                            @{{ transaction.id }}
                                        </a>
                                        <i class="fa fa-flag" aria-hidden="true" style="color:red; cursor:pointer;" ng-if="transaction.is_important" ng-click="onIsImportantClicked(transaction.id, $index)"></i>
                                        <i class="fa fa-flag" aria-hidden="true" style="color:grey; cursor:pointer;" ng-if="!transaction.is_important" ng-click="onIsImportantClicked(transaction.id, $index)"></i>
                                    </td>

                                    @if(!auth()->user()->hasRole('hd_user'))
                                    <td class="col-md-1 text-center">@{{ transaction.cust_id }} </td>
                                    <td class="col-md-1 text-center">
                                        <a href="/person/@{{ transaction.person_id }}">
                                            @{{ transaction.cust_id[0] == 'D' || transaction.cust_id[0] == 'H' ? transaction.name : transaction.company }}
                                        </a>
                                    </td>
                                    <td class="col-md-1 text-center">
                                        {{-- print invoice         --}}
                                        <a href="/transaction/download/@{{ transaction.id }}" class="btn btn-primary btn-sm" ng-if="transaction.status != 'Pending' && transaction.status != 'Cancelled'">Print</a>
                                        {{-- button view shown when cancelled --}}
                                        <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-sm btn-default" ng-if="transaction.status == 'Cancelled'">View</a>
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(transaction, $index)" ng-if="driverOptionShowing"><i class="fa fa-map-o"></i> Map</button>
                                    </td>
                                    <td class="col-md-1 text-center">@{{ transaction.custcategory }} </td>
                                    <td class="col-md-1 text-center">@{{ transaction.del_postcode }}</td>
                                    <td class="col-md-1 text-left">
                                        <span ng-if="transaction.west == 1">West</span>
                                        <span ng-if="transaction.east == 1">East</span>
                                        <span ng-if="transaction.north == 1">North</span>
                                        <span ng-if="transaction.others == 1">Others</span>
                                        <span ng-if="transaction.sup == 1">Sup</span>
                                        <span ng-if="transaction.ops == 1">Ops</span>
                                    </td>
                                    @else
                                    <td class="col-md-1 text-center">@{{ transaction.do_po }} </td>
                                    <td class="col-md-1 text-center">@{{ transaction.requester_name }} </td>
                                    <td class="col-md-1 text-center">@{{ transaction.pickup_location_name }} </td>
                                    <td class="col-md-1 text-center">@{{ transaction.delivery_location_name }}</td>
                                    @endif

                                    {{-- status by color --}}
                                    <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.status == 'Pending'">
                                        @{{ transaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: orange;" ng-if="transaction.status == 'Confirmed'">
                                        @{{ transaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.status == 'Delivered'">
                                        @{{ transaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: black; background-color:orange;" ng-if="transaction.status == 'Verified Owe'">
                                        @{{ transaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: black; background-color:green;" ng-if="transaction.status == 'Verified Paid'">
                                        @{{ transaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" ng-if="transaction.status == 'Cancelled'">
                                        <span style="color: white; background-color: red;" > @{{ transaction.status }} </span>
                                    </td>
                                    {{-- status by color ended --}}
                                    <td class="col-md-1 text-center">@{{ transaction.po_no}}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.name}}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.contact}}</td>
                                    @if(!auth()->user()->hasRole('hd_user'))
                                    <td class="col-md-1 text-center">@{{ transaction.del_date}}</td>
                                    <td class="col-md-1 text-center">
                                        @{{ transaction.driver }}
                                        @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                                        <ui-select ng-model="transaction.driverchosen" on-select="onFormDriverChanged(transaction, $index)" ng-if="driverOptionShowing">
                                            <ui-select-match allow-clear="true">
                                                <span ng-bind="$select.driver.name"></span>
                                            </ui-select-match>
                                            <ui-select-choices null-option="removeDriver" repeat="user in users | filter: $select.search">
                                                <div ng-bind-html="user.name | highlight: $select.search"></div>
                                            </ui-select-choices>
                                        </ui-select>
                                        @endif
                                    </td>
                                    @else
                                    <td class="col-md-1 text-center">@{{ transaction.delivery_date1}}</td>
                                    @endif
                                    <td class="col-md-1 text-right">
                                        @{{ transaction.total | currency: "": 2}}
                                    </td>
                                    @if(!auth()->user()->hasRole('hd_user'))
                                    <td class="col-md-1 text-center">@{{ transaction.total_qty }}</td>
                                    @endif
                                    {{-- pay status --}}
                                    <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.pay_status == 'Owe'">
                                        @{{ transaction.pay_status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.pay_status == 'Paid'">
                                        @{{ transaction.pay_status }}
                                    </td>
                                    {{-- pay status ended --}}
                                    <td class="col-md-1 text-center">@{{ transaction.del_address}}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.updated_by}}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.updated_at }}</td>
                                </tr>
                                <tr ng-if="!alldata || alldata.length == 0">
                                    <td colspan="24" class="text-center">No Records Found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
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

    <script src="/js/transaction_index.js"></script>
    <script>
        $('#delfrom').datetimepicker({
            format: 'DD-MMMM-YYYY'
        });

        // $('.select').select2({});
    </script>
@stop