@inject('deals', 'App\Deal')
@inject('transactions', 'App\Transaction')
@inject('products', 'App\Item')
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
                <th colspan="2" align="center">{{$transaction->id}}</th>
            @endforeach
        </tr>
        <tr>
            <th>Delivery Date</th>
            <th></th>
            <th></th>
            @foreach($allTransactions as $transaction)
                <th colspan="2" align="center">{{$transaction->delivery_date}}</th>
            @endforeach
        </tr>
        <tr>
            <th>Delivered By</th>
            <th></th>
            <th></th>
            @foreach($allTransactions as $transaction)
                <th colspan="2" align="center">{{$transaction->driver}}</th>
            @endforeach
        </tr>
        <tr>
            <th>Payment</th>
            <th></th>
            <th></th>
            @foreach($allTransactions as $transaction)
                <th colspan="2" align="center">{{$transaction->pay_method}}</th>
            @endforeach
        </tr>
        <tr>
            <th align="center">Item</th>
            <th align="center">Total Qty</th>
            <th align="center">Total $</th>
            @foreach($allTransactions as $transaction)
                <th align="center">Qty</th>
                <th align="center">$</th>
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
                <th colspan="2">Sale Quatity (Based on Analog)</th>
                <th></th>
                @foreach($allTransactions as $index => $transaction)
                    <td data-format="0" colspan="2" align="center">
                        {{($index + 1) < count($allTransactions) ? $transaction->analog_clock - $allTransactions[$index + 1]->analog_clock : 0 }}
                    </td>
                @endforeach
            </tr>
            <tr>
                <th colspan="2">Digital Clocker</th>
                <th></th>
                @foreach($allTransactions as $transaction)
                    <td data-format="0" colspan="2" align="center">{{$transaction->digital_clock}}</td>
                @endforeach
            </tr>
            <tr>
                <th colspan="2">Analog Clocker</th>
                <th></th>
                @foreach($allTransactions as $transaction)
                    <td data-format="0" colspan="2" align="center">{{$transaction->analog_clock}}</td>
                @endforeach
            </tr>
            <tr>
                <th colspan="2">Balance Coin</th>
                <th></th>
                @foreach($allTransactions as $transaction)
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
                {{ ($allTransactions[0]->analog_clock - $allTransactions[count($allTransactions) - 1]->analog_clock) * $person->vending_piece_price }}
            </td>
            @foreach($allTransactions as $index => $transaction)
                <td colspan="2" data-format="0.00" align="center">
                    {{$index + 1 < count($allTransactions) ? ($transaction->analog_clock - $allTransactions[$index + 1]->analog_clock) * $person->vending_piece_price : 0.00}}
                </td>
            @endforeach
        </tr>
        <tr>
            <td colspan="2">Balance Coin</td>
            <td></td>
            @foreach($allTransactions as $transaction)
                <td colspan="2" data-format="0.00" align="center">{{$transaction->balance_coin}}</td>
            @endforeach
        </tr>
        </table>

        @if($deals::whereIn('transaction_id', $allTransactionsId)->whereItemId($products::whereProductId('051')->first()->id)->first() and $deals::whereIn('transaction_id', $allTransactionsId)->whereItemId($products::whereProductId('051a')->first()->id)->first() and $deals::whereIn('transaction_id', $allTransactionsId)->whereItemId($products::whereProductId('052')->first()->id)->first())
            <tr>
                <td colspan="2">{{$products::whereProductId('051')->first()->product_id}} - {{$products::whereProductId('051')->first()->name}}</td>
                <td></td>
                @foreach($allTransactions as $transaction)
                    <td colspan="2" data-format="0.00" align="center">
                        {{$deals::whereTransactionId($transaction->id)->whereItemId($products::whereProductId('051')->first()->id)->first() ? $deals::whereTransactionId($transaction->id)->whereItemId($products::whereProductId('051')->first()->id)->first()->amount : 0.00}}
                    </td>
                @endforeach
            </tr>
            <tr>
                <td colspan="2">Actual Subtotal Received</td>
                <td data-format="0.00">{{$allTransactions[0]->balance_coin + ($deals::whereIn('transaction_id', $allTransactionsId)->whereItemId($products::whereProductId('051')->first()->id)->sum('amount')) + ($deals::whereIn('transaction_id', $allTransactionsId)->whereItemId($products::whereProductId('052')->first()->id)->sum('amount'))}}</td>
            </tr>
            <tr>
                <th colspan="2">Difference(Actual - Expected)</th>
                <td data-format="0.00">{{ $allTransactions[0]->balance_coin + ($deals::whereIn('transaction_id', $allTransactionsId)->whereItemId($products::whereProductId('051')->first()->id)->sum('amount')) - ($allTransactions[0]->analog_clock - $allTransactions[count($allTransactions)-1]->analog_clock) * $person->vending_piece_price}}</td>
            </tr>
            <tr>
                <td colspan="2">
                    {{$products::whereProductId('051a')->first()->product_id}} - {{$products::whereProductId('051a')->first()->name}}
                </td>
                <td  data-format="0.00">
                    {{$deals::whereIn('transaction_id', $allTransactionsId)->whereItemId($products::whereProductId('051a')->first()->id)->sum('amount')}}
                </td>
                @foreach($allTransactions as $transaction)
                    <td data-format="0.00" colspan="2" align="center">
                        {{ $deals::whereTransactionId($transaction->id)->whereItemId($products::whereProductId('051a')->first()->id)->first() ? $deals::whereTransactionId($transaction->id)->whereItemId($products::whereProductId('051a')->first()->id)->first()->amount : 0.00 }}
                    </td>
                @endforeach
            </tr>
            <tr>
                <th colspan="2">Stock Value in VM</th>
                <td data-format="0.00">
                    {{ ($deals::whereIn('transaction_id', $allTransactionsId)->whereItemId($products::whereProductId('051a')->first()->id)->sum('amount')) + ($allTransactions[0]->balance_coin + ($deals::whereIn('transaction_id', $allTransactionsId)->whereItemId($products::whereProductId('051')->first()->id)->sum('amount')) + ($deals::whereIn('transaction_id', $allTransactionsId)->whereItemId($products::whereProductId('052')->first()->id)->sum('amount')))}}
                </td>
            </tr>
        @endif
    </table>
    @endif
@endif