@inject('people', 'App\Person')
@inject('deals', 'App\Deal')
@inject('items', 'App\Item')
@inject('transactions', 'App\Transaction')
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
        <tr>
            <th>Total Revenue ($)</th>
            <td data-format="0.00">{{$deals::whereIn('transaction_id', $transactionsId)->sum('amount')}}</td>
        </tr>
        <tr>
            <th>Total Ice Cream Cost ($)</th>
            <td data-format="0.00">{{$deals::whereIn('transaction_id', $transactionsId)->sum(DB::raw('qty * unit_cost'))}}</td>
        </tr>
        <tr>
            <th>Gross Earning ($)</th>
            <td data-format="0.00">{{$deals::whereIn('transaction_id', $transactionsId)->sum('amount') - $deals::whereIn('transaction_id', $transactionsId)->sum(DB::raw('qty * unit_cost'))}}</td>
        </tr>
        @if($deals::whereIn('transaction_id', $transactionsId)->sum('amount') != 0)
            <tr>
                <th>Gross Earning (%)</th>
                <td data-format="0.00">
                    {{(($deals::whereIn('transaction_id', $transactionsId)->sum('amount') - $deals::whereIn('transaction_id', $transactionsId)->sum(DB::raw('qty * unit_cost'))) / ($deals::whereIn('transaction_id', $transactionsId)->sum('amount'))) * 100}}
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
            <th align="center">Item</th>
            <th align="center">Total Qty</th>
            <th align="center">Total $</th>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                <th align="center">Qty</th>
                <th align="center">$</th>
            @endforeach
        </tr>
        @foreach($items::whereIn('id', $itemsId)->orderBy('product_id', 'asc')->get() as $item)
        <tr>
            <td>{{$item->product_id}} - {{$item->name}}</td>
            <td data-format="0.0000">{{$deals::whereIn('transaction_id', $transactionsId)->whereItemId($item->id)->sum('qty')}}</td>
            <td data-format="0.00">{{$deals::whereIn('transaction_id', $transactionsId)->whereItemId($item->id)->sum('amount')}}</td>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                <td data-format="0.0000">{{$deals::whereTransactionId($transaction->id)->whereItemId($item->id)->sum('qty')}}</td>
                <td data-format="0.00">{{$deals::whereTransactionId($transaction->id)->whereItemId($item->id)->sum('amount')}}</td>
            @endforeach
        </tr>
        @endforeach

        <tr>
            <th>Total</th>
            <th data-format="0.0000">{{$deals::whereIn('transaction_id', $transactionsId)->sum('qty')}}</th>
            <th data-format="0.00">{{$deals::whereIn('transaction_id', $transactionsId)->sum('amount')}}</th>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                <td data-format="0.0000">{{$deals::whereTransactionId($transaction->id)->sum('qty')}}</td>
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
                <td data-format="0.00">{{$transactions::whereIn('id', $transactionsId)->latest()->first()->analog_clock - $transactions::whereIn('id', $transactionsId)->oldest()->first()->analog_clock}}</td>
            </tr>
            <tr>
                <th>Average Sales Per Day</th>
                <td data-format="0.00">
                    @if(count($transactions::whereIn('id', $transactionsId)->get()) > 1)
                        {{  $transactions::whereIn('id', $transactionsId)->latest()->first()->analog_clock - $transactions::whereIn('id', $transactionsId)->oldest()->first()->analog_clock) / \Carbon\Carbon::parse($transactions::whereIn('id', $transactionsId)->latest()->first()->delivery_date)->diffInDays(\Carbon\Carbon::parse($transactions::whereIn('id', $transactionsId)->oldest()->first()->delivery_date)}}
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
                        {{($index + 1) < count($transactions::whereIn('id', $transactionsId)->latest()->get()) ? $transaction->analog_clock - $transactions::whereIn('id', $transactionsId)->latest()->get()[$index + 1]->analog_clock : 0 }}
                    </td>
                @endforeach
            </tr>
            <tr>
                <th colspan="2">Digital Clocker</th>
                <th></th>
                @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $index => $transaction)
                    <td data-format="0" colspan="2" align="center">{{$transaction->digital_clock}}</td>
                @endforeach
            </tr>
            <tr>
                <th colspan="2">Analog Clocker</th>
                <th></th>
                @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $index => $transaction)
                    <td data-format="0" colspan="2" align="center">{{$transaction->analog_clock}}</td>
                @endforeach
            </tr>
            <tr>
                <th colspan="2">Balance Coin</th>
                <th></th>
                @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $index => $transaction)
                    <td data-format="0.00" colspan="2" align="center">{{$transaction->balance_coin}}</td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <table>
        <tr></tr>
        <tr>
            <th colspan="2">Payment Received</th>
            <th>Total</th>
        </tr>
        <tr>
            <td colspan="2">Expected Payment Received</td>
            <td data-format="0.00">
                {{ ($transactions::whereIn('id', $transactionsId)->latest()->get()[0]->analog_clock - $transactions::whereIn('id', $transactionsId)->latest()->get()[count($transactions::whereIn('id', $transactionsId)->latest()->get()) - 1]->analog_clock) * $people::find($person_id)->vending_piece_price }}
            </td>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $index => $transaction)
                <td colspan="2" data-format="0.00" align="center">
                    {{$index + 1 < count($transactions::whereIn('id', $transactionsId)->latest()->get()) ? ($transaction->analog_clock - $transactions::whereIn('id', $transactionsId)->latest()->get()[$index + 1]->analog_clock) * $people::find($person_id)->vending_piece_price : 0.00}}
                </td>
            @endforeach
        </tr>
        <tr>
            <td colspan="2">Balance Coin</td>
            <td></td>
            @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                <td colspan="2" data-format="0.00" align="center">{{$transaction->balance_coin}}</td>
            @endforeach
        </tr>
        </table>

        @if($deals::whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('051')->first()->id)->first() and $deals::whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('051a')->first()->id)->first() and $deals::whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('052')->first()->id)->first())
            <tr>
                <td colspan="2">{{$items::whereProductId('051')->first()->product_id}} - {{$items::whereProductId('051')->first()->name}}</td>
                <td></td>
                @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                    <td colspan="2" data-format="0.00" align="center">
                        {{$deals::whereTransactionId($transaction->id)->whereItemId($items::whereProductId('051')->first()->id)->first() ? $deals::whereTransactionId($transaction->id)->whereItemId($items::whereProductId('051')->first()->id)->first()->amount : 0.00}}
                    </td>
                @endforeach
            </tr>
            <tr>
                <td colspan="2">Actual Subtotal Received</td>
                <td data-format="0.00">{{$transactions::whereIn('id', $transactionsId)->latest()->get()[0]->balance_coin + ($deals::whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('051')->first()->id)->sum('amount')) + ($deals::whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('052')->first()->id)->sum('amount'))}}</td>
            </tr>
            <tr>
                <th colspan="2">Difference(Actual - Expected)</th>
                <th data-format="0.00">
                    {{ $transactions::whereIn('id', $transactionsId)->latest()->get()[0]->balance_coin + ($deals::whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('051')->first()->id)->sum('amount')) - ($transactions::whereIn('id', $transactionsId)->latest()->get()[0]->analog_clock - $transactions::whereIn('id', $transactionsId)->latest()->get()[count($transactions::whereIn('id', $transactionsId)->latest()->get())-1]->analog_clock) * $people::find($person_id)->vending_piece_price}}
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    {{$items::whereProductId('051a')->first()->product_id}} - {{$items::whereProductId('051a')->first()->name}}
                </td>
                <td  data-format="0.00">
                    {{$deals::whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('051a')->first()->id)->sum('amount')}}
                </td>
                @foreach($transactions::whereIn('id', $transactionsId)->latest()->get() as $transaction)
                    <td data-format="0.00" colspan="2" align="center">
                        {{ $deals::whereTransactionId($transaction->id)->whereItemId($items::whereProductId('051a')->first()->id)->first() ? $deals::whereTransactionId($transaction->id)->whereItemId($items::whereProductId('051a')->first()->id)->first()->amount : 0.00 }}
                    </td>
                @endforeach
            </tr>
            <tr>
                <th colspan="2">Stock Value in VM</th>
                <th data-format="0.00">
                    {{ ($deals::whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('051a')->first()->id)->sum('amount')) + ($transactions::whereIn('id', $transactionsId)->latest()->get()[0]->balance_coin + ($deals::whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('051')->first()->id)->sum('amount')) + ($deals::whereIn('transaction_id', $transactionsId)->whereItemId($items::whereProductId('052')->first()->id)->sum('amount')))}}
                </th>
            </tr>
        @endif
    </table>
    @endif
@endif