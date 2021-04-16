@inject('sprofiles', 'App\Profile')
@inject('sdeals', 'App\Deal')
@inject('speople', 'App\Person')
@inject('scustcategories', 'App\Custcategory')
@inject('custcategoryGroups', 'App\CustcategoryGroup')
@inject('susers', 'App\User')

@extends('template')
@section('title')
	{{$DETAILRPT_TITLE}}
@stop
@section('content')

<div class="row">
	<a class="title_hyper pull-left" href="/detailrpt/stock/billing"><h1>Stock Billing (Bring Forward Stock Value)</h1></a>
</div>

<div class="panel panel-primary" ng-app="app" ng-controller="stockBillingController" ng-cloak>
    <div class="panel-heading">
        Stock Billing (Bring Forward Stock Value)
        <span ng-show="spinner"> <i class="fa fa-spinner fa-2x fa-spin"></i></span>
    </div>

    <div class="panel-body">
        {!! Form::open(['id'=>'submit_form', 'method'=>'POST', 'action'=>['DetailRptController@exportBillingPdf']]) !!}
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('profile_id', [''=>'All']+
                        $sprofiles::filterUserProfile()
                            ->pluck('name', 'id')
                            ->all(),
                        null,
                        [
                            'class'=>'select form-control',
                            'ng-model'=>'search.profile_id',
                            'ng-change'=>'searchDB()',
                            'ng-model-options'=>'{ debounce: 500 }'
                        ])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('delivery_from', 'Delivery From', ['class'=>'control-label search-title']) !!}
                    <datepicker selector="form-control">
                        <div class="input-group">
                            <input
                                type = "text"
                                name="delivery_from"
                                class = "form-control input-sm"
                                placeholder = "Delivery From"
                                ng-model = "search.delivery_from"
                                ng-change = "onDeliveryFromChanged(search.delivery_from)"
                            />
                            <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_from', search.delivery_from)"></span>
                            <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_from', search.delivery_from)"></span>
                        </div>
                    </datepicker>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('delivery_to', 'Delivery To', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                        <datepicker selector="form-control">
                            <input
                                type = "text"
                                name = "delivery_to"
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
                    {!! Form::label('driver', 'Delivered By', ['class'=>'control-label search-title']) !!}
                    <select name="driver" class="form-control select" ng-model="search.driver" ng-change="searchDB()">
                        <option value="">All</option>
                        @foreach($susers::where('is_active', 1)->orderBy('name')->get() as $user)
                            @if(($user->hasRole('driver') or $user->hasRole('technician') or $user->hasRole('driver-supervisor')) and count($user->profiles) > 0)
                                <option value="{{$user->name}}">
                                    {{$user->name}}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('cust_id',
                        null,
                        [
                            'class'=>'form-control',
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
                    {!! Form::label('company', 'ID Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('company',
                        null,
                        [
                            'class'=>'form-control',
                            'ng-model'=>'search.company',
                            'placeholder'=>'ID Name',
                            'ng-change'=>'searchDB()',
                            'ng-model-options'=>'{ debounce: 500 }'
                        ])
                    !!}
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('person_id',
                        [''=>'All'] +
                        $speople::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))
                            ->whereActive('Yes')
                            ->where('cust_id', 'NOT LIKE', 'H%')
                            ->whereHas('profile', function($q) {
                                $q->filterUserProfile();
                            })
                            ->orderBy('cust_id')
                            ->pluck('full', 'id')
                            ->all(),
                        null,
                        [
                            'class'=>'select form-control',
                            'ng-model'=>'search.person_id',
                            'ng-change'=>'searchDB()'
                        ])
                    !!}
                </div>
            </div>
        </div>
        <div class="row form-group">
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
                    {!! Form::select('custcategory', [''=>'All'] + $scustcategories::orderBy('name')->pluck('name', 'id')->all(),
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
                    {!! Form::label('is_commission', 'Include Comm & SFee', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('is_commission', ['0'=>'No', ''=>'Yes, all', '1'=>'VM Commission', '2'=> 'Supermarket Fee'], null,
                        [
                            'class'=>'select form-control',
                            'ng-model'=>'search.is_commission',
                            'ng-change'=>'searchDB()'
                        ])
                    !!}
                </div>
            </div>


        </div>

        <div class="row">
            <div class="col-md-3 col-sm-3 col-xs-12">

                @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                    <button class="btn btn-primary" ng-click="exportData($event)"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
                @endif
                <button type="submit" class="btn btn-default" form="submit_form" name="exportpdf" value="consolidate" ng-disabled="!search.profile_id"><i class="fa fa-book"></i> Consolidate Sales Report</button>
            </div>
            <div class="col-md-5 col-sm-5 col-xs-12">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Total Qty
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{total_qty ? total_qty : 0.00 | currency: "": 4}}
                            </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Total Cost $
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{total_costs ? total_costs : 0.00 | currency: "": 2}}
                            </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Total Selling Value $
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{total_sell_value ? total_sell_value : 0.00 | currency: "": 2}}
                            </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Total Gross Profit $
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{total_gross_profit ? total_gross_profit : 0.00 | currency: "": 2}}
                            </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Gross Profit %
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{total_gross_profit_percent ? total_gross_profit_percent : 0 | currency: "": 0}} %
                            </strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 15px;">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            SFee
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{total_sf_fee ? total_sf_fee : 0.00 | currency: "": 2}}
                            </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Gross Profit After SFee
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{total_gross_after_sf_fee ? total_gross_after_sf_fee : 0.00 | currency: "": 2}}
                            </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Gross Profit % After SFee
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{total_gross_after_sf_fee_percent ? total_gross_after_sf_fee_percent : 0 | currency: "": 0}} %
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12 text-right">
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

        {{-- <div class="row" ng-show="internal_billing_div"> --}}
            <hr class="row">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('bill_profile', 'Profile in Action ', ['class'=>'control-label search-title']) !!}
{{--                                 <select class="select form-control" name="bill_profile">
                                    @foreach($sprofiles::filterUserProfile()->get() as $index => $profile)
                                        <option value="{{$profile->id}}">{{$profile->name}}</option>
                                    @endforeach
                                </select> --}}
                                {!! Form::select('bill_profile',
                                    $sprofiles::filterUserProfile()
                                        ->pluck('name', 'id'),
                                    null,
                                    [
                                        'class'=>'select form-control'
                                    ])
                                !!}
                                <p class="text-muted">*For issue bill, must select "Profile" at stock billing filter (Cannot be "All")</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                            <label class="control-label"></label>
                            <div class="btn-group-control">
                                <button type="submit" class="btn btn-default" form="submit_form" name="exportpdf" value="bill" ng-disabled="!search.profile_id"><i class="fa fa-usd"></i> Issue Bill</button>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            <hr class="row">
        {{-- </div> --}}
        {!! Form::close() !!}

        <div class="table-responsive" id="exportable_stockbilling" style="padding-top: 20px;">
            <table class="table table-list-search table-hover table-bordered">
                <tr class="hidden">
                    <td></td>
                    <td data-tableexport-display="always">Total Costing $</td>
                    <td data-tableexport-display="always" class="text-right">@{{total_costs | currency: "": 2}}</td>
                </tr>
                <tr class="hidden">
                    <td></td>
                    <td data-tableexport-display="always">Total Selling Value $</td>
                    <td data-tableexport-display="always" class="text-right">@{{total_sell_value | currency: "": 2}}</td>
                </tr>
                <tr class="hidden">
                    <td></td>
                    <td data-tableexport-display="always">Total Gross Profit $</td>
                    <td data-tableexport-display="always" class="text-right">@{{total_gross_profit | currency: "": 2}}</td>
                </tr>
                <tr class="hidden" data-tableexport-display="always">
                    <td></td>
                </tr>

                <tr>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        #
                    </th>
                    <th class="col-md-2 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('profile_id')">
                        Profile
                        <span ng-if="search.sortName == 'profile_id' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'profile_id' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('product_id')">
                        ID
                        <span ng-if="search.sortName == 'product_id' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'product_id' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-2 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('item_name')">
                        Product
                        <span ng-if="search.sortName == 'item_name' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'item_name' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('unit')">
                        Unit
                        <span ng-if="search.sortName == 'unit' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'unit' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('is_inventory')">
                        Is Inventory
                        <span ng-if="search.sortName == 'is_inventory' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'is_inventory' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('is_supermarket_fee')">
                        Is SFee
                        <span ng-if="search.sortName == 'is_supermarket_fee' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'is_supermarket_fee' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('qty')">
                        Total Qty
                        <span ng-if="search.sortName == 'qty' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'qty' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('avg_unit_cost')">
                        Avg Unit Cost
                        <span ng-if="search.sortName == 'avg_unit_cost' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'avg_unit_cost' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('total_cost')">
                        Total Costing
                        <span ng-if="search.sortName == 'total_cost' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'total_cost' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('avg_sell_value')">
                        Avg Unit Sell Price
                        <span ng-if="search.sortName == 'avg_sell_value' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'avg_sell_value' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('amount')">
                        Total Selling Value
                        <span ng-if="search.sortName == 'amount' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'amount' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('gross')">
                        Gross Profit
                        <span ng-if="search.sortName == 'gross' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'gross' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                </tr>

                <tbody>
                    <tr dir-paginate="deal in alldata | itemsPerPage:itemsPerPage" pagination-id="stock_billing" total-items="totalCount" current-page="currentPage">
                        <td class="col-md-1 text-center">
                            @{{$index + indexFrom}}
                        </td>
                        <td class="col-md-2 text-left">
                            @{{search.profile_id === '' ? '-' : deal.profile_name}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{deal.product_id}}
                        </td>
                        <td class="col-md-2 text-left">
                            @{{deal.item_name}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{deal.unit}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{deal.is_inventory ? 'Yes' : 'No'}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{deal.is_supermarket_fee ? 'Yes' : 'No'}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.qty | currency: "": 4}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.avg_unit_cost | currency: "": 2}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.total_cost | currency: "": 2}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.avg_sell_value | currency: "": 2}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.amount | currency: "": 2}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.gross | currency: "": 2}}
                        </td>
                    </tr>
                    <tr ng-if="!alldata || alldata.length == 0">
                        <td colspan="18" class="text-center">No Records Found</td>
                    </tr>

                </tbody>
            </table>

            <div>
                  <dir-pagination-controls max-size="5" pagination-id="stock_billing" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
            </div>
        </div>

    </div>
</div>

<script src="/js/stock_billing.js"></script>
@stop