<div ng-controller="productMonthDetailController">
<div class="col-md-12 col-xs-12">
    <div class="row">
        <div class="col-md-4 col-xs-6">
            <div class="form-group">
                {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                {!! Form::select('profile_id', [''=>'All']+$profiles::lists('name', 'id')->all(), null,
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
                    <option value="">All</option>
                    @foreach($month_options as $key => $value)
                        <option value="{{$key}}" selected="{{Carbon\Carbon::today()->month.'-'.Carbon\Carbon::today()->year ? 'selected' : ''}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-xs-6">
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
        <div class="col-md-4 col-xs-6">
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
    </div>
</div>

<div class="row" style="padding-left: 15px;">
    <div class="col-md-4 col-xs-12" style="padding-top: 20px;">
        <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
    </div>
    <div class="col-md-4 col-xs-12" style="padding-top: 20px;">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Total Amount:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_amount | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Total Qty:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_qty }}</strong>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-xs-12 text-right">
        <label for="display_num">Display</label>
        <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
            <option ng-value="100">100</option>
            <option ng-value="200">200</option>
            <option ng-value="All">All</option>
        </select>
        <label for="display_num2" style="padding-right: 20px">per Page</label>
        <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
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
                <th class="col-md-2 text-center">
                    <a href="" ng-click="sortType = 'profile_id'; sortReverse = !sortReverse">
                    Profile
                    <span ng-if="sortType == 'profile_id' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'profile_id' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'product_id'; sortReverse = !sortReverse">
                    Product ID
                    <span ng-if="sortType == 'product_id' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'product_id' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-3 text-center">
                    <a href="" ng-click="sortType = 'product_name'; sortReverse = !sortReverse">
                    Product Name
                    <span ng-if="sortType == 'product_name' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'product_name' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'amount'; sortReverse = !sortReverse">
                    Amount (This Month)
                    <span ng-if="sortType == 'amount' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'amount' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'qty'; sortReverse = !sortReverse">
                    Qty (This Month)
                    <span ng-if="sortType == 'qty' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'qty' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'prev_qty'; sortReverse = !sortReverse">
                    Qty (Last Month)
                    <span ng-if="sortType == 'prev_qty' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'prev_qty' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'prev2_qty'; sortReverse = !sortReverse">
                    Qty (Last 2 Months)
                    <span ng-if="sortType == 'prev2_qty' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'prev2_qty' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'prevyear_qty'; sortReverse = !sortReverse">
                    Qty (Last Yr Same Mth)
                    <span ng-if="sortType == 'prevyear_qty' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'prevyear_qty' && sortReverse" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tbody>

                <tr dir-paginate="item in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" pagination-id="product_detail_day" total-items="totalCount" current-page="currentPage">
                    <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ item.profile_name }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ item.product_id }}
                    </td>
                    <td class="col-md-6 text-left">
                        @{{ item.product_name }}
                        <span ng-if="item.remark">
                            - @{{ item.remark }}
                        </span>
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ item.amount }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ item.qty | currency: "": 4}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ item.prevqty | currency: "": 4}}
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