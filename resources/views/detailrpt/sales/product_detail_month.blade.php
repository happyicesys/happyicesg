<div ng-controller="productMonthDetailController">
<div class="col-md-12 col-xs-12">
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
                    'ng-model'=>'search.profile_id',
                    'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('current_month', 'Current Month', ['class'=>'control-label search-title']) !!}
                <select class="select form-control" name="current_month" ng-model="search.current_month" ng-change="searchDB()">
                    <option value="">All</option>
                    @foreach($month_options as $key => $value)
                        <option value="{{$key}}" selected="{{Carbon\Carbon::today()->month.'-'.Carbon\Carbon::today()->year ? 'selected' : ''}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
                {!! Form::select('status', [''=>'All', 'Pending'=>'Pending', 'Confirmed'=>'Confirmed', 'Delivered'=>'Delivered', 'Cancelled'=>'Cancelled'], null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.status',
                    'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('is_inventory', 'Product Type', ['class'=>'control-label search-title']) !!}
                <select class="select form-control" id="is_inventory5" ng-model="search.is_inventory" ng-change="onProductTypeSelected()">
                    <option value="1">
                        Inventory Item
                    </option>
                    <option value="">
                        All
                    </option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('item_id', 'Product', ['class'=>'control-label']) !!}
                <select class="selectmultiple form-control" id="item_id5" ng-model="search.item_id" ng-change="searchDB()" multiple>
                    <option ng-repeat="item in itemOptions track by item.id" value="@{{item.id}}">
                        @{{ item.product_id + ' - ' + item.name }}
                    </option>
                </select>
            </div>
        </div>

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
    </div>
    <div class="row">
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
                {!! Form::label('id', 'ID', ['class'=>'control-label search-title']) !!}
                <label class="pull-right">
                    <input type="checkbox" name="strictCustId" ng-model="search.strictCustId">
                    <span style="margin-top: 5px; margin-right: 5px;">
                        Strict
                    </span>
                </label>
                {!! Form::text('id', null,
                                            [
                                                'class'=>'form-control input-sm',
                                                'ng-model'=>'search.cust_id',
                                                'ng-change'=>'searchDB()',
                                                'placeholder'=>'Cust ID',
                                                'ng-model-options'=>'{ debounce: 700 }'
                                            ])
                !!}
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
                    <input type="checkbox" name="exclude_custcategory" ng-model="search.exclude_custcategory" ng-true-value="'1'" ng-false-value="'0'" ng-change="searchDB($event)">
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
                        'ng-change' => 'searchDB($event)'
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
                        'ng-change' => "searchDB($event)"
                    ])
                !!}
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
            {!! Form::label('item_group_id', 'Item Group', ['class'=>'control-label search-title']) !!}
            <select name="item_group_id" class="selectmultiple form-control" ng-model="search.item_group_id" ng-change="searchDB($event)" multiple>
                <option value="">All</option>
                @foreach($itemGroups->orderBy('name')->get() as $itemGroup)
                    <option value="{{$itemGroup->id}}">
                        {{$itemGroup->name}}
                    </option>
                @endforeach
            </select>
            </div>
        </div>
    </div>
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
    <div class="col-md-4 col-xs-12">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Total Amount:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_amount ? total_amount : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Total Qty:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_qty ? total_qty : 0.0000 }}</strong>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-xs-12 text-right">
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

    <div class="table-responsive" id="exportable_productmonth" style="padding-top: 20px;">
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
                <th class="col-md-4 text-center">
                    <a href="" ng-click="sortTable('product_name')">
                    Product Name
                    <span ng-if="search.sortName == 'product_name' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'product_name' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('amount')">
                    Amount (This Month)
                    <span ng-if="search.sortName == 'amount' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'amount' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('qty')">
                    Qty (This Month)
                    <span ng-if="search.sortName == 'qty' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'qty' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('prevqty')">
                    Qty (Last Month)
                    <span ng-if="search.sortName == 'prevqty' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'prevqty' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('prev2qty')">
                    Qty (Last 2 Months)
                    <span ng-if="search.sortName == 'prev2qty' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'prev2qty' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('prevyrqty')">
                    Qty (Last Yr Same Mth)
                    <span ng-if="search.sortName == 'prevyrqty' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'prevyrqty' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tbody>

                <tr dir-paginate="item in alldata | itemsPerPage:itemsPerPage" pagination-id="product_detail_month" total-items="totalCount" current-page="currentPage">
                    <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ item.product_id }}
                    </td>
                    <td class="col-md-4 text-left">
                        @{{ item.product_name }}
                        <span ng-if="item.remark">
                            - @{{ item.remark }}
                        </span>
                    </td>
                    <td class="col-md-1 text-right">
                        <a href="/detailrpt/sales/@{{item.id}}/thismonth?current=@{{search.current_month}}">@{{ item.amount | currency: "": 2 }}</a>
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ item.qty | currency: "": 4}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ item.prevqty | currency: "": 4}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ item.prev2qty | currency: "": 4}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ item.prevyrqty | currency: "": 4}}
                    </td>
                </tr>
                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="14" class="text-center">No Records Found</td>
                </tr>
            </tbody>
        </table>

        <div>
              <dir-pagination-controls max-size="5" pagination-id="product_detail_month" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>