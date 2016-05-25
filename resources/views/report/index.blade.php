@inject('people', 'App\Person')
@inject('drivers', 'App\User')
@inject('profiles', 'App\Profile')
@inject('roles', 'App\Role')

@extends('template')
@section('title')
{{ $REPORT_TITLE }}
@stop
@section('content')

    <meta charset="UTF-8">

    <div class="row">
    <a class="title_hyper pull-left" href="/report"><h1>{{ $REPORT_TITLE }} <i class="fa fa-file-text-o"></i></h1></a>
    </div>

            <div class="panel panel-warning" ng-app="app" ng-controller="rptController">
                <div class="panel-heading">
                        <ul class="nav nav-pills nav-justified" role="tablist">
                            @cannot('transaction_view')
                            <li><a href="#person" role="tab" data-toggle="tab">Customer</a></li>
                            <li><a href="#transaction" role="tab" data-toggle="tab">Transaction</a></li>
                            <li><a href="#byproduct" role="tab" data-toggle="tab">By Product</a></li>
                            <li><a href="#driver" role="tab" data-toggle="tab">Driver</a></li>
                            @endcannot
                            <li class="active"><a href="#dailyrpt" role="tab" data-toggle="tab">Daily Report</a></li>
                        </ul>
                </div>

                <div class="panel-body">
                    <div class="tab-content">
                        {{-- first content --}}
                        <div class="tab-pane" id="person">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-8 col-md-offset-2">
                                        <div class="form-group">
                                        {!! Form::label('cust_choice', 'Select Customer', ['class'=>'control-label']) !!}
                                        {!! Form::open(['id'=>'person_form', 'method'=>'POST','action'=>['RptController@generatePerson']]) !!}
                                        {!! Form::select('cust_choice', [''=>null, 'all'=>'ALL (Info Only)']+$people::where('cust_id', 'NOT LIKE', 'H%')->select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->lists('full', 'id')->all(),
                                            null, ['class'=>'select form-control', 'id'=>'cust_choice']) !!}
                                        {!! Form::close() !!}
                                        </div>

                                        {!! Form::submit('Generate', ['class'=> 'btn btn-primary', 'form'=>'person_form']) !!}
                                     </div>
                                </div>
                            </div>
                        </div>
                        {{-- second content --}}
                        <div class="tab-pane" id="transaction">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-8 col-md-offset-2">
                                        {!! Form::open(['id'=>'transaction_form', 'method'=>'POST','action'=>['RptController@generateTransaction']]) !!}

                                        <div class="row">
                                            <div class="form-group">
                                                {!! Form::radio('choice_transac', 'tran_specific', 'tran_specific') !!}
                                                {!! Form::label('tran_specific', 'Specific') !!}
                                            </div>

                                            <div class="row">
                                               <div class="desc" id="tran_specific">
                                                   <div class="col-md-4">
                                                        {!! Form::label('transaction_datefrom', 'Dates between', ['class'=>'control-label']) !!}
                                                        {!! Form::text('transaction_datefrom', null, ['id'=>'transaction_datefrom', 'class'=>'date form-control']) !!}
                                                   </div>

                                                   <div class="col-md-1 text-center">
                                                   <br/>
                                                        {!! Form::label('and', 'To', ['class'=>'control-label', 'style'=>'margin-top: 10px;']) !!}
                                                   </div>

                                                   <div class="col-md-4">
                                                   <br/>
                                                        {!! Form::text('transaction_dateto', null, ['id'=>'transaction_dateto', 'class'=>'date form-control', 'style'=>'margin-top: 10px;']) !!}
                                                   </div>
                                               </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="form-group">
                                                {!! Form::radio('choice_transac', 'tran_all') !!}
                                                {!! Form::label('tran_all', 'By Year') !!}
                                            </div>

                                            <div class="desc col-md-12" id="tran_all">
                                                <select id="transac_year" name="transac_year" class="select">
                                                    <option value="{{Carbon\Carbon::now()->year}}">{{Carbon\Carbon::now()->year}}</option>
                                                    <option value="{{Carbon\Carbon::now()->subYear()->year}}">{{Carbon\Carbon::now()->subYear()->year}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <br/>
{{--
                                        <div class="row">
                                        <div class="form-group">
                                        {!! Form::radio('choice_transac', 'tran_month') !!}
                                        {!! Form::label('tran_month', 'By Month') !!}
                                        </div>

                                       <div class="desc col-md-12" id="tran_month">
                                            <select id="transac_month" name="transac_month" class="select">
                                            @for($i=1; $i<=Carbon\Carbon::now()->month; $i++)
                                                <option value="{{$i}}">{{date("F", mktime(0, 0, 0, $i, 10))}} {{Carbon\Carbon::now()->subYear()->year}}</option>
                                            @endfor
                                            </select>
                                       </div>
                                        </div>
                                        <br/>
 --}}
                                        {!! Form::close() !!}

                                        <div class="col-md-12" style="padding-top: 20px">
                                        {!! Form::submit('Generate', ['class'=> 'btn btn-primary', 'form'=>'transaction_form']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- end of second --}}
                        {{-- start of third --}}
                        <div class="tab-pane" id="byproduct">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-8 col-md-offset-2">
                                        {!! Form::open(['id'=>'byproduct_form', 'method'=>'POST','action'=>['RptController@generateByProduct']]) !!}
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="desc">
                                                    <div class="col-md-4">
                                                    {!! Form::label('byproduct_datefrom', 'Dates between', ['class'=>'control-label']) !!}
                                                    {!! Form::text('byproduct_datefrom', null, ['id'=>'byproduct_datefrom', 'class'=>'date form-control']) !!}
                                                    </div>

                                                    <div class="col-md-1 text-center">
                                                        <br/>
                                                        {!! Form::label('and', 'To', ['class'=>'control-label', 'style'=>'margin-top: 10px;']) !!}
                                                    </div>

                                                    <div class="col-md-4">
                                                        <br/>
                                                        {!! Form::text('byproduct_dateto', null, ['id'=>'byproduct_dateto', 'class'=>'date form-control', 'style'=>'margin-top: 10px;']) !!}
                                                    </div>
                                                    </div>
                                                </div>
                                        {!! Form::close() !!}
                                            </div>

                                        {!! Form::submit('Generate', ['class'=> 'btn btn-primary', 'form'=>'byproduct_form']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- end of third--}}
                        {{-- start of fourth--}}
                        <div class="tab-pane" id="driver">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-8 col-md-offset-2">
                                        {!! Form::open(['id'=>'driver_form', 'method'=>'POST','action'=>['RptController@generateDriver']]) !!}

                                        <div class="form-group">
                                            {!! Form::label('driver', 'Driver', ['class'=>'control-label']) !!}
                                            @can('transaction_view')
                                                {!! Form::select('driver', [''=>null]+$drivers::whereHas('roles', function($q){
                                                        $q->whereName('driver');
                                                })->lists('name', 'name')->all(),
                                                Auth::user()->name, ['class'=>'select form-control', 'id'=>'cust_choice']) !!}
                                            @else
                                                {!! Form::select('driver', [''=>null]+$drivers::whereHas('roles', function($q){
                                                        $q->whereName('driver');
                                                })->lists('name', 'name')->all(),
                                                null, ['class'=>'select form-control', 'id'=>'cust_choice']) !!}
                                            @endcan
                                        </div>


                                        <div class="form-group">
                                            <div class="row">
                                               <div class="desc">
                                                   <div class="col-md-4">
                                                        {!! Form::label('driver_datefrom', 'Dates between', ['class'=>'control-label']) !!}
                                                        {!! Form::text('driver_datefrom', null, ['id'=>'driver_datefrom', 'class'=>'datetoday form-control']) !!}
                                                   </div>

                                                   <div class="col-md-1 text-center">
                                                   <br/>
                                                        {!! Form::label('and', 'To', ['class'=>'control-label', 'style'=>'margin-top: 10px;']) !!}
                                                   </div>

                                                   <div class="col-md-4">
                                                   <br/>
                                                        {!! Form::text('driver_dateto', null, ['id'=>'driver_dateto', 'class'=>'datetoday form-control', 'style'=>'margin-top: 10px;']) !!}
                                                   </div>
                                               </div>
                                            </div>
                                        {!! Form::close() !!}
                                        </div>

                                        {!! Form::submit('Generate', ['class'=> 'btn btn-primary', 'form'=>'driver_form']) !!}
                                     </div>
                                </div>
                            </div>
                        </div>
                        {{-- end of fourth --}}

                        {{-- start of fifth --}}
                        <div class="tab-pane active" id="dailyrpt">

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="panel-title">

                                        <div class="pull-left display_num">
                                            <label for="display_num">Display</label>
                                            <select ng-model="itemsPerPage" ng-init="itemsPerPage='70'">
                                                <option ng-value="10">10</option>
                                                <option ng-value="30">30</option>
                                                <option ng-value="70">70</option>
                                                <option ng-value="All">All</option>
                                            </select>
                                            <label for="display_num2" style="padding-right: 20px">per Page</label>
                                        </div>
{{--                                         <div class="col-md-6 pull-right">
                                            <div class="col-md-2"  style="padding-top:10px">
                                                <label for="role_id" class="search">Role:</label>
                                            </div>
                                            <div class="col-md-9" style="padding-top:10px">
                                                {!! Form::select('role_id', [''=>'All']+$roles::lists('label', 'name')->all(), null, ['id'=>'profile_id',
                                                    'class'=>'select',
                                                    'ng-model'=>'role',
                                                    'ng-change'=>'onRoleChanged(role)'
                                                    ])
                                                !!}
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>

                                <div class="panel-body">
                                {!! Form::hidden('user_id', Auth::user()->id, ['class'=>'form-group', 'id'=>'user_id']) !!}
                                    {!! Form::open(['id'=>'daily_rpt', 'method'=>'POST','action'=>['RptController@getDailyPdf']]) !!}
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                            {!! Form::label('transaction_id', 'Invoice:', ['class'=>'control-label search-title']) !!}
                                            {!! Form::text('transaction_id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.id', 'placeholder'=>'Inv Num']) !!}
                                        </div>
                                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                            {!! Form::label('cust_id', 'ID:', ['class'=>'control-label search-title']) !!}
                                            {!! Form::text('cust_id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.cust_id', 'placeholder'=>'Cust ID']) !!}
                                        </div>
                                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                            {!! Form::label('company', 'Company:', ['class'=>'control-label search-title']) !!}
                                            {!! Form::text('company', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.company', 'placeholder'=>'Company']) !!}
                                        </div>
                                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                            {!! Form::label('status', 'Status:', ['class'=>'control-label search-title']) !!}
                                            {!! Form::text('status', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.status', 'placeholder'=>'Status']) !!}
                                        </div>
                                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                            {!! Form::label('pay_status', 'Payment:', ['class'=>'control-label search-title']) !!}
                                            {!! Form::text('pay_status', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.pay_status', 'placeholder'=>'Payment']) !!}
                                        </div>
                                        {{-- driver can only view himself --}}
                                        @unless(Auth::user()->hasRole('driver'))
                                            <div class="form-group col-md-2 col-sm-4 col-xs-6 hidden">
                                                {!! Form::label('paid_by', 'Pay Received By:', ['class'=>'control-label search-title']) !!}
                                                {!! Form::text('paid_by', null, ['class'=>'form-control input-sm', 'ng-model'=>'paid_by', 'ng-change'=>'paidByChange(paid_by)', 'placeholder'=>'Pay Received By']) !!}
                                            </div>
                                        @else
                                            <div class="form-group col-md-2 col-sm-4 col-xs-6 hidden">
                                                {!! Form::label('paid_by', 'Pay Received By:', ['class'=>'control-label search-title']) !!}
                                                {!! Form::text('paid_by', Auth::user()->name, ['class'=>'form-control input-sm', 'placeholder'=>'Pay Received By', 'disabled'=>'disbaled']) !!}
                                            </div>
                                        @endunless

                                        {{-- paid_at toggle only when on change because need to fulfil orWhere --}}
                                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                            {!! Form::label('paid_at', 'Date:', ['class'=>'control-label search-title']) !!}
                                            <div class="dropdown">
                                                <a class="dropdown-toggle" id="dropdown3" role="button" data-toggle="dropdown" data-target="" href="">
                                                    <div class="input-group">
                                                        {!! Form::text('paid_at', null, ['class'=>'form-control input-sm', 'ng-model'=>'paid_at', 'ng-init'=>"paid_at=today", 'placeholder'=>'Date']) !!}
                                                    </div>
                                                </a>
                                                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                                <datetimepicker data-ng-model="paid_at" data-datetimepicker-config="{ dropdownSelector: '#dropdown3', minView: 'day'}" ng-change="dateChange2(paid_at)"/>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-2 col-sm-4 col-xs-6 hidden">
                                            {!! Form::label('delivery_date', 'Delivery On:', ['class'=>'control-label search-title']) !!}
                                            <div class="dropdown">
                                                <a class="dropdown-toggle" id="dropdown2" role="button" data-toggle="dropdown" data-target="" href="">
                                                    <div class="input-group">
                                                        {!! Form::text('delivery_date', null, ['class'=>'form-control input-sm', 'ng-model'=>'delivery_date', 'ng-init'=>"delivery_date=today", 'placeholder'=>'Delivery Date']) !!}
                                                    </div>
                                                </a>
                                                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                                <datetimepicker data-ng-model="delivery_date" data-datetimepicker-config="{ dropdownSelector: '#dropdown2', minView: 'day'}" ng-change="dateChange(delivery_date)"/>
                                                </ul>
                                            </div>
                                        </div>
                                        {{-- driver can only view himself --}}
                                        @unless(Auth::user()->hasRole('driver'))
                                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                                {!! Form::label('driver', 'User:', ['class'=>'control-label search-title']) !!}
                                                {!! Form::text('driver', null, ['class'=>'form-control input-sm', 'ng-model'=>'driver', 'ng-change'=>'driverChange(driver)', 'placeholder'=>'User']) !!}
                                            </div>
                                        @else
                                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                                {!! Form::label('driver', 'User:', ['class'=>'control-label search-title']) !!}
                                                {!! Form::text('driver', Auth::user()->name, ['class'=>'form-control input-sm', 'placeholder'=>'User', 'readonly'=>'readonly']) !!}
                                            </div>
                                        @endunless
                                        {!! Form::close() !!}
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            {!! Form::label('daily_rpt1', 'For Today Delivery Date: (@{{delivery_date}})', ['class'=>'control-label']) !!}
                                            <div class="row">
                                                <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                                                    Total Amount for 'Delivered'
                                                </div>
                                                <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid">
                                                    @{{rptdata.amt_del | currency: "": 2}}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                                                    Total Qty for 'Delivered'
                                                </div>
                                                <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid;">
                                                    @{{rptdata.qty_del | currency: "": 4}}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                                                    Total Amount for 'Paid'
                                                </div>
                                                <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid;">
                                                    @{{rptdata.amt_mod | currency: "": 2}}
                                                </div>
                                            </div>
                                        </div>
                                        @cannot('transaction_view')
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            {!! Form::label('daily_rpt2', 'For This Paid Date: (@{{paid_at}})', ['class'=>'control-label']) !!}
                                            <div class="row">
                                                <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                                                    Total Amount for 'Paid'
                                                </div>
                                                <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid">
                                                    @{{rptdata.amt_mod | currency: "": 2}}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                                                    Total Paid 'Cash'
                                                </div>
                                                <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid;">
                                                    @{{rptdata.cash_mod | currency: "": 2}}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5 col-sm-5 col-xs-5" style="margin-left: 15px;">
                                                    Total Paid 'Cheque/TT'
                                                </div>
                                                <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid;">
                                                    @{{rptdata.cheque_mod | currency: "": 2}}
                                                </div>
                                            </div>
                                        </div>
                                        @endcannot
                                    </div>

                                    <div class="row">
                                        <div style="padding: 20px 0px 10px 15px">
                                            <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
                                            {{-- <button class="btn btn-warning" ng-click="exportPDF()">Export PDF</button> --}}
{{--                                             <button class="btn btn-warning" onclick="$('.export-table').tableExport({
                                                                                        type:'pdf',
                                                                                        format: 'a4',
                                                                                        ignoreColumn: [13, 14, 15],
                                                                                        jspdf: {
                                                                                            orientation: 'l',
                                                                                            autotable: {
                                                                                                theme: 'grid',
                                                                                                alternateRowStyles: 'none'
                                                                                            },
                                                                                        },
                                                                                        escape:'false',
                                                                                        pdfFontSize:10,
                                                                                        fileName: 'DailyRpt'
                                                                                    });" >Export PDF</button> --}}
                                            {!! Form::submit('Export PDF', ['name'=>'export_pdf', 'class'=> 'btn btn-warning', 'form'=>'daily_rpt']) !!}
                                            {{-- {!! Form::submit('Batch Verify', ['name'=>'verify', 'class'=> 'btn btn-success', 'form'=>'verify']) !!} --}}
                                            <label class="pull-right" style="padding-right:18px;" for="totalnum">Showing @{{(transactions | filter:search).length}} of @{{transactions.length}} entries</label>
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
                                                @if(Auth::user()->hasRole('driver'))
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
                                                    <td ng-if="!getdriver()">Total Paid 'Cheque/TT'</td>
                                                    <td ng-if="!getdriver()" class="text-right">@{{rptdata.cheque_mod | currency: "": 2}}</td>
                                                </tr>
                                                <tr class="hidden" data-tableexport-display="always">
                                                    <td></td>
                                                </tr>
                                                <tr style="background-color: #DDFDF8">
                                                    {{-- <th class="col-md-1 text-center"></th> --}}
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
                                                        Company
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
                                                        <a href="" ng-click="sortType = 'driver'; sortReverse = !sortReverse">
                                                        Delivered By
                                                        <span ng-if="sortType == 'driver' && !sortReverse" class="fa fa-caret-down"></span>
                                                        <span ng-if="sortType == 'driver' && sortReverse" class="fa fa-caret-up"></span>
                                                    </th>
                                                    <th class="col-md-1 text-center">
                                                        <a href="" ng-click="sortType = 'total'; sortReverse = !sortReverse">
                                                        Total Amount
                                                        <span ng-if="sortType == 'total' && !sortReverse" class="fa fa-caret-down"></span>
                                                        <span ng-if="sortType == 'total' && sortReverse" class="fa fa-caret-up"></span>
                                                    </th>
                                                    <th class="col-md-1 text-center">
                                                        <a href="" ng-click="sortType = 'total_qty'; sortReverse = !sortReverse">
                                                        Total Qty
                                                        <span ng-if="sortType == 'total_qty' && !sortReverse" class="fa fa-caret-down"></span>
                                                        <span ng-if="sortType == 'total_qty' && sortReverse" class="fa fa-caret-up"></span>
                                                    </th>
                                                    <th class="col-md-1 text-center">
                                                        <a href="" ng-click="sortType = 'pay_status'; sortReverse = !sortReverse">
                                                        Payment
                                                        <span ng-if="sortType == 'pay_status' && !sortReverse" class="fa fa-caret-down"></span>
                                                        <span ng-if="sortType == 'pay_status' && sortReverse" class="fa fa-caret-up"></span>
                                                    </th>
                                                    <th class="col-md-1 text-center">
                                                        <a href="" ng-click="sortType = 'paid_by'; sortReverse = !sortReverse">
                                                        Pay Received By
                                                        <span ng-if="sortType == 'paid_by' && !sortReverse" class="fa fa-caret-down"></span>
                                                        <span ng-if="sortType == 'paid_by' && sortReverse" class="fa fa-caret-up"></span>
                                                    </th>
                                                    <th class="col-md-1 text-center">
                                                        <a href="" ng-click="sortType = 'paid_at'; sortReverse = !sortReverse">
                                                        Pay Received Dt
                                                        <span ng-if="sortType == 'paid_at' && !sortReverse" class="fa fa-caret-down"></span>
                                                        <span ng-if="sortType == 'paid_at' && sortReverse" class="fa fa-caret-up"></span>
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
{{--                                                     <tr>
                                                        <td class="hidden text-center" data-tableexport-display="always">#</td>
                                                        <td class="hidden text-center" data-tableexport-display="always">Inv</td>
                                                        <td class="hidden text-center" data-tableexport-display="always">ID</td>
                                                        <td class="hidden text-center" data-tableexport-display="always">Company</td>
                                                        <td class="hidden text-center" data-tableexport-display="always">Status</td>
                                                        <td class="hidden text-center" data-tableexport-display="always">Delivery Date</td>
                                                        <td class="hidden text-center" data-tableexport-display="always">Delivery By</td>
                                                        <td class="hidden text-center" data-tableexport-display="always">Total Amount</td>
                                                        <td class="hidden text-center" data-tableexport-display="always">Total Qty</td>
                                                        <td class="hidden text-center" data-tableexport-display="always">Payment</td>
                                                        <td class="hidden text-center" data-tableexport-display="always">Pay Received By</td>
                                                        <td class="hidden text-center" data-tableexport-display="always">Pay Received Dt</td>
                                                    </tr> --}}
                                                    <tr dir-paginate="transaction in transactions | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage" current-page="currentPage" ng-controller="repeatController">
                                                        {{-- <td class="col-md-1 text-center">{!! Form::checkbox('checkbox[@{{transaction.id}}]') !!}</td> --}}
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

                                                        <td class="col-md-1 text-center" ng-if="transaction.gst">@{{ (+(transaction.total * 7/100).toFixed(2) + transaction.total * 1).toFixed(2)}}</td>
                                                        <td class="col-md-1 text-center" ng-if="!transaction.gst">@{{ transaction.total }}</td>
                                                        <td class="col-md-1 text-center">@{{ transaction.total_qty }}</td>
                                                        {{-- pay status --}}
                                                        <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.pay_status == 'Owe'">
                                                            @{{ transaction.pay_status }}
                                                        </td>
                                                        <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.pay_status == 'Paid'">
                                                            @{{ transaction.pay_status }}
                                                        </td>
                                                        <td class="col-md-1 text-center"> @{{ transaction.paid_by ? transaction.paid_by : '-' }}</td>
                                                        <td class="col-md-1 text-center"> @{{ transaction.paid_at ? transaction.paid_at : '-'}}</td>
                                                        {{-- pay status ended --}}
                                                        @cannot('transaction_view')
                                                        <td class="col-md-1 text-center">
                                                            {{-- print invoice         --}}
                                                            {{-- <a href="/transaction/download/@{{ transaction.id }}" class="btn btn-primary btn-sm" ng-if="transaction.status != 'Pending' && transaction.status != 'Cancelled'">Print</a> --}}
                                                            {{-- button view shown when cancelled --}}
                                                            {{-- <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-sm btn-default" ng-if="transaction.status == 'Cancelled'">View</a> --}}
                                                            {{-- <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-sm btn-warning" ng-if="transaction.status != 'Cancelled'">Edit</a> --}}
                                                            {{-- Payment Verification --}}
                                                            <a href="/transaction/status/@{{transaction.id}}" class="btn btn-warning btn-sm" ng-if="transaction.status == 'Delivered' && transaction.pay_status == 'Owe'">Verify Owe</a>
                                                            {{-- <a href="#" class="btn btn-success btn-sm" ng-if="(transaction.status == 'Verified Owe' || transaction.status == 'Delivered') && transaction.pay_status == 'Paid'" ng-click="onVerifiedPaid($event, transaction.id, payMethodModel, noteModel)">Verify Paid</a> --}}
                                                            <a href="/transaction/status/@{{transaction.id}}" class="btn btn-success btn-sm" ng-if="(transaction.status == 'Verified Owe' || transaction.status == 'Delivered') && transaction.pay_status == 'Paid'">Verify Paid</a>
                                                        </td>
                                                        <td class="col-md-1 text-center" ng-if="!transaction.pay_method">
                                                            {!! Form::select('pay_method[@{{transaction.id}}]', ['cash'=>'Cash', 'cheque'=>'Cheque/TT'], null, [
                                                                                'class'=>'form-control input-sm',
                                                                                'ng-model'=>'payMethodModel',
                                                                                'ng-if'=>"(transaction.status == 'Delivered' || transaction.status == 'Verified Owe') && transaction.pay_status == 'Paid'",
                                                                                'placeholder'=>'Inv Num'
                                                                            ]) !!}
                                                        </td>
                                                        <td class="col-md-1 text-center" ng-if="transaction.pay_method">
                                                            @{{transaction.pay_method == 'cash' ? 'Cash' : 'Cheque/TT'}}
                                                        </td>
                                                        <td class="col-md-2 text-center">
                                                            {!! Form::textarea('note[@{{transaction.id}}]', null, [
                                                                            'class'=>'input-sm form-control',
                                                                            'rows'=>'2',
                                                                            'ng-model'=>'noteModel',
                                                                            'ng-show'=>"(transaction.status == 'Delivered' || transaction.status == 'Verified Owe') && transaction.pay_status == 'Paid'",
                                                                            'style'=>'width:100px;'
                                                                            ]) !!}
                                                            <span ng-if="transaction.pay_method">@{{transaction.note}}</span>
                                                        </td>
                                                        @endcannot
                                                    </tr>
                                                    <tr ng-if="(transactions | filter:search).length == 0 || ! transactions.length">
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
                        </div>
                        {{-- end of fifth --}}

                    </div>
                </div>
            </div>

<script src="/js/rpt_index.js"></script>
<script>
    $('.select').select2({'placeholder':'Select...'});

    $('.date').datetimepicker({
        format: 'DD MMM YY'
    });

    $('.datetoday').datetimepicker({
        format: 'DD MMM YY',
        defaultDate: new Date()
    });

    $(document).ready(function() {
        $('#tran_all').hide();
        $('#tran_month').hide();
        $("input[name$='choice_transac']").click(function() {
            var test = $(this).val();
            $("div.desc").hide();
            $('#'+test).show();
        });
    });

    $(function() {
        // for bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // save the latest tab; use cookies if you like 'em better:
            localStorage.setItem('lastTab', $(this).attr('href'));
        });
        // go to the latest tab, if it exists:
        var lastTab = localStorage.getItem('lastTab');
        if (lastTab) {
            $('[href="' + lastTab + '"]').tab('show');
        }
    });
</script>
@stop
