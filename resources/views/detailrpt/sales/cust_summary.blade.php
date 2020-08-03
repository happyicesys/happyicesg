@inject('profiles', 'App\Profile')
@inject('custcategories', 'App\Custcategory')

<div ng-controller="custSummaryController">
<div class="col-md-12 col-xs-12">
    <div class="row">
        <div class="col-md-4 col-xs-6">
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
        <div class="col-md-4 col-xs-6">
            <div class="form-group">
                {!! Form::label('current_month', 'Current Month', ['class'=>'control-label search-title']) !!}
                <select class="select form-control" name="current_month" ng-model="search.current_month" ng-change="searchDB()">
                    @foreach($month_options as $key => $value)
                        <option value="{{$key}}" selected="{{Carbon\Carbon::today()->month.'-'.Carbon\Carbon::today()->year ? 'selected' : ''}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4 col-xs-6">
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
    </div>
    <div class="row">
        <div class="col-md-4 col-xs-6">
            <div class="form-group">
                {!! Form::label('id_prefix', 'ID Group', ['class'=>'control-label search-title']) !!}
                <select class="select form-group" name="id_prefix" ng-model="search.id_prefix" ng-change="searchDB()">
                    <option value="">All</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                    <option value="F">F</option>
                    <option value="G">G</option>
                    <option value="H">H</option>
                    <option value="R">R</option>
                    <option value="S">S</option>
                    <option value="V">V</option>
                    <option value="W">W</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 col-xs-6">
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
                        'ng-change' => 'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-xs-6">
            <div class="form-group">
                {!! Form::label('is_commission', 'Include Commission', ['class'=>'control-label search-title']) !!}
                {!! Form::select('is_commission', ['0'=>'No', ''=>'Yes'], null,
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
        <div class="col-md-4 col-sm-6">
            <div class="form-group">
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
        <div class="col-md-4 col-sm-6">
            <div class="form-group">
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
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="form-group">
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
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
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
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('item_id', 'Product', ['class'=>'control-label']) !!}
                {!! Form::select('item_id',
                        [''=>'All']+$items::where('is_active', 1)->where('is_inventory', 1)->select(DB::raw("CONCAT(product_id,' - ',name) AS full, id"))->lists('full', 'id')->all(),
                        null,
                        [
                            'class'=>'select form-control',
                            'ng-model'=>'search.item_id',
                            'ng-change'=>'searchDB()'
                        ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('zone_id', 'Zone', ['class'=>'control-label']) !!}
                {!! Form::select('zone_id',
                        [''=>'All']+ $zones::lists('name', 'id')->all(),
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
        <div class="col-md-4 col-xs-6">
            <div class="form-group">
                {!! Form::label('cust_id', 'Cust ID', ['class'=>'control-label search-title']) !!}
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
        <div class="col-md-4 col-xs-6">
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
</div>

<div class="row" style="padding-left: 15px; padding-top:20px;">
    <div class="col-md-4 col-xs-12">
        <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
        <span ng-show="spinner"> <i style="color:red;" class="fa fa-spinner fa-2x fa-spin"></i></span>
    </div>
    <div class="col-md-4 col-xs-12">
        <div class="row">
            <div class="col-md-12 col-xs-12 text-center">
                <strong>
                    This Year
                </strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Transac:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ totals.thisyear_transactiontotal ? totals.thisyear_transactiontotal : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Tax:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ totals.thisyear_taxtotal ? totals.thisyear_taxtotal : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Sales:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ totals.thisyear_salestotal ? totals.thisyear_salestotal : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Comm:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ totals.thisyear_commtotal ? totals.thisyear_commtotal : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-xs-12 text-right">
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

    <div class="table-responsive" id="exportable_custsummary" style="padding-top: 20px;">
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
                    #
                </th>
                <th class="col-md-1 text-center">
                <a href="" ng-click="sortTable('custcategory')">
                    Category
                    <span ng-if="search.sortName == 'custcategory' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'custcategory' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                <a href="" ng-click="sortTable('custcategory_desc')">
                    Category Desc
                    <span ng-if="search.sortName == 'custcategory_desc' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'custcategory_desc' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('thistotal.salestotal')">
                    This Month
                    <span ng-if="search.sortName == 'thistotal.salestotal' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'thistotal.salestotal' && search.sortBy" class="fa fa-caret-up"></span>
                </th>

                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('prevtotal.salestotal')">
                    Last Month
                    <span ng-if="search.sortName == 'prevtotal.salestotal' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'prevtotal.salestotal' && search.sortBy" class="fa fa-caret-up"></span>
                </th>

                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('prev2total.salestotal')">
                    Last 2 Month
                    <span ng-if="search.sortName == 'prev2total.salestotal' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'prev2total.salestotal' && search.sortBy" class="fa fa-caret-up"></span>
                </th>

                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('prevyeartotal.salestotal')">
                    Last Yr Same Mth
                    <span ng-if="search.sortName == 'prevyeartotal.salestotal' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'prevyeartotal.salestotal' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
            </tr>
            <tr style="background-color: #DDFDF8">
                <th colspan="3"></th>
                <th class="col-md-1 text-right" style="font-size: 13px;">
                    <span class="pull-left">
                        Trans:
                    </span>
                    @{{ totals.this_transactiontotal ? totals.this_transactiontotal : 0.00 | currency: "": 2}} <br>
                    <span class="pull-left">
                        Tax:
                    </span>
                    @{{ totals.this_taxtotal ? totals.this_taxtotal : 0.00 | currency: "": 2}} <br>
                    <span class="pull-left">
                        Sales:
                    </span>
                    @{{ totals.this_salestotal ? totals.this_salestotal : 0.00 | currency: "": 2}} <br>
                    <span class="pull-left">
                        Comm:
                    </span>
                    @{{ totals.this_commtotal ? totals.this_commtotal : 0.00 | currency: "": 2}}
                </th>
                <th class="col-md-1 text-right" style="font-size: 13px;">
                    <span class="pull-left">
                        Trans:
                    </span>
                    @{{ totals.prev_transactiontotal ? totals.prev_transactiontotal : 0.00 | currency: "": 2}} <br>
                    <span class="pull-left">
                        Tax:
                    </span>
                    @{{ totals.prev_taxtotal ? totals.prev_taxtotal : 0.00 | currency: "": 2}} <br>
                    <span class="pull-left">
                        Sales:
                    </span>
                    @{{ totals.prev_salestotal ? totals.prev_salestotal : 0.00 | currency: "": 2}} <br>
                    <span class="pull-left">
                        Comm:
                    </span>
                    @{{ totals.prev_commtotal ? totals.prev_commtotal : 0.00 | currency: "": 2}}
                </th>
                <th class="col-md-1 text-right" style="font-size: 13px;">
                    <span class="pull-left">
                        Trans:
                    </span>
                    @{{ totals.prev2_transactiontotal ? totals.prev2_transactiontotal : 0.00 | currency: "": 2}} <br>
                    <span class="pull-left">
                        Tax:
                    </span>
                    @{{ totals.prev2_taxtotal ? totals.prev2_taxtotal : 0.00 | currency: "": 2}} <br>
                    <span class="pull-left">
                        Sales:
                    </span>
                    @{{ totals.prev2_salestotal ? totals.prev2_salestotal : 0.00 | currency: "": 2}} <br>
                    <span class="pull-left">
                        Comm:
                    </span>
                    @{{ totals.prev2_commtotal ? totals.prev2_commtotal : 0.00 | currency: "": 2}}
                </th>
                <th class="col-md-1 text-right" style="font-size: 13px;">
                    <span class="pull-left">
                        Trans:
                    </span>
                    @{{ totals.prevyear_transactiontotal ? totals.prevyear_transactiontotal : 0.00 | currency: "": 2}} <br>
                    <span class="pull-left">
                        Tax:
                    </span>
                    @{{ totals.prevyear_taxtotal ? totals.prevyear_taxtotal : 0.00 | currency: "": 2}} <br>
                    <span class="pull-left">
                        Sales:
                    </span>
                    @{{ totals.prevyear_salestotal ? totals.prevyear_salestotal : 0.00 | currency: "": 2}} <br>
                    <span class="pull-left">
                        Comm:
                    </span>
                    @{{ totals.prevyear_commtotal ? totals.prevyear_commtotal : 0.00 | currency: "": 2}}
                </th>
            </tr>

            <tbody>
                <tr dir-paginate="transaction in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" pagination-id="cust_summary" total-items="totalCount" current-page="currentPage">
                    <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ transaction.custcategory }}
                    </td>
                    <td class="col-md-1 text-left">
                        @{{ transaction.custcategory_desc }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.this_salestotal | currency: "": 2 }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.prev_salestotal | currency: "": 2 }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.prev2_salestotal | currency: "": 2 }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.prevyear_salestotal | currency: "": 2 }}
                    </td>
                </tr>
                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="14" class="text-center">No Records Found</td>
                </tr>
            </tbody>
        </table>

        <div>
              <dir-pagination-controls max-size="5" pagination-id="cust_summary" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>