@inject('people', 'App\Person')

@inject('profiles', 'App\Profile')
@inject('custcategories', 'App\Custcategory')

<div ng-controller="invoiceBreakdownController">
<div class="row form-group">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
            {!! Form::select('person_id', [''=>null]+$people::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id')->whereActive('Yes')->where('cust_id', 'NOT LIKE', 'H%')->lists('full', 'id')->all(), null,
                [
                'class'=>'select form-control',
                'ng-model'=>'search.person_id',
                'ng-change'=>'searchDB()'
                ])
            !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
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
    <div class="col-md-3 col-sm-6 col-xs-12">
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
    <div class="col-md-3 col-sm-6 col-xs-12">
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
</div>

<div class="row form-group">
    <div class="col-md-4 col-sm-3 col-xs-12">
        <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
    </div>
    <div class="col-md-4 col-sm-5 col-xs-12">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
                Total Amount:
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_amount ? total_amount : 0 | currency: "": 2}}</strong>
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
            <tr class="hidden" data-tableexport-display="always">
                <td></td>
            </tr>

            <tr>
                <th class="col-md-1 text-left">
                    Invoice #
                </th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th class="col-md-1 text-left">
                    Delivery Date
                </th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th class="col-md-1 text-left">
                    Delivered By
                </th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th class="col-md-1 text-left">
                    Payment
                </th>
                <th></th>
                <th></th>
            </tr>
            <tr></tr>


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