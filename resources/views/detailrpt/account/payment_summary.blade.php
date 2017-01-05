<div ng-controller="custPaySummaryController">
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
                {!! Form::label('paid_from', 'Payment From', ['class'=>'control-label search-title']) !!}
                <datepicker>
                    <input
                        type="text"
                        class="form-control input-sm"
                        name="paid_from"
                        placeholder="Payment From"
                        ng-model="search.paid_from"
                        ng-change="onPaidFromChanged(search.paid_from)"
                    />
                </datepicker>
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
    </div>
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
                {!! Form::label('paid_to', 'Payment To', ['class'=>'control-label search-title']) !!}
                <datepicker>
                    <input
                        type="text"
                        class="form-control input-sm"
                        name="paid_to"
                        placeholder="Payment To"
                        ng-model="search.paid_to"
                        ng-change="onPaidToChanged(search.paid_to)"
                    />
                </datepicker>
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
    <div class="row">
        <div class="col-md-4 col-xs-6">
            <div class="form-group">
                {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
                {!! Form::select('person_id',
                    [''=>'All'] + $customers::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id')->whereActive('Yes')->where('cust_id', 'NOT LIKE', 'H%')->lists('full', 'id')->all(),
                    null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.person_id',
                    'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-xs-6">
            <div class="form-group">
                {!! Form::label('pay_method', 'Payment Method', ['class'=>'control-label search-title']) !!}
                {!! Form::select('pay_method',
                    [''=>'All', 'cash'=>'Cash', 'cheque'=>'Cheque'],
                    null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.pay_method',
                    'ng-change'=>'searchDB()'
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
                Total Inv Amount:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_inv_amount | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Total GST:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_gst | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Total Amount:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_amount | currency: "": 2}}</strong>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-xs-12 text-right">
        <label for="display_num">Display</label>
        <select ng-model="itemsPerPage1" name="pageNum" ng-init="itemsPerPage1='100'" ng-change="pageNumChanged()">
            <option ng-value="100">100</option>
            <option ng-value="200">200</option>
            <option ng-value="All">All</option>
        </select>
        <label for="display_num2" style="padding-right: 20px">per Page</label>
        <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
    </div>
</div>

    <div class="table-responsive" id="exportable" style="padding-top: 20px;">
        <table class="table table-list-search table-hover table-bordered">

            {{-- hidden table for excel export --}}
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Total Inv Amount</td>
                <td data-tableexport-display="always" class="text-right">@{{total_inv_amount | currency: "": 2}}</td>
            </tr>
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Total GST</td>
                <td data-tableexport-display="always" class="text-right">@{{total_gst | currency: "": 2}}</td>
            </tr>
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
                    <a href="" ng-click="sortType = 'profile_id'; sortReverse = !sortReverse">
                    Profile
                    <span ng-if="sortType == 'profile_id' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'profile_id' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'id'; sortReverse = !sortReverse">
                    Inv #
                    <span ng-if="sortType == 'id' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'id' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'cust_id'; sortReverse = !sortReverse">
                    ID
                    <span ng-if="sortType == 'cust_id' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'cust_id' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'company'; sortReverse = !sortReverse">
                    ID Name
                    <span ng-if="sortType == 'company' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'company' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'paid_at'; sortReverse = !sortReverse">
                    Pay Received Dt
                    <span ng-if="sortType == 'paid_at' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'paid_at' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'delivery_date'; sortReverse = !sortReverse">
                    Delivery Dt
                    <span ng-if="sortType == 'delivery_date' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'delivery_date' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'inv_amount'; sortReverse = !sortReverse">
                    Inv Amount
                    <span ng-if="sortType == 'inv_amount' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'inv_amount' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'gst'; sortReverse = !sortReverse">
                    GST
                    <span ng-if="sortType == 'gst' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'gst' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'total'; sortReverse = !sortReverse">
                    Amount
                    <span ng-if="sortType == 'total' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'total' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'pay_status'; sortReverse = !sortReverse">
                    Payment
                    <span ng-if="sortType == 'pay_status' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'pay_status' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'pay_method'; sortReverse = !sortReverse">
                    Pay Method
                    <span ng-if="sortType == 'pay_method' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'pay_method' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'note'; sortReverse = !sortReverse">
                    Note
                    <span ng-if="sortType == 'note' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'note' && sortReverse" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tbody>
                <tr dir-paginate="transaction in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" pagination-id="payment_detail" total-items="totalCount">
                    <td class="col-md-1 text-center">@{{ $index + indexFrom }} </td>
                    <td class="col-md-1 text-center">@{{ transaction.profile_name }}</td>
                    <td class="col-md-1 text-center">@{{ transaction.id }}</td>
                    <td class="col-md-1 text-center">@{{ transaction.cust_id }} </td>
                    <td class="col-md-1 text-center">
                        <a href="/person/@{{ transaction.person_id }}">
                            @{{ transaction.cust_id[0] == 'D' || transaction.cust_id[0] == 'H' ? transaction.name : transaction.company }}
                        </a>
                    </td>
                    <td class="col-md-1 text-center">@{{transaction.paid_at | delDate: "yyyy-MM-dd"}}</td>
                    <td class="col-md-1 text-center">@{{transaction.delivery_date | delDate: "yyyy-MM-dd"}}</td>
                    <td class="col-md-1 text-center">@{{transaction.inv_amount | currency: "": 2}}</td>
                    <td class="col-md-1 text-center">@{{transaction.gst | currency: "": 2}}</td>
                    <td class="col-md-1 text-center">@{{transaction.amount | currency: "": 2}}</td>
                    <td class="col-md-1 text-center">@{{transaction.pay_status}}</td>
                    <td class="col-md-1 text-center">@{{transaction.pay_method}}</td>
                    <td class="col-md-1 text-center">@{{transaction.note}}</td>
                </tr>

                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="14" class="text-center">No Records Found</td>
                </tr>

            </tbody>
        </table>

        <div>
              <dir-pagination-controls max-size="5" pagination-id="payment_detail" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>