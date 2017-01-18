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
                <datepicker>
                    <input
                        type="text"
                        class="form-control input-sm"
                        name="delivery_from"
                        placeholder="Delivery From"
                        ng-model="search.delivery_from"
                        ng-change="onDeliveryFromChanged(search.delivery_from)"
                    />
                </datepicker>
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
                <datepicker>
                    <input
                        type="text"
                        class="form-control input-sm"
                        name="delivery_to"
                        placeholder="Delivery To"
                        ng-model="search.delivery_to"
                        ng-change="onDeliveryToChanged(search.delivery_to)"
                    />
                </datepicker>
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
        <button class="btn btn-primary" ng-click="exportData_productday()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
    </div>
    <div class="col-md-4 col-xs-12" style="padding-top: 20px;">
            <div class="col-md-5 col-xs-5">
                Total Amount:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ total_amount | currency: "": 2}}</strong>
            </div>
            <div class="col-md-5 col-xs-5">
                Total Qty:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ total_qty }}</strong>
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

    <div class="table-responsive" id="exportable_custdetail" style="padding-top: 20px;">
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
                    <a href="" ng-click="sortType = 'product_id'; sortReverse = !sortReverse">
                    Product ID
                    <span ng-if="sortType == 'product_id' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'product_id' && sortReverse" class="fa fa-caret-up"></span>
                </th>

                <th class="col-md-5 text-center">
                    <a href="" ng-click="sortType = 'product_name'; sortReverse = !sortReverse">
                    Product Name
                    <span ng-if="sortType == 'product_name' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'product_name' && sortReverse" class="fa fa-caret-up"></span>
                </th>

                <th class="col-md-2 text-center">
                    <a href="" ng-click="sortType = 'amount'; sortReverse = !sortReverse">
                    Total Amount
                    <span ng-if="sortType == 'amount' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'amount' && sortReverse" class="fa fa-caret-up"></span>
                </th>

                <th class="col-md-2 text-center">
                    <a href="" ng-click="sortType = 'qty'; sortReverse = !sortReverse">
                    Total Qty
                    <span ng-if="sortType == 'qty' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'qty' && sortReverse" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tbody>

                <tr dir-paginate="transaction in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" pagination-id="product_detail_day" total-items="totalCount" current-page="currentPage">
                    <td class="col-md-2 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-2 text-center">
                        @{{ transaction.product_id }}
                    </td>
                    <td class="col-md-5 text-left">
                        @{{ transaction.product_name }}
                    </td>
                    <td class="col-md-2 text-right">
                        @{{ transaction.amount }}
                    </td>
                    <td class="col-md-2 text-right">
                        @{{ transaction.qty }}
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