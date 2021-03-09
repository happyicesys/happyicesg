@inject('profiles', 'App\Profile')
@inject('people', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('franchisees', 'App\User')
@inject('items', 'App\Item')
@inject('persontags', 'App\Persontag')
@inject('users', 'App\User')
@inject('zones', 'App\Zone')

@extends('template')
@section('title')
Job Assign
@stop
@section('content')

    <div ng-app="app" ng-controller="transController">

    <div class="row">
        <a class="title_hyper pull-left" href="/transaction"><h1> Job Assign <i class="fa fa-paper-plane" aria-hidden="true"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
    </div>

    <div class="panel panel-default" ng-cloak>
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="pull-right">
                        <a href="/transaction/create" class="btn btn-success">
                            <i class="fa fa-plus"></i>
                            @if(!auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                                <span class="hidden-xs"> New {{ $TRANS_TITLE }} </span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            {!! Form::open(['id'=>'transaction_rpt', 'method'=>'POST','action'=>['TransactionController@exportAccConsolidatePdf']]) !!}
                @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                <div class="row">
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('invoice', 'Invoice', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('invoice', null,
                                                        [
                                                            'class'=>'form-control input-sm',
                                                            'ng-model'=>'search.transaction_id',
                                                            'ng-change'=>'searchDB()',
                                                            'placeholder'=>'Inv Num',
                                                            'ng-model-options'=>'{ debounce: 1000 }'
                                                        ]) !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('id', 'ID', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('id', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.cust_id',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Cust ID',
                                                        'ng-model-options'=>'{ debounce: 1000 }'
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
                                                            'ng-model-options'=>'{ debounce: 1000 }'
                                                        ])
                        !!}
                    </div>
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
                </div>
                <div class="row">
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
{{--
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
                    </div> --}}

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
                                    @if(($user->hasRole('driver') or $user->hasRole('technician') or $user->hasRole('driver-supervisor') or $user->id === 100010) and count($user->profiles) > 0)
                                        <option value="{{$user->name}}">
                                            {{$user->name}}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                        <label class="pull-right">
                            <input type="checkbox" name="p_category" ng-model="search.p_category" ng-change="onPCategoryChanged()">
                            <span style="margin-top: 5px; margin-right: 5px;">
                                P
                            </span>
                            <input type="checkbox" name="exclude_custcategory" ng-model="search.exclude_custcategory" ng-true-value="'1'" ng-false-value="'0'" ng-change="searchDB()">
                            <span style="margin-top: 5px;">
                                Exclude
                            </span>
                        </label>
                        {!! Form::select('custcategory', [''=>'All'] + $custcategories::orderBy('name')->pluck('name', 'id')->all(),
                            null,
                            [
                                'class'=>'selectmultiplecustcat form-control',
                                'ng-model'=>'search.custcategory',
                                'multiple'=>'multiple',
                                'ng-change' => "onCustCategoryChanged()"
                            ])
                        !!}
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
                </div>
                <div class="row">
{{--
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
                    </div> --}}
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
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
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('po_no', 'PO Num', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('po_no', null,
                                                        [
                                                            'class'=>'form-control input-sm',
                                                            'ng-model'=>'search.po_no',
                                                            'ng-change'=>'searchDB()',
                                                            'placeholder'=>'PO Num',
                                                            'ng-model-options'=>'{ debounce: 1000 }'
                                                        ]) !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('contact', 'Attn Contact', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('contact', null,
                                                        [
                                                            'class'=>'form-control input-sm',
                                                            'ng-model'=>'search.contact',
                                                            'ng-change'=>'searchDB()',
                                                            'placeholder'=>'Attn Contact',
                                                            'ng-model-options'=>'{ debounce: 1000 }'
                                                        ]) !!}
                    </div>
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
{{--
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
                    </div> --}}
                </div>
                @else
                    <div class="row">
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('invoice', 'Invoice', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('invoice', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.transaction_id',
                                                                'ng-change'=>'searchDB()',
                                                                'placeholder'=>'Inv Num',
                                                                'ng-model-options'=>'{ debounce: 1000 }'
                                                            ]) !!}
                        </div>
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
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
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('po_no', 'PO Num', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('po_no', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.po_no',
                                                                'ng-change'=>'searchDB()',
                                                                'placeholder'=>'PO Num',
                                                                'ng-model-options'=>'{ debounce: 1000 }'
                                                            ]) !!}
                        </div>
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('contact', 'Attn Contact', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('contact', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.contact',
                                                                'ng-change'=>'searchDB()',
                                                                'placeholder'=>'Attn Contact',
                                                                'ng-model-options'=>'{ debounce: 1000 }'
                                                            ]) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('driver', 'Assigned Driver', ['class'=>'control-label search-title']) !!}
                            @if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician'))
                            <select name="driver" class="form-control select" ng-model="search.driver" ng-change="searchDB()" ng-init="onDriverInit('{{auth()->user()->name}}')">
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
                                        @if(($user->hasRole('driver') or $user->hasRole('technician') or $user->hasRole('driver-supervisor')) and count($user->profiles) > 0)
                                            <option value="{{$user->name}}">
                                                {{$user->name}}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="row">
{{--
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
                    </div> --}}

                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('delivery_from', 'Delivery Date', ['class'=>'control-label search-title']) !!}
                        <div class="input-group">
                            <datepicker>
                                <input
                                    name = "delivery_from"
                                    type = "text"
                                    class = "form-control input-sm"
                                    placeholder = "Delivery Date"
                                    ng-model = "search.delivery_from"
                                    ng-change = "dateChange('delivery_from', search.delivery_from)"
                                />
                            </datepicker>
                            <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_from', search.delivery_from)"></span>
                            <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_from', search.delivery_from)"></span>
                        </div>
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12 hidden">
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
                    @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        <label class="control-label">
                            Row Search Invoices#
                        </label>
                        <span class="text-muted small">
                            (separated by new line)
                        </span>
                        {!! Form::textarea('transactions_row', null,
                                                        [
                                                            'class'=>'form-control input-xs',
                                                            'ng-model'=>'search.transactions_row',
                                                            'ng-change'=>'searchDB()',
                                                            'placeholder'=>'',
                                                            'ng-model-options'=>'{ debounce: 1000 }',
                                                            'rows' => '3'
                                                        ]) !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        <label class="control-label">
                            Row Search PO#
                        </label>
                        <span class="text-muted small">
                            (separated by new line)
                        </span>
                        {!! Form::textarea('po_row', null,
                                                        [
                                                            'class'=>'form-control input-xs',
                                                            'ng-model'=>'search.po_row',
                                                            'ng-change'=>'searchDB()',
                                                            'placeholder'=>'',
                                                            'ng-model-options'=>'{ debounce: 1000 }',
                                                            'rows' => '3'
                                                        ]) !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('item_id', 'Has Product', ['class'=>'control-label search-title']) !!}
                        {!! Form::select('item_id', [''=>'All']+$items::where('is_active', 1)->select(DB::raw("CONCAT(product_id,' - ',name) AS full, id"))->orderBy('product_id', 'asc')->pluck('full', 'id')->all(), null, [
                            'id'=>'item_id',
                            'class'=>'selectmultiple form-control',
                            'ng-model'=>'search.item_id',
                            'ng-change' => 'searchDB()',
                            'multiple' => 'multiple'
                            ])
                        !!}
                    </div>

                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('account_manager', 'Account Manager', ['class'=>'control-label']) !!}
                        @if(auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
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
                    @endif
                </div>

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        @if(!auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                            <button class="btn btn-sm btn-primary" ng-click="exportData($event)">Export All Excel</button>
                        @endif
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked()" ng-if="drivers.length > 0"><i class="fa fa-map-o"></i> Generate Map</button>
                        @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                        <button class="btn btn-sm btn-default" ng-click="onDriverAssignToggleClicked($event)">
                            <span ng-if="driverOptionShowing === true">
                                Hide Extra Buttons
                            </span>
                            <span ng-if="driverOptionShowing === false">
                                Show Extra Buttons
                            </span>
                        </button>

                        <button class="btn btn-sm btn-primary" ng-click="onBatchFunctionClicked($event)">
                            Batch Function
                            <span ng-if="!showBatchFunctionPanel" class="fa fa-caret-down"></span>
                            <span ng-if="showBatchFunctionPanel" class="fa fa-caret-up"></span>
                        </button>

                        <button class="btn btn-sm btn-info" ng-click="onRouteTemplateClicked($event)">
                            Save Route Template
                            <span ng-if="!showRouteTemplatePanel" class="fa fa-caret-down"></span>
                            <span ng-if="showRouteTemplatePanel" class="fa fa-caret-up"></span>
                        </button>
                        @endif
                        @if(!auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                        <button class="btn btn-sm btn-primary" ng-click="onExportPdfClicked($event)">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                            Export PDF
                        </button>
                        @endif
                        @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                        <button class="btn btn-sm btn-warning" ng-click="onRefreshTableClicked($event)">
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </button>
                        @endif
                        <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span>
                    </div>

                </div>
                <div ng-show="showBatchFunctionPanel">
                    <hr class="row">
                    <div class="row">
                        <div class="row col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('batch_assign_driver', 'Batch Assign Driver', ['class'=>'control-label search-title']) !!}
                                    <select name="driver" class="form-control select" ng-model="form.driver">
                                        <option value="-1">
                                            -- Clear --
                                        </option>
                                        @foreach($users::where('is_active', 1)->orderBy('name')->get() as $user)
                                            @if(($user->hasRole('driver') or $user->hasRole('technician') or $user->hasRole('driver-supervisor') or $user->id === 100010) and count($user->profiles) > 0)
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

                        <div class="row col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('batch_change_delivery_date', 'Batch Change Delivery Date', ['class'=>'control-label search-title']) !!}
                                    <datepicker>
                                        <input
                                            name = "delivery_date"
                                            type = "text"
                                            class = "form-control input-sm"
                                            placeholder = "Delivery Date"
                                            ng-model = "form.delivery_date"
                                            ng-change = "formDeliveryDateChange('delivery_date', form.delivery_date)"
                                        />
                                    </datepicker>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                <label class="control-label"></label>
                                <div class="btn-group-control">
                                    <button type="submit" class="btn btn-sm btn-warning" name="batch_change_delivery_date" value="invoice" ng-click="onBatchChangeDeliveryDateClicked($event)"><i class="fa fa-calendar" aria-hidden="true"></i> Batch Change Delivery Date</button>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <button class="btn btn-sm btn-primary" ng-click="onBothBatchAssignClicked($event)">
                                    Batch Update (driver & date)
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr class="row">
                </div>
                <div ng-show="showRouteTemplatePanel">
                    <hr class="row">
                        <div class="row col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label for="template-name">
                                    Template Name
                                </label>
                                <label style="color: red;">*</label>
                                <input type="text" class="form-control" ng-model="form.template_name">
                            </div>
                            <div class="form-group">
                                <label for="template-desc">
                                    Template Desc
                                </label>
                                <textarea class="form-control" ng-model="form.template_desc" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-sm btn-success" ng-click="onSaveTemplateButtonClicked($event)">
                                    Save Template
                                </button>
                            </div>
                        </div>
                    <hr class="row">
                </div>
                @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                <div class="row">
                    <div class="col-md-5 col-sm-6 col-xs-12" style="padding: 10px 0px 10px 0px;">
                        <div class="col-md-5 col-xs-5">
                            Total Amount
                        </div>
                        <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                            <strong>@{{grand_delivered_total + ' / ' + grand_total}}</strong>
                        </div>
                        <div class="col-md-5 col-xs-5">
                            Total Qty
                        </div>
                        <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                            <strong>@{{grand_delivered_qty + ' / ' + grand_qty}}</strong>
                        </div>
                        <div class="col-md-5 col-xs-5">
                            Total Count
                        </div>
                        <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                            <strong>@{{grand_delivered_count + ' / ' + grand_count}}</strong>
                        </div>
                    </div>
                </div>
                @endif

                {!! Form::close() !!}
                <div class="alt-table-responsive" id="exportable">
                    <table class="table table-list-search table-hover table-bordered" ng-repeat="(driverkey, driver) in drivers">
                        {{-- hidden table for excel export --}}
                        <tr style="background-color: #8BD5FC">
                            @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                            <th colspan="17">
                            @else
                            <th colspan="13">
                            @endif
                                @{{driver.name}}
                                @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                                <button type="button" class="btn btn-sm btn-default" ng-click="onDriverRowToggleClicked($event, driverkey)">
                                    <span ng-if="!driver.showrow" class="fa fa-caret-down"></span>
                                    <span ng-if="driver.showrow" class="fa fa-caret-up"></span>
                                </button>
                                @endif
                                <button type="button" class="btn btn-xs btn-default" style="margin-left: 5px;" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(null, driverkey, null)" ng-if="driver.total_count > 0"><i class="fa fa-map-o"></i> Driver Map</button>
                                @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                                    <button type="button" class="btn btn-xs btn-warning" ng-click="onDriverRefreshClicked($event, driverkey)"><i class="fa fa-refresh" aria-hidden="true"></i> Sort</button>
                                    <button class="btn btn-xs btn-default" ng-click="onInitTransactionsSequence($event, driverkey)">
                                        Re-number
                                    </button>
                                @endif
{{--
                                <button class="btn btn-xs btn-primary" ng-click="onExportPdfClicked($event, driver.name)">
                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    Export PDF
                                </button> --}}
{{--
                                <span class="pull-right">

                                </span> --}}
                            </th>
                            <th class="text-center">
                                (Inv#)
                                <span class="col-md-12 col-sm-12 col-xs-12">
                                    @{{driver.delivered_count}}/
                                </span>
                                <span class="col-md-12 col-sm-12 col-xs-12">
                                    @{{driver.total_count}}
                                </span>
                            </th>
                            <th class="text-center">
                                (S$)
                                <span class="col-md-12 col-sm-12 col-xs-12">
                                    @{{driver.delivered_amount}}/
                                </span>
                                <span class="col-md-12 col-sm-12 col-xs-12">
                                    @{{driver.total_amount}}
                                </span>
                            </th>
                            <th class="text-center">
                                (Qty)
                                <span class="col-md-12 col-sm-12 col-xs-12">
                                    @{{driver.delivered_qty}}/
                                </span>
                                <span class="col-md-12 col-sm-12 col-xs-12">
                                    @{{driver.total_qty}}
                                </span>
                            </th>
                        </tr>
                        <tr style="background-color: #DDFDF8; font-size: 12px;" ng-show="driver.showrow">
                            <th class="col-md-1 text-center">
                                <input type="checkbox" id="check_all" ng-model="form.checkall[driverkey]" ng-change="onCheckAllChecked(driverkey)"/>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('sequence', driverkey)">
                                #
                                <span ng-if="search.sortName == 'sequence' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'sequence' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('transactions.id', driverkey)">
                                INV #
                                <span ng-if="search.sortName == 'transactions.id' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'transactions.id' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('cust_id', driverkey)">
                                ID
                                <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
{{--
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('company')">
                                ID Name
                                <span ng-if="search.sortName == 'company' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'company' && search.sortBy" class="fa fa-caret-up"></span>
                            </th> --}}
                            <th class="col-md-1 text-center">
                                Action
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('transactions.del_postcode', driverkey)">
                                Postcode
                                <span ng-if="search.sortName == 'transactions.del_postcode' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'transactions.del_postcode' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                Inv Details
                            </th>
                            <th class="col-md-2 text-center">
                                Address
                            </th>
                            <th class="col-md-1 text-center">
                                PO Num
{{--
                                <a href="" ng-click="sortTable('transactions.contact', driverkey)">
                                Contact
                                <span ng-if="search.sortName == 'transactions.contact' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'transactions.contact' && search.sortBy" class="fa fa-caret-up"></span> --}}
                            </th>
                            <th class="col-md-1 text-center">
                                <span class="col-md-12">
                                    注释
                                </span>
                                <span class="col-md-12">
                                    T.Remark
                                </span>
                            </th>
                            <th class="col-md-2 text-center">
                                <span class="col-md-12">
                                    客户属性
                                </span>
                                <span class="col-md-12">
                                    Ops
                                </span>
                            </th>
                            @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                            <th class="col-md-1 text-center">
                                Zone
                            </th>
                            @endif
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('status', driverkey)">
                                Status
                                <span ng-if="search.sortName == 'status' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'status' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            {{-- @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician')) --}}
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('driver', driverkey)">
                                Assigned Driver
                                <span ng-if="search.sortName == 'driver' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'driver' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            {{-- @endif --}}
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('total', driverkey)">
                                Total Amount
                                <span ng-if="search.sortName == 'total' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'total' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('total_qty', driverkey)">
                                Total Qty
                                <span ng-if="search.sortName == 'total_qty' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'total_qty' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('pay_status', driverkey)">
                                Pay Status
                                <span ng-if="search.sortName == 'pay_status' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'pay_status' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('updated_by', driverkey)">
                                Last Modified By
                                <span ng-if="search.sortName == 'updated_by' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'updated_by' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('updated_at', driverkey)">
                                Last Modified Time
                                <span ng-if="search.sortName == 'updated_at' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'updated_at' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('transactions.creator_name', driverkey)">
                                Created By
                                <span ng-if="search.sortName == 'transactions.creator_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'transactions.creator_name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            @endif
                        </tr>
                        <tbody ng-show="driver.showrow">
                            <tr ng-repeat="(transactionkey, transaction) in driver.transactions" style="font-size: 14px;">
                                <td class="col-md-1 text-center">
                                    <input type="checkbox" name="checkbox" ng-model="transaction.check">
                                </td>
                                <td class="col-md-1 text-center">
                                    @if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician') or auth()->user()->hasRole('event') or auth()->user()->hasRole('event_plus'))
                                        @{{transaction.sequence ? transaction.sequence * 1 : ''}}
                                    @else
                                        <input type="text" class=" text-center" style="width:40px" ng-model="transaction.sequence" ng-value="transaction.sequence = transaction.sequence ? transaction.sequence * 1 : '' " ng-model-options="{ debounce: 1000 }" ng-change="onSequenceChanged(transaction, driverkey, transactionkey)">
                                        <span class="hidden">
                                            @{{transaction.sequence ? transaction.sequence * 1 : ''}}
                                        </span>
                                    @endif
                                </td>
                                <td class="col-md-1 text-center">
                                    <a href="/transaction/@{{ transaction.id }}/edit" ng-style="{'color': transaction.label_color, 'background-color': transaction.back_color}">
                                        @{{ transaction.id }}
                                    </a>
                                    <span class="col-md-12">
                                        <i class="fa fa-flag" aria-hidden="true" style="color:red; cursor:pointer;" ng-if="transaction.is_important" ng-click="onIsImportantClicked(transaction.id, driverkey, transactionkey)"></i>
                                        <i class="fa fa-flag" aria-hidden="true" style="color:grey; cursor:pointer;" ng-if="!transaction.is_important" ng-click="onIsImportantClicked(transaction.id, driverkey, transactionkey)"></i>
                                    </span>
                                </td>

                                <td class="col-md-1 text-center">
                                    <span class="col-md-12">
                                        @{{ transaction.cust_id }}
                                    </span>
                                    <span class="col-md-12"><a href="/person/@{{ transaction.person_id }}">@{{transaction.cust_id[0] == 'D' || transaction.cust_id[0] == 'H' ? transaction.name : transaction.company}}</a></span>
                                </td>
{{--
                                <td class="col-md-1 text-center">

                                </td> --}}
                                <td class="col-md-1 text-center">
                                    {{-- print invoice         --}}
                                    <a href="/transaction/download/@{{ transaction.id }}" class="btn btn-primary btn-xs" ng-if="transaction.status != 'Pending' && transaction.status != 'Cancelled'">Print</a>
                                    {{-- button view shown when cancelled --}}
                                    <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-xs btn-default" ng-if="transaction.status == 'Cancelled'">View</a>
                                    <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(transaction, driverkey, transactionkey)" ng-if="driverOptionShowing"><i class="fa fa-map-o fa-xs"></i> Map</button>
                                    <button type="button" class="btn btn-success btn-xs" ng-click="onWhatsappClicked(transaction)" ng-if="driverOptionShowing"><i class="fa fa-whatsapp" aria-hidden="true"></i> Whatsapp</button>
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ transaction.del_postcode }}
                                </td>
                                <td class="col-md-1 text-center" style="min-width: 80px; font-size: 12px;">
                                    <span ng-if="transaction.deals">
                                        <span ng-repeat="deal in transaction.deals">
                                            <span class="row" style="margin: 0px 0px 0px 0px;">
                                                @{{deal.item.product_id}} (@{{deal.qty | currency: "": 1}})
                                            </span>
                                        </span>
                                    </span>
                                </td>
                                <td class="col-md-2 text-left">
                                    @{{transaction.del_address}}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ transaction.po_no}}
                                </td>
                                <td class="col-md-1 text-center">
                                    @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                                        <textarea name="transremark" rows="3" style="max-width: 120px;" ng-model='transaction.transremark' ng-change="onTransRemarkChanged(transaction.id, driverkey, transactionkey)" ng-model-options="{debounce: 500}" ng-if="driverOptionShowing"></textarea>
                                        <span ng-if="!driverOptionShowing">
                                            @{{transaction.transremark}}
                                        </span>
                                    @else
                                        <span v-if="transaction.is_important">
                                            @{{transaction.transremark}}
                                        </span>
                                        <span v-if="!transaction.is_important">
                                            @{{transaction.transremark | cut:true:30:'...'}}
                                        </span>
                                    @endif
                                </td>
                                <td class="col-md-2 text-center">
                                    @{{ transaction.operation_note | cut:true:40:'...' }}
                                </td>
                                @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                                <td class="col-md-1 text-center">
                                    @{{transaction.zone_name}}
                                </td>
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
                                <td class="col-md-1 text-center">
                                    @{{ transaction.driver }}
                                    @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                                    <ui-select ng-model="transaction.driverchosen" on-select="onFormDriverChanged(transaction, driverkey, transactionkey)" ng-if="driverOptionShowing">
                                        <ui-select-match allow-clear="true">
                                            <span ng-bind="$select.driver.name"></span>
                                        </ui-select-match>
                                        <ui-select-choices null-option="removeDriver" repeat="user in users | filter: $select.search">
                                            <div ng-bind-html="user.name | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>
                                    @endif
                                </td>
                                <td class="col-md-1 text-right">
                                    @{{ transaction.total | currency: "": 2}}
                                </td>
                                <td class="col-md-1 text-center">@{{ transaction.total_qty }}</td>
                                <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.pay_status == 'Owe'">
                                    @{{ transaction.pay_status }}
                                </td>
                                <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.pay_status == 'Paid'">
                                    @{{ transaction.pay_status }}
                                </td>
                                @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                                <td class="col-md-1 text-center">@{{ transaction.updated_by}}</td>
                                <td class="col-md-1 text-center">@{{ transaction.updated_at}}</td>
                                <td class="col-md-1 text-center">@{{ transaction.creator_name }}</td>
                                @endif
                            </tr>
                            <tr ng-if="!driver.transactions || driver.transactions.length == 0">
                                <td colspan="24" class="text-center">No Records Found</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-list-search table-hover table-bordered" ng-if="!drivers || drivers.length == 0">
                        <tr>
                            <td colspan="24" class="text-center">No Records Found</td>
                        </tr>
                    </table>
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

    <script src="/js/jobassign.js"></script>
    <script>
        $('#delfrom').datetimepicker({
            format: 'DD-MMMM-YYYY'
        });
    </script>
@stop