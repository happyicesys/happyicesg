<div ng-controller="custDetailController">
{!! Form::open(['id'=>'exportData', 'method'=>'POST', 'action'=>['DetailRptController@getAccountCustdetailApi']]) !!}
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
                {!! Form::label('payment_from', 'Payment From', ['class'=>'control-label search-title']) !!}
                <datepicker>
                    <input
                        type="text"
                        class="form-control input-sm"
                        name="payment_from"
                        placeholder="Payment From"
                        ng-model="search.payment_from"
                        ng-change="onPaymentFromChanged(search.payment_from)"
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
                {!! Form::label('payment_to', 'Payment To', ['class'=>'control-label search-title']) !!}
                <datepicker>
                    <input
                        type="text"
                        class="form-control input-sm"
                        name="payment_to"
                        placeholder="Payment To"
                        ng-model="search.payment_to"
                        ng-change="onPaymentToChanged(search.payment_to)"
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
    </div>
</div>

<div class="row" style="padding-left: 15px;">
    <div class="col-md-4 col-xs-12" style="padding-top: 20px;">
        <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
        <button type="submit" class="btn btn-success" form="exportData" name="exportSOA" value="exportSOA"><i class="fa fa-outdent"></i><span class="hidden-xs"></span> Export SOA</button>
    </div>
    <div class="col-md-4 col-xs-12" style="padding-top: 20px;">
            <div class="col-md-5 col-xs-5">
                Total:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ total_amount | currency: "": 2}}</strong>
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
{!! Form::close() !!}

    <div class="table-responsive" id="exportable_custdetail" style="padding-top: 20px;">
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
                    <a href="" ng-click="sortType = 'id'; sortReverse = !sortReverse">
                    INV #
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
                    <a href="" ng-click="sortType = 'status'; sortReverse = !sortReverse">
                    Status
                    <span ng-if="sortType == 'status' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'status' && sortReverse" class="fa fa-caret-up"></span>
                </th>

                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'delivery_date'; sortReverse = !sortReverse">
                    Delivery Date
                    <span ng-if="sortType == 'delivery_date' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'delivery_date' && sortReverse" class="fa fa-caret-up"></span>
                </th>

                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'total'; sortReverse = !sortReverse">
                    Total Amount
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
                    <a href="" ng-click="sortType = 'paid_at'; sortReverse = !sortReverse">
                    Payment Received Dt
                    <span ng-if="sortType == 'paid_at' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'paid_at' && sortReverse" class="fa fa-caret-up"></span>
                </th>

                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'profile_id'; sortReverse = !sortReverse">
                    Profile
                    <span ng-if="sortType == 'profile_id' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'profile_id' && sortReverse" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tbody>

                <tr dir-paginate="transaction in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" pagination-id="cust_detail" total-items="totalCount" current-page="currentPage">
                    <td class="col-md-1 text-center">@{{ $index + indexFrom }} </td>
                    <td class="col-md-1 text-center">
                        <a href="/transaction/@{{ transaction.id }}/edit">
                            @{{ transaction.id }}
                        </a>
                    </td>
                    <td class="col-md-1 text-center">@{{ transaction.cust_id }} </td>

                    <td class="col-md-1 text-center">
                        <a href="/person/@{{ transaction.person_id }}">
                            @{{ transaction.cust_id[0] == 'D' || transaction.cust_id[0] == 'H' ? transaction.name : transaction.company }}
                        </a>
                    </td>

                    {{-- status by color --}}
                    <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.status == 'Pending'">
                        @{{ transaction.status }}
                    </td>
                    <td class="col-md-1 text-center" style="color: orange;" ng-if="transaction.status == 'Confirmed'">
                        @{{ transaction.status }}
                    </td>
                    <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.status == 'Delivered'">
                        @{{ transaction.status }}
                    </td>
                    <td class="col-md-1 text-center" style="color: black; background-color:orange;" ng-if="transaction.status == 'Verified Owe'">
                        @{{ transaction.status }}
                    </td>
                    <td class="col-md-1 text-center" style="color: black; background-color:green;" ng-if="transaction.status == 'Verified Paid'">
                        @{{ transaction.status }}
                    </td>
                    <td class="col-md-1 text-center" ng-if="transaction.status == 'Cancelled'">
                        <span style="color: white; background-color: red;" > @{{ transaction.status }} </span>
                    </td>
                    {{-- status by color ended --}}
                    <td class="col-md-1 text-center">@{{ transaction.delivery_date | delDate: "yyyy-MM-dd"}}</td>

                    <td class="col-md-1 text-center">@{{ transaction.total | currency: ""}} </td>
                    {{-- pay status --}}
                    <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.pay_status == 'Owe'">
                        @{{ transaction.pay_status }}
                    </td>
                    <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.pay_status == 'Paid'">
                        @{{ transaction.pay_status }}
                    </td>
                    {{-- pay status ended --}}
                    <td class="col-md-1 text-center">@{{ transaction.paid_at }}</td>
                    <td class="col-md-1 text-center">@{{ transaction.profile_name }}</td>
                </tr>

                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="14" class="text-center">No Records Found</td>
                </tr>

            </tbody>
        </table>

        <div>
              <dir-pagination-controls max-size="5" pagination-id="cust_detail" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>