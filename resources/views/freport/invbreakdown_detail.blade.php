@inject('people', 'App\Person')
@inject('deals', 'App\Deal')
@inject('items', 'App\Item')
@inject('transactions', 'App\Transaction')
@inject('ftransactions', 'App\Ftransaction')
@inject('variances', 'App\Variance')

<div class="panel panel-info">
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

            @php
                $revenue = 0;
                $costs = 0;
                $gross_earn = 0;
                $gross_earn_percent = 0;

                $revenue = $ftransactions::whereIn('id', $ftransactionsId)->sum('total');
                $costs = \DB::table('transactions')
                            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                            ->whereIn('transactions.id', $transactionsId)
                            ->sum(
                                    DB::raw('ROUND((CASE WHEN transactions.gst=1 THEN (
                                                CASE
                                                WHEN transactions.is_gst_inclusive=0
                                                THEN total*((100+transactions.gst_rate)/100)
                                                ELSE transactions.total
                                                END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)')
                            );
                $gross_earn = $revenue - $costs;
                if($revenue and $revenue != 0) {
                    $gross_earn_percent = $gross_earn/$revenue * 100;
                }
            @endphp
            <div class="col-md-5 col-sm-5 col-xs-12">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Total Revenue ($):
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>{{ $revenue ? number_format($revenue, 2) : 0 }}</strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Total Costs ($):
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>{{ $costs ? number_format($costs, 2) : 0 }}</strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Gross Earning ($):
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>
                            {{number_format($gross_earn, 2)}}
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
                                {{number_format($gross_earn_percent, 2)}}
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
                    <th class="col-md-1 text-center">Total Qty (pcs)</th>
                    <th class="col-md-1 text-center">Total $</th>
                    @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                        <th class="col-md-1 text-center">Qty (pcs)</th>
                        <th class="col-md-1 text-center">$</th>
                    @endforeach
                </tr>

                @php
                    $total_pieces = 0;
                @endphp
                @foreach($items::whereIn('id', $itemsId)->orderBy('product_id', 'asc')->get() as $item)
                    @php
                        $pieces = number_format($deals::whereIn('transaction_id', $transactionsId)->whereItemId($item->id)->sum(DB::raw('CASE WHEN divisor>1 THEN dividend ELSE qty *'.$item->base_unit.' END')));
                        $total_pieces += $pieces;
                    @endphp
                <tr>
                    <td class="col-md-4">{{$item->product_id}} - {{$item->name}}</td>
                    <td class="col-md-1 text-right">
                        {{$pieces}}
                    </td>
                    <td class="col-md-1 text-right">
                        {{number_format($deals::whereIn('transaction_id', $transactionsId)->whereItemId($item->id)->sum('amount'), 2)}}
                    </td>
                    @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                        <td class="col-md-1 text-right">
                            {{number_format($deals::whereTransactionId($transaction->id)->whereItemId($item->id)->sum(DB::raw('CASE WHEN divisor>1 THEN dividend ELSE qty *'.$item->base_unit.' END')))}}
                        </td>
                        <td class="col-md-1 text-right">
                            {{number_format($deals::whereTransactionId($transaction->id)->whereItemId($item->id)->sum('amount'), 2)}}
                        </td>
                    @endforeach
                </tr>
                @endforeach

                @php
                    $person = $people::find($person_id);
                @endphp
                @if($person and count($transactions::whereIn('id', $transactionsId)->get()) > 0)
                    @if($person->profile->gst)
                    <tr>
                        <th class="col-md-4">Subtotal</th>
                        <th class="col-md-1 text-right">
                        </th>
                        <td class="col-md-1 text-right">
                            {{number_format($deals::whereIn('transaction_id', $transactionsId)->sum('amount'), 2)}}
                        </td>
                        @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                            <td class="col-md-1 text-right">
                            </td>
                            <td class="col-md-1 text-right">
                                {{number_format($deals::whereTransactionId($transaction->id)->sum('amount'), 2)}}
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th class="col-md-4">GST ({{$person->gst_rate + 0}}%)</th>
                        <th class="col-md-1 text-right">
                        </th>
                        <td class="col-md-1 text-right">
                            {{number_format($deals::whereIn('transaction_id', $transactionsId)->sum(DB::raw('amount * '.$person->gst_rate/100 )), 2)}}
                        </td>
                        @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                            <td class="col-md-1 text-right">
                            </td>
                            <td class="col-md-1 text-right">
                                {{number_format($deals::whereTransactionId($transaction->id)->sum(DB::raw('amount * '.$person->gst_rate/100 )), 2)}}
                            </td>
                        @endforeach
                    </tr>
                    @endif
                    <tr>
                        <th class="col-md-4">Total</th>
                        <th class="col-md-1 text-right">
                            {{-- {{number_format($deals::whereIn('transaction_id', $transactionsId)->sum('qty'), 4)}} --}}
                            {{number_format($total_pieces)}}
                        </th>
                        <th class="col-md-1 text-right">
                            {{number_format($transactions::whereIn('id', $transactionsId)->sum(DB::raw('total * '.(100 + $person->gst_rate)/100)), 2)}}
                        </th>
                        @foreach($transactions::whereIn('id', $transactionsId)->latest()->take(3)->get() as $transaction)
                            @php
                                $piece_subtotal = 0;
                                $dealsData = $deals::whereTransactionId($transaction->id)->get();
                                foreach($dealsData as $dealData) {
                                    if($dealData->divisor > 1) {
                                        $piece_subtotal += $dealData->dividend;
                                    }else {
                                        $piece_subtotal += $dealData->qty * $dealData->item->base_unit;
                                    }
                                }
                            @endphp
                            <td class="col-md-1 text-right">
                                {{number_format($piece_subtotal)}}
                            </td>
                            <td class="col-md-1 text-right">
                                {{number_format($transactions::where('id', $transaction->id)->sum(DB::raw('total * '.(100 + $person->gst_rate)/100)), 2)}}
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
            <div class="panel panel-info">
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
                                Total Sales Qty (pcs):
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>
                                    {{number_format($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->analog_clock - $transactions::isAnalog()->whereIn('id', $transactionsId)->oldest()->first()->analog_clock)}}
                                </strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Average Sales Per Day (pcs):
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>
                                    @if(count($transactions::whereIn('id', $transactionsId)->get()) > 1)
                                        @php
                                            $delivery_to = request('delivery_to') ? request('delivery_to') : $transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->delivery_date;
                                            $delivery_from = request('delivery_from') ? request('delivery_from') : $transactions::isAnalog()->whereIn('id', $transactionsId)->oldest()->first()->delivery_date;
                                            $day_diff = \Carbon\Carbon::parse($delivery_from)->diffInDays(\Carbon\Carbon::parse($delivery_to)) + 1;
                                        @endphp
                                        {{ $day_diff ? number_format(($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->analog_clock - $transactions::isAnalog()->whereIn('id', $transactionsId)->oldest()->first()->analog_clock) / $day_diff, 2) : ''}}
                                    @else
                                        N/A
                                    @endif
                                </strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Difference Stock In & Analog
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>{{number_format($total_pieces - ($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->analog_clock - $transactions::isAnalog()->whereIn('id', $transactionsId)->oldest()->first()->analog_clock), 2)}}</strong>
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