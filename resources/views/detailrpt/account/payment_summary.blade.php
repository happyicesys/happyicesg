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
</div>

<div class="row" style="padding-left: 15px;">
    <div class="col-md-2 col-xs-12" style="padding-top: 20px;">
        <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"> Export Excel</span></button>
        <button class="btn btn-success" type="submit" form="submit_form"><i class="fa fa-pencil-square-o"></i><span class="hidden-xs"> Batch Update</span></button>
    </div>
    <div class="col-md-3 col-xs-12" style="padding-top: 20px;">
        <div class="row">
            <div class="col-md-12 col-xs-12 text-center">
                <strong>HappyIce P/L</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Cash:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_cash_happyice ? total_cash_happyice : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Cheque/ TT:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_cheque_happyice ? total_cheque_happyice : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-xs-12" style="padding-top: 20px;">
        <div class="row">
            <div class="col-md-12 col-xs-12 text-center">
                <strong>HappyIce Logistics P/L</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Cash:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_cash_logistic ? total_cash_logistic : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                Cheque/ TT:
            </div>
            <div class="col-md-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_cheque_logistic ? total_cheque_logistic : 0.00 | currency: "": 2}}</strong>
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

    {!! Form::open(['id'=>'submit_form', 'method'=>'POST','action'=>['DetailRptController@submitPaySummary']]) !!}
    <div class="table-responsive" id="exportable" style="padding-top: 20px;">
        <table class="table table-list-search table-hover table-bordered">

            {{-- hidden table for excel export --}}
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Happyice P/L</td>
                <td></td>
                <td></td>
                <td data-tableexport-display="always">Happyice Logistic P/L</td>
            </tr>
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Total Cash</td>
                <td data-tableexport-display="always" class="text-right">@{{total_cash_happyice | currency: "": 2}}</td>
                <td></td>
                <td data-tableexport-display="always">Total Cash</td>
                <td data-tableexport-display="always" class="text-right">@{{total_cash_logistic | currency: "": 2}}</td>
            </tr>
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Total Cheque/ TT</td>
                <td data-tableexport-display="always" class="text-right">@{{total_cheque_happyice | currency: "": 2}}</td>
                <td></td>
                <td data-tableexport-display="always">Total Cheque/ TT</td>
                <td data-tableexport-display="always" class="text-right">@{{total_cheque_logistic | currency: "": 2}}</td>
            </tr>
            <tr class="hidden" data-tableexport-display="always">
                <td></td>
            </tr>

            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    <input type="checkbox" id="checkAll" />
                </th>
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'payreceived_date'; sortReverse = !sortReverse">
                    Pay Received Date
                    <span ng-if="sortType == 'payreceived_date' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'payreceived_date' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'pay_method'; sortReverse = !sortReverse">
                    Pay Method
                    <span ng-if="sortType == 'pay_method' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'pay_method' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType = 'total'; sortReverse = !sortReverse">
                    Total
                    <span ng-if="sortType == 'total' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'total' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-2 text-center">
                    <a href="" ng-click="sortType = 'profile'; sortReverse = !sortReverse">
                    Profile
                    <span ng-if="sortType == 'profile' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'profile' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-3 text-center">
                    <a href="" ng-click="sortType = 'remark'; sortReverse = !sortReverse">
                    Remark
                    <span ng-if="sortType == 'remark' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'remark' && sortReverse" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-2 text-center">
                    <a href="" ng-click="sortType = 'updated_by'; sortReverse = !sortReverse">
                    Updated By
                    <span ng-if="sortType == 'updated_by' && !sortReverse" class="fa fa-caret-down"></span>
                    <span ng-if="sortType == 'updated_by' && sortReverse" class="fa fa-caret-up"></span>
                </th>
            </tr>
            <tbody>
                <tr dir-paginate="transaction in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" pagination-id="payment_summary" total-items="totalCount">
                    <td class="col-md-1 text-center">{!! Form::checkbox('checkboxes[@{{$index}}]') !!}</td>
                    <td class="col-md-1 text-center">@{{ $index + indexFrom }} </td>
                    <td class="col-md-1 text-center">@{{ transaction.payreceived_date | delDate: "yyyy-MM-dd" }}</td>
                    <td class="col-md-1 text-center">@{{ transaction.pay_method }}</td>
                    <td class="col-md-1 text-right">@{{ transaction.total }} </td>
                    <td class="col-md-3 text-center">@{{ transaction.profile }} </td>
                    <td class="col-md-3 text-left">
                        {!! Form::textarea('remarks[@{{$index}}]', null, [
                                        'class'=>'input-sm form-control',
                                        'rows'=>'2',
                                        'ng-model'=>'transaction.remark',
                                        ]) !!}
                    </td>
                    <td class="col-md-1 text-left">@{{ transaction.name }} </td>

                    <td class="hidden">{!! Form::text('paid_ats[@{{$index}}]', null, ['class'=>'form-control hidden', 'ng-model'=>'transaction.payreceived_date']) !!}</td>
                    <td class="hidden">{!! Form::text('pay_methods[@{{$index}}]', null, ['class'=>'form-control hidden', 'ng-model'=>'transaction.pay_method']) !!}</td>
                    <td class="hidden">{!! Form::text('profile_ids[@{{$index}}]', null, ['class'=>'form-control hidden', 'ng-model'=>'transaction.profile_id']) !!}</td>
                </tr>
                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="14" class="text-center">No Records Found</td>
                </tr>
            </tbody>
        </table>
        {!! Form::close() !!}
        <div>
              <dir-pagination-controls max-size="5" pagination-id="payment_summary" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>