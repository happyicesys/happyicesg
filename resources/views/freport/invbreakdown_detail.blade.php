@inject('people', 'App\Person')
@inject('deals', 'App\Deal')
@inject('items', 'App\Item')
@inject('transactions', 'App\Transaction')

@extends('template')
@section('title')
    {{$FRANCHISE_RPT}}
@stop
@section('content')

<div class="row">
    <a class="title_hyper pull-left" href="/freport"><h1>Invoice Breakdown - Detail</h1></a>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        Invoice Breakdown
        @if($people::find($person_id))
            <span>{{$people::find($person_id)->cust_id}}: {{$people::find($person_id)->company}}</span>
        @endif
    </div>

    <div class="panel-body">
        <div class="row form-group">
            {!! Form::open(['id'=>'submit_invoicebreakdown', 'method'=>'POST', 'action'=>['FreportController@getInvoiceBreakdownDetail']]) !!}
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('person_id', [''=>null]+
                        $people::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))
                            ->whereActive('Yes')
                            ->where('cust_id', 'NOT LIKE', 'H%')
                            ->whereHas('profile', function($q) {
                                $q->filterUserProfile();
                            })
                            ->filterFranchiseePeople()
                            ->orderBy('cust_id')
                            ->pluck('full', 'id')
                            ->all(),
                        $request->person_id ? $request->person_id : null,
                        ['class'=>'select form-control'])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('status', [''=>'All', 'Delivered'=>'Delivered', 'Confirmed'=>'Confirmed', 'Cancelled'=>'Cancelled'], $request->status ? $request->status : 'Delivered', ['class'=>'select form-control'])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('delivery_from', 'Delivery From', ['class'=>'control-label search-title']) !!}
                    <div class="input-group date">
                        {!! Form::text('delivery_from', $request->delivery_from ? $request->delivery_from : null, ['class'=>'form-control', 'id'=>'delivery_from']) !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('delivery_to', 'Delivery To', ['class'=>'control-label search-title']) !!}
                    <div class="input-group date">
                        {!! Form::text('delivery_to', $request->delivery_to ? $request->delivery_to : null, ['class'=>'form-control', 'id'=>'delivery_to']) !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>

        <div class="row form-group">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <button type="submit" class="btn btn-default" form="submit_invoicebreakdown">
                    <i class="fa fa-search"></i><span class="hidden-xs"> Search</span>
                </button>
                @if($people::find($person_id))
                    <button type="submit" class="btn btn-primary" name="export_excel" value="export_excel" form="submit_invoicebreakdown">
                    <i class="fa fa-file-excel-o"></i><span class="hidden-xs"> Export All Excel</span>
                    </button>
                @endif
            </div>
            <div class="col-md-5 col-sm-5 col-xs-12">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Total Revenue ($):
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        {{-- retrieve vend cash received (product_id 051) --}}
                        <strong>{{ number_format($deals::where('item_id', 38)->whereIn('transaction_id', $transactionsId)->sum('amount'), 2) }}</strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Total Ice Cream Cost ($):
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>{{ number_format($deals::where('item_id', '!=', 38)->whereIn('transaction_id', $transactionsId)->sum(DB::raw('amount')), 2) }}</strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Gross Earning ($):
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>
                            {{number_format($deals::whereIn('transaction_id', $transactionsId)->sum('amount') - $deals::whereIn('transaction_id', $transactionsId)->sum(DB::raw('qty * unit_cost')), 2)}}
                        </strong>
                    </div>
                </div>
                @if($deals::whereIn('transaction_id', $transactionsId)->sum('amount') != 0)
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Gross Earning (%):
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                {{number_format((($deals::whereIn('transaction_id', $transactionsId)->sum('amount') - $deals::whereIn('transaction_id', $transactionsId)->sum(DB::raw('qty * unit_cost'))) / ($deals::whereIn('transaction_id', $transactionsId)->sum('amount'))) * 100 , 2)}}
                            </strong>
                        </div>
                    </div>
                @endif
                @if(count($transactions::whereIn('id', $transactionsId)->get()) > 0)
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            First Inv Date:
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                {{$transactions::where('person_id', $person_id)->oldest()->first()->delivery_date}}
                            </strong>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="table-responsive" style="padding-top: 20px;">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-4">Invoice #</th>
                    <th class="col-md-1"></th>
                    <th class="col-md-1"></th>
                    @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                        <th class="col-md-1 text-center" colspan="2">
                            <a href="/transaction/{{$transaction->id}}/edit">{{$transaction->id}}</a>
                        </th>
                    @endforeach
                </tr>
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-4">Delivery Date</th>
                    <th class="col-md-1"></th>
                    <th class="col-md-1"></th>
                    @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                        <th class="col-md-1 text-center" colspan="2">{{$transaction->delivery_date}}</th>
                    @endforeach
                </tr>
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-4">Delivered By</th>
                    <th class="col-md-1"></th>
                    <th class="col-md-1"></th>
                    @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                        <th class="col-md-1 text-center" colspan="2">{{$transaction->driver}}</th>
                    @endforeach
                </tr>
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-4">Payment</th>
                    <th class="col-md-1"></th>
                    <th class="col-md-1"></th>
                    @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                        <th class="col-md-1 text-center" colspan="2">{{$transaction->pay_method}}</th>
                    @endforeach
                </tr>
                <tr></tr>
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-4">Item</th>
                    <th class="col-md-1 text-center">Total Qty</th>
                    <th class="col-md-1 text-center">Total $</th>
                    @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                        <th class="col-md-1 text-center">Qty</th>
                        <th class="col-md-1 text-center">$</th>
                    @endforeach
                </tr>

                @foreach($items::whereIn('id', $itemsId)->orderBy('product_id', 'asc')->get() as $item)
                <tr>
                    <td class="col-md-4">{{$item->product_id}} - {{$item->name}}</td>
                    <td class="col-md-1 text-right">
                        {{number_format($deals::whereIn('transaction_id', $transactionsId)->whereItemId($item->id)->sum('qty'), 4)}}
                    </td>
                    <td class="col-md-1 text-right">
                        {{number_format($deals::whereIn('transaction_id', $transactionsId)->whereItemId($item->id)->sum('amount'), 2)}}
                    </td>
                    @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                        <td class="col-md-1 text-right">
                            {{number_format($deals::whereTransactionId($transaction->id)->whereItemId($item->id)->sum('qty'), 4)}}
                        </td>
                        <td class="col-md-1 text-right">
                            {{number_format($deals::whereTransactionId($transaction->id)->whereItemId($item->id)->sum('amount'), 2)}}
                        </td>
                    @endforeach
                </tr>
                @endforeach

                @if($people::find($person_id) and count($transactions::whereIn('id', $transactionsId)->get()) > 0)
                    <tr>
                        <th class="col-md-4">Total</th>
                        <th class="col-md-1 text-right">
                            {{number_format($deals::whereIn('transaction_id', $transactionsId)->sum('qty'), 4)}}
                        </th>
                        <th class="col-md-1 text-right">
                            {{number_format($deals::whereIn('transaction_id', $transactionsId)->sum('amount'), 2)}}
                        </th>
                        @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                            <td class="col-md-1 text-right">
                                {{number_format($deals::whereTransactionId($transaction->id)->sum('qty'), 4)}}
                            </td>
                            <td class="col-md-1 text-right">
                                {{number_format($deals::whereTransactionId($transaction->id)->sum('amount'), 2)}}
                            </td>
                        @endforeach
                    </tr>
                @else
                    <tr>
                        <td colspan="14" class="text-center">No results found</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>
</div>

    @if($people::find($person_id) and count($transactions::whereIn('id', $transactionsId)->get()) > 0)
        @if($people::find($person_id)->is_vending)
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Vending Machine Data: {{$people::find($person_id)->cust_id}} - {{$people::find($person_id)->company}}
                </div>
                <div class="panel-body">

                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Price Per Piece ($):
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>{{number_format($people::find($person_id)->vending_piece_price, 2)}}</strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Monthly Rental ($):
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>{{number_format($people::find($person_id)->vending_monthly_rental, 2)}}</strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Profit Sharing:
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>{{$people::find($person_id)->vending_profit_sharing}}</strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Total Sales Qty:
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>
                                    {{number_format($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->analog_clock - $transactions::isAnalog()->whereIn('id', $transactionsId)->oldest()->first()->analog_clock, 4)}}
                                </strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Average Sales Per Day:
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>
                                    @if(count($transactions::whereIn('id', $transactionsId)->get()) > 1)
                                        @php
                                            $delivery_to = request('delivery_to') ? request('delivery_to') : $transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->delivery_date;
                                            $delivery_from = request('delivery_from') ? request('delivery_from') : $transactions::isAnalog()->whereIn('id', $transactionsId)->oldest()->first()->delivery_date;
                                            $day_diff = \Carbon\Carbon::parse($delivery_from)->diffInDays(\Carbon\Carbon::parse($delivery_to));
                                        @endphp
                                        {{ $day_diff ? number_format(($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->analog_clock - $transactions::isAnalog()->whereIn('id', $transactionsId)->oldest()->first()->analog_clock) / $day_diff, 2) : ''}}
                                    @else
                                        N/A
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                    <div class="table-responsive" style="padding-top: 20px;">
                        <table class="table table-list-search table-hover table-bordered">
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-3">Invoice #</th>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                                    <th class="col-md-3 text-center">
                                        <a href="/transaction/{{$transaction->id}}/edit">
                                            {{$transaction->id}}
                                        </a>
                                    </th>
                                @endforeach
                            </tr>
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-3">Delivery Date</th>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                                    <th class="col-md-3 text-center">{{$transaction->delivery_date}}</th>
                                @endforeach
                            </tr>
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-3">Delivered By</th>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                                    <th class="col-md-3 text-center">{{$transaction->driver}}</th>
                                @endforeach
                            </tr>
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-3">Payment</th>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                                    <th class="col-md-3 text-center">{{$transaction->pay_method}}</th>
                                @endforeach
                            </tr>
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-3">Analog Required</th>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                                    <th class="col-md-3 text-center">{{$transaction->is_required_analog ? 'Yes' : 'No'}}</th>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="col-md-3">Sale Quatity (Based on Analog)</th>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $index => $transaction)
                                    <td class="col-md-3 text-right">
                                        {{$index + 1 < count($transactions::whereIn('id', $transactionsId)->latest()->get()) ? $transaction->analog_clock - $transactions::whereIn('id', $transactionsId)->latest()->get()[$index + 1]->analog_clock : $transaction->digital_clock }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="col-md-3">Digital Clocker</th>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                                    <td class="col-md-3 text-right">{{$transaction->digital_clock}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="col-md-3">Analog Clocker</th>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                                    <td class="col-md-3 text-right">{{$transaction->analog_clock}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="col-md-3">Balance Coin</th>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                                    <td class="col-md-3 text-right">{{$transaction->balance_coin}}</td>
                                @endforeach
                            </tr>
                        </table>

                        {{-- need to filter with analog required flag --}}
                        <table class="table table-list-search table-hover table-bordered">
                            <tr>
                                <th class="col-md-2">Payment Received</th>
                                <th class="col-md-1 text-left">Total</th>
                            </tr>
                            <tr>
                                <td class="col-md-2">Expected Payment Received</td>
                                <td class="col-md-1 text-right">
                                    {{ number_format(($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->analog_clock - $transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->get()[count($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->get()) - 1]->analog_clock) * $people::find($person_id)->vending_piece_price , 2) }}
                                </td>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $index => $transaction)
                                    <td class="col-md-3 text-right">
                                        @if($transaction->is_required_analog)
                                        {{$index + 1 < count($transactions::whereIn('id', $transactionsId)->latest()->get()) ? number_format(($transaction->analog_clock - $transactions::whereIn('id', $transactionsId)->latest()->get()[$index + 1]->analog_clock) * $people::find($person_id)->vending_piece_price, 2) : 0.00}}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <tr>
                                <td class="col-md-2">Balance Coin</td>
                                <td class="col-md-1"></td>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                                    <td class="col-md-3 text-right">
                                        @if($transaction->is_required_analog)
                                            {{number_format($transaction->balance_coin, 2)}}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="col-md-2">{{$items::whereProductId('051')->first()->product_id}} - {{$items::whereProductId('051')->first()->name}}</td>
                                <td></td>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                                    <td class="col-md-3 text-right">
                                        @if($transaction->is_required_analog)
                                            {{$deals::whereTransactionId($transaction->id)->whereItemId($items::whereProductId('051')->first()->id)->first() ? number_format($deals::whereTransactionId($transaction->id)->whereItemId($items::whereProductId('051')->first()->id)->first()->amount, 2) : 0.00}}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                             <tr>
                                <td class="col-md-2">Actual Subtotal Received</td>
                                <td class="col-md-1 text-right">{{number_format($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->balance_coin + ($deals::isTransactionAnalog()->whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('051')->first()->id)->sum('amount')) + ($deals::isTransactionAnalog()->whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('052')->first()->id)->sum('amount')), 2)}}</td>
                            </tr>
                            <tr>
                                <th class="col-md-2">Difference(Actual - Expected)</th>
                                <td class="col-md-1 text-right">{{number_format(($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->balance_coin + ($deals::isTransactionAnalog()->whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('051')->first()->id)->sum('amount')) + ($deals::isTransactionAnalog()->whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('052')->first()->id)->sum('amount'))) - (($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->analog_clock - $transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->get()[count($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->get()) - 1]->analog_clock) * $people::find($person_id)->vending_piece_price), 2)}}</td>
                            </tr>
                            <tr>
                                <td class="col-md-2">
                                    {{$items::whereProductId('051a')->first()->product_id}} - {{$items::whereProductId('051a')->first()->name}}
                                </td>
                                <td class="col-md-1 text-right">
                                    {{number_format($deals::isTransactionAnalog()->whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('051a')->first()->id)->sum('amount'), 2)}}
                                </td>
                                @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                                    <td class="col-md-3 text-right">
                                        @if($transaction->is_required_analog)
                                            {{$deals::whereTransactionId($transaction->id)->whereItemId($items::whereProductId('051a')->first()->id)->first() ? number_format($deals::whereTransactionId($transaction->id)->whereItemId($items::whereProductId('051a')->first()->id)->first()->amount, 2) : 0.00}}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="col-md-2">Stock Value in VM</th>
                                <td class="col-md-1 text-right">
                                    {{ number_format(($deals::isTransactionAnalog()->whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('051a')->first()->id)->sum('amount')) + ($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->balance_coin + ($deals::isTransactionAnalog()->whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('051')->first()->id)->sum('amount')) + ($deals::isTransactionAnalog()->whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('052')->first()->id)->sum('amount'))), 2)}}
                                </td>
                            </tr>
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