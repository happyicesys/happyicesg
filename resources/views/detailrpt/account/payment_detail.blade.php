<div ng-controller="custPayDetailController">
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
                {!! Form::label('payment_from', 'Payment From', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker selector="form-control">
                        <input
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Payment From"
                            ng-model = "search.payment_from"
                            ng-change = "onPaidFromChanged(search.payment_from)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('payment_from', search.payment_from)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('payment_from', search.payment_from)"></span>
                </div>
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
                {!! Form::label('payment_to', 'Payment To', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker selector="form-control">
                        <input
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Payment To"
                            ng-model = "search.payment_to"
                            ng-change = "onPaidToChanged(search.payment_to)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('payment_to', search.payment_to)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('payment_to', search.payment_to)"></span>
                </div>
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
    </div>
    <div class="row">
        <div class="col-md-4 col-xs-6">
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
                {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                {!! Form::select('custcategory', [''=>'All'] + $custcategories::orderBy('name')->pluck('name', 'id')->all(), null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.custcategory',
                    'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-xs-6">
            <div class="form-group">
                {!! Form::label('pay_method', 'Payment Method', ['class'=>'control-label search-title']) !!}
                {!! Form::select('pay_method',
                    [''=>'All', 'cash'=>'Cash', 'cheque'=>'Cheque', 'tt'=>'TT'],
                    null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.pay_method',
                    'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
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
    </div>
</div>

<div class="row" style="padding-left: 15px;">
    <div class="col-md-4 col-xs-12" style="padding-top: 20px;">
        <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
        <span ng-show="spinner"> <i style="color:red;" class="fa fa-spinner fa-2x fa-spin"></i></span>
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

    <div class="table-responsive" id="exportable_paydetail" style="padding-top: 20px;">
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
                    <a href="" ng-click="sortTable('transactions.id')">
                    Inv #
                    <span ng-if="search.sortName == 'transactions.id' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'transactions.id' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('cust_id')">
                    ID
                    <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('company')">
                    ID Name
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
                    <a href="" ng-click="sortTable('paid_at')">
                    Pay Received Dt
                    <span ng-if="search.sortName == 'paid_at' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'paid_at' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('delivery_date')">
                    Delivery Dt
                    <span ng-if="search.sortName == 'delivery_date' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'delivery_date' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('inv_amount')">
                    Inv Amount
                    <span ng-if="search.sortName == 'inv_amount' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'inv_amount' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('gst')">
                    GST
                    <span ng-if="search.sortName == 'gst' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'gst' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('total')">
                    Amount
                    <span ng-if="search.sortName == 'total' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'total' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('pay_status')">
                    Payment
                    <span ng-if="search.sortName == 'pay_status' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'pay_status' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('pay_method')">
                    Pay Method
                    <span ng-if="search.sortName == 'pay_method' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'pay_method' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('note')">
                    Note
                    <span ng-if="search.sortName == 'note' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'note' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tbody>
                <tr dir-paginate="transaction in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" pagination-id="payment_detail" total-items="totalCount">
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
                    <td class="col-md-1 text-center">@{{ transaction.custcategory }}</td>
                    <td class="col-md-1 text-center">@{{transaction.paid_at | delDate: "yyyy-MM-dd"}}</td>
                    <td class="col-md-1 text-center">@{{transaction.delivery_date | delDate: "yyyy-MM-dd"}}</td>
                    <td class="col-md-1 text-right">@{{transaction.inv_amount | currency: "": 2}}</td>
                    <td class="col-md-1 text-right">@{{transaction.gst | currency: "": 2}}</td>
                    <td class="col-md-1 text-right">@{{transaction.amount | currency: "": 2}}</td>
                    <td class="col-md-1 text-center">@{{transaction.pay_status}}</td>
                    <td class="col-md-1 text-center">@{{transaction.pay_method | capitalize}}</td>
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