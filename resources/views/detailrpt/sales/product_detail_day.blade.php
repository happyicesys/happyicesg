@inject('profiles', 'App\Profile')
@inject('custcategories', 'App\Custcategory')

<div ng-controller="productDayDetailController">
<div class="col-md-12 col-xs-12">
    <div class="row">
        <div class="col-md-4 col-xs-6">
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
        <div class="col-md-4 col-xs-6">
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
    </div>
    <div class="row">
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
        <div class="col-md-4 col-xs-6">
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
                {!! Form::label('cust_category', 'Cust Category', ['class'=>'control-label search-title']) !!}
                {!! Form::select('cust_category', [''=>'All']+$custcategories::lists('name', 'id')->all(), null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.cust_category',
                    'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-xs-6">
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
    </div>
</div>

<div class="row" style="padding-left: 15px; padding-top:20px;">
    <div class="col-md-4 col-xs-12">
        <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
        <span ng-show="spinner"> <i style="color:red;" class="fa fa-spinner fa-2x fa-spin"></i></span>
    </div>
    <div class="col-md-4 col-xs-12">
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
                    <td class="col-md-6 text-left">
                        @{{ item.product_name }}
                        <span ng-if="item.remark">
                            - @{{ item.remark }}
                        </span>
                    </td>
                    <td class="col-md-2 text-right">
                        @{{ item.amount }}
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