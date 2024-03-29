<div class="panel panel-default" ng-cloak>
    <div class="panel-heading">
        <div class="panel-title">
            <div class="pull-right">
                @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user')and !auth()->user()->hasRole('driver-supervisor') and !auth()->user()->hasRole('event'))
                @cannot('transaction_view')
                    <a href="/person/create" class="btn btn-sm btn-success">+ New {{ $PERSON_TITLE }}</a>
                    @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('event_plus') and !auth()->user()->hasRole('salesperson'))
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
                    {!! Form::label('code', 'Cust Code', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('code', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.code',
                                                        'placeholder'=>'Customer Code',
                                                    ])
                    !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('cust_prefix_id', 'Cust Prefix', ['class'=>'control-label search-title']) !!}
                    <select name="cust_prefix_id" id="cust_prefix_id" class="selectmultiple form-control" ng-model="search.cust_prefix_id" multiple>
                        <option value="-1">-- Unassigned --</option>
                        @foreach($custPrefixes::orderBy('code')->get() as $custPrefix)
                            <option value="{{$custPrefix->id}}">
                                {{$custPrefix->code}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-12">
                    <div class="form-group">
                    {!! Form::label('company', 'Cust Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('company', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.company',
                                                        'placeholder'=>'ID Name',
                                                    ])
                    !!}
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-12">
                    <div class="form-group">
                    {!! Form::label('vend_code', 'Vend ID', ['class'=>'control-label search-title']) !!}
                    <span style="font-size: 11px;">
                        ("," for multiple)
                    </span>
                    {!! Form::text('vend_code', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.vend_code',
                                                        'placeholder'=>'Vend ID',
                                                    ])
                    !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                    <label class="pull-right">
                        <input type="checkbox" name="excludeCustCat" ng-model="search.excludeCustCat">
                        <span style="margin-top: 5px; margin-right: 5px; font-size: 12px;">
                            Exclude
                        </span>
                    </label>
                    <select name="custcategory" class="selectmultiple form-control" ng-model="search.custcategory" multiple>
                        <option value="">All</option>
                        @foreach($custcategories::orderBy('name')->get() as $custcategory)
                        <option value="{{$custcategory->id}}">{{$custcategory->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('custcategory_group', 'CustCategory Group', ['class'=>'control-label search-title']) !!}
                    <label class="pull-right">
                    </label>
                    {!! Form::select('custcategory_group', [''=>'All'] + $custcategoryGroups::orderBy('name')->pluck('name', 'id')->all(),
                        null,
                        [
                            'class'=>'selectmultiple form-control',
                            'ng-model'=>'search.custcategory_group',
                            'multiple'=>'multiple',
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
                                                    ])
                    !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('active', 'Status', ['class'=>'control-label search-title']) !!}
                    <select name="active" id="active" class="selectmultiple form-control" ng-model="search.active" multiple>
                        <option value="">All</option>
                        <option value="Potential">Potential</option>
                        <option value="New">New</option>
                        <option value="Yes">Active</option>
                        @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                            <option value="Pending">Pending</option>
                            <option value="No">Inactive</option>
                        @endif
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('is_vend', 'Is Vending Machine?', ['class'=>'control-label']) !!}
                    <select name="is_vend" class="select form-control" ng-model="search.is_vend">
                        <option value="">All</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('del_address', 'Del Address', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('del_address', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.del_address',
                                                        'placeholder'=>'Del Address',
                                                    ])
                    !!}
                </div>
            </div>
            <div class="row">
                @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user'))
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('profile_id', [''=>'All']+$profiles::filterUserProfile()->pluck('name', 'id')->all(), null, ['id'=>'profile_id',
                        'class'=>'select form-control',
                        'ng-model'=>'search.profile_id',
                        ])
                    !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('franchisee_id', 'Franchisee', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('franchisee_id', [''=>'All', '0' => 'Own']+$franchisees::filterUserFranchise()->select(DB::raw("CONCAT(user_code,' (',name,')') AS full, id"))->orderBy('user_code')->pluck('full', 'id')->all(), null, ['id'=>'franchisee_id',
                        'class'=>'select form-control',
                        'ng-model'=>'search.franchisee_id',
                        ])
                    !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('account_manager', 'Account Manager', ['class'=>'control-label']) !!}
                    @if(auth()->user()->hasRole('merchandiser'))
                        <select name="account_manager" class="select form-control" ng-model="search.account_manager" ng-init="merchandiserInit('{{auth()->user()->id}}')" disabled>
                            <option value="">All</option>
                            @foreach($users::whereIn('type', ['staff', 'admin'])->orderBy('name')->get() as $user)
                            <option value="{{$user->id}}">
                                {{$user->name}}
                            </option>
                            @endforeach
                        </select>
                    @else
                        {!! Form::select('account_manager',
                                [''=>'All']+$users::whereIn('type', ['staff', 'admin'])->lists('name', 'id')->all(),
                                null,
                                [
                                    'class'=>'select form-control',
                                    'ng-model'=>'search.account_manager',
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
                            ])
                    !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('freezers', 'Freezer', ['class'=>'control-label search-title']) !!}
                    <select name="freezers" id="freezers" class="selectmultiple form-control" ng-model="search.freezers" multiple>
                        @foreach($freezers::orderBy('name')->get() as $freezer)
                            <option value="{{$freezer->id}}">
                                {{$freezer->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('created_month', 'Created Month', ['class'=>'control-label search-title']) !!}
                    <select class="select form-control" name="created_month" ng-model="search.created_month">
                        <option value="">All</option>
                        @foreach($month_options as $key => $value)
                            <option value="{{$key}}" selected="{{Carbon\Carbon::today()->month.'-'.Carbon\Carbon::today()->year ? 'selected' : ''}}">{{$value}}</option>
                        @endforeach
                        <option value="-1">Earlier than that</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('tags', 'Tags', ['class'=>'control-label search-title']) !!}
                    <select name="tags" id="tags" class="selectmultiple form-control" ng-model="search.tags" multiple>
                        <option value="">All</option>
                        @foreach($persontags::orderBy('name')->get() as $persontag)
                            <option value="{{$persontag->id}}">
                                {{$persontag->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('price_templates', 'Price Template', ['class'=>'control-label search-title']) !!}
                    <select name="price_templates" id="price_templates" class="selectmultiple form-control" ng-model="search.priceTemplates" multiple>
                        <option value="">All</option>
                        @foreach($priceTemplates::latest()->get() as $priceTemplate)
                            <option value="{{$priceTemplate->id}}">
                                {{$priceTemplate->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('is_pwp', 'Is PWP?', ['class'=>'control-label search-title']) !!}
                    <select name="is_pwp" id="is_pwp" class="select form-control" ng-model="search.is_pwp">
                        <option value="">All</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('location_type_id', 'Location Type', ['class'=>'control-label search-title']) !!}
                    <select name="location_type_id" id="location_type_id" class="selectmultiple form-control" ng-model="search.location_type_id" multiple>
                        <option value="-1">-- Unassigned --</option>
                        @foreach($locationTypes::orderBy('sequence')->get() as $locationType)
                            <option value="{{$locationType->id}}">
                                {{$locationType->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('created_from', 'Created From', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                      <datepicker>
                          <input
                              name = "created_from"
                              type = "text"
                              class = "form-control input-sm"
                              placeholder = "Date From"
                              ng-model = "search.created_from"
                              ng-change = "dateChange('created_from', search.created_from)"
                          />
                      </datepicker>
                      <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('created_from', search.created_from)"></span>
                      <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('created_from', search.created_from)"></span>
                  </div>
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                  {!! Form::label('created_to', 'Created To', ['class'=>'control-label search-title']) !!}
                  <div class="input-group">
                    <datepicker>
                        <input
                            name = "created_to"
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Date To"
                            ng-model = "search.created_to"
                            ng-change = "dateChange('created_to', search.created_to)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('created_to', search.created_to)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('created_to', search.created_to)"></span>
                  </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('customer_types', 'Types', ['class'=>'control-label search-title']) !!}
                    <select name="customer_types" id="customer_types" class="selectmultiple form-control" ng-model="search.customer_types" multiple>
                        <option value="vm">-- All VM --</option>
                        <option value="is_vending">FVM</option>
                        <option value="is_dvm">DVM</option>
                        <option value="is_combi">Combi</option>
                        <option value="is_subsidiary">Freezer(Supermarket)</option>
                        <option value="is_non_freezer_point">Non Freezer Point</option>
                        <option value="na">-- N/A --</option>
                    </select>
                </div>


                {{-- <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('prefix_code', 'Prefix + Code', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('prefix_code', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.prefix_code',
                                                        'placeholder'=>'Prefix + Code',
                                                    ])
                    !!}
                </div> --}}
            </div>


            <div class="row" style="padding-top: 20px;">
                <div class="col-md-8 col-xs-12">
                    <span class="row" ng-if="search.edited">
                        <small>You have edited the filter, search?</small>
                    </span>
                    <div class="btn-group hidden-xs">
                        <button class="btn btn-sm btn-success" ng-click="onSearchButtonClicked($event)">
                            Search
                            <i class="fa fa-search" ng-show="!spinner"></i>
                            <i class="fa fa-spinner fa-1x fa-spin" ng-show="spinner"></i>
                        </button>
                        @if(!auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                            @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                                <button class="btn btn-sm btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
                            @endif
                        @endif
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(null, null, 1)" ng-if="alldata.length > 0"><i class="fa fa-map-o"></i> Generate Map</button>
                        <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(null, null, 2)" ng-if="alldata.length > 0"><i class="fa fa-map-o"></i> Map with ID & Name</button>
                        @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus') and !auth()->user()->hasRole('salesperson'))
                            <button class="btn btn-sm btn-primary" ng-click="onBatchFunctionClicked($event)">
                                Batch Function
                                <span ng-if="!showBatchFunctionPanel" class="fa fa-caret-down"></span>
                                <span ng-if="showBatchFunctionPanel" class="fa fa-caret-up"></span>
                            </button>
                        @endif
                    </div>
                    <div class="visible-xs" style="margin-top: 20px; margin-bottom: 30px;">
                        <button class="btn btn-sm btn-success btn-block" ng-click="onSearchButtonClicked($event)">
                            Search
                            <i class="fa fa-search" ng-show="!spinner"></i>
                            <i class="fa fa-spinner fa-1x fa-spin" ng-show="spinner"></i>
                        </button>
                        @if(!auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                            @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                                <button class="btn btn-sm btn-primary btn-block" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
                            @endif
                        @endif
                        <button type="button" class="btn btn-sm btn-info btn-block" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(null, null, 1)" ng-if="alldata.length > 0"><i class="fa fa-map-o"></i> Generate Map</button>
                        <button type="button" class="btn btn-sm btn-default btn-block" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(null, null, 2)" ng-if="alldata.length > 0"><i class="fa fa-map-o"></i> Map with ID & Name</button>
                        @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                            <button class="btn btn-sm btn-primary btn-block" ng-click="onBatchFunctionClicked($event)">
                                Batch Function
                                <span ng-if="!showBatchFunctionPanel" class="fa fa-caret-down"></span>
                                <span ng-if="showBatchFunctionPanel" class="fa fa-caret-up"></span>
                            </button>
                        @endif
                    </div>
                    <i class="fa fa-spinner fa-2x fa-spin" ng-show="spinner"></i>
                </div>
                <div class="col-md-4 col-xs-12 text-right">
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

        <div ng-show="showBatchFunctionPanel">
            <hr class="row">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('assign_cust_prefix', 'Batch Assign Cust Prefix', ['class'=>'control-label search-title']) !!}
                            <select name="custPrefix" class="select form-control" ng-model="assignForm.custPrefix" ng-change="searchDB()">
                                <option value="">None</option>
                                @foreach($custPrefixes::orderBy('code')->get() as $custPrefix)
                                    <option value="{{$custPrefix->id}}">{{$custPrefix->code}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                        <label class="control-label"></label>
                        <div class="btn-group-control">
                            <button type="submit" class="btn btn-sm btn-warning" ng-click="onBatchAssignClicked($event, 'custPrefix')" style="margin-top: 9px;"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Assign Cust Prefix</button>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('locationType', 'Batch Assign Location Type', ['class'=>'control-label search-title']) !!}
                            <select name="locationType" class="select form-control" ng-model="assignForm.locationType" ng-change="searchDB()">
                                <option value="">None</option>
                                @foreach($locationTypes::orderBy('sequence')->get() as $locationType)
                                    <option value="{{$locationType->id}}">{{$locationType->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                        <label class="control-label"></label>
                        <div class="btn-group-control">
                            <button type="submit" class="btn btn-sm btn-info" ng-click="onBatchAssignClicked($event, 'locationType')" style="margin-top: 9px;"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Assign Location Type</button>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('assign_cust_category', 'Batch Assign Cust Category', ['class'=>'control-label search-title']) !!}
                            <select name="custcategory" class="select form-control" ng-model="assignForm.custcategory" ng-change="searchDB()">
                                <option value="">None</option>
                                @foreach($custcategories::orderBy('name')->get() as $custcategory)
                                    <option value="{{$custcategory->id}}">{{$custcategory->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                        <label class="control-label"></label>
                        <div class="btn-group-control">
                            <button type="submit" class="btn btn-sm btn-warning" ng-click="onBatchAssignClicked($event, 'custcategory')" style="margin-top: 9px;"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Assign Category</button>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('assign_acc_manager', 'Batch Assign Acc Manager', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('account_manager',
                            [''=>'None']+$users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->lists('name', 'id')->all(),
                            null,
                            [
                                'class'=>'select form-control',
                                'ng-model'=>'assignForm.account_manager'
                            ])
                    !!}
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                        <label class="control-label"></label>
                        <div class="btn-group-control">
                            <button type="submit" class="btn btn-sm btn-info" ng-click="onBatchAssignClicked($event, 'account_manager')" style="margin-top: 9px;"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Assign Acc Manager</button>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('assign_zone', 'Batch Assign Zone', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('zone_id',
                            [''=>'None']+ $zones::orderBy('priority')->lists('name', 'id')->all(),
                            null,
                            [
                                'class'=>'select form-control',
                                'ng-model'=>'assignForm.zone_id'
                            ])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                        <label class="control-label"></label>
                        <div class="btn-group-control">
                            <button type="submit" class="btn btn-sm btn-warning" ng-click="onBatchAssignClicked($event, 'zone_id')" style="margin-top: 9px;"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Assign Zone</button>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label for="assign_price_templates" class="control-label search-title">
                                Batch
                                <span ng-if="!assignForm.detach_price_template">
                                    Assign
                                </span>
                                <span ng-if="assignForm.detach_price_template">
                                    Detach
                                </span>
                                Price Template
                            </label>
                            {{-- <label class="pull-right">
                                <input type="checkbox" name="detach_price_template" ng-model="assignForm.detach_price_template">
                                <span style="margin-top: 5px; margin-right: 5px;">
                                    Detach
                                </span>
                            </label> --}}
                            <select name="tag" class="select form-control" ng-model="assignForm.price_template_id">
                                <option value="">None</option>
                                <option value="-1">-- Detach --</option>
                                @foreach($priceTemplates::latest()->get() as $priceTemplate)
                                    <option value="{{$priceTemplate->id}}">
                                        {{$priceTemplate->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                        <label class="control-label"></label>
                        <div class="btn-group-control">
                            <button type="submit" class="btn btn-sm btn-info" ng-click="onBatchAssignClicked($event, 'price_template_id')" style="margin-top: 9px;"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                                <span ng-if="!assignForm.detach_price_template">
                                    Assign
                                </span>
                                <span ng-if="assignForm.detach_price_template">
                                    Detach
                                </span>
                                Price Template
                            </button>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label for="assign_tags" class="control-label search-title">
                                Batch
                                <span ng-if="!assignForm.detach">
                                    Assign
                                </span>
                                <span ng-if="assignForm.detach">
                                    Detach
                                </span>
                                Tag
                            </label>
                            <label class="pull-right">
                                <input type="checkbox" name="detach" ng-model="assignForm.detach">
                                <span style="margin-top: 5px; margin-right: 5px;">
                                    Detach
                                </span>
                            </label>
                            <select name="tag" class="select form-control" ng-model="assignForm.tag_id">
                                <option value="">None</option>
                                @foreach($persontags::orderBy('name')->get() as $persontag)
                                    <option value="{{$persontag->id}}">
                                        {{$persontag->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                        <label class="control-label"></label>
                        <div class="btn-group-control">
                            <button type="submit" class="btn btn-sm btn-info" ng-click="onBatchAssignClicked($event, 'tag_id')" style="margin-top: 9px;"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                                <span ng-if="!assignForm.detach">
                                    Assign
                                </span>
                                <span ng-if="assignForm.detach">
                                    Detach
                                </span>
                                Tag
                            </button>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('remark', 'Batch Change Customer Remarks', ['class'=>'control-label search-title']) !!}
                            <textarea name="remark" class="form-control" rows="6" ng-model="assignForm.remark"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                        <label class="control-label"></label>
                        <div class="btn-group-control">
                            <button type="submit" class="btn btn-sm btn-warning" ng-click="onBatchAssignClicked($event, 'remark')" style="margin-top: 9px;"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Update Remarks</button>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('operation_note', 'Batch Change Ops Note', ['class'=>'control-label search-title']) !!}
                            <textarea name="operation_note" class="form-control" rows="6" ng-model="assignForm.operation_note"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                        <label class="control-label"></label>
                        <div class="btn-group-control">
                            <button type="submit" class="btn btn-sm btn-warning" ng-click="onBatchAssignClicked($event, 'operation_note')" style="margin-top: 9px;"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Update Ops Note</button>
                        </div>
                        </div>
                    </div>
                </div>
                <hr class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                <u>
                                    Batch Generate Invoices
                                </u>
                            </label>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label for="driver" class="control-label search-title">
                                    Delivery Date
                                </label>
                                <datepicker>
                                    <input
                                        name = "delivery_date"
                                        type = "text"
                                        class = "form-control input-sm"
                                        placeholder = "Delivery Date"
                                        ng-model = "assignForm.delivery_date"
                                        ng-change = "formDateChange('delivery_date', assignForm.delivery_date)"
                                    />
                                </datepicker>
                            </div>
                            <div class="form-group">
                                <label for="driver" class="control-label search-title">
                                    Driver
                                </label>
                                <select name="driver" class="form-control select" ng-model="assignForm.driver">
                                    <option value="">
                                        -- None --
                                    </option>
                                    @foreach($users::where('is_active', 1)->orderBy('name')->get() as $user)
                                        @if(($user->hasRole('driver') or $user->hasRole('technician') or $user->hasRole('driver-supervisor') or $user->id === 100010)  and count($user->profiles) > 0)
                                            <option value="{{$user->name}}">
                                                {{$user->name}}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="driver" class="control-label search-title">
                                    Is Service Notice?
                                </label>
                                <select name="is_service" class="form-control select" ng-model="assignForm.is_service">
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="control-label search-title">
                                    Transremark
                                </label>
                                <textarea class="form-control" rows="3" ng-model="assignForm.transremark"></textarea>
                            </div>
                            <div class="form-group" ng-if="assignForm.is_service == 'true'">
                                <label for="control-label search-title">
                                    Batch Create Service Notice (Every line as an item)
                                </label>
                                <textarea class="form-control" rows="5" ng-model="assignForm.serviceNotices"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                            <label class="control-label"></label>
                            <div class="btn-group-control">
                                <button type="submit" class="btn btn-sm btn-warning" ng-click="onBatchAssignClicked($event, 'transactions')" style="margin-top: 9px;"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                                    Batch Generate
                                </button>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="row">
        </div>

        <div class="table-responsive" id="exportable_people_list" style="padding-top: 20px; overflow: scroll">
            <table class="table table-list-search table-hover table-bordered" style="font-size: 14px;">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        <input type="checkbox" id="checkAll" ng-model="checkall" ng-change="onCheckAllChecked()"/>
                    </th>
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('code')">
                        Cust Code
                        <span ng-if="search.sortName == 'code' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'code' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('cust_prefix_code')">
                        Cust Prefix
                        <span ng-if="search.sortName == 'cust_prefix_code' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'cust_prefix_code' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="" ng-click="sortTable('company')">
                        Cust Name
                        <span ng-if="search.sortName == 'company' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'company' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="" ng-click="sortTable('vend_code')">
                        Vend ID
                        <span ng-if="search.sortName == 'vend_code' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'vend_code' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('earliest_delivery_date')">
                        First Inv Date
                        <span ng-if="search.sortName == 'earliest_delivery_date' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'earliest_delivery_date' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('unit_number')">
                        # of Unit
                        <span ng-if="search.sortName == 'unit_number' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'unit_number' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('custcategory')">
                        Cat
                        <span ng-if="search.sortName == 'custcategory' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'custcategory' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('custcategory_group_name')">
                        Group
                        <span ng-if="search.sortName == 'custcategory_group_name' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'custcategory_group_name' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('account_managers.name')">
                        Acc Manager
                        <span ng-if="search.sortName == 'account_managers.name' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'account_managers.name' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    @if(!auth()->user()->hasRole('salesperson'))
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
                    @endif
                    <th class="col-md-3 text-center">
                        <a href="" ng-click="sortTable('del_address')">
                        Delivery Add
                        <span ng-if="search.sortName == 'del_address' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'del_address' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('del_postcode')">
                        Postcode
                        <span ng-if="search.sortName == 'del_postcode' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'del_postcode' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        Tag(s)
                    </th>
                    <th class="col-md-1 text-center">
                        Freezer(s)
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
                        <a href="" ng-click="sortTable('updated_at')">
                        Updated At
                        <span ng-if="search.sortName == 'updated_at' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'updated_at' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('updated_by')">
                        Updated By
                        <span ng-if="search.sortName == 'updated_by' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'updated_by' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('created_at')">
                        Created At
                        <span ng-if="search.sortName == 'created_at' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'created_at' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('active')">
                        Status
                        <span ng-if="search.sortName == 'active' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'active' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('location_type_id')">
                        Location Type
                        <span ng-if="search.sortName == 'location_type_id' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'location_type_id' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                </tr>

                <tbody>
                    <tr dir-paginate="person in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage">
                        <td class="col-md-1 text-center">
                            <input type="checkbox" name="checkbox" ng-model="person.check">
                        </td>
                        <td class="col-md-1 text-center">
                            @{{ $index + indexFrom }}
                        </td>
                        <td class="col-md-1 text-center">
                            <a href="/person/@{{ person.id }}/edit">
                            @{{ person.code }}
                            </a>
                        </td>
                        <td class="col-md-1 text-center" style="max-width: 50px;">
                            @{{ person.cust_prefix_code }}
                        </td>

                        <td class="col-md-2">
                            <a href="/person/@{{ person.id }}/edit">
                            @{{ person.company }}
                            </a>
                        </td>
                        <td class="col-md-1 text-center" style="max-width: 60px;">
                            @{{ person.vend_code }}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{ person.earliest_delivery_date }}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{ person.unit_number }}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{ person.custcategory_name }}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{ person.custcategory_group_name }}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{ person.account_manager_name }}
                        </td>
                        @if(!auth()->user()->hasRole('salesperson'))
                        <td class="col-md-1" style="max-width: 100px;">@{{ person.name }}</td>
                        <td class="col-md-1">
                            @{{ person.contact }}
                            <span ng-show="person.alt_contact.length > 0">
                            / @{{ person.alt_contact }}
                            </span>
                        </td>
                        @endif
                        <td class="col-md-2" style="max-width: 200px;">
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(person, $index, 1)"><i class="fa fa-map-o"></i></button>
                                @{{ person.del_address }}
                        </td>
                        <td class="col-md-1 text-center">@{{ person.del_postcode }}</td>
                        <td class="col-md-1 text-left" style="max-width: 160; font-size: 13px;">
                            <span class="col-md-12" ng-repeat="tag in person.persontags">
                                - @{{tag.name}}
                            </span>
{{--
                            <ul style="padding-left: 15px;">
                                <li ng-repeat="tag in person.persontags">
                                    @{{tag.name}}
                                </li>
                            </ul> --}}
                        </td>
                        <td class="col-md-1 text-left" style="max-width: 150; font-size: 13px;">
                            <span class="col-md-12" ng-repeat="freezer in person.freezers">
                                - @{{freezer.name}}
                            </span>
                            {{--
                            <ul style="padding-left: 15px;">
                                <li ng-repeat="freezer in person.freezers">
                                    @{{freezer.name}}
                                </li>
                            </ul> --}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{ person.zone_name }}
                        </td>
                        <td class="col-md-1 text-center">@{{person.payterm}}</td>
                        <td class="col-md-1 text-center">@{{ person.updated_at }}</td>
                        <td class="col-md-1 text-center">@{{ person.updated_by }}</td>
                        <td class="col-md-1 text-center">@{{ person.created_at }}</td>
                        <td class="col-md-1 text-center">@{{ person.active }}</td>
                        <td class="col-md-1 text-left">@{{ person.location_type_name }}</td>
                    </tr>
                    <tr ng-if="!alldata || alldata.length == 0">
                        <td colspan="18" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel-footer">
        <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
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