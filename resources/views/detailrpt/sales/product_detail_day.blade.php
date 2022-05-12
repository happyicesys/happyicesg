@inject('profiles', 'App\Profile')
@inject('custcategories', 'App\Custcategory')
@inject('users', 'App\User')

<div ng-controller="productDayDetailController">
<div class="col-md-12 col-xs-12">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                {!! Form::text('cust_id', null,
                                            [
                                                'class'=>'form-control input-sm',
                                                'ng-model'=>'search.cust_id',
                                                'placeholder'=>'Cust ID',
                                                'ng-change'=>'searchDB()',
                                                'ng-model-options'=>'{ debounce: 500 }'
                                            ])
                !!}
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('delivery_from', 'Delivery From', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker selector="form-control">
                        <input
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Delivery From"
                            ng-model = "search.delivery_from"
                            ng-change = "onDeliveryFromChanged(search.delivery_from)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_from', search.delivery_from)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_from', search.delivery_from)"></span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('product_id', 'Product ID', ['class'=>'control-label search-title']) !!}
                {!! Form::text('product_id', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.product_id',
                                                    'placeholder'=>'Product ID',
                                                    'ng-change'=>'searchDB()',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                !!}
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
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
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('delivery_to', 'Delivery To', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker selector="form-control">
                        <input
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Delivery To"
                            ng-model = "search.delivery_to"
                            ng-change = "onDeliveryToChanged(search.delivery_to)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_to', search.delivery_to)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_to', search.delivery_to)"></span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('product_name', 'Product Name', ['class'=>'control-label search-title']) !!}
                {!! Form::text('product_name', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.product_name',
                                                    'placeholder'=>'Product Name',
                                                    'ng-change'=>'searchDB()',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                !!}
            </div>
        </div>
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
                    'ng-model'=>'search.profile_id',
                    'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
                <select name="statuses" class="selectmultiple form-control" ng-model="search.statuses" ng-change="searchDB()" multiple>
                    <option value="">All</option>
                    <option value="Pending">Pending</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('is_commission', 'Include Commission', ['class'=>'control-label search-title']) !!}
                {!! Form::select('is_commission', ['0'=>'No', ''=>'Yes, all', '1'=>'VM Commission', '2'=> 'Supermarket Fee'], null,
                    [
                        'class'=>'select form-control',
                        'ng-model'=>'search.is_commission',
                        'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('person_active', 'Customer Status', ['class'=>'control-label search-title']) !!}
                <select name="person_active" id="person_active" class="selectmultiple form-control" ng-model="search.person_active" ng-change="searchDB()" multiple>
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
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('is_inventory', 'Product Type', ['class'=>'control-label search-title']) !!}
                {!! Form::select('is_inventory', ['1'=>'Inventory Item', ''=>'All'],
                    null,
                    [
                        'class'=>'select form-control',
                        'ng-model'=>'search.is_inventory',
                        'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('driver', 'Assigned Driver', ['class'=>'control-label search-title']) !!}
                @if(Auth::user()->hasRole('driver') or auth()->user()->hasRole('technician'))
                    {{-- <input type="text" class="form-control input-sm" placeholder="Delivered By" ng-model="search.driver" ng-init="driverInit('{{auth()->user()->name}}')" readonly> --}}
                    <select name="driver" class="form-control selectmultiple" ng-model="search.driver" ng-change="searchDB()" ng-init="driverInit('{{auth()->user()->name}}')" multiple disabled>
                        {{-- <option value="">All</option> --}}
                        <option value="{{auth()->user()->name}}">
                            {{auth()->user()->name}}
                        </option>
                    </select>
                @else
                    <select name="driver" class="form-control selectmultiple" ng-model="search.driver" ng-change="searchDB()" multiple>
                        {{-- <option value="">All</option> --}}
                        @foreach($users::where('is_active', 1)->orderBy('name')->get() as $user)
                            @if($user->hasRole('driver') or $user->hasRole('technician') or $user->hasRole('driver-supervisor'))
                                <option value="{{$user->name}}">
                                    {{$user->name}}
                                </option>
                            @endif
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
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
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
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
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('tags', 'Cust Tags', ['class'=>'control-label search-title']) !!}
                <select name="tags" id="tags" class="selectmultiple form-control" ng-model="search.tags" ng-change="searchDB($event)" multiple>
                    <option value="">All</option>
                    @foreach($persontags::orderBy('name')->get() as $persontag)
                        <option value="{{$persontag->id}}">
                            {{$persontag->name}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                <label class="pull-right">
                    <input type="checkbox" name="exACategory" ng-model="search.exACategory" ng-change="onExACategoryChanged()">
                    <span style="margin-top: 5px; margin-right: 5px;">
                        Ex A
                    </span>
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
                        'ng-change' => 'searchDB()'
                    ])
                !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('custcategory_group', 'CustCategory Group', ['class'=>'control-label search-title']) !!}
                {!! Form::select('custcategory_group', [''=>'All'] + $custcategoryGroups::orderBy('name')->pluck('name', 'id')->all(),
                    null,
                    [
                        'class'=>'selectmultiple form-control',
                        'ng-model'=>'search.custcategory_group',
                        'multiple'=>'multiple',
                        'ng-change' => "searchDB()"
                    ])
                !!}
            </div>
        </div>
    </div>
{{--
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('item_id', 'Product', ['class'=>'control-label']) !!}
                {!! Form::select('item_id',
                        [''=>'All']+$items::where('is_active', 1)->where('is_inventory', 1)->select(DB::raw("CONCAT(product_id,' - ',name) AS full, id"))->lists('full', 'id')->all(),
                        null,
                        [
                            'class'=>'selectmultiple form-control',
                            'ng-model'=>'search.item_id',
                            'ng-change'=>'searchDB()',
                            'multiple'=>'multiple'
                        ])
                !!}
            </div>
        </div>
    </div> --}}
</div>

<div class="row" style="padding-left: 15px; padding-top:20px;">
    <div class="col-md-4 col-xs-12">
        <span class="row" ng-if="search.edited">
            <small>You have edited the filter, search?</small>
        </span>
        <button class="btn btn-success" ng-click="onSearchButtonClicked($event)">
            Search
            <i class="fa fa-search" ng-show="!spinner"></i>
            <i class="fa fa-spinner fa-1x fa-spin" ng-show="spinner"></i>
        </button>
            @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
            @endif
        <span ng-show="spinner"> <i style="color:red;" class="fa fa-spinner fa-2x fa-spin"></i></span>
    </div>
    <div class="col-md-5 col-xs-12">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Total Amount:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ totals['total_amount'] ? totals['total_amount'] : 0 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Total Qty:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ totals['total_qty'] ? totals['total_qty'] : 0 | currency: "": 4 }}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Total Cost:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ totals['total_cost'] ? totals['total_cost'] : 0 | currency: "": 2 }}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Total Gross:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ totals['total_gross'] ? totals['total_gross'] : 0 | currency: "": 2 }}</strong>
            </div>
        </div>
        <div class="row" ng-if="totals['total_gross'] > 0">
            <div class="col-md-6 col-xs-6">
                Total Gross (%):
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ totals['total_amount'] ? (totals['total_gross']/ totals['total_amount'] * 100) : 0 | currency: "": 0 }}</strong>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-xs-12 text-right">
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
            <label style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
        </div>
    </div>
</div>

    <div class="table-responsive" id="exportable_productday" style="padding-top: 20px;">
        <table class="table table-list-search table-hover table-bordered">

            {{-- hidden table for excel export --}}
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Total Amount</td>
                <td data-tableexport-display="always" class="text-right">@{{total_amount | currency: "": 2}}</td>
            </tr>
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Total Qty</td>
                <td data-tableexport-display="always" class="text-right">@{{total_qty }}</td>
            </tr>
            <tr class="hidden" data-tableexport-display="always">
                <td></td>
            </tr>

            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>

                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('product_id')">
                    Product ID
                    <span ng-if="search.sortName == 'product_id' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'product_id' && search.sortBy" class="fa fa-caret-up"></span>
                </th>

                <th class="col-md-6 text-center">
                    <a href="" ng-click="sortTable('product_name')">
                    Product Name
                    <span ng-if="search.sortName == 'product_name' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'product_name' && search.sortBy" class="fa fa-caret-up"></span>
                </th>

                <th class="col-md-2 text-center">
                    <a href="" ng-click="sortTable('amount')">
                    Total Amount
                    <span ng-if="search.sortName == 'amount' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'amount' && search.sortBy" class="fa fa-caret-up"></span>
                </th>

                <th class="col-md-2 text-center">
                    <a href="" ng-click="sortTable('qty')">
                    Total Qty
                    <span ng-if="search.sortName == 'qty' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'qty' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tbody>

                <tr dir-paginate="item in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" pagination-id="product_detail_day" total-items="totalCount" current-page="currentPage">
                    <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ item.product_id }}
                    </td>
                    <td class="col-md-6 text-left" >
                        @{{ item.product_name }}
                        <span ng-if="item.remark">
                            - @{{ item.remark }}
                        </span>
                    </td>
                    <td class="col-md-2 text-right" >
                        @{{ item.amount | currency: "": 2}}
                    </td>
                    <td class="col-md-2 text-right">
                        @{{ item.qty | currency: "": 4}}
                    </td>
                </tr>
                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="14" class="text-center">No Records Found</td>
                </tr>
            </tbody>
        </table>

        <div>
              <dir-pagination-controls max-size="5" pagination-id="product_detail_day" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>