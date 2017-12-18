@inject('people', 'App\Person')
@inject('deals', 'App\Deal')
@inject('items', 'App\Item')
@inject('transactions', 'App\Transaction')
@inject('ftransactions', 'App\Ftransaction')
<meta charset="utf-8">
<table>
    <tbody>
        @if($people::find($person_id))
            <tr>
                <th style="border: 1px solid #000000">
                    {{$people::find($person_id)->cust_id}} - {{$people::find($person_id)->company}}
                </th>
                <th style="border: 1px solid #000000">
                    {{$request->status}}
                </th>
                @if($request->delivery_from and $request->delivery_to)
                    <th style="border: 1px solid #000000">
                        {{$request->delivery_from}}
                    </th>
                    <th style="border: 1px solid #000000">
                        {{$request->delivery_to}}
                    </th>
                @endif
            </tr>
        @endif
        <tr></tr>
        <tr></tr>
        @php
            $revenue = 0;
            $costs = 0;
            $gross_earn = 0;
            $gross_earn_percent = 0;

            $revenue = $ftransactions::whereIn('id', $ftransactionsId)->sum('total');
            $costs = $deals::whereIn('transaction_id', $transactionsId)->sum('amount');
            $gross_earn = $revenue - $costs;
            if($revenue) {
                $gross_earn_percent = $gross_earn/$revenue * 100;
            }
        @endphp
        <tr>
            <th>Total Revenue ($)</th>
            <td data-format="0.00">{{$revenue ? $revenue : 0}}</td>
        </tr>
        <tr>
            <th>Total Ice Cream Cost ($)</th>
            <td data-format="0.00">{{$costs ? $costs : 0}}</td>
        </tr>
        <tr>
            <th>Gross Earning ($)</th>
            <td data-format="0.00">{{$gross_earn}}</td>
        </tr>
        @if($deals::whereIn('transaction_id', $transactionsId)->sum('amount') != 0)
            <tr>
                <th>Gross Earning (%)</th>
                <td data-format="0.00">
                    {{$gross_earn_percent}}
                </td>
            </tr>
        @endif
        @if(count($transactions::whereIn('id', $transactionsId)->get()) > 0)
            <tr>
                <th>First Inv Date</th>
                <td align="right">
                    {{$transactions::whereIn('id', $transactionsId)->oldest()->first()->delivery_date}}
                </td>
            </tr>
        @endif
        <tr></tr>
        <tr>
            <th>Invoice #</th>
            <th></th>
            <th></th>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                <th colspan="2" align="center">{{$transaction->id}}</th>
            @endforeach
        </tr>
        <tr>
            <th>Delivery Date</th>
            <th></th>
            <th></th>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                <th colspan="2" align="center">{{$transaction->delivery_date}}</th>
            @endforeach
        </tr>
        <tr>
            <th>Delivered By</th>
            <th></th>
            <th></th>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                <th colspan="2" align="center">{{$transaction->driver}}</th>
            @endforeach
        </tr>
        <tr>
            <th>Payment</th>
            <th></th>
            <th></th>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                <th colspan="2" align="center">{{$transaction->pay_method}}</th>
            @endforeach
        </tr>
        <tr>
            <th>Analog Required</th>
            <th></th>
            <th></th>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                <td colspan="2" align="center">{{$transaction->is_required_analog ? 'Yes' : 'No'}}</td>
            @endforeach
        </tr>
        <tr>
            <th align="center">Item</th>
            <th align="center">Total Qty</th>
            <th align="center">Total $</th>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                <th align="center">Qty</th>
                <th align="center">$</th>
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
            <td>{{$item->product_id}} - {{$item->name}}</td>
            <td data-format="0">{{$pieces}}</td>
            <td data-format="0.00">{{$deals::whereIn('transaction_id', $transactionsId)->whereItemId($item->id)->sum('amount')}}</td>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                <td data-format="0">{{$deals::whereTransactionId($transaction->id)->whereItemId($item->id)->sum(DB::raw('CASE WHEN divisor>1 THEN dividend ELSE qty *'.$item->base_unit.' END'))}}</td>
                <td data-format="0.00">{{$deals::whereTransactionId($transaction->id)->whereItemId($item->id)->sum('amount')}}</td>
            @endforeach
        </tr>
        @endforeach

        <tr>
            <th>Total</th>
            <th data-format="0">{{$total_pieces}}</th>
            <th data-format="0.00">{{$deals::whereIn('transaction_id', $transactionsId)->sum('amount')}}</th>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
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
                <td data-format="0">{{$piece_subtotal}}</td>
                <td data-format="0.00">{{$deals::whereTransactionId($transaction->id)->sum('amount')}}</td>
            @endforeach
        </tr>
    </tbody>
</table>

@if($people::find($person_id) and count($transactions::whereIn('id', $transactionsId)->get()) > 0)
    @if($people::find($person_id)->is_vending)
    <table>
        <tbody>
            <tr></tr>
            <tr>
                <th>Price Per Piece ($)</th>
                <td data-format="0.00">{{$people::find($person_id)->vending_piece_price}}</td>
            </tr>
            <tr>
                <th>Monthly Rental ($)</th>
                <td data-format="0.00">{{$people::find($person_id)->vending_monthly_rental}}</td>
            </tr>
            <tr>
                <th>Profit Sharing</th>
                <td data-format="0.00">{{$people::find($person_id)->vending_profit_sharing}}</td>
            </tr>
            <tr>
                <th>Total Sales Qty</th>
                <td data-format="0.00">{{$transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->analog_clock - $transactions::isAnalog()->whereIn('id', $transactionsId)->oldest()->first()->analog_clock}}</td>
            </tr>
            <tr>
                <th>Average Sales Per Day</th>
                <td data-format="0.00">
                    @if(count($transactions::whereIn('id', $transactionsId)->get()) > 1)
                        {{
                            \Carbon\Carbon::parse($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->delivery_date)->diffInDays(\Carbon\Carbon::parse($transactions::isAnalog()->whereIn('id', $transactionsId)->oldest()->first()->delivery_date))
                            ?
                            ($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->analog_clock - $transactions::isAnalog()->whereIn('id', $transactionsId)->oldest()->first()->analog_clock) / \Carbon\Carbon::parse($transactions::isAnalog()->whereIn('id', $transactionsId)->latest()->first()->delivery_date)->diffInDays(\Carbon\Carbon::parse($transactions::isAnalog()->whereIn('id', $transactionsId)->oldest()->first()->delivery_date))
                            :
                            ''
                        }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr></tr>

            <tr>
                <th colspan="2">Sale Quatity (Based on Analog)</th>
                <th></th>
                @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $index => $transaction)
                    <td data-format="0" colspan="2" align="center">
                        @if($transaction->is_required_analog)
                        {{($index + 1) < count($transactions::whereIn('id', $transactionsId)->latest()->get()) ? $transaction->analog_clock - $transactions::whereIn('id', $transactionsId)->latest()->get()[$index + 1]->analog_clock : $transaction->digital_clock }}
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                <th colspan="2">Digital Clocker</th>
                <th></th>
                @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $index => $transaction)
                    <td data-format="0" colspan="2" align="center">
                        @if($transaction->is_required_analog)
                            {{$transaction->digital_clock}}
                        @endif
                    </td>

                @endforeach
            </tr>
            <tr>
                <th colspan="2">Analog Clocker</th>
                <th></th>
                @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $index => $transaction)
                    <td data-format="0" colspan="2" align="center">
                        @if($transaction->is_required_analog)
                            {{$transaction->analog_clock}}
                        @endif
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
    @endif
@endif