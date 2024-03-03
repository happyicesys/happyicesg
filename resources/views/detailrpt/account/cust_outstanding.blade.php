<div ng-controller="custOutstandingController">
<div class="col-md-12 col-xs-12">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('code', 'Cust Code', ['class'=>'control-label search-title']) !!}
                {!! Form::text('code', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.code',
                                                    'placeholder'=>'Customer Code',
                                                    'ng-change'=>'searchDB()',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                !!}
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('cust_prefix_id', 'Cust Prefix', ['class'=>'control-label search-title']) !!}
                <select name="cust_prefix_id" id="cust_prefix_id" class="selectmultiple form-control" ng-model="search.cust_prefix_id" ng-change="searchDB()" multiple>
                    <option value="-1">-- Unassigned --</option>
                    @foreach($custPrefixes::orderBy('code')->get() as $custPrefix)
                        <option value="{{$custPrefix->id}}">
                            {{$custPrefix->code}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 col-xs-6">
            <div class="form-group">
                {!! Form::label('company', 'Cust Name', ['class'=>'control-label search-title']) !!}
                {!! Form::text('company', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.company',
                                                    'placeholder'=>'Cust Name',
                                                    'ng-change'=>'searchDB()',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-xs-6">
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
                {!! Form::select('status', [''=>'All', 'Delivered'=>'Delivered', 'Confirmed'=>'Confirmed', 'Cancelled'=>'Cancelled'], null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.status',
                    'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-3 col-xs-6">
            <div class="form-group">
                {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
                {!! Form::select('person_id',
                    [''=>'All'] +
                    $customers::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))
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
        <div class="col-md-3 col-xs-6">
            <div class="form-group">
                {!! Form::label('payment', 'Payment', ['class'=>'control-label search-title']) !!}
                {!! Form::select('payment',
                    [''=>'All', 'Paid'=>'Paid', 'Owe'=>'Owe'],
                    null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.payment',
                    'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-3 col-xs-6">
            <div class="form-group">
                {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                <select name="custcategory" class="selectmultiple form-control" ng-model="search.custcategory" ng-change="searchDB()" multiple>
                    <option value="">All</option>
                    @foreach($custcategories::orderBy('name')->get() as $custcategory)
                    <option value="{{$custcategory->id}}">{{$custcategory->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('custcategory_group', 'CustCategory Group', ['class'=>'control-label search-title']) !!}
                <label class="pull-right">
                </label>
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
        <div class="col-md-3 col-xs-6">
            <div class="form-group">
                {!! Form::label('current_month', 'Delivery Month', ['class'=>'control-label search-title']) !!}
                <select class="select form-control" name="current_month" ng-model="search.current_month" ng-change="searchDB()">
                    <option value="">All</option>
                    @foreach($month_options as $key => $value)
                        <option value="{{$key}}" selected="{{Carbon\Carbon::today()->month.'-'.Carbon\Carbon::today()->year ? 'selected' : ''}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 col-xs-6">
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
    </div>
</div>

<div class="row" style="padding-left: 15px;">
    <div class="col-md-4 col-xs-12" style="padding-top: 20px;">

        @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
            <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
        @endif
        <span ng-show="spinner"> <i style="color:red;" class="fa fa-spinner fa-2x fa-spin"></i></span>
    </div>
    <div class="col-md-4 col-xs-12" style="padding-top: 20px;">
            <div class="col-md-5 col-xs-5">
                Total Outstanding:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ totals.alltotal ? totals.alltotal : 0 | currency: "": 2}}</strong>
            </div>
    </div>
    <div class="col-md-4 col-xs-12 text-right">
        <label for="display_num">Display</label>
        <select ng-model="itemsPerPage" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
            <option ng-value="100">100</option>
            <option ng-value="200">200</option>
            <option ng-value="All">All</option>
        </select>
        <label for="display_num2" style="padding-right: 20px">per Page</label>
        <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
    </div>
</div>

    <div class="table-responsive" id="exportable_custoutstanding" style="padding-top: 20px;">
        <table class="table table-list-search table-hover table-bordered">

            {{-- hidden table for excel export --}}
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Total Last 3 Mths</td>
                <td data-tableexport-display="always" class="text-right">@{{ totals.last3total ? totals.last3total : 0 | currency: "": 2}}</td>
            </tr>
            <tr class="hidden" data-tableexport-display="always">
                <td></td>
            </tr>

            <tr style="background-color: #DDFDF8">
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
                <th class="col-md-3 text-center">
                    <a href="" ng-click="sortTable('company')">
                    Cust Name
                    <span ng-if="search.sortName == 'company' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'company' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('custcategories.id')">
                    Category
                    <span ng-if="search.sortName == 'custcategories.id' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'custcategories.id' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('thistotal.outstanding')">
                    This Month
                    <span ng-if="search.sortName == 'thistotal.outstanding' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'thistotal.outstanding' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('prevtotal')">
                    Last Month
                    <span ng-if="search.sortName == 'prevtotal.outstanding' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'prevtotal.outstanding' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('prev2total')">
                    Last 2 Months
                    <span ng-if="search.sortName == 'prev2total.outstanding' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'prev2total.outstanding' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('prevmore3total')">
                    >3 Months
                    <span ng-if="search.sortName == 'prevmore3total.outstanding' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'prevmore3total.outstanding' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tr style="background-color: #DDFDF8">
                <th colspan="5"></th>
                <th class="col-md-1 text-right" style="font-size: 14px;">
                    @{{ totals.thistotal ? totals.thistotal : 0.00 | currency: "": 2}} <br>
                </th>
                <th class="col-md-1 text-right" style="font-size: 14px;">
                    @{{ totals.prevtotal ? totals.prevtotal : 0.00 | currency: "": 2}} <br>
                </th>
                <th class="col-md-1 text-right" style="font-size: 14px;">
                    @{{ totals.prev2total ? totals.prev2total : 0.00 | currency: "": 2}} <br>
                </th>
                <th class="col-md-1 text-right" style="font-size: 14px;">
                    @{{ totals.prevmore3total ? totals.prevmore3total : 0.00 | currency: "": 2}} <br>
                </th>
            </tr>

            <tbody>
                <tr dir-paginate="transaction in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" pagination-id="cust_outstanding" total-items="totalCount" current-page="currentPage">
                    <td class="col-md-1 text-center">@{{ $index + indexFrom }} </td>
                    <td class="col-md-1 text-center" style="max-width: 90px;">
                        @{{ transaction.code }}
                    </td>
                    <td class="col-md-1 text-center" style="max-width: 80px;">
                        @{{ transaction.cust_prefix_code }}
                    </td>
                    {{-- <td class="col-md-1 text-center">@{{ transaction.cust_id }} </td> --}}

                    <td class="col-md-3 text-center">
                        <a href="/person/@{{ transaction.person_id }}">
                            @{{ transaction.cust_id[0] == 'D' || transaction.cust_id[0] == 'H' ? transaction.name : transaction.company }}
                        </a>
                    </td>
                    <td class="col-md-1 text-center">@{{ transaction.custcategory }}</td>
                    <td class="col-md-1 text-right">@{{transaction.thistotal | currency: "": 2}}</td>
                    <td class="col-md-1 text-right">@{{transaction.prevtotal | currency: "": 2}}</td>
                    <td class="col-md-1 text-right">@{{transaction.prev2total | currency: "": 2}}</td>
                    <td class="col-md-1 text-right">@{{transaction.prevmore3total | currency: "": 2}}</td>
                </tr>

                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="14" class="text-center">No Records Found</td>
                </tr>

            </tbody>
        </table>

        <div>
              <dir-pagination-controls max-size="5" pagination-id="cust_outstanding" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>