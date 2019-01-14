@inject('caldeals', 'App\Deal')

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row">
            <div class="col-md-3 col-sm-3 col-xs-12">
                <div class="form-group">
                    {!! Form::label('id', 'Inv', ['class'=>'control-label']) !!}
                    {!! Form::text('id', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.id',
                                                    'placeholder'=>'Inv',
                                                    'ng-change'=>'searchDB()',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12">
                <div class="form-group">
                    {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('status', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.status',
                                                    'placeholder'=>'Status',
                                                    'ng-change'=>'searchDB()',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12">
                <div class="form-group">
                    {!! Form::label('pay_status', 'Payment', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('pay_status', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.pay_status',
                                                    'placeholder'=>'Payment',
                                                    'ng-change'=>'searchDB()',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12">
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
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row">
            <div class="col-md-3 col-sm-3 col-xs-12">
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
            <div class="col-md-3 col-sm-3 col-xs-12">
                <div class="form-group">
                    {!! Form::label('driver', 'Delivered By', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('driver', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.driver',
                                                    'placeholder'=>'Delivered By',
                                                    'ng-change'=>'searchDB()',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                    !!}
                </div>
            </div>
        </div>
    </div>
</div>

<form id="invbreakdown" method="POST" action="/detailrpt/invbreakdown/detail">
    {!! csrf_field() !!}
    <input type="text" class="hidden" name="person_id" value="{{$person->id}}">
</form>

<div class="row">
    <div class="col-md-3 col-sm-3 col-xs-12" style="padding-top: 20px;">
        <button class="btn btn-primary" ng-click="exportDataTransRpt()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
        @if(!auth()->user()->hasRole('franchisee') and ! auth()->user()->hasRole('watcher'))
            <button type="submit" class="btn btn-default" form="invbreakdown">Invoice Breakdown</button>
        @endif
    </div>
    <div class="col-md-5 col-sm-5 col-xs-12" style="padding-top: 20px;">
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Total:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ total_amount ? total_amount : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Total Paid:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ total_paid ? total_paid : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Total Owe:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ total_owe ? total_owe : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        @if(!auth()->user()->hasRole('franchisee'))
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Gross Earning:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>
                    @{{profileDealsGrossProfit ? profileDealsGrossProfit : 0.00 | currency: "": 2}}
                </strong>
            </div>
        </div>
        @endif
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12 text-right">
        <label for="display_num">Display</label>
        <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='20'" ng-change="pageNumChanged()">
            <option ng-value="20">20</option>
            <option ng-value="50">50</option>
            <option ng-value="100">100</option>
            <option ng-value="200">200</option>
            <option ng-value="All">All</option>
        </select>
        <label for="display_num2" style="padding-right: 20px">per Page</label>
        <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
    </div>
</div>

<div class="table-responsive" id="exportable_trans" style="padding-top: 20px;">
    <table class="table table-list-search table-hover table-bordered">
        {{-- hidden table for excel export --}}
        <tr class="hidden">
            <td></td>
            <td data-tableexport-display="always">Total Amount</td>
            <td data-tableexport-display="always" class="text-right">@{{total_amount | currency: "": 2}}</td>
        </tr>
        <tr class="hidden">
            <td></td>
            <td data-tableexport-display="always">Total Paid</td>
            <td data-tableexport-display="always" class="text-right">@{{total_paid | currency: "": 2}}</td>
        </tr>
        <tr class="hidden">
            <td></td>
            <td data-tableexport-display="always">Total Owe</td>
            <td data-tableexport-display="always" class="text-right">@{{total_owe | currency: "": 2}}</td>
        </tr>
        <tr class="hidden">
            <td></td>
            <td data-tableexport-display="always">Gross Earning</td>
            <td data-tableexport-display="always" class="text-right">@{{profileDealsGrossProfit | currency: "": 2}}</td>
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
                <a href="" ng-click="sortType = 'driver'; sortReverse = !sortReverse">
                Delivered By
                <span ng-if="sortType == 'driver' && !sortReverse" class="fa fa-caret-down"></span>
                <span ng-if="sortType == 'driver' && sortReverse" class="fa fa-caret-up"></span>
            </th>

            <th class="col-md-1 text-center">
                <a href="" ng-click="sortType = 'pcs'; sortReverse = !sortReverse">
                Pieces
                <span ng-if="sortType == 'pcs' && !sortReverse" class="fa fa-caret-down"></span>
                <span ng-if="sortType == 'pcs' && sortReverse" class="fa fa-caret-up"></span>
            </th>

            <th class="col-md-1 text-center">
                <a href="" ng-click="sortType = 'amount'; sortReverse = !sortReverse">
                Amount
                <span ng-if="sortType == 'amount' && !sortReverse" class="fa fa-caret-down"></span>
                <span ng-if="sortType == 'amount' && sortReverse" class="fa fa-caret-up"></span>
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
                <a href="" ng-click="sortType = 'updated_by'; sortReverse = !sortReverse">
                Last Mod By
                <span ng-if="sortType == 'updated_by' && !sortReverse" class="fa fa-caret-down"></span>
                <span ng-if="sortType == 'updated_by' && sortReverse" class="fa fa-caret-up"></span>
            </th>

            <th class="col-md-1 text-center">
                <a href="" ng-click="sortType = 'updated_at'; sortReverse = !sortReverse">
                Last Mod Time
                <span ng-if="sortType == 'updated_at' && !sortReverse" class="fa fa-caret-down"></span>
                <span ng-if="sortType == 'updated_at' && sortReverse" class="fa fa-caret-up"></span>
            </th>
            <th class="col-md-1 text-center">
                Action
            </th>
        </tr>

        <tbody>

            <tr dir-paginate="transaction in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage">
                <td class="col-md-1 text-center">@{{ $index + indexFrom }} </td>
                <td class="col-md-1 text-center">
                    <a href="/transaction/@{{ transaction.id }}/edit" ng-if="transaction.cust_id[0] !== 'H'">
                        @{{ transaction.id }}
                    </a>
                    <a href="/market/deal/@{{ transaction.id }}/edit" ng-if="transaction.cust_id[0] === 'H'">
                        @{{ transaction.id }}
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
                <td class="col-md-1 text-center">@{{ transaction.del_date}}</td>
                <td class="col-md-1 text-center">@{{ transaction.driver }}</td>
                <td class="col-md-1 text-center">@{{ transaction.pieces }} </td>
                <td class="col-md-1 text-center">@{{ transaction.total | currency: ""}} </td>
                <td class="col-md-1 text-center" style="color: @{{transaction.pay_status == 'Owe' ? 'red' : 'green'}};">
                    @{{ transaction.pay_status }}
                </td>
                <td class="col-md-1 text-center">@{{ transaction.pay_method | capitalize }}</td>
                <td class="col-md-1 text-center">@{{ transaction.updated_by }}</td>
                <td class="col-md-1 text-center">@{{ transaction.updated_at }}</td>
                <td class="col-md-1 text-center">
                    <a href="/transaction/download/@{{ transaction.id }}" class="btn btn-primary btn-sm" ng-if="transaction.status != 'Pending' && transaction.status != 'Cancelled'">Print</a>
                    <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-sm btn-default" ng-if="transaction.status == 'Cancelled'">View</a>
                    @cannot('transaction_view')
                        <a href="/transaction/status/@{{ transaction.id }}" class="btn btn-warning btn-sm" ng-if="transaction.status == 'Delivered' && transaction.pay_status == 'Owe'">Verify Owe</a>
                        <a href="/transaction/status/@{{ transaction.id }}" class="btn btn-success btn-sm" ng-if="(transaction.status == 'Verified Owe' || transaction.status == 'Delivered') && transaction.pay_status == 'Paid'">Verify Paid</a>
                    @endcannot
                </td>
            </tr>

            <tr ng-if="!alldata || alldata.length == 0">
                <td colspan="16" class="text-center">No Records Found</td>
            </tr>

        </tbody>
    </table>

    <div>
          <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
    </div>
</div>