@inject('people', 'App\Person')
@inject('deals', 'App\Deal')
@inject('products', 'App\Item')
@extends('template')
@section('title')
{{ $DETAILRPT_TITLE }}
@stop
@section('content')

<div class="row">
    <a class="title_hyper pull-left" href="/detailrpt/account"><h1>Invoice Breakdown - {{ $DETAILRPT_TITLE }}</h1></a>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        Invoice Breakdown
        @if($person)
            <span>{{$person->cust_id}}: {{$person->company}}</span>
        @endif
    </div>
    <div class="panel-body">
        <div class="row form-group">
            {!! Form::open(['id'=>'submit_invoicebreakdown', 'method'=>'POST', 'action'=>['DetailRptController@getInvoiceBreakdownIndex']]) !!}
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('person_id', [''=>null]+$people::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id')->whereActive('Yes')->where('cust_id', 'NOT LIKE', 'H%')->lists('full', 'id')->all(), isset($person) ? $person->id : null,
                        ['class'=>'select form-control'])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('status', [''=>'All', 'Delivered'=>'Delivered', 'Confirmed'=>'Confirmed', 'Cancelled'=>'Cancelled'], 'Delivered', ['class'=>'select form-control'])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('delivery_from', 'Delivery From', ['class'=>'control-label search-title']) !!}
                    <div class="input-group date">
                        {!! Form::text('delivery_from', null, ['class'=>'form-control', 'id'=>'delivery_from']) !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('delivery_to', 'Delivery To', ['class'=>'control-label search-title']) !!}
                    <div class="input-group date">
                        {!! Form::text('delivery_to', null, ['class'=>'form-control', 'id'=>'delivery_to']) !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>

        <div class="row form-group">
            <div class="col-md-6 col-sm-6 col-xs-12">
                {{-- <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button> --}}
                <button type="submit" class="btn btn-default" form="submit_invoicebreakdown">
                    <i class="fa fa-search"></i><span class="hidden-xs"> Search</span>
                </button>
    {{--             <button type="submit" class="btn btn-primary" form="submit_invoicebreakdown">
                    <i class="fa fa-file-excel-o"></i><span class="hidden-xs"> Export Excel</span>
                </button> --}}
            </div>
            <div class="col-md-5 col-sm-5 col-xs-12">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Total Revenue:
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>{{ number_format($deals::whereIn('transaction_id', $transactionArr)->sum('amount'), 2, '.', '') }}</strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Total Cost:
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>{{ number_format($deals::whereIn('transaction_id', $transactionArr)->sum(DB::raw('qty * unit_cost')), 2, '.', '') }}</strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Gross Earning:
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>{{number_format($deals::whereIn('transaction_id', $transactionArr)->sum('amount') - $deals::whereIn('transaction_id', $transactionArr)->sum(DB::raw('qty * unit_cost')), 2, '.', '')}}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive" style="padding-top: 20px;">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-4">Invoice #</th>
                    <th class="col-md-1"></th>
                    <th class="col-md-1"></th>
                    @foreach($transactions as $transaction)
                        <th class="col-md-1 text-center" colspan="2">{{$transaction->id}}</th>
                    @endforeach
                </tr>
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-4">Delivery Date</th>
                    <th class="col-md-1"></th>
                    <th class="col-md-1"></th>
                    @foreach($transactions as $transaction)
                        <th class="col-md-1 text-center" colspan="2">{{$transaction->delivery_date}}</th>
                    @endforeach
                </tr>
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-4">Delivered By</th>
                    <th class="col-md-1"></th>
                    <th class="col-md-1"></th>
                    @foreach($transactions as $transaction)
                        <th class="col-md-1 text-center" colspan="2">{{$transaction->driver}}</th>
                    @endforeach
                </tr>
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-4">Payment</th>
                    <th class="col-md-1"></th>
                    <th class="col-md-1"></th>
                    @foreach($transactions as $transaction)
                        <th class="col-md-1 text-center" colspan="2">{{$transaction->pay_method}}</th>
                    @endforeach
                </tr>
                <tr></tr>
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-4">Item</th>
                    <th class="col-md-1 text-center">Total Qty</th>
                    <th class="col-md-1 text-center">Total $</th>
                    @foreach($transactions as $transaction)
                        <th class="col-md-1 text-center">Qty</th>
                        <th class="col-md-1 text-center">$</th>
                    @endforeach
                </tr>

                @foreach($items as $item)
                <tr>
                    <td class="col-md-4">{{$item->product_id}} - {{$item->name}}</td>
                    <td class="col-md-1 text-right">{{$deals::whereIn('transaction_id', $transactionArr)->whereItemId($item->id)->sum('qty')}}</td>
                    <td class="col-md-1 text-right">{{$deals::whereIn('transaction_id', $transactionArr)->whereItemId($item->id)->sum('amount')}}</td>
                    @foreach($transactions as $transaction)
                        <td class="col-md-1 text-right">{{$deals::whereTransactionId($transaction->id)->whereItemId($item->id)->sum('qty')}}</td>
                        <td class="col-md-1 text-right">{{$deals::whereTransactionId($transaction->id)->whereItemId($item->id)->sum('amount')}}</td>
                    @endforeach
                </tr>
                @endforeach

                <tr>
                    <th class="col-md-4">Total</th>
                    <th class="col-md-1 text-right">{{$deals::whereIn('transaction_id', $transactionArr)->sum('qty')}}</th>
                    <th class="col-md-1 text-right">{{$deals::whereIn('transaction_id', $transactionArr)->sum('amount')}}</th>
                    @foreach($transactions as $transaction)
                        <td class="col-md-1 text-right">{{$deals::whereTransactionId($transaction->id)->sum('qty')}}</td>
                        <td class="col-md-1 text-right">{{$deals::whereTransactionId($transaction->id)->sum('amount')}}</td>
                    @endforeach
                </tr>
            </table>
        </div>
    </div>
</div>

    @if($person)
        @if($person->is_vending)
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Vending Machine Data: {{$person->cust_id}} - {{$person->company}}
                </div>
                <div class="panel-body">

                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Price Per Piece:
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>{{$person->vending_piece_price}}</strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Monthly Rental:
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>{{$person->vending_monthly_rental}}</strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Profit Sharing:
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>{{$person->vending_profit_sharing}}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                    <div class="table-responsive" style="padding-top: 20px;">
                        <table class="table table-list-search table-hover table-bordered">
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-3">Invoice #</th>
                                @foreach($transactions as $transaction)
                                    <th class="col-md-3 text-center">{{$transaction->id}}</th>
                                @endforeach
                            </tr>
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-3">Delivery Date</th>
                                @foreach($transactions as $transaction)
                                    <th class="col-md-3 text-center">{{$transaction->delivery_date}}</th>
                                @endforeach
                            </tr>
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-3">Delivered By</th>
                                @foreach($transactions as $transaction)
                                    <th class="col-md-3 text-center">{{$transaction->driver}}</th>
                                @endforeach
                            </tr>
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-3">Payment</th>
                                @foreach($transactions as $transaction)
                                    <th class="col-md-3 text-center">{{$transaction->pay_method}}</th>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="col-md-3">Sale Quatity (Based on Analog)</th>
                                @foreach($caltransactions as $index => $caltransaction)
                                    <td class="col-md-3 text-right">
                                        {{$index + 1 < count($caltransactions) ? $caltransaction->analog_clock - $caltransactions[$index + 1]->analog_clock : 0 }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="col-md-3">Digital Clocker</th>
                                @foreach($transactions as $transaction)
                                    <td class="col-md-3 text-right">{{$transaction->digital_clock}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="col-md-3">Analog Clocker</th>
                                @foreach($transactions as $transaction)
                                    <td class="col-md-3 text-right">{{$transaction->analog_clock}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="col-md-3">Balance Coin</th>
                                @foreach($transactions as $transaction)
                                    <td class="col-md-3 text-right">{{$transaction->balance_coin}}</td>
                                @endforeach
                            </tr>
                        </table>

                        <table class="table table-list-search table-hover table-bordered">
                            <tr>
                                <th class="col-md-2">Payment Received</th>
                                <th class="col-md-1 text-left">Total</th>
                            </tr>
                            <tr>
                                <td class="col-md-2">Expected Payment Received</td>
                                <td class="col-md-1 text-right">
                                    {{ number_format(($transactions[0]->analog_clock - $transactions[2]->analog_clock) * $person->vending_piece_price , 2, '.', '') }}
                                </td>
                                @foreach($caltransactions as $index => $caltransaction)
                                    <td class="col-md-3 text-right">
                                        {{$index + 1 < count($caltransactions) ? number_format(($caltransaction->analog_clock - $caltransactions[$index + 1]->analog_clock) * $person->vending_piece_price, 2, '.', '') : 0.00}}
                                    </td>
                                @endforeach
                            </tr>

                            <tr>
                                <td class="col-md-2">Balance Coin</td>
                                <td class="col-md-1"></td>
                                @foreach($transactions as $transaction)
                                    <td class="col-md-3 text-right">{{$transaction->balance_coin}}</td>
                                @endforeach
                            </tr>
 {{--                            @if(count($deals::whereIn('transaction_id', $transactionArr)->whereItemId($products::whereProductId('051')->get())) > 0 and count($deals::whereIn('transaction_id', $transactionArr)->whereItemId($products::whereProductId('051a')->get())) > 0) --}}

                            <tr>
                                <td class="col-md-2">{{$products::whereProductId('051')->first()->product_id}} - {{$products::whereProductId('051')->first()->name}}</td>
                                <td></td>
                                @foreach($transactions as $transaction)
                                    <td class="col-md-3 text-right">
                                        {{$deals::whereTransactionId($transaction->id)->whereItemId($products::whereProductId('051')->first()->id)->first() ? $deals::whereTransactionId($transaction->id)->whereItemId($products::whereProductId('051')->first()->id)->first()->amount : 0.00}}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="col-md-2">Actual Subtotal Received</td>
                                <td class="col-md-1 text-right">{{number_format($transactions[0]->balance_coin + ($deals::whereIn('transaction_id', $transactionArr)->whereItemId($products::whereProductId('051')->first()->id)->sum('amount')), 2, '.', '')}}</td>
                            </tr>
                            <tr>
                                <th class="col-md-2">Difference(Actual - Expected)</th>
                                <td class="col-md-1 text-right">{{number_format($transactions[0]->balance_coin + ($deals::whereIn('transaction_id', $transactionArr)->whereItemId($products::whereProductId('051')->first()->id)->sum('amount')) - (($transactions[0]->analog_clock - $transactions[2]->analog_clock) * $person->vending_piece_price), 2, '.', '')}}</td>
                            </tr>
                            <tr>
                                <td class="col-md-2">
                                    {{$products::whereProductId('051a')->first()->product_id}} - {{$products::whereProductId('051a')->first()->name}}
                                </td>
                                <td class="col-md-1 text-right">
                                    {{$deals::whereIn('transaction_id', $transactionArr)->whereItemId($products::whereProductId('051a')->first()->id)->sum('amount')}}
                                </td>
                                @foreach($transactions as $transaction)
                                    <td class="col-md-3 text-right">
                                        {{$deals::whereTransactionId($transaction->id)->whereItemId($products::whereProductId('051a')->first()->id)->first() ? $deals::whereTransactionId($transaction->id)->whereItemId($products::whereProductId('051a')->first()->id)->first()->amount : 0.00}}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="col-md-2">Stock Value in VM</th>
                                <td class="col-md-1 text-right">
                                    {{ number_format(($deals::whereIn('transaction_id', $transactionArr)->whereItemId($products::whereProductId('051a')->first()->id)->sum('amount')) - ($transactions[0]->balance_coin + ($deals::whereIn('transaction_id', $transactionArr)->whereItemId($products::whereProductId('051')->first()->id)->sum('amount'))), 2, '.', '')}}
                                </td>
                            </tr>
                            {{-- @endif --}}
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <script>
        $('.date').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $('.select').select2({
            placeholder: 'Select...'
        });
    </script>
@stop