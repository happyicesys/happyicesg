@inject('profiles', 'App\Profile')

<style>
    ._720kb-datepicker-calendar{
        margin-top:0;
        z-index: 9999;
    }
</style>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">

            <div class="pull-left display_num">
                <label for="display_num">Display</label>
                <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
                    <option ng-value="100">100</option>
                    <option ng-value="200">200</option>
                    <option ng-value="All">All</option>
                </select>
                <label for="display_num2" style="padding-right: 20px">per Page</label>
            </div>
        </div>
    </div>

    <div class="panel-body">
    {!! Form::hidden('user_id', Auth::user()->id, ['class'=>'form-group', 'id'=>'user_id']) !!}
        {!! Form::open(['id'=>'daily_rpt', 'method'=>'POST','action'=>['RptController@getDailyPdf']]) !!}
        <div class="row col-md-12 col-sm-12 col-xs-12">
            <div class="form-group col-md-2 col-sm-6 col-xs-12">
                {!! Form::label('transaction_id', 'Invoice:', ['class'=>'control-label search-title']) !!}
                {!! Form::text('transaction_id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.id', 'ng-change'=>'dbSearch()', 'ng-model-options'=>'{ debounce: 350 }', 'placeholder'=>'Inv Num']) !!}
            </div>
            <div class="form-group col-md-2 col-sm-6 col-xs-12">
                {!! Form::label('cust_id', 'ID:', ['class'=>'control-label search-title']) !!}
                {!! Form::text('cust_id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.cust_id', 'ng-change'=>'dbSearch()', 'ng-model-options'=>'{ debounce: 350 }', 'placeholder'=>'Cust ID']) !!}
            </div>
            <div class="form-group col-md-2 col-sm-6 col-xs-12">
                {!! Form::label('company', 'Company:', ['class'=>'control-label search-title']) !!}
                {!! Form::text('company', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.company', 'ng-change'=>'dbSearch()', 'ng-model-options'=>'{ debounce: 350 }', 'placeholder'=>'Company']) !!}
            </div>
            <div class="form-group col-md-2 col-sm-6 col-xs-12">
                {!! Form::label('statuses', 'Status:', ['class'=>'control-label search-title']) !!}
{{--
                <select name="statuses" class="selectmultiple form-control" ng-model="search.statuses" ng-change="dbSearch()" multiple>
                    <option value="">All</option>
                    <option value="Pending">Pending</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Verified Owe">Verified Owe</option>
                    <option value="Verified Paid">Verified Paid</option>
                </select> --}}
                <select name="status" class="select form-control" ng-model="search.status" ng-change="dbSearch()">
                    <option value="">All</option>
                    <option value="Pending">Pending</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Verified Owe">Verified Owe</option>
                    <option value="Verified Paid">Verified Paid</option>
                </select>
{{--

                {!! Form::text('status', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.status', 'ng-change'=>'dbSearch()', 'ng-model-options'=>'{ debounce: 350 }', 'placeholder'=>'Status']) !!} --}}
            </div>
{{--
            <div class="form-group col-md-2 col-sm-6 col-xs-12">
                {!! Form::label('pay_status', 'Payment:', ['class'=>'control-label search-title']) !!}
                {!! Form::text('pay_status', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.pay_status', 'ng-change'=>'dbSearch()', 'ng-model-options'=>'{ debounce: 350 }', 'placeholder'=>'Payment']) !!}
            </div> --}}
            <div class="form-group col-md-2 col-sm-6 col-xs-12">
                {!! Form::label('pay_status', 'Payment', ['class'=>'control-label search-title']) !!}
                {!! Form::select('pay_status', [''=>'All', 'Owe'=>'Owe', 'Paid'=>'Paid'], null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.pay_status',
                    'ng-change'=>'dbSearch()'
                    ])
                !!}
            </div>
            {{-- driver can only view himself --}}
            @unless(Auth::user()->hasRole('driver') or auth()->user()->hasRole('technician'))
                <div class="form-group col-md-2 col-sm-6 col-xs-12 hidden">
                    {!! Form::label('paid_by', 'Pay Received By:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('paid_by', null, ['class'=>'form-control input-sm', 'ng-model'=>'paid_by', 'ng-change'=>'paidByChange(paid_by)', 'ng-model-options'=>'{ debounce: 350 }', 'placeholder'=>'Pay Received By']) !!}
                </div>
            @else
                <div class="form-group col-md-2 col-sm-6 col-xs-12 hidden">
                    {!! Form::label('paid_by', 'Pay Received By:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('paid_by', Auth::user()->name, ['class'=>'form-control input-sm', 'placeholder'=>'Pay Received By', 'disabled'=>'disbaled']) !!}
                </div>
            @endunless
            {{-- paid_at toggle only when on change because need to fulfil orWhere --}}
            <div class="form-group col-md-2 col-sm-6 col-xs-12">
                {!! Form::label('paid_at', 'Date:', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker date-set="@{{today}}" date-format="yyyy-MM-dd">
                        <input
                            type = "text"
                            name = "paid_at"
                            class = "form-control input-sm"
                            placeholder = "Date"
                            ng-model = "paid_at"
                            ng-change = "dateChange2(paid_at)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked(paid_at)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked(paid_at)"></span>
                </div>
            </div>
            <div class="form-group col-md-2 col-sm-6 col-xs-12 hidden">
                {!! Form::label('delivery_date', 'Delivery On:', ['class'=>'control-label search-title']) !!}

                <datepicker date-set="@{{today}}" date-format="yyyy-MM-dd">
                    <input
                        type = "text"
                        name = "delivery_date"
                        class = "form-control input-sm"
                        placeholder = "Delivery Date"
                        ng-model = "delivery_date"
                        ng-change = "dateChange(delivery_date)"
                    />
                </datepicker>
            </div>
        </div>
        <div class="row col-md-12 col-sm-12 col-xs-12">
            {{-- driver can only view himself --}}
            @unless(Auth::user()->hasRole('driver') or auth()->user()->hasRole('technician'))
                <div class="form-group col-md-2 col-sm-6 col-xs-12">
                    {!! Form::label('driver', 'User:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('driver', null, ['class'=>'form-control input-sm', 'ng-model'=>'driver', 'ng-change'=>'driverChange(driver)', 'ng-model-options'=>'{ debounce: 350 }', 'placeholder'=>'User']) !!}
                </div>
            @else
                <div class="form-group col-md-2 col-sm-6 col-xs-12">
                    {!! Form::label('driver', 'User:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('driver', Auth::user()->name, ['class'=>'form-control input-sm', 'placeholder'=>'User', 'readonly'=>'readonly']) !!}
                </div>
            @endunless
            <div class="form-group col-md-2 col-sm-6 col-xs-12">
                {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                {!! Form::select('profile_id', [''=>'All']+
                    $profiles::filterUserProfile()
                    ->pluck('name', 'id')
                    ->all(),
                    null, ['id'=>'profile_id',
                    'class'=>'select_profile form-control',
                    'ng-model'=>'search.profile_id',
                    'ng-change' => 'dbSearch()'
                    ])
                !!}
            </div>
        </div>
            {!! Form::close() !!}
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {!! Form::label('daily_rpt1', 'For Today Delivery Date: (@{{delivery_date}})', ['class'=>'control-label']) !!}
                    <div class="row">
                        <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                            Total Amount for 'Delivered'
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid;">
                            @{{del_amount ? del_amount : 0 | currency: "": 2}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                            Total Qty for 'Delivered'
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid;">
                            @{{del_qty ? del_qty : 0 | currency: "": 4}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                            Total Amount for 'Paid'
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid;">
                            @{{del_paid ? del_paid : 0  | currency: "": 2}}
                        </div>
                    </div>
                </div>
                {{-- @cannot('transaction_view') --}}
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {!! Form::label('daily_rpt2', 'For This Paid Date: (@{{paid_at}})', ['class'=>'control-label']) !!}
                    <div class="row">
                        <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                            Total Amount for 'Paid'
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid" ng-style="{'background-color': paid_equals ? '#98FB98' : '#FF9999'}">
                            @{{paid_amount ? paid_amount : 0 | currency: "": 2}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                            Total Paid 'Cash'
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid;">
                            @{{paid_cash ? paid_cash : 0 | currency: "": 2}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                            Total Paid 'Cheque In'
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid;">
                            @{{paid_cheque_in ? paid_cheque_in : 0 | currency: "": 2}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                            Total Paid 'Cheque Out'
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid;">
                            @{{paid_cheque_out ? paid_cheque_out : 0 | currency: "": 2}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                            Total Paid 'TT'
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid;">
                            @{{paid_tt ? paid_tt : 0 | currency: "": 2}}
                        </div>
                    </div>
                </div>
                {{-- @endcannot --}}
            </div>
        </div>

        <div class="row">
            <div style="padding: 20px 0px 10px 15px">
                <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
                {!! Form::submit('Export PDF', ['name'=>'export_pdf', 'class'=> 'btn btn-warning', 'form'=>'daily_rpt']) !!}
                @cannot('transaction_view')
                {!! Form::submit('Batch Verify', ['name'=>'verify', 'class'=> 'btn btn-success', 'form'=>'verify']) !!}
                @endcannot
                <label class="pull-right" style="padding-right:18px;" for="totalnum">Showing @{{transactions.length}} of @{{transactions.length}} entries</label>
            </div>
        </div>
            {!! Form::open(['id'=>'verify', 'method'=>'POST','action'=>['RptController@getVerifyPaid']]) !!}
            <div class="table-responsive" id="exportable">
                <table class="table table-list-search table-hover table-bordered export-table" data-tableexport-display="always">
                    <tr class="hidden" data-tableexport-display="always">
                        <th></th>
                        <td>Invoice:</td>
                        <td>@{{search.id}}</td>
                        <th></th>
                        <td>ID:</td>
                        <td>@{{search.cust_id}}</td>
                    </tr>
                    <tr class="hidden" data-tableexport-display="always">
                        <th></th>
                        <td>Company:</td>
                        <td>@{{search.company}}</td>
                        <th></th>
                        <td>Status:</td>
                        <td>@{{search.status}}</td>
                    </tr>
                    <tr class="hidden" data-tableexport-display="always">
                        <th></th>
                        <td>Delivery Date:</td>
                        <td>@{{delivery_date}}</td>
                        <th></th>
                        <td>Paid Date:</td>
                        <td>@{{paid_at}}</td>
                    </tr>
                    @if(Auth::user()->hasRole('driver') or auth()->user()->hasRole('technician'))
                        <tr class="hidden" data-tableexport-display="always">
                            <th></th>
                            <td>Pay Received By:</td>
                            <td>{{Auth::user()->name}}</td>
                            <th></th>
                            <td>Delivered By:</td>
                            <td>{{Auth::user()->name}}</td>
                        </tr>
                    @else
                        <tr class="hidden" data-tableexport-display="always">
                            <th></th>
                            <td>Pay Received By:</td>
                            <td>@{{paid_by}}</td>
                            <th></th>
                            <td>Delivered By:</td>
                            <td>@{{driver}}</td>
                        </tr>
                    @endif
                    <tr class="hidden" data-tableexport-display="always">
                        <th></th>
                        <td>Status:</td>
                        <td>@{{search.status}}</td>
                    </tr>

                    <tr class="hidden" data-tableexport-display="always">
                        <td></td>
                    </tr>
                    <tr class="hidden">
                        <td></td>
                        <td data-tableexport-display="always">Total Amount for 'Delivered'</td>
                        <td data-tableexport-display="always" class="text-right">@{{rptdata.amt_del | currency: "": 2}}</td>
                        <td></td>
                        <td ng-if="!getdriver()">Total Amount for 'Paid'</td>
                        <td ng-if="!getdriver()" class="text-right">@{{rptdata.amt_mod | currency: "": 2}}</td>
                    </tr>
                    <tr class="hidden">
                        <td></td>
                        <td data-tableexport-display="always">Total Qty for 'Delivered'</td>
                        <td data-tableexport-display="always" class="text-right">@{{rptdata.qty_del | currency: "": 4}}</td>
                        <td></td>
                        <td ng-if="!getdriver()">Total Paid 'Cash'</td>
                        <td ng-if="!getdriver()" class="text-right">@{{rptdata.cash_mod | currency: "": 2}}</td>
                    </tr>
                    <tr class="hidden">
                        <td></td>
                        <td data-tableexport-display="always">Total Amount for 'Paid'</td>
                        <td data-tableexport-display="always" class="text-right">@{{rptdata.amt_mod | currency: "": 2}}</td>
                        <td></td>
                        <td ng-if="!getdriver()">Total Paid 'Cheque In'</td>
                        <td ng-if="!getdriver()" class="text-right">@{{rptdata.chequein_mod | currency: "": 2}}</td>
                    </tr>
                    <tr class="hidden">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td ng-if="!getdriver()">Total Paid 'Cheque Out'</td>
                        <td ng-if="!getdriver()" class="text-right">@{{rptdata.chequeout_mod | currency: "": 2}}</td>
                    </tr>
                    <tr class="hidden">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td ng-if="!getdriver()">Total Paid 'TT'</td>
                        <td ng-if="!getdriver()" class="text-right">@{{rptdata.tt_mod | currency: "": 2}}</td>
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
                            <a href="" ng-click="sortTable('id')">
                            INV #
                            <span ng-if="search.sortName == 'id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('cust_id')">
                            ID
                            <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('company')">
                            Company
                            <span ng-if="search.sortName == 'company' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'company' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('status')">
                                Status
                            <span ng-if="search.sortName == 'status' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'status' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('delivery_date')">
                                Delivery Date
                            <span ng-if="search.sortName == 'delivery_date' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'delivery_date' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('driver')">
                                Delivery By
                            <span ng-if="search.sortName == 'driver' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'driver' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('total')">
                            Total Amount
                            <span ng-if="search.sortName == 'total' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'total' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('total_qty')">
                            Total Qty
                            <span ng-if="search.sortName == 'total_qty' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'total_qty' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('pay_status')">
                            Payment
                            <span ng-if="search.sortName == 'pay_status' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'pay_status' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('paid_by')">
                            Pay Received By
                            <span ng-if="search.sortName == 'paid_by' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'paid_by' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('paid_at')">
                            Pay Received Dt
                            <span ng-if="search.sortName == 'paid_at' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'paid_at' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        @cannot('transaction_view')
                        <th class="col-md-1 text-center">
                            Action
                        </th>
                        <th class="col-md-1 text-center">
                            Payment Method
                        </th>
                        <th class="col-md-2 text-center">
                            Note
                        </th>
                        @endcannot
                    </tr>
                    <tbody>
                        {{-- <tr><td>@{{transactions}}</td></tr> --}}
                        <tr dir-paginate="transaction in transactions| itemsPerPage:itemsPerPage" current-page="currentPage" ng-controller="repeatController">
                            <td class="col-md-1 text-center">{!! Form::checkbox('checkbox[@{{transaction.id}}]') !!}</td>
                            <td class="col-md-1 text-center">@{{ number }} </td>
                            <td class="col-md-1 text-center">
                                <a href="/transaction/@{{ transaction.id }}/edit">
                                    @{{ transaction.id }}
                                </a>
                            </td>
                            <td class="col-md-1 text-center">@{{ transaction.cust_id }} </td>
                            <td class="col-md-1 text-center" id="to-pdf">
                            <a href="/person/@{{ transaction.person_id }}">
                            @{{ transaction.company }}
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
                            <td class="col-md-1 text-center">@{{ transaction.driver }}</td>
                            <td class="col-md-1 text-center">
                                @{{ transaction.total | currency: "": 2}}
                            </td>
                            <td class="col-md-1 text-center">@{{ transaction.total_qty }}</td>
                            {{-- pay status --}}
                            <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.pay_status == 'Owe'">
                                @{{ transaction.pay_status }}
                            </td>
                            <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.pay_status == 'Paid'">
                                @{{ transaction.pay_status }}
                            </td>
                            <td class="col-md-1 text-center"> @{{ transaction.paid_by ? transaction.paid_by : '-' }}</td>
                            <td class="col-md-1 text-center" ng-if="transaction.paid_at"> @{{ transaction.paid_at | delDate: "yyyy-MM-dd" }}</td>
                            <td class="col-md-1 text-center" ng-if="!transaction.paid_at">-</td>
                            {{-- pay status ended --}}
                            @cannot('transaction_view')
                            <td class="col-md-1 text-center">
                                <a href="/transaction/status/@{{transaction.id}}" class="btn btn-warning btn-sm" ng-if="transaction.status == 'Delivered' && transaction.pay_status == 'Owe'">Verify Owe</a>
                                <a href="#" class="btn btn-success btn-sm" ng-if="(transaction.status == 'Verified Owe' || transaction.status == 'Delivered') && transaction.pay_status == 'Paid'" ng-click="onVerifiedPaid($event, transaction.id, transaction.payMethodModel, transaction.noteModel)">Verify Paid</a>
                            </td>
                            <td class="col-md-1 text-center" ng-if="!transaction.pay_method">
                                {!! Form::select('pay_method[@{{transaction.id}}]', ['cash'=>'Cash', 'cheque'=>'Cheque', 'tt'=>'TT'], null, [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'transaction.payMethodModel',
                                                    'ng-if'=>"(transaction.status == 'Delivered' || transaction.status == 'Verified Owe') && transaction.pay_status == 'Paid'",
                                                    'ng-init'=>"transaction.payMethodModel='cash'",
                                                ]) !!}
                            </td>
                            <td class="col-md-1 text-center" ng-if="transaction.pay_method">
                                @{{transaction.pay_method | capitalize}}
                            </td>
                            <td class="col-md-2 text-center" ng-if="!transaction.pay_method">
                                {!! Form::textarea('note[@{{transaction.id}}]', null, [
                                                'class'=>'input-sm form-control',
                                                'rows'=>'2',
                                                'ng-model'=>'transaction.noteModel',
                                                'ng-show'=>"(transaction.status == 'Delivered' || transaction.status == 'Verified Owe') && transaction.pay_status == 'Paid'",
                                                'style'=>'width:100px;'
                                                ]) !!}
                                <span ng-if="transaction.pay_method">@{{transaction.note}}</span>
                            </td>
                            <td class="col-md-1 text-center" ng-if="transaction.pay_method">
                                <span>@{{transaction.note}}</span>
                            </td>
                            @endcannot
                        </tr>
                        <tr ng-if="transactions.length == 0 || ! transactions.length">
                            <td colspan="16" class="text-center">No Records Found</td>
                        </tr>
                    </tbody>
                </table>
                {!! Form::close() !!}
            </div>
    </div>
        <div class="panel-footer">
            <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
        </div>
</div>