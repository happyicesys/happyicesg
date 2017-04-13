@inject('deals', 'App\Deal')
@inject('transactions', 'App\Transaction')
<meta charset="utf-8">
<table>
    <tbody>
        <tr></tr>
        <tr>
            <th>Total Revenue</th>
            <td data-format="0.00">{{$deals::whereIn('transaction_id', $allTransactionsId)->sum('amount')}}</td>
        </tr>
        <tr>
            <th>Total Cost</th>
            <td data-format="0.00">{{$deals::whereIn('transaction_id', $allTransactionsId)->sum(DB::raw('qty * unit_cost'))}}</td>
        </tr>
        <tr>
            <th>Gross Earning</th>
            <td data-format="0.00">{{$deals::whereIn('transaction_id', $allTransactionsId)->sum('amount') - $deals::whereIn('transaction_id', $allTransactionsId)->sum(DB::raw('qty * unit_cost'))}}</td>
        </tr>
        <tr></tr>
        <tr>
            <th>Invoice #</th>
            <th></th>
            <th></th>
            @foreach($allTransactions as $transaction)
                <th colspan="2">{{$transaction->id}}</th>
            @endforeach
        </tr>
        <tr>
            <th>Delivery Date</th>
            <th></th>
            <th></th>
            @foreach($allTransactions as $transaction)
                <th colspan="2">{{$transaction->delivery_date}}</th>
            @endforeach
        </tr>
        <tr>
            <th>Delivered By</th>
            <th></th>
            <th></th>
            @foreach($allTransactions as $transaction)
                <th colspan="2">{{$transaction->driver}}</th>
            @endforeach
        </tr>
        <tr>
            <th>Payment</th>
            <th></th>
            <th></th>
            @foreach($allTransactions as $transaction)
                <th colspan="2">{{$transaction->pay_method}}</th>
            @endforeach
        </tr>
        <tr>
            <th>Item</th>
            <th>Total Qty</th>
            <th>Total $</th>
            @foreach($allTransactions as $transaction)
                <th>Qty</th>
                <th>$</th>
            @endforeach
        </tr>
        @foreach($items as $item)
        <tr>
            <td>{{$item->product_id}} - {{$item->name}}</td>
            <td data-format="0.0000">{{$deals::whereIn('transaction_id', $allTransactionsId)->whereItemId($item->id)->sum('qty')}}</td>
            <td data-format="0.00">{{$deals::whereIn('transaction_id', $allTransactionsId)->whereItemId($item->id)->sum('amount')}}</td>
            @foreach($allTransactions as $transaction)
                <td data-format="0.0000">{{$deals::whereTransactionId($transaction->id)->whereItemId($item->id)->sum('qty')}}</td>
                <td data-format="0.00">{{$deals::whereTransactionId($transaction->id)->whereItemId($item->id)->sum('amount')}}</td>
            @endforeach
        </tr>
        @endforeach

        <tr>
            <th>Total</th>
            <th data-format="0.0000">{{$deals::whereIn('transaction_id', $allTransactionsId)->sum('qty')}}</th>
            <th data-format="0.00">{{$deals::whereIn('transaction_id', $allTransactionsId)->sum('amount')}}</th>
            @foreach($allTransactions as $transaction)
                <td data-format="0.0000">{{$deals::whereTransactionId($transaction->id)->sum('qty')}}</td>
                <td data-format="0.00">{{$deals::whereTransactionId($transaction->id)->sum('amount')}}</td>
            @endforeach
        </tr>
    </tbody>
</table>

@if($person)
    @if($person->is_vending)
    <table>
        <tbody>
            <tr></tr>
            <tr>
                <th>Price Per Piece</th>
                <td data-format="0.00">{{$person->vending_piece_price}}</td>
            </tr>
            <tr>
                <th>Monthly Rental</th>
                <td data-format="0.00">{{$person->vending_monthly_rental}}</td>
            </tr>
            <tr>
                <th>Profit Sharing</th>
                <td data-format="0.00">{{$person->vending_profit_sharing}}</td>
            </tr>
            <tr></tr>
            <tr>
                <th>Sale Quatity (Based on Analog)</th>
                <th></th>
                <th></th>
                @foreach($allTransactions as $index => $transaction)
                    <td data-format="0" colspan="2">
                        {{($index + 1) < count($allTransactions) ? $transaction->analog_clock - $allTransactions[$index + 1]->analog_clock : 0 }}
                    </td>
                @endforeach
            </tr>
            <tr>
                <th>Digital Clocker</th>
                <th></th>
                <th></th>
                @foreach($allTransactions as $transaction)
                    <td data-format="0" colspan="2">{{$transaction->digital_clock}}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    @endif
@endif